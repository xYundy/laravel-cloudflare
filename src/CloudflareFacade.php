<?php

namespace xYundy\Cloudflare;

use Illuminate\Support\Facades\Facade;

class CloudflareFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-cloudflare';
    }
}
