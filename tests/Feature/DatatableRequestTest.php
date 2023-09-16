<?php
namespace Nuwebs\PrimevueDatatable\Tests\Feature;

use Nuwebs\PrimevueDatatable\Tests\PrimevueDatatableTestCase;

class DatatableRequestTest extends PrimevueDatatableTestCase
{

  public function testJsonDatatableRequestWorks(): void
  {
    $this->getJson('/test?dt_params={}')->assertUnprocessable();
  }

  public function testDatatableRequestWorks(): void
  {
    $this->get('/test?dt_params={}')->assertStatus(302);
  }
}