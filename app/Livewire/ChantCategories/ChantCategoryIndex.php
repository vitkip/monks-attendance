<?php

namespace App\Livewire\ChantCategories;

use Livewire\Component;
use App\Models\ChantCategory;
use Illuminate\Support\Str;

class ChantCategoryIndex extends Component
{
    public bool $showModal = false;
    public ?int $editId = null;

    public string $name = '';
    public ?int $parent_id = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'parent_id' => [
                'nullable',
                'exists:chant_categories,id',
                function ($attribute, $value, $fail) {
                    if ($value && $this->editId && in_array((int) $value, $this->invalidParentIds(), true)) {
                        $fail('ບໍ່ສາມາດເລືອກໝວດໝູ່ຍ່ອຍຂອງຕົນເອງເປັນໝວດໝູ່ແມ່ໄດ້');
                    }
                },
            ],
        ];
    }

    protected $messages = [
        'name.required' => 'ກະລຸນາປ້ອນຊື່ໝວດໝູ່',
    ];

    private function invalidParentIds(): array
    {
        if (! $this->editId) {
            return [];
        }

        $category = ChantCategory::find($this->editId);

        return $category ? array_merge([$category->id], $category->descendantIds()) : [];
    }

    public function openCreate(?int $parentId = null): void
    {
        $this->reset(['name', 'editId']);
        $this->parent_id = $parentId;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $category = ChantCategory::findOrFail($id);
        $this->editId    = $id;
        $this->name      = $category->name;
        $this->parent_id = $category->parent_id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            ChantCategory::findOrFail($this->editId)->update([
                'name'      => $this->name,
                'parent_id' => $this->parent_id ?: null,
            ]);
            session()->flash('success', 'ແກ້ໄຂໝວດໝູ່ສຳເລັດ');
        } else {
            ChantCategory::create([
                'name'      => $this->name,
                'slug'      => $this->generateSlug($this->name),
                'parent_id' => $this->parent_id ?: null,
            ]);
            session()->flash('success', 'ເພີ່ມໝວດໝູ່ສຳເລັດ');
        }

        $this->showModal = false;
        $this->reset(['name', 'editId', 'parent_id']);
    }

    public function delete(int $id): void
    {
        $category = ChantCategory::withCount('children')->findOrFail($id);

        if ($category->children_count > 0) {
            session()->flash('error', 'ບໍ່ສາມາດລຶບໝວດໝູ່ທີ່ມີໝວດໝູ່ຍ່ອຍໄດ້ ກະລຸນາລຶບ ຫຼື ຍ້າຍໝວດໝູ່ຍ່ອຍກ່ອນ');
            return;
        }

        $category->delete();
        session()->flash('success', 'ລຶບໝວດໝູ່ສຳເລັດ');
    }

    private function generateSlug(string $name): string
    {
        $base = Str::slug($name) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (ChantCategory::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function render()
    {
        $categories = ChantCategory::tree();

        $parentOptions = $categories->reject(
            fn (ChantCategory $c) => in_array($c->id, $this->invalidParentIds(), true)
        );

        return view('livewire.chant-categories.chant-category-index', compact('categories', 'parentOptions'));
    }
}
