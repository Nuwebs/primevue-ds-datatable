<?php

namespace Nuwebs\PrimevueDatatable;

use Illuminate\Support\Facades\Facade;

class PrimevueDatatableFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'primevue-datatable';
    }
}