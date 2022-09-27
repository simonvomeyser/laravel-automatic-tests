<?php

namespace SimonVomEyser\LaravelAutomaticTests\Classes;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\TestResponse;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
use Spatie\Crawler\CrawlQueues\ArrayCrawlQueue;

class StaticPagesTester
{
    public $crawler;
    public $testCase;
    public $urisToTest = [];
    public $urisHandled = [];
    public $baseUrl;
    public $maxCrawlDepth = 4;
    public $currentCrawlDepth = 1;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
        $this->crawler = Crawler::create();
        $this->baseUrl = url('/');

    }

    public static function create(TestCase $testCase = null): self
    {
        // Helper to get the calling test case
        $trace = debug_backtrace();
        $testCase = $testCase ?? $trace[1]['object'];

        return new self($testCase);
    }

    protected function addToUriQueue(string $url): void
    {
        $isExternal = !str_starts_with($url, '/') && (new Uri($url))->getHost() !== (new Uri($this->baseUrl))->getHost();
        $relativeUrl = str_replace($this->baseUrl, '', $url);// The url without the base url

        if ($isExternal) {
            return;
        }

        if (in_array($relativeUrl, $this->urisToTest)) {
            return;
        }

        if (!$relativeUrl) {
            return;
        }

        $this->urisToTest[] = $relativeUrl;
    }

    public function findUrisInResponse(TestResponse $response): array
    {
        $crawler = new \Symfony\Component\DomCrawler\Crawler($response->getContent());

        return $crawler->filter('a')
            ->each(fn($node) => $node->attr('href'));
    }

    public function crawlUriRecursively($uri, $depth = 1, $foundOnUri = ''): void
    {
        if (!$this->shouldCrawl($uri)) {
            return;
        }

        // Actually get the response
        $response = $this->testCase->get($uri);
        $this->applyAssertions($response, $uri, $foundOnUri);

        $this->urisHandled[] = $uri;

        $urisFoundOnPage = $this->findUrisInResponse($response);
        $urisToParse = array_filter($urisFoundOnPage, fn($link) => $this->shouldCrawl($link));

        foreach ($urisToParse as $uriToParse) {
            $this->crawlUriRecursively($uriToParse, $depth + 1, $uri);
        }

    }


    public function run(): void
    {
        $this->crawlUriRecursively('/');

        return;
        $queue = new ArrayCrawlQueue();
        $observer = new ResponseTestCrawlObserver();
        $profile = new CrawlInternalUrls(url('/'));

        dump($this->crawler);
        do {
            $this->crawler
                ->setCrawlQueue($queue)
                ->setCurrentCrawlLimit(1)
                ->setCrawlProfile($profile)
                ->setCrawlObserver($observer)
                ->startCrawling(url('/'));

        } while ($queue->hasPendingUrls());

    }

    public function configureCrawler(callable $cb): self
    {
        $cb($this->crawler);

        return $this;
    }

    protected function shouldCrawl($uri): bool
    {
        $isExternal = !str_starts_with($uri, '/') && !str_contains($uri, $this->baseUrl);
        $isHandled = in_array($uri, $this->urisHandled);

        return !$isExternal && !$isHandled;
    }

    protected function applyAssertions(TestResponse $response, $uri, $foundOnUri = ""): void
    {
        $foundOnUri = $foundOnUri ?: $this->baseUrl;
        $message = "The url $uri is not returning a 4xx or 5xx status code, but a " . $response->status() ." (found on $foundOnUri)";
        $this->testCase->assertTrue($response->status() <= 399, $message);
    }

}
