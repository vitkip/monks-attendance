<?php

namespace App\Livewire\ConstructionProjects;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ConstructionProject;
use App\Models\ConstructionTransaction;
use Illuminate\Support\Facades\Storage;

class ProjectIndex extends Component
{
    use WithPagination, WithFileUploads;

    // ── Project list state ──────────────────────────────────────────
    public string $search = '';
    public string $filterStatus = '';

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editId = null;
    public ?int $deleteId = null;

    public string $name = '';
    public string $description = '';
    public string $start_date = '';
    public string $target_amount = '';
    public string $status = 'ongoing';
    public $image = null;
    public ?string $currentImage = null;

    // ── Project detail / transactions state ─────────────────────────
    public ?int $viewingProjectId = null;

    public string $filterType = '';

    public bool $showTxModal = false;
    public bool $showTxDeleteModal = false;
    public ?int $editTxId = null;
    public ?int $deleteTxId = null;

    public string $tx_type = 'expense';
    public string $tx_amount = '';
    public string $tx_transaction_date = '';
    public string $tx_description = '';
    public $tx_image = null;
    public ?string $tx_currentImage = null;

    protected function rules(): array
    {
        return [
            'name'          => 'required|string|max:200',
            'description'   => 'nullable|string|max:2000',
            'start_date'    => 'nullable|date',
            'target_amount' => 'nullable|numeric|min:0',
            'image'         => 'nullable|image|max:4096',
            'status'        => 'required|in:' . implode(',', array_keys(ConstructionProject::statuses())),
        ];
    }

    protected function txRules(): array
    {
        return [
            'tx_type'             => 'required|in:income,expense',
            'tx_amount'           => 'required|numeric|min:0',
            'tx_transaction_date' => 'required|date',
            'tx_description'      => 'nullable|string|max:255',
            'tx_image'            => 'nullable|image|max:4096',
        ];
    }

    protected $messages = [
        'name.required'              => 'ກະລຸນາປ້ອນຊື່ໂຄງການ',
        'status.required'            => 'ກະລຸນາເລືອກສະຖານະໂຄງການ',
        'tx_type.required'           => 'ກະລຸນາເລືອກປະເພດລາຍການ',
        'tx_amount.required'        => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
        'tx_amount.numeric'         => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
        'tx_transaction_date.required' => 'ກະລຸນາເລືອກວັນທີ',
        'tx_image.image'            => 'ໄຟລ໌ຕ້ອງເປັນຮູບພາບ',
    ];

    // ── Project list filters ─────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage('projectsPage');
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage('projectsPage');
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterStatus']);
        $this->resetPage('projectsPage');
    }

    // ── Project CRUD ─────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->reset(['name', 'description', 'start_date', 'target_amount', 'image', 'currentImage', 'editId']);
        $this->status = 'ongoing';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $project = ConstructionProject::findOrFail($id);
        $this->editId        = $id;
        $this->name          = $project->name;
        $this->description   = (string) $project->description;
        $this->start_date    = $project->start_date?->format('Y-m-d') ?? '';
        $this->target_amount = $project->target_amount !== null ? (string) $project->target_amount : '';
        $this->status         = $project->status;
        $this->image          = null;
        $this->currentImage   = $project->image;
        $this->showModal      = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'          => $this->name,
            'description'   => $this->description !== '' ? $this->description : null,
            'start_date'    => $this->start_date !== '' ? $this->start_date : null,
            'target_amount' => $this->target_amount !== '' ? $this->target_amount : null,
            'status'        => $this->status,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('construction-projects', 'public');
        }

        if ($this->editId) {
            $project = ConstructionProject::findOrFail($this->editId);

            if ($this->image && $project->image) {
                Storage::disk('public')->delete($project->image);
            }

            $project->update($data);
            session()->flash('success', 'ແກ້ໄຂໂຄງການສຳເລັດ');
        } else {
            $data['user_id'] = auth()->id();
            ConstructionProject::create($data);
            session()->flash('success', 'ເພີ່ມໂຄງການສຳເລັດ');
        }

        $this->showModal = false;
        $this->reset(['name', 'description', 'start_date', 'target_amount', 'image', 'currentImage', 'editId']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $project = ConstructionProject::findOrFail($this->deleteId);

            $project->transactions()->whereNotNull('image')->get()->each(function ($tx) {
                Storage::disk('public')->delete($tx->image);
            });

            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }

            $project->delete();
            session()->flash('success', 'ລຶບໂຄງການສຳເລັດ');
        }
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    // ── Project detail navigation ────────────────────────────────────

    public function viewProject(int $id): void
    {
        $this->viewingProjectId = $id;
        $this->reset(['filterType']);
        $this->resetPage('transactionsPage');
    }

    public function backToList(): void
    {
        $this->viewingProjectId = null;
    }

    // ── Transaction filters ──────────────────────────────────────────

    public function updatingFilterType(): void
    {
        $this->resetPage('transactionsPage');
    }

    // ── Transaction CRUD ──────────────────────────────────────────────

    public function openTxCreate(): void
    {
        $this->reset(['tx_amount', 'tx_transaction_date', 'tx_description', 'tx_image', 'editTxId', 'tx_currentImage']);
        $this->tx_type = 'expense';
        $this->showTxModal = true;
    }

    public function openTxEdit(int $id): void
    {
        $tx = ConstructionTransaction::findOrFail($id);
        $this->editTxId           = $id;
        $this->tx_type             = $tx->type;
        $this->tx_amount           = (string) $tx->amount;
        $this->tx_transaction_date = $tx->transaction_date->format('Y-m-d');
        $this->tx_description      = (string) $tx->description;
        $this->tx_image            = null;
        $this->tx_currentImage     = $tx->image;
        $this->showTxModal         = true;
    }

    public function saveTx(): void
    {
        $this->validate($this->txRules());

        $data = [
            'type'             => $this->tx_type,
            'amount'           => $this->tx_amount,
            'transaction_date' => $this->tx_transaction_date,
            'description'      => $this->tx_description !== '' ? $this->tx_description : null,
        ];

        if ($this->tx_image) {
            $data['image'] = $this->tx_image->store('construction-transactions', 'public');
        }

        if ($this->editTxId) {
            $tx = ConstructionTransaction::findOrFail($this->editTxId);

            if ($this->tx_image && $tx->image) {
                Storage::disk('public')->delete($tx->image);
            }

            $tx->update($data);
            session()->flash('success', 'ແກ້ໄຂລາຍການສຳເລັດ');
        } else {
            $data['construction_project_id'] = $this->viewingProjectId;
            $data['user_id'] = auth()->id();

            ConstructionTransaction::create($data);
            session()->flash('success', 'ເພີ່ມລາຍການສຳເລັດ');
        }

        $this->showTxModal = false;
        $this->reset(['tx_amount', 'tx_transaction_date', 'tx_description', 'tx_image', 'editTxId', 'tx_currentImage']);
    }

    public function confirmTxDelete(int $id): void
    {
        $this->deleteTxId = $id;
        $this->showTxDeleteModal = true;
    }

    public function deleteTx(): void
    {
        if ($this->deleteTxId) {
            $tx = ConstructionTransaction::findOrFail($this->deleteTxId);
            if ($tx->image) {
                Storage::disk('public')->delete($tx->image);
            }
            $tx->delete();
            session()->flash('success', 'ລຶບລາຍການສຳເລັດ');
        }
        $this->showTxDeleteModal = false;
        $this->deleteTxId = null;
    }

    public function render()
    {
        if ($this->viewingProjectId) {
            $project = ConstructionProject::findOrFail($this->viewingProjectId);

            $transactions = $project->transactions()
                ->when($this->filterType !== '', fn($q) => $q->where('type', $this->filterType))
                ->with('recordedBy')
                ->latest('transaction_date')
                ->latest()
                ->paginate(10, ['*'], 'transactionsPage');

            return view('livewire.construction-projects.project-index', [
                'project'      => $project,
                'transactions' => $transactions,
                'statuses'     => ConstructionProject::statuses(),
            ]);
        }

        $projects = ConstructionProject::query()
            ->when($this->filterStatus !== '', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withSum('incomeTransactions as income_sum', 'amount')
            ->withSum('expenseTransactions as expense_sum', 'amount')
            ->withCount('transactions')
            ->with('recordedBy')
            ->latest()
            ->paginate(9, ['*'], 'projectsPage');

        return view('livewire.construction-projects.project-index', [
            'projects'        => $projects,
            'statuses'        => ConstructionProject::statuses(),
            'totalIncomeAll'  => (float) ConstructionTransaction::where('type', 'income')->sum('amount'),
            'totalExpenseAll' => (float) ConstructionTransaction::where('type', 'expense')->sum('amount'),
            'ongoingCount'    => ConstructionProject::where('status', 'ongoing')->count(),
        ]);
    }
}
