<?php

namespace App\Models;

class GameRating extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'game_ratings';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
