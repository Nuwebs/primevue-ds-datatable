<?php

namespace Nuwebs\PrimevueDatatable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Business extends Model
{
  protected $guarded = [];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function city(): BelongsTo
  {
    return $this->belongsTo(City::class);
  }
}