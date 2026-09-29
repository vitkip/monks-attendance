<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('public:news_categories');
            \Illuminate\Support\Facades\Cache::forget('news_categories_admin');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('public:news_categories');
            \Illuminate\Support\Facades\Cache::forget('news_categories_admin');
        });
    }

    public function news()
    {
        return $this->hasMany(News::class, 'category_id');
    }
}
