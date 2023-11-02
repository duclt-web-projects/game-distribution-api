<?php

namespace App\Models;

use App\Scopes\GlobalScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'games';

    protected $guarded = [];

    protected $casts = [
        'rating' => 'float'
    ];

    protected static function boot()
    {
        parent::boot();

        return static::addGlobalScope(new GlobalScope());
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, CategoryGame::class, 'game_id', 'category_id')
            ->withoutGlobalScopes();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, GameTag::class, 'game_id', 'tag_id')
            ->withoutGlobalScopes();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(GameRating::class);
    }

    public function scopeActive(Builder $query, $value)
    {
        return $query->where('active', $value);
    }

    public function scopeAuthorId(Builder $query, $value)
    {
        return $query->where('author_id', $value);
    }

    public function scopeCategories(Builder $query, $value)
    {
        $categories = explode(',', $value);
        return $query->whereHas('categories', function ($q) use ($categories) {
            $q->whereIn('categories.id', $categories);
        });
    }

    public function scopeTags(Builder $query, $value)
    {
        $tags = explode(',', $value);
        return $query->whereHas('tags', function ($q) use ($tags) {
            $q->whereIn('tags.id', $tags);
        });
    }
}
