<?php

namespace Nuwebs\PrimevueDatatable\Tests\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetupTables extends Migration
{
  public function up()
  {
    Schema::create('countries', function (Blueprint $table) {
      $table->id();
      $table->string('country_name');
    });
    Schema::create('cities', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('zip_code');
      $table->foreignId('country_id')->constrained();
    });
    Schema::create('businesses', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('slogan');
      $table->foreignId('city_id')->constrained();
      $table->foreignId('user_id')->constrained();
    });
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('username');
    });
  }

  public function down()
  {
    Schema::dropIfExists('countries');
    Schema::dropIfExists('cities');
    Schema::dropIfExists('businesses');
    Schema::dropIfExists('users');
  }
}