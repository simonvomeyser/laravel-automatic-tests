<?php

namespace SimonVomEyser\LaravelAutomaticTests\Classes;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use SimonVomEyser\LaravelAutomaticTests\Tests\TestCase;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class ResponseTestCrawlObserver extends CrawlObserver
{
    public int $status = 0;
    public string $crawledUrl = "";

    /**
     * Called when the crawler will crawl the url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     */
    public function willCrawl(UriInterface $url): void
    {
    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawled(
        UriInterface      $url,
        ResponseInterface $response,
        ?UriInterface     $foundOnUrl = null
    ): void
    {

        Assert::assertLessThan(400, $response->getStatusCode());
        $this->crawledUrl = $url->__toString();
        $this->status = $response->getStatusCode();
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawlFailed(
        UriInterface     $url,
        RequestException $requestException,
        ?UriInterface    $foundOnUrl = null
    ): void
    {
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        dump("$this->crawledUrl => $this->status");
    }
}
