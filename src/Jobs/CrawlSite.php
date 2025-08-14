<?php

namespace Spatie\Export\Jobs;

use Illuminate\Contracts\Routing\UrlGenerator;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
use Spatie\Export\Crawler\LocalClient;
use Spatie\Export\Crawler\Observer;
use Spatie\Export\Destination;
use Spatie\Export\Traits\NormalizedPath;

class CrawlSite
{
    use NormalizedPath;

    public function handle(UrlGenerator $urlGenerator, Destination $destination): void
    {
        $entry = $urlGenerator->to('/');

        $observer = new Observer($entry, $destination);
        if ($this->locale) {
            $observer->setLocale($this->locale);
        }

        (new Crawler(new LocalClient))
            ->setCrawlObserver($observer)
            ->setCrawlProfile(new CrawlInternalUrls($entry))
            ->startCrawling($entry);
    }
}
