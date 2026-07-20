<?php

namespace App\Livewire\NewsCategories;

use Livewire\Component;
use App\Models\NewsCategory;
use Illuminate\Support\Str;

class NewsCategoryIndex extends Component
{
    public bool $showModal = false;
    public ?int $editId = null;

    public string $name = '';

    protected $rules = [
        'name' => 'required|string|max:100',
    ];

    protected $messages = [
        'name.required' => 'ກະລຸນາປ້ອນຊື່ໝວດໝູ່',
    ];

    public function openCreate(): void
    {
        $this->reset(['name', 'editId']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $category = NewsCategory::findOrFail($id);
        $this->editId = $id;
        $this->name   = $category->name;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            NewsCategory::findOrFail($this->editId)->update(['name' => $this->name]);
            session()->flash('success', 'ແກ້ໄຂໝວດໝູ່ສຳເລັດ');
        } else {
            NewsCategory::create([
                'name' => $this->name,
                'slug' => $this->generateSlug($this->name),
            ]);
            session()->flash('success', 'ເພີ່ມໝວດໝູ່ສຳເລັດ');
        }

        $this->showModal = false;
        $this->reset(['name', 'editId']);
    }

    public function delete(int $id): void
    {
        NewsCategory::findOrFail($id)->delete();
        session()->flash('success', 'ລຶບໝວດໝູ່ສຳເລັດ');
    }

    private function generateSlug(string $name): string
    {
        $base = Str::slug($name) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (NewsCategory::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function render()
    {
        $categories = NewsCategory::withCount('news')->orderBy('name')->get();

        return view('livewire.news-categories.news-category-index', compact('categories'));
    }
}
