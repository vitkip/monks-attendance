<?php

namespace App\Livewire\FineRates;

use Livewire\Component;
use App\Models\FineRate;

class FineRateIndex extends Component
{
    public bool $showModal = false;
    public ?int $editId = null;

    public string $name = '';
    public float|string $amount = 0;
    public string $description = '';

    protected $rules = [
        'name'        => 'required|string|max:100',
        'amount'      => 'required|numeric|min:0',
        'description' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'name.required'   => 'ກະລຸນາປ້ອນຊື່ປະເພດ',
        'amount.required' => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
        'amount.numeric'  => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
    ];

    public function openCreate(): void
    {
        $this->reset(['name', 'amount', 'description', 'editId']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $rate = FineRate::findOrFail($id);
        $this->editId      = $id;
        $this->name        = $rate->name;
        $this->amount      = $rate->amount;
        $this->description = $rate->description ?? '';
        $this->showModal   = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'        => $this->name,
            'amount'      => $this->amount,
            'description' => $this->description ?: null,
        ];

        if ($this->editId) {
            FineRate::findOrFail($this->editId)->update($data);
            session()->flash('success', 'ແກ້ໄຂຂໍ້ມູນສຳເລັດ');
        } else {
            FineRate::create($data);
            session()->flash('success', 'ເພີ່ມຂໍ້ມູນສຳເລັດ');
        }

        $this->showModal = false;
        $this->reset(['name', 'amount', 'description', 'editId']);
    }

    public function delete(int $id): void
    {
        FineRate::findOrFail($id)->delete();
        session()->flash('success', 'ລຶບຂໍ້ມູນສຳເລັດ');
    }

    public function render()
    {
        $rates = FineRate::withCount('absences')->latest()->get();
        return view('livewire.fine-rates.fine-rate-index', compact('rates'));
    }
}
