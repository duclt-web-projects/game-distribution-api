<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryGame extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'category_games';

    public $timestamps = false;
}
