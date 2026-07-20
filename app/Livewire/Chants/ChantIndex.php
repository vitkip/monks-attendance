<?php

namespace App\Livewire\Chants;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Chant;
use App\Models\ChantCategory;
use Illuminate\Support\Str;

class ChantIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';

    public bool $showModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;

    public ?int $editId = null;
    public ?int $viewId = null;
    public ?int $deleteId = null;

    public string $title = '';
    public string $content = '';
    public ?int $category_id = null;

    // Bumped on every openCreate/openEdit so the TipTap editor's wire:key
    // changes and Livewire remounts it with fresh content instead of reusing
    // stale DOM from a previous modal session (the editor lives in wire:ignore).
    public int $editorNonce = 0;

    protected function rules(): array
    {
        return [
            'title'       => 'required|string|max:200',
            'content'     => ['required', 'string', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('ກະລຸນາປ້ອນເນື້ອຫາບົດສູດມົນ');
                }
            }],
            'category_id' => 'nullable|exists:chant_categories,id',
        ];
    }

    protected $messages = [
        'title.required'   => 'ກະລຸນາປ້ອນຫົວຂໍ້ບົດສູດມົນ',
        'content.required' => 'ກະລຸນາປ້ອນເນື້ອຫາບົດສູດມົນ',
    ];

    public function isAdmin(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        abort_unless($this->isAdmin(), 403);

        $this->reset(['title', 'content', 'category_id', 'editId']);
        $this->editorNonce++;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        abort_unless($this->isAdmin(), 403);

        $chant = Chant::findOrFail($id);
        $this->editId      = $id;
        $this->title       = $chant->title;
        $this->content     = $chant->content_html;
        $this->category_id = $chant->category_id;
        $this->editorNonce++;
        $this->showModal   = true;
    }

    public function openView(int $id): void
    {
        $this->viewId = $id;
        $this->showViewModal = true;
    }

    public function save(): void
    {
        abort_unless($this->isAdmin(), 403);

        $this->validate();

        $data = [
            'title'       => $this->title,
            'content'     => clean($this->content, 'editor'),
            'category_id' => $this->category_id ?: null,
        ];

        if ($this->editId) {
            Chant::findOrFail($this->editId)->update($data);
            session()->flash('success', 'ແກ້ໄຂບົດສູດມົນສຳເລັດ');
        } else {
            $data['slug'] = $this->generateSlug($this->title);
            Chant::create($data);
            session()->flash('success', 'ເພີ່ມບົດສູດມົນສຳເລັດ');
        }

        $this->showModal = false;
        $this->reset(['title', 'content', 'category_id', 'editId']);
    }

    public function confirmDelete(int $id): void
    {
        abort_unless($this->isAdmin(), 403);

        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        abort_unless($this->isAdmin(), 403);

        if ($this->deleteId) {
            Chant::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'ລຶບບົດສູດມົນສຳເລັດ');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    private function generateSlug(string $title): string
    {
        $base = Str::slug($title) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (Chant::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function render()
    {
        $chants = Chant::query()
            ->when($this->filterCategory !== '', function ($q) {
                $category = ChantCategory::find($this->filterCategory);
                $ids = $category ? array_merge([$category->id], $category->descendantIds()) : [$this->filterCategory];
                $q->whereIn('category_id', $ids);
            })
            ->when($this->search, fn($q) =>
                $q->where('title', 'like', "%{$this->search}%")
            )
            ->with('category')
            ->orderBy('title')
            ->paginate(9);

        $viewing = $this->viewId ? Chant::with('category')->find($this->viewId) : null;
        $categories = ChantCategory::tree();

        return view('livewire.chants.chant-index', compact('chants', 'viewing', 'categories'));
    }
}
