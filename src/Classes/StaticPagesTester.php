<?php

namespace SimonVomEyser\LaravelAutomaticTests\Classes;


use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\TestCase;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Symfony\Component\DomCrawler\Crawler;

class StaticPagesTester
{
    public TestCase|OrchestraTestCase $testCase;
    public array $urisHandled = [];
    public string $baseUrl;
    protected bool $ignoreQueryParameters = false;
    protected bool $ignorePageAnchors = false;
    protected bool $skipDefaultAssertion = false;
    protected array $customAssertions = [];

    public function __construct(TestCase|OrchestraTestCase $testCase)
    {
        $this->testCase = $testCase;
        $this->baseUrl = url('/');
    }

    public static function create(TestCase|OrchestraTestCase $testCase = null): self
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
            ->each(function ($node) {
                $href = $node->attr('href');
                $query = parse_url($href, PHP_URL_QUERY);
                $fragment = parse_url($href, PHP_URL_FRAGMENT);

                if ($query && $this->ignoreQueryParameters) {
                    $href = str_replace('?' . $query, '', $href);
                }
                if ($fragment && $this->ignorePageAnchors) {
                    $href = str_replace('#' . $fragment, '', $href);
                }
                return $href;
            });
    }

    public function crawlUriRecursively($uri, $depth = 1, $foundOnUri = ''): void
    {
        if (!$this->shouldCrawl($uri)) {
            return;
        }

        // Actually get the response
        $response = $this->testCase->get($uri);

        // the default assertion is added
        if(!$this->skipDefaultAssertion) {
            $this->applyAssertions($response, $uri, $foundOnUri);
        }

        // if the user added custom assertions, we apply them
        foreach($this->customAssertions as $customAssertion) {
            $customAssertion($response, $uri, $foundOnUri);
        }

        $this->urisHandled[] = $uri;

        $urisFoundOnPage = $this->findUrisInResponse($response);
        $urisToParse = array_filter($urisFoundOnPage, fn($link) => $this->shouldCrawl($link));

        foreach ($urisToParse as $uriToParse) {
            $this->crawlUriRecursively($uriToParse, $depth + 1, $uri);
        }

    }

    public function run(): self
    {
        $this->crawlUriRecursively($this->baseUrl);

        return $this;
    }

    public function ignoreQueryParameters(): self
    {
        $this->ignoreQueryParameters = true;

        return $this;
    }

    public function ignorePageAnchors(): self
    {
        $this->ignorePageAnchors = true;

        return $this;
    }

    public function startFromUrl($baseUrl): self
    {
        $this->baseUrl = url($baseUrl);

        return $this;
    }

    public function skipDefaultAssertion(): self
    {
        $this->skipDefaultAssertion = true;

        return $this;
    }

    public function addAssertion(Callable $cb): self
    {
        $this->customAssertions[] = $cb;

        return $this;
    }

    protected function shouldCrawl($uri): bool
    {
        $isExternal = !str_starts_with($uri, '/') && !str_contains($uri, url('/'));
        $isHandled = in_array($uri, $this->urisHandled);

        return !$isExternal && !$isHandled;
    }


    protected function applyAssertions(TestResponse $response, $uri, $foundOnUri = ""): void
    {
        $foundOnUri = $foundOnUri ?: $this->baseUrl;
        $message = "The url $uri is not returning a success or redirect status code, but the status code " . $response->status();
        $message .= $foundOnUri ? " (found on $foundOnUri)" : "";

        $this->testCase->assertTrue($response->status() <= 399, $message);
    }

}
