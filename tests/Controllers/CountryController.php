<?php

namespace Nuwebs\PrimevueDatatable\Tests\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Nuwebs\PrimevueDatatable\Datatable\DatatableRequest;
use Nuwebs\PrimevueDatatable\PrimevueDatatable;
use Nuwebs\PrimevueDatatable\Tests\Models\Country;
use Nuwebs\PrimevueDatatable\Tests\Resources\CountryResource;

class CountryController extends Controller
{
  use AuthorizesRequests, ValidatesRequests;

  public function index(DatatableRequest $request)
  {
    return CountryResource::collection(PrimevueDatatable::of(Country::query())->make());
  }
}