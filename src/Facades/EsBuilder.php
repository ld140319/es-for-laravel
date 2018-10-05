<?php
namespace Ethansmart\EsBuilder\Facades;

class EsBuilder extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'esbuilder';
    }
}