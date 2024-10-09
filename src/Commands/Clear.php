<?php

namespace Arnyee\Opcache\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\EntryNotFoundException;
use Illuminate\Http\Client\RequestException;
use Arnyee\Opcache\CreatesRequest;

class Clear extends Command
{
    use CreatesRequest;

    protected $signature = 'opcache:clear';

    protected $description = 'Clear OPCache';

    public function handle(): int
    {
        try {
            $request = $this->sendRequest('clear');
            $request->throw();
            $response = $request->json();

            if ($response['success']) {
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
