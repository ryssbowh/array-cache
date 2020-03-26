<?php

namespace Ryssbowh\ArrayCache;

use Illuminate\Support\Facades\Facade;

class ArrayCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'array-cache';
    }
}