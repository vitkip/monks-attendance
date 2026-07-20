<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\HasRichContent;

class News extends Model
{
    use HasRichContent;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'image', 'status', 'published_at', 'user_id', 'category_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getExcerptOrSummaryAttribute(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        return \Illuminate\Support\Str::limit(strip_tags($this->content), 120);
    }
}
