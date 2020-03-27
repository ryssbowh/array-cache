<?php

namespace Ryssbowh\ArrayCache\Facade;

use Illuminate\Support\Facades\Facade;

class ArrayCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'array-cache';
    }
}