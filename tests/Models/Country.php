<?php

namespace Nuwebs\PrimevueDatatable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
  protected $guarded = [];

  public function cities(): HasMany
  {
    return $this->hasMany(City::class);
  }
}