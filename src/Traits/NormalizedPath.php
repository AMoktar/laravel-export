<?php

namespace Spatie\Export\Traits;

use Illuminate\Support\Str;

trait NormalizedPath
{
    protected ?string $locale = null;

    public function setLocale (?string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }
    
    protected function normalizePath(string $path)
    {
        // Sanitize path for filesystem compatibility
        $path = $this->sanitizePathForFilesystem($path);
        
        if (! Str::contains(basename($path), '.')) {
            $path .= '/index.html';
        }

        $normalizedPath = ltrim($path, '/');

        // Add locale subdirectory if locale is set
        if ($this->locale) {
            $normalizedPath = $this->locale . '/' . $normalizedPath;
        }

        return $normalizedPath;
    }

    protected function sanitizePathForFilesystem(string $path): string
    {
        // If there's a query string, convert it into a subdirectory
        if (strpos($path, '?') !== false) {
            $parts = explode('?', $path, 2);
            $basePath = $parts[0];
            $query = $parts[1];
            // Do not encode '=' or '&', just append as subdirectory
            $path = rtrim($basePath, '/') . '/' . $query;
        }
        // Encode other problematic characters except '=' and '&' in the query string
        $problematicChars = [
            ':' => '%3A',
            '<' => '%3C',
            '>' => '%3E',
            '"' => '%22',
            '|' => '%7C',
            '*' => '%2A',
            // Don't encode forward slashes as they're path separators
        ];
        return str_replace(array_keys($problematicChars), array_values($problematicChars), $path);
    }
}
