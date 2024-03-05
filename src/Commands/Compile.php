<?php

namespace Pollen\Opcache\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Http\Client\RequestException;
use Pollen\Opcache\CreatesRequest;

class Compile extends Command
{
    use CreatesRequest;

    protected $signature = 'opcache:compile {--force}';

    protected $description = 'Pre-compile your application code';

    public function handle(): int
    {
        $this->line('Compiling scripts...');

        try {
            $request = $this->sendRequest('compile', ['force' => $this->option('force')]);
            $request->throw(); // Ensure the method is supported and correctly handles exceptions.
            $response = $request->json();

            if (isset($response['success'])) {
                $this->warn($response['message']);

                return 1;
            } else {
                $this->error($response['message']);
                return 2;
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
}
