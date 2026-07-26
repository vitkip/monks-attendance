<?php

namespace App\Livewire\ElectricityBills;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ElectricityBill;
use Illuminate\Support\Facades\Storage;

class ElectricityBillIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterProvince = '';
    public string $filterMonth = '';

    public bool $showModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;

    public ?int $editId = null;
    public ?int $viewId = null;
    public ?int $deleteId = null;

    public bool $isDuplicate = false;

    public string $account_number = '';
    public string $province = '';
    public string $customer_name = '';
    public string $bill_month = '';
    public string $amount = '';
    public $image = null;
    public ?string $currentImage = null;

    protected function rules(): array
    {
        return [
            'account_number' => 'required|string|max:50',
            'province'       => 'required|string|in:' . implode(',', ElectricityBill::provinces()),
            'customer_name'  => 'required|string|max:200',
            'bill_month'     => 'required|date_format:Y-m',
            'amount'         => 'required|numeric|min:0',
            'image'          => $this->editId ? 'nullable|image|max:4096' : 'required|image|max:4096',
        ];
    }

    protected $messages = [
        'account_number.required' => 'ກະລຸນາປ້ອນເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ',
        'province.required'       => 'ກະລຸນາເລືອກແຂວງ',
        'customer_name.required'  => 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ໄຟຟ້າ',
        'bill_month.required'     => 'ກະລຸນາເລືອກເດືອນຂອງໃບບິນ',
        'amount.required'         => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
        'amount.numeric'          => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
        'image.required'          => 'ກະລຸນາອັບໂຫລດຮູບພາບໃບບິນ',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterProvince(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMonth(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterProvince', 'filterMonth']);
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset(['account_number', 'province', 'customer_name', 'bill_month', 'amount', 'image', 'editId', 'currentImage']);
        $this->isDuplicate = false;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $bill = ElectricityBill::findOrFail($id);
        $this->editId         = $id;
        $this->account_number = $bill->account_number;
        $this->province       = $bill->province;
        $this->customer_name  = $bill->customer_name;
        $this->bill_month     = $bill->bill_month->format('Y-m');
        $this->amount         = (string) $bill->amount;
        $this->image          = null;
        $this->currentImage   = $bill->image;
        $this->isDuplicate    = false;
        $this->showModal      = true;
    }

    public function duplicate(int $id): void
    {
        $bill = ElectricityBill::findOrFail($id);
        $this->editId         = null;
        $this->account_number = $bill->account_number;
        $this->province       = $bill->province;
        $this->customer_name  = $bill->customer_name;
        $this->bill_month     = '';
        $this->amount         = '';
        $this->image          = null;
        $this->currentImage   = null;
        $this->isDuplicate    = true;
        $this->showModal      = true;
    }

    public function openView(int $id): void
    {
        $this->viewId = $id;
        $this->showViewModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'account_number' => $this->account_number,
            'province'       => $this->province,
            'customer_name'  => $this->customer_name,
            'bill_month'     => $this->bill_month . '-01',
            'amount'         => $this->amount,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('electricity-bills', 'public');
        }

        if ($this->editId) {
            $bill = ElectricityBill::findOrFail($this->editId);

            if ($this->image && $bill->image) {
                Storage::disk('public')->delete($bill->image);
            }

            $bill->update($data);
            session()->flash('success', 'ແກ້ໄຂໃບບິນຄ່າໄຟຟ້າສຳເລັດ');
        } else {
            $data['user_id'] = auth()->id();

            ElectricityBill::create($data);
            session()->flash('success', 'ເພີ່ມໃບບິນຄ່າໄຟຟ້າສຳເລັດ');
        }

        $this->showModal = false;
        $this->isDuplicate = false;
        $this->reset(['account_number', 'province', 'customer_name', 'bill_month', 'amount', 'image', 'editId', 'currentImage']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $bill = ElectricityBill::findOrFail($this->deleteId);
            Storage::disk('public')->delete($bill->image);
            $bill->delete();
            session()->flash('success', 'ລຶບໃບບິນຄ່າໄຟຟ້າສຳເລັດ');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $bills = ElectricityBill::query()
            ->when($this->filterProvince !== '', fn($q) => $q->where('province', $this->filterProvince))
            ->when($this->filterMonth !== '', function ($q) {
                [$y, $m] = explode('-', $this->filterMonth);
                $q->whereYear('bill_month', $y)->whereMonth('bill_month', $m);
            })
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('account_number', 'like', "%{$this->search}%")
                       ->orWhere('customer_name', 'like', "%{$this->search}%")
                )
            )
            ->with('recordedBy')
            ->latest('bill_month')
            ->latest()
            ->paginate(10);

        $viewing = $this->viewId ? ElectricityBill::with('recordedBy')->find($this->viewId) : null;
        $provinces = ElectricityBill::provinces();

        return view('livewire.electricity-bills.electricity-bill-index', compact('bills', 'viewing', 'provinces'));
    }
}
