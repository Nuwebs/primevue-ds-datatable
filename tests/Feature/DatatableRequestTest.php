<?php
namespace Nuwebs\PrimevueDatatable\Tests\Feature;

use Nuwebs\PrimevueDatatable\Tests\PrimevueDatatableTestCase;

class DatatableRequestTest extends PrimevueDatatableTestCase
{

  public function test_json_datatable_request_works(): void
  {
    $this->getJson('/test?dt_params={}')->assertUnprocessable();
  }

  public function test_datatable_request_works(): void
  {
    $this->get('/test?dt_params={}')->assertStatus(302);
  }

  public function test_do_not_accept_malformed_filter_array(): void
  {
    $payload = '{"columns":["name"],"first":0,"rows":20,"page":0,"sortField":null,"sortOrder":null,"filters":[{"permissions.name":{"value":"roles-create","matchMode":"contains"}},{"global":{"value":"","matchMode":"contains"}}]}';
    $this->getJson("/test?dt_params=$payload")->assertUnprocessable();
  }

  public function test_do_not_accept_arbitrary_filter_match_modes(): void
  {
    $payload = '{"columns":["name"],"first":0,"rows":20,"page":0,"sortField":null,"sortOrder":null,"filters":{"permissions.name":{"value":"roles-create","matchMode":"CONTAINS"},"global":{"value":"","matchMode":"contains"}}}';
    $this->getJson("/test?dt_params=$payload")->assertUnprocessable();
  }
}