<?php

namespace Arnyee\Opcache\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Http\Client\RequestException;
use Arnyee\Opcache\CreatesRequest;

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

                if (isset($response['result']['message'])) {
                    $this->warn($response['result']['message']);

                    return 1;
                } elseif ($response['result']) {
                    $this->info(sprintf('%s of %s files compiled', $response['result']['compiled_count'], $response['result']['total_files_count']));
                } else {
                    $this->error('OPcache not configured');
                    return 2;
                }
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
