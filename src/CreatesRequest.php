<?php

namespace Pollen\Opcache;

use Illuminate\Container\EntryNotFoundException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

trait CreatesRequest
{
    /**
     * Send a request to the configured URL with the given parameters.
     *
     * @param  string  $url  The URL path to append to the base opcache URL from config.
     * @param  array  $parameters  Optional parameters to include in the request.
     *
     * @throws EntryNotFoundException
     */
    public function sendRequest(string $url, array $parameters = []): Response
    {
        // Prepare the full URL by combining base URL, prefix, and the specific endpoint.
        $fullUrl = rtrim(config('opcache.url'), '/').'/'.trim(config('opcache.prefix'), '/').'/'.ltrim($url, '/');

        // Encrypt the key to be sent with the request.
        $encryptedKey = Crypt::encrypt('opcache');

        // Merge the encrypted key with any additional parameters.
        $queryParams = array_merge(['key' => $encryptedKey], $parameters);

        // Send the HTTP GET request with the specified headers and options.
        return Http::withHeaders(config('opcache.headers'))
            ->withOptions(['verify' => config('opcache.verify')])
            ->get($fullUrl, $queryParams);
    }
}
