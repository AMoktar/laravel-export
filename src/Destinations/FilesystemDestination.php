<?php

namespace Spatie\Export\Destinations;

use Illuminate\Contracts\Filesystem\Filesystem;
use Spatie\Export\Destination;

class FilesystemDestination implements Destination
{
    /** @var \Illuminate\Contracts\Filesystem */
    protected $filesystem;

    /** @var string|null */
    protected $subdirectory;

    public function __construct(Filesystem $filesystem, ?string $subdirectory = null)
    {
        $this->filesystem = $filesystem;
        $this->subdirectory = $subdirectory;
    }

    public function clean()
    {
        if ($this->subdirectory) {
            // Clean only the subdirectory
            if ($this->filesystem->exists($this->subdirectory)) {
                $this->filesystem->deleteDirectory($this->subdirectory);
            }
        } else {
            // Clean everything (existing behavior)
            $this->filesystem->delete($this->filesystem->files());

            foreach ($this->filesystem->directories() as $directory) {
                $this->filesystem->deleteDirectory($directory);
            }
        }
    }

    public function write(string $path, string $contents)
    {
        $finalPath = $this->subdirectory ? $this->subdirectory . '/' . ltrim($path, '/') : $path;
        $this->filesystem->put($finalPath, $contents);
    }
}
