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
    protected $guarded = [];

    public function categoryGames()
    {
        return $this->hasMany(CategoryGame::class, 'game_id');
    }

    public function games()
    {
        return $this->hasManyThrough(Game::class, CategoryGame::class, 'category_id', 'id', 'id', 'game_id');
    }
}
