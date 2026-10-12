<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasRichContent;

class Chant extends Model
{
    use HasRichContent;

    protected $fillable = ['title', 'slug', 'content', 'category_id'];

    public function category()
    {
        return $this->belongsTo(ChantCategory::class, 'category_id');
    }

    protected static function booted(): void
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('chant_categories_tree'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('chant_categories_tree'));
    }
}
