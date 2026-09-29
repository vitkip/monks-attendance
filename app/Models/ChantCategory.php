<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ChantCategory extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id'];

    public function chants()
    {
        return $this->hasMany(Chant::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(ChantCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChantCategory::class, 'parent_id')->orderBy('name');
    }

    /**
     * IDs of all nested descendants (children, grandchildren, ...).
     */
    public function descendantIds(): array
    {
        $ids = [];

        foreach ($this->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->descendantIds());
        }

        return $ids;
    }

    protected static function booted(): void
    {
        static::saved(fn () => \Illuminate\Support\Facades\Cache::forget('chant_categories_tree'));
        static::deleted(fn () => \Illuminate\Support\Facades\Cache::forget('chant_categories_tree'));
    }

    /**
     * All categories flattened depth-first, each with a `depth` attribute,
     * so parent/child order is preserved for indented dropdowns and lists.
     */
    public static function tree(): Collection
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('chant_categories_tree', function () {
            $all = static::withCount('chants')->orderBy('name')->get();
            $byParent = $all->groupBy(fn (ChantCategory $c) => $c->parent_id ?? 0);

            $flatten = function ($parentId, $depth) use (&$flatten, $byParent) {
                $result = collect();

                foreach ($byParent->get($parentId, collect()) as $category) {
                    $category->depth = $depth;
                    $result->push($category);
                    $result = $result->merge($flatten($category->id, $depth + 1));
                }

                return $result;
            };

            return $flatten(0, 0);
        });
    }
}
