<?php

namespace App\Livewire\HeroSlides;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\HeroSlide;
use Illuminate\Support\Facades\Storage;

class HeroSlideIndex extends Component
{
    use WithFileUploads;

    public bool $showModal = false;
    public bool $showDeleteModal = false;

    public ?int $editId = null;
    public ?int $deleteId = null;

    public string $title = '';
    public string $subtitle = '';
    public string $link_url = '';
    public string $button_text = '';
    public $image = null;
    public ?string $currentImage = null;
    public bool $publishNow = true;

    protected function rules(): array
    {
        return [
            'title'       => 'required|string|max:200',
            'subtitle'    => 'nullable|string|max:500',
            'link_url'    => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:50',
            'image'       => $this->editId ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ];
    }

    protected $messages = [
        'title.required' => 'ກະລຸນາປ້ອນຫົວຂໍ້ສະໄລ້',
        'image.required' => 'ກະລຸນາເລືອກຮູບພາບສະໄລ້',
        'link_url.url'   => 'ກະລຸນາປ້ອນລິ້ງທີ່ຖືກຕ້ອງ (ຕ້ອງຂຶ້ນຕົ້ນດ້ວຍ http:// ຫຼື https://)',
    ];

    public function openCreate(): void
    {
        $this->reset(['title', 'subtitle', 'link_url', 'button_text', 'image', 'editId', 'currentImage']);
        $this->publishNow = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $slide = HeroSlide::findOrFail($id);
        $this->editId       = $id;
        $this->title        = $slide->title;
        $this->subtitle      = $slide->subtitle ?? '';
        $this->link_url      = $slide->link_url ?? '';
        $this->button_text   = $slide->button_text ?? '';
        $this->publishNow    = (bool) $slide->status;
        $this->image         = null;
        $this->currentImage  = $slide->image;
        $this->showModal     = true;
    }

    public function removeCurrentImage(): void
    {
        $this->currentImage = null;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title'       => $this->title,
            'subtitle'    => $this->subtitle ?: null,
            'link_url'    => $this->link_url ?: null,
            'button_text' => $this->button_text ?: null,
            'status'      => $this->publishNow,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('hero-slides', 'public');
        }

        if ($this->editId) {
            $slide = HeroSlide::findOrFail($this->editId);

            if ($this->image && $slide->image) {
                Storage::disk('public')->delete($slide->image);
            }

            $slide->update($data);
            session()->flash('success', 'ແກ້ໄຂສະໄລ້ສຳເລັດ');
        } else {
            $data['sort_order'] = (int) HeroSlide::max('sort_order') + 1;

            HeroSlide::create($data);
            session()->flash('success', 'ເພີ່ມສະໄລ້ສຳເລັດ');
        }

        $this->showModal = false;
        $this->reset(['title', 'subtitle', 'link_url', 'button_text', 'image', 'editId', 'currentImage']);
    }

    public function togglePublish(int $id): void
    {
        $slide = HeroSlide::findOrFail($id);
        $slide->status = ! $slide->status;
        $slide->save();

        session()->flash('success', $slide->status ? 'ເຜີຍແຜ່ສະໄລ້ແລ້ວ' : 'ເກັບເປັນຮ່າງແລ້ວ');
    }

    public function moveUp(int $id): void
    {
        $slides = HeroSlide::ordered()->get();
        $index = $slides->search(fn($s) => $s->id === $id);

        if ($index === false || $index === 0) {
            return;
        }

        $current = $slides[$index];
        $previous = $slides[$index - 1];

        [$currentOrder, $previousOrder] = [$current->sort_order, $previous->sort_order];
        $current->update(['sort_order' => $previousOrder]);
        $previous->update(['sort_order' => $currentOrder]);
    }

    public function moveDown(int $id): void
    {
        $slides = HeroSlide::ordered()->get();
        $index = $slides->search(fn($s) => $s->id === $id);

        if ($index === false || $index === $slides->count() - 1) {
            return;
        }

        $current = $slides[$index];
        $next = $slides[$index + 1];

        [$currentOrder, $nextOrder] = [$current->sort_order, $next->sort_order];
        $current->update(['sort_order' => $nextOrder]);
        $next->update(['sort_order' => $currentOrder]);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $slide = HeroSlide::findOrFail($this->deleteId);

            if ($slide->image) {
                Storage::disk('public')->delete($slide->image);
            }

            $slide->delete();
            session()->flash('success', 'ລຶບສະໄລ້ສຳເລັດ');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $slides = HeroSlide::ordered()->get();

        return view('livewire.hero-slides.hero-slide-index', compact('slides'));
    }
}
