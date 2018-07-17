<?php

namespace Gurinder\Storage\Facades;


use Illuminate\Support\Facades\Facade;

class Storage extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \Gurinder\Storage\Storage\Storage::class;
    }

}