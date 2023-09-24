<?php

namespace App\Models;

use App\Scopes\GlobalScope;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    protected static function boot()
    {
        parent::boot();

        return static::addGlobalScope(new GlobalScope());
    }
}
