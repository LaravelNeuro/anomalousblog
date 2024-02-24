<?php

namespace App\Corporations\CreepyPastaMachine\Spiders;

use Generator;
use RoachPHP\Http\Request;
use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;
use RoachPHP\Extensions\LoggerExtension;
use RoachPHP\Extensions\StatsCollectorExtension;
use RoachPHP\Downloader\Middleware\RequestDeduplicationMiddleware;

class NewsScraper extends BasicSpider
{
    public array $downloaderMiddleware = [
        RequestDeduplicationMiddleware::class,
    ];

    public array $spiderMiddleware = [
        //
    ];

    public array $itemProcessors = [
        //
    ];

    public array $extensions = [
        LoggerExtension::class,
        StatsCollectorExtension::class,
    ];

    public int $concurrency = 2;

    public int $requestDelay = 1;

    /**
     * @return Generator<ParseResult>
     */
    public function parse(Response $response): Generator
    {
        $article = $response->filter('article > p, content > p, main > p, #articlebody > p, #maincontent > p, #contentbody > p, #articlecontent > p')->each(function($e){return $e->text();});

        if(empty(implode("\n", $article)))
            $article = $response->filter('article p, #articlebody p, #maincontent p, #contentbody p, #articlecontent p')->each(function($e){return $e->text();});

        $articleContent = [implode("\n", $article)];

        foreach($articleContent as $content)
        {
            yield $this->item(['content' => $content]);
        }
    }
}
