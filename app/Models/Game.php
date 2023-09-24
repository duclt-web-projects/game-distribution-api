<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'games';

    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany(Category::class, CategoryGame::class, 'game_id', 'category_id',);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, GameTag::class, 'game_id', 'tag_id');
    }
}
