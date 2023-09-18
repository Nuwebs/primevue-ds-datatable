<?php

namespace Nuwebs\PrimevueDatatable\Tests;

use Illuminate\Support\Facades\Route;
use Nuwebs\PrimevueDatatable\PrimevueDatatableServiceProvider;
use Nuwebs\PrimevueDatatable\Tests\Controllers\CountryController;
use Orchestra\Testbench\TestCase;

class PrimevueDatatableTestCase extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    Route::get('/test', [CountryController::class, 'index']);
  }
  protected function getPackageProviders($app): array
  {
    return [
      PrimevueDatatableServiceProvider::class,
    ];
  }

  protected function getEnvironmentSetUp($app): void
  {
    $app['config']->set('database.default', 'testdb');
    $app['config']->set('database.connections.testdb', [
      'driver' => 'sqlite',
      'database' => ':memory:'
    ]);
  }
}