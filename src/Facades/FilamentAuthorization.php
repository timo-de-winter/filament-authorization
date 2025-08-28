<?php

namespace TimoDeWinter\FilamentAuthorization\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TimoDeWinter\FilamentAuthorization\FilamentAuthorization
 */
class FilamentAuthorization extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TimoDeWinter\FilamentAuthorization\FilamentAuthorization::class;
    }
}
