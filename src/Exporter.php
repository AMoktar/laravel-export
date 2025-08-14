<?php

namespace Spatie\Export;

use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Str;
use Spatie\Export\Jobs\CleanDestination;
use Spatie\Export\Jobs\CrawlSite;
use Spatie\Export\Jobs\ExportPath;
use Spatie\Export\Jobs\IncludeFile;
use Spatie\Export\Traits\NormalizedPath;

class Exporter
{
    use NormalizedPath;
    
    /** @var \Illuminate\Contracts\Bus\Dispatcher */
    protected $dispatcher;

    /** @var UrlGenerator */
    protected $urlGenerator;

    /** @var bool */
    protected $cleanBeforeExport = false;

    /** @var bool */
    protected $crawl = false;

    /** @var string[] */
    protected $paths = [];

    /** @var string[] */
    protected $includeFiles = [];

    /** @var string[] */
    protected $excludeFilePatterns = [];

    public function __construct(Dispatcher $dispatcher, UrlGenerator $urlGenerator)
    {
        $this->dispatcher = $dispatcher;
        $this->urlGenerator = $urlGenerator;
    }

    public function cleanBeforeExport(bool $cleanBeforeExport): self
    {
        $this->cleanBeforeExport = $cleanBeforeExport;

        return $this;
    }

    public function crawl(bool $crawl): self
    {
        $this->crawl = $crawl;

        // TODO: 

        return $this;
    }

    public function paths(...$paths): self
    {
        $paths = is_array($paths[0]) ? $paths[0] : $paths;

        $this->paths = array_merge($this->paths, $paths);

        return $this;
    }

    public function urls(...$urls): self
    {
        $urls = is_array($urls[0]) ? $urls[0] : $urls;

        $this->paths(array_map(function (string $url): string {
            return Str::replaceFirst($this->urlGenerator->to('/'), '', $url);
        }, $urls));

        return $this;
    }

    public function includeFiles(array $includeFiles): self
    {
        $this->includeFiles = array_merge($this->includeFiles, $includeFiles);

        return $this;
    }

    public function excludeFilePatterns(array $excludeFilePatterns): self
    {
        $this->excludeFilePatterns = array_merge($this->excludeFilePatterns, $excludeFilePatterns);

        return $this;
    }

    public function export()
    {
        if ($this->cleanBeforeExport) {
            $this->dispatcher->dispatchNow(
                new CleanDestination
            );
        }

        if ($this->crawl) {
            $crawlJob = new CrawlSite();
            if ($this->locale) {
                $crawlJob->setLocale($this->locale);
            }
            $this->dispatcher->dispatchNow($crawlJob);
        }

        foreach ($this->paths as $path) {
            $exportJob = new ExportPath($path);
            if ($this->locale) {
                $exportJob->setLocale($this->locale);
            }
            $this->dispatcher->dispatchNow($exportJob);
        }

        foreach ($this->includeFiles as $source => $target) {
            $includeJob = new IncludeFile($source, $target, $this->excludeFilePatterns);
            if ($this->locale) {
                $includeJob->setLocale($this->locale);
            }
            $this->dispatcher->dispatchNow($includeJob);
        }
    }
}
