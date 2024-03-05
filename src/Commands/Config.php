<?php

namespace Pollen\Opcache\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Http\Client\RequestException;
use Pollen\Opcache\CreatesRequest;

class Config extends Command
{
    use CreatesRequest;

    protected $signature = 'opcache:config';

    protected $description = 'Show your OPcache configuration';

    public function handle(): int
    {
        try {
            $response = $this->sendRequest('config');
            $response->throw(); // Ensure the method is supported and correctly handles exceptions.

            if ($response['result']) {
                $this->line('Version info:');
                $this->table([], $this->parseTable($response['result']['version']));

                $this->line(PHP_EOL.'Configuration info:');
                $this->table([], $this->parseTable($response['result']['directives']));

                return 0;
            }
        } catch (RequestException $e) {
            $this->error("Request failed: {$e->getMessage()}");

            return 1;
        } catch (EntryNotFoundException $e) {
            $this->error("Entry not found: {$e->getMessage()}");
        }

        $this->error('OPcache not configured');

        return 2;
    }

    protected function parseTable($input): array
    {
        $input = (array) $input;

        return array_map(function ($key, $value) {
            $bytes = ['opcache.memory_consumption'];

            if (in_array($key, $bytes)) {
                $value = number_format($value / 1048576, 2).' MB';
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            return [
                'key' => $key,
                'value' => $value,
            ];
        }, array_keys($input), $input);
    }
}
