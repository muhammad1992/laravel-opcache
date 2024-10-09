<?php

namespace Arnyee\Opcache\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Request
{
    public function handle($request, Closure $next)
    {
        if (! $this->isAllowed($request)) {
            throw new AccessDeniedHttpException('This action is unauthorized.');
        }

        return $next($request);
    }

    protected function isAllowed($request): bool
    {
        try {
            $decrypted = Crypt::decrypt($request->query('key', ''));
        } catch (DecryptException $e) {
            return false;
        }

        return $decrypted === 'opcache' || in_array($this->getRequestIp($request), [$this->getServerIp(), '127.0.0.1', '::1']);
    }

    protected function getRequestIp($request)
    {
        return $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();
    }

    protected function getServerIp()
    {
        return $_SERVER['SERVER_ADDR'] ?? $_SERVER['LOCAL_ADDR'] ?? '127.0.0.1';
    }
}
