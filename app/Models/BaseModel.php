<?php

namespace App\Models;

use App\Scopes\GlobalScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{

    protected static function boot()
    {
        parent::boot();

        return static::addGlobalScope(new GlobalScope());
    }

    public function scopeFilters(Builder $query, array $filter = [])
    {
        if ($filter) {
            foreach ($filter as $name => $value) {
                if (is_null($value) || $value == '') {
                    continue;
                }
                $method = 'scope' . Str::studly($name);

                if (method_exists($this, $method)) {
                    $query = $this->{$method}($query, $value);
                    continue;
                }
            }
        }

        return $query;
    }

    public function scopeName(Builder $query, $value)
    {
        return $query->where('name', 'LIKE', '%' . $value . '%');
    }

    public function scopeStatus(Builder $query, $value)
    {
        return $query->where('status', $value);
    }
}
