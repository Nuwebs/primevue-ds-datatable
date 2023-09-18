<?php
namespace Nuwebs\PrimevueDatatable\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwebs\PrimevueDatatable\Tests\Database\Seeders\DbSeeder;
use Nuwebs\PrimevueDatatable\Tests\PrimevueDatatableTestCase;

class QueryTest extends PrimevueDatatableTestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();    
    $this->seed(DbSeeder::class);
    $this->withoutExceptionHandling();
  }  

  protected function defineDatabaseMigrations(): void
  {
    $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
  }

  public function test_simple_countries_query(): void
  {
    $payload = [
      'columns' => ['name'],
      'first' => 0,
      'rows' => 20,
      'page' => 0
    ];
    $payload = json_encode($payload);
    $response = $this->getJson("/test?dt_params=$payload");
    $response->assertStatus(200);
  }
  
}