<?php

namespace App\Livewire\News;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Support\Str;

class NewsIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterCategory = '';

    public bool $showModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;

    public ?int $editId = null;
    public ?int $viewId = null;
    public ?int $deleteId = null;

    public string $title = '';
    public string $excerpt = '';
    public string $content = '';
    public $image = null;
    public ?string $currentImage = null;
    public bool $removeExistingImage = false;
    public bool $publishNow = true;
    public ?int $category_id = null;

    // Bumped on every openCreate/openEdit so the TipTap editor's wire:key
    // changes and Livewire remounts it with fresh content instead of reusing
    // stale DOM from a previous modal session (the editor lives in wire:ignore).
    public int $editorNonce = 0;

    protected function rules(): array
    {
        return [
            'title'   => 'required|string|max:200',
            'excerpt' => 'nullable|string|max:500',
            'content' => ['required', 'string', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('ກະລຸນາປ້ອນເນື້ອຫາຂ່າວ');
                }
            }],
            'image'   => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:news_categories,id',
        ];
    }

    protected $messages = [
        'title.required'   => 'ກະລຸນາປ້ອນຫົວຂໍ້ຂ່າວ',
        'content.required' => 'ກະລຸນາປ້ອນເນື້ອຫາຂ່າວ',
    ];

    public function isAdmin(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset(['title', 'excerpt', 'content', 'image', 'editId', 'category_id', 'currentImage', 'removeExistingImage']);
        $this->publishNow = true;
        $this->editorNonce++;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        abort_unless($this->isAdmin(), 403);

        $news = News::findOrFail($id);
        $this->editId      = $id;
        $this->title       = $news->title;
        $this->excerpt     = $news->excerpt ?? '';
        $this->content     = $news->content_html;
        $this->publishNow  = (bool) $news->status;
        $this->category_id = $news->category_id;
        $this->image       = null;
        $this->currentImage = $news->image;
        $this->removeExistingImage = false;
        $this->editorNonce++;
        $this->showModal   = true;
    }

    public function removeCurrentImage(): void
    {
        $this->currentImage = null;
        $this->removeExistingImage = true;
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
            'excerpt'     => $this->excerpt ?: null,
            'content'     => clean($this->content, 'editor'),
            'status'      => $this->publishNow,
            'category_id' => $this->category_id ?: null,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('news', 'public');
        } elseif ($this->removeExistingImage) {
            $data['image'] = null;
        }

        if ($this->editId) {
            $news = News::findOrFail($this->editId);

            if (($this->image || $this->removeExistingImage) && $news->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($news->image);
            }

            if ($this->publishNow && ! $news->status) {
                $data['published_at'] = now();
            }

            $news->update($data);
            session()->flash('success', 'ແກ້ໄຂຂ່າວສຳເລັດ');
        } else {
            $data['slug'] = $this->generateSlug($this->title);
            $data['user_id'] = auth()->id();
            $data['published_at'] = $this->publishNow ? now() : null;

            News::create($data);
            session()->flash('success', 'ເພີ່ມຂ່າວສຳເລັດ');
        }

        $this->showModal = false;
        $this->reset(['title', 'excerpt', 'content', 'image', 'editId', 'currentImage', 'removeExistingImage']);
    }

    public function togglePublish(int $id): void
    {
        abort_unless($this->isAdmin(), 403);

        $news = News::findOrFail($id);
        $news->status = ! $news->status;

        if ($news->status && ! $news->published_at) {
            $news->published_at = now();
        }

        $news->save();
        session()->flash('success', $news->status ? 'ເຜີຍແຜ່ຂ່າວແລ້ວ' : 'ເກັບເປັນຮ່າງແລ້ວ');
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
            News::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'ລຶບຂ່າວສຳເລັດ');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    private function generateSlug(string $title): string
    {
        $base = Str::slug($title) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (News::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function render()
    {
        $news = News::query()
            ->when(! $this->isAdmin(), fn($q) => $q->published())
            ->when($this->isAdmin() && $this->filterStatus !== '', fn($q) =>
                $q->where('status', $this->filterStatus)
            )
            ->when($this->filterCategory !== '', fn($q) =>
                $q->where('category_id', $this->filterCategory)
            )
            ->when($this->search, fn($q) =>
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('excerpt', 'like', "%{$this->search}%")
            )
            ->with(['author', 'category'])
            ->latest('published_at')
            ->latest()
            ->paginate(9);

        $viewing = $this->viewId ? News::with(['author', 'category'])->find($this->viewId) : null;
        $categories = NewsCategory::orderBy('name')->get();

        return view('livewire.news.news-index', compact('news', 'viewing', 'categories'));
    }
}
