<?php

namespace Nuwebs\PrimevueDatatable\Tests\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Nuwebs\PrimevueDatatable\Datatable\DatatableRequest;

class CountryController extends Controller
{
  use AuthorizesRequests, ValidatesRequests;

  public function index(DatatableRequest $request)
  {
    return response()->json([], 200);
  }
}