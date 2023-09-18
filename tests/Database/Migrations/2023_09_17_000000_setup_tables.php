<?php

namespace Nuwebs\PrimevueDatatable\Tests\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('countries', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->timestamps();
    });
    Schema::create('cities', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('zip_code');
      $table->foreignId('country_id')->constrained();
      $table->timestamps();
    });
    Schema::create('businesses', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->foreignId('city_id')->constrained();
      $table->foreignId('user_id')->constrained();
      $table->timestamps();
    });
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('username');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('countries');
    Schema::dropIfExists('cities');
    Schema::dropIfExists('businesses');
    Schema::dropIfExists('users');
  }
};