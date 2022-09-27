<?php

namespace SimonVomEyser\LaravelAutomaticTests\Classes;


use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class StaticPagesTester
{
    public TestCase $testCase;
    public array $urisHandled = [];
    public string $baseUrl;
    public bool $ignoreQueryParameters = false;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
        $this->baseUrl = url('/');
    }

    public static function create(TestCase $testCase = null): self
    {
        // Helper to get the calling test case
        $trace = debug_backtrace();
        $guessTestCase = $trace[1]['object'] ?? null;
        $testCase = $testCase ?? $guessTestCase;

        return new self($testCase);
    }

    public function findUrisInResponse(TestResponse $response): array
    {
        $crawler = new Crawler($response->getContent());

        return $crawler->filter('a')
            ->each(function($node) {
                if($this->ignoreQueryParameters) {
                    return Str::before($node->attr('href'), '?');
                }
                return $node->attr('href');
            });
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
    }

    public function ignoreQueryParameters($value = true): self
    {
        $this->ignoreQueryParameters = $value;

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
