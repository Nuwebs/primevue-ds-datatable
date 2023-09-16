<?php

namespace Nuwebs\PrimevueDatatable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
  protected $guarded = [];

  public function businesses(): HasMany
  {
    return $this->hasMany(Business::class);
  }
}