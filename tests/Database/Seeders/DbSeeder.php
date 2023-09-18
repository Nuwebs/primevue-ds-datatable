<?php

namespace Nuwebs\PrimevueDatatable\Tests\Database\Seeders;
use Illuminate\Database\Seeder;
use Nuwebs\PrimevueDatatable\Tests\Models\Business;
use Nuwebs\PrimevueDatatable\Tests\Models\City;
use Nuwebs\PrimevueDatatable\Tests\Models\Country;
use Nuwebs\PrimevueDatatable\Tests\Models\User;

class DbSeeder extends Seeder
{
  public function run(): void
  {
    $this->seedCountries();
    $this->seedCities();
    $this->seedUsers();
    $this->seedBusinesses();
  }

  private function seedCountries(): void
  {
    $countries = ['Colombia', 'Argentina', 'Perú'];
    foreach ($countries as $country) {
      Country::create([
        'name' => $country
      ]);
    }
  }

  private function seedCities(): void
  {
    $cities = [
      'Colombia' => [
        'Bogotá',
        'Bucaramanga'
      ],
      'Argentina' => ['Buenos Aires'],
      'Perú' => ['Lima']
    ];
    $zCode = 1;
    foreach ($cities as $country => $cityArr) {
      $zZone = 0;
      $cModel = Country::where('name', $country)->first();
      foreach ($cityArr as $city){
        City::create([
          'name' => $city,
          'zip_code' => "$zCode$zZone",
          'country_id' => $cModel->id
        ]);
        $zZone += 1;
      }
      $zCode += 1;
    }
  }

  private function seedUsers(): void
  {
    User::create([
      'username' => 'Douglas'
    ]);
  }

  private function seedBusinesses(): void
  {
    $cities = City::all();
    $c = 0;
    foreach ($cities as $city) {
      Business::create([
        'name' => "$city->name $c",
        'city_id' => $city->id,
        'user_id' => 1
      ]);
      $c+=1;
    }
  }
}