<?php

namespace Arnyee\Opcache;

use Illuminate\Support\Facades\Facade;

/**
 * OpcacheFacade class.
 *
 * Provides a static interface to access the OpcacheClass methods
 * allowing better integration with the Laravel service container.
 *
 * @method static bool|null clear()
 * @method static array|null getConfig()
 * @method static array|null getStatus()
 * @method static array|null compile(bool $force = false)
 *
 * @see \Arnyee\Opcache\OpcacheClass
 */
class OpcacheFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * This method returns the service container binding type,
     * which allows the facade to resolve the underlying instance of OpcacheClass.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return OpcacheClass::class;
    }
}
