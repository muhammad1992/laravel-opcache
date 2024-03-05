<?php

namespace Pollen\Opcache\Commands;

use Pollen\Opcache\CreatesRequest;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;

class Status extends Command
{
    use CreatesRequest;

    protected $signature = 'opcache:status';

    protected $description = 'Show OPcache status';

    public function handle(): int
    {
        try {
            $request = $this->sendRequest('status');
            $request->throw(); // Ensure the method is supported and correctly handles exceptions.
            $response = $request->json();
            if ($response['success']) {
                $this->displayTables($response['status']);
                return 0;
            }
        } catch (RequestException $e) {
            $this->error("Request failed: {$e->getMessage()}");
            return 1;
        }

        $this->error('OPcache not configured');
        return 2;
    }

    protected function displayTables($data)
    {
        $general = $data;

        foreach (['memory_usage', 'interned_strings_usage', 'opcache_statistics', 'preload_statistics'] as $unset) {
            unset($general[$unset]);
        }

        $this->table([], $this->parseTable($general));

        $this->line(PHP_EOL.'Memory usage:');
        $this->table([], $this->parseTable($data['memory_usage']));

        if (isset($data['opcache_statistics'])) {
            $this->line(PHP_EOL.'Statistics:');
            $this->table([], $this->parseTable($data['opcache_statistics']));
        }

        if (isset($data['interned_strings_usage'])) {
            $this->line(PHP_EOL.'Interned strings usage:');
            $this->table([], $this->parseTable($data['interned_strings_usage']));
        }

        if (isset($data['preload_statistics'])) {
            $this->line(PHP_EOL.'Preload statistics:');
            $this->table([], $this->parseTable($data['preload_statistics']));
        }
    }

    protected function parseTable($input)
    {
        $input = (array) $input;

        return array_map(function ($key, $value) {
            $times = ['start_time', 'last_restart_time'];
            $bytes = ['used_memory', 'free_memory', 'wasted_memory', 'buffer_size'];

            if (in_array($key, $bytes)) {
                $value = number_format($value / 1048576, 2).' MB';
            } elseif (in_array($key, $times)) {
                $value = date('Y-m-d H:i:s', $value);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            return [
                'key' => $key,
                'value' => is_array($value) ? implode(PHP_EOL, $value) : $value,
            ];
        }, array_keys($input), $input);
    }
}
