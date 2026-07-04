<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editId = null;
    public ?int $deleteId = null;

    public string $name = '';
    public string $email = '';
    public string $role = 'staff';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        $emailUnique = $this->editId
            ? "unique:users,email,{$this->editId}"
            : 'unique:users,email';

        $passwordRules = $this->editId
            ? 'nullable|min:6|confirmed'
            : 'required|min:6|confirmed';

        return [
            'name'                  => 'required|string|max:100',
            'email'                 => "required|email|{$emailUnique}",
            'role'                  => 'required|in:admin,staff',
            'password'              => $passwordRules,
            'password_confirmation' => 'nullable',
        ];
    }

    protected $messages = [
        'name.required'     => 'ກະລຸນາໃສ່ຊື່',
        'email.required'    => 'ກະລຸນາໃສ່ອີເມລ',
        'email.email'       => 'ຮູບແບບອີເມລບໍ່ຖືກຕ້ອງ',
        'email.unique'      => 'ອີເມລນີ້ຖືກໃຊ້ແລ້ວ',
        'role.required'     => 'ກະລຸນາເລືອກສິດ',
        'role.in'           => 'ສິດບໍ່ຖືກຕ້ອງ',
        'password.required' => 'ກະລຸນາໃສ່ລະຫັດຜ່ານ',
        'password.min'      => 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ',
        'password.confirmed'=> 'ລະຫັດຜ່ານບໍ່ຕົງກັນ',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->resetForm();
        $this->editId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $user = User::findOrFail($id);
        $this->editId                = $id;
        $this->name                  = $user->name;
        $this->email                 = $user->email;
        $this->role                  = $user->role;
        $this->password              = '';
        $this->password_confirmation = '';
        $this->showModal             = true;
    }

    public function save(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $this->validate();

        // Prevent demoting the last admin
        if ($this->editId) {
            $target = User::findOrFail($this->editId);
            if ($target->isAdmin() && $this->role !== 'admin') {
                if (User::where('role', 'admin')->count() <= 1) {
                    $this->addError('role', 'ບໍ່ສາມາດຫຼຸດສິດ Admin ສຸດທ້າຍໃນລະບົບໄດ້');
                    return;
                }
            }
        }

        $data = [
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editId) {
            User::findOrFail($this->editId)->update($data);
            session()->flash('success', 'ແກ້ໄຂຂໍ້ມູນຜູ້ໃຊ້ສຳເລັດ');
        } else {
            User::create($data);
            session()->flash('success', 'ເພີ່ມຜູ້ໃຊ້ສຳເລັດ');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($id === auth()->id()) {
            session()->flash('error', 'ບໍ່ສາມາດລຶບບັນຊີຕົນເອງໄດ້');
            return;
        }

        $target = User::findOrFail($id);
        if ($target->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            session()->flash('error', 'ບໍ່ສາມາດລຶບ Admin ສຸດທ້າຍໃນລະບົບໄດ້');
            return;
        }

        $this->deleteId       = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if (! $this->deleteId) {
            $this->showDeleteModal = false;
            return;
        }

        if ($this->deleteId === auth()->id()) {
            session()->flash('error', 'ບໍ່ສາມາດລຶບບັນຊີຕົນເອງໄດ້');
            $this->showDeleteModal = false;
            $this->deleteId        = null;
            return;
        }

        $target = User::findOrFail($this->deleteId);
        if ($target->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            session()->flash('error', 'ບໍ່ສາມາດລຶບ Admin ສຸດທ້າຍໃນລະບົບໄດ້');
            $this->showDeleteModal = false;
            $this->deleteId        = null;
            return;
        }

        User::findOrFail($this->deleteId)->delete();
        session()->flash('success', 'ລຶບຜູ້ໃຊ້ສຳເລັດ');
        $this->showDeleteModal = false;
        $this->deleteId        = null;
    }

    private function resetForm(): void
    {
        $this->name                  = '';
        $this->email                 = '';
        $this->role                  = 'staff';
        $this->password              = '';
        $this->password_confirmation = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->orderByRaw("FIELD(role, 'admin', 'staff')")
            ->orderBy('name')
            ->paginate(15);

        $adminCount = User::where('role', 'admin')->count();

        return view('livewire.users.user-index', compact('users', 'adminCount'));
    }
}
