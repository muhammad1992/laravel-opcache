<?php

namespace Pollen\Opcache;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Finder\Finder;

class OpcacheClass
{
    /**
     * Clear the OPcache.
     */
    public function clear(): ?bool
    {
        if (function_exists('opcache_reset')) {
            return opcache_reset();
        }

        return null;
    }

    /**
     * Retrieve the configuration values of OPcache.
     */
    public function getConfig(): ?array
    {
        if (function_exists('opcache_get_configuration')) {
            return opcache_get_configuration();
        }

        return null;
    }

    /**
     * Get status information about the cache.
     */
    public function getStatus(): ?array
    {
        if (function_exists('opcache_get_status')) {
            return opcache_get_status(false);
        }

        return null;
    }

    /**
     * Pre-compile PHP scripts.
     *
     * @param  bool  $force  Whether to force the compilation regardless of opcache.dups_fix setting.
     */
    public function compile(bool $force = false): ?array
    {
        if (! ini_get('opcache.dups_fix') && ! $force) {
            return ['message' => 'opcache.dups_fix must be enabled, or run with --force'];
        }

        if (function_exists('opcache_compile_file')) {
            $compiled = 0;
            $finder = new Finder();
            $finder->in(config('opcache.directories'))
                ->files()
                ->name('*.php')
                ->ignoreUnreadableDirs()
                ->notContains('#!/usr/bin/env php')
                ->exclude(config('opcache.exclude'))
                ->followLinks();

            foreach ($finder as $file) {
                $filePath = $file->getRealPath();
                if (! opcache_is_script_cached($filePath)) {
                    try {
                        opcache_compile_file($filePath);
                        $compiled++;
                    } catch (\Exception $e) {
                        // Logging any exceptions encountered during compilation
                        Log::error("Failed to compile file: $filePath", ['exception' => $e->getMessage()]);
                    }
                }
            }

            return [
                'total_files_count' => iterator_count($finder),
                'compiled_count' => $compiled,
            ];
        }

        return null;
    }
}
