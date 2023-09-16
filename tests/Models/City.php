<?php

namespace Nuwebs\PrimevueDatatable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
  protected $guarded = [];

  public function country(): BelongsTo
  {
    return $this->belongsTo(Country::class);
  }

  public function businesses(): HasMany
  {
    return $this->hasMany(Business::class);
  }
}