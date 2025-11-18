<?php
namespace Tigusigalpa\BingX\Facades;

use Illuminate\Support\Facades\Facade;

class Bingx extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bingx';
    }
}