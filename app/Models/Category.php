<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * @var string
     */
    protected $table = 'categories';

    public function categoryGames()
    {
        return $this->hasMany(CategoryGame::class, 'game_id');
    }
}
