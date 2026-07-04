<?php

namespace App\Livewire\Monks;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Monk;

class MonkIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterType = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editId = null;
    public ?int $deleteId = null;

    public string $name = '';
    public string $surname = '';
    public string $type = 'monk';
    public int $pansa = 0;
    public string $temple = '';
    public $photo = null;

    protected function rules(): array
    {
        return [
            'name'    => 'required|string|max:100',
            'surname' => 'required|string|max:100',
            'type'    => 'required|in:monk,novice',
            'pansa'   => 'required|integer|min:0',
            'temple'  => 'nullable|string|max:150',
            'photo'   => $this->editId ? 'nullable|image|max:2048' : 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'name.required'    => 'ກະລຸນາປ້ອນຊື່',
        'surname.required' => 'ກະລຸນາປ້ອນນາມສະກຸນ',
        'pansa.required'   => 'ກະລຸນາປ້ອນພັນສາ',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'surname', 'type', 'pansa', 'temple', 'photo', 'editId']);
        $this->pansa = 0;
        $this->type = 'monk';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $monk = Monk::findOrFail($id);
        $this->editId  = $id;
        $this->name    = $monk->name;
        $this->surname = $monk->surname;
        $this->type    = $monk->type;
        $this->pansa   = $monk->pansa;
        $this->temple  = $monk->temple ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'    => $this->name,
            'surname' => $this->surname,
            'type'    => $this->type,
            'pansa'   => $this->pansa,
            'temple'  => $this->temple ?: null,
        ];

        if ($this->photo) {
            $data['photo'] = $this->photo->store('monks', 'public');
        }

        if ($this->editId) {
            Monk::findOrFail($this->editId)->update($data);
            session()->flash('success', 'ແກ້ໄຂຂໍ້ມູນສຳເລັດ');
        } else {
            Monk::create($data);
            session()->flash('success', 'ເພີ່ມຂໍ້ມູນສຳເລັດ');
        }

        $this->showModal = false;
        $this->reset(['name', 'surname', 'type', 'pansa', 'temple', 'photo', 'editId']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            Monk::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'ລຶບຂໍ້ມູນສຳເລັດ');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $monks = Monk::query()
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('surname', 'like', "%{$this->search}%")
                  ->orWhere('temple', 'like', "%{$this->search}%")
            )
            ->when($this->filterType, fn($q) =>
                $q->where('type', $this->filterType)
            )
            ->where('status', 1)
            ->withCount('absences')
            ->withSum('absences', 'fine_amount')
            ->latest()
            ->paginate(10);

        return view('livewire.monks.monk-index', compact('monks'));
    }
}
