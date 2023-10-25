<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'tags';

    protected $guarded = [];

    public function games()
    {
        return $this->hasManyThrough(Game::class, GameTag::class, 'tag_id', 'id', 'id', 'game_id');
    }
}
