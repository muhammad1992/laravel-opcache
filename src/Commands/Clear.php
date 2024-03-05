<?php

namespace Pollen\Opcache\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Http\Client\RequestException;
use Pollen\Opcache\CreatesRequest;

class Clear extends Command
{
    use CreatesRequest;

    protected $signature = 'opcache:clear';

    protected $description = 'Clear OPCache';

    public function handle(): int
    {
        try {
            $response = $this->sendRequest('clear');
            $response->throw(); // Assurez-vous que cette méthode est compatible et gère correctement les exceptions.

            if ($response['result']) {
                $this->info('OPcache cleared');
            } else {
                $this->error('OPcache not configured');

                return 2;
            }
        } catch (RequestException $e) {
            $this->error("Request failed: {$e->getMessage()}");

            return 1;
        } catch (EntryNotFoundException $e) {
            $this->error("Entry not found: {$e->getMessage()}");
        }

        return 0;
    }
}
