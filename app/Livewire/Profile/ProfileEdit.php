<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ProfileEdit extends Component
{
    public string $name = '';
    public string $email = '';
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $this->name  = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    protected function rulesInfo(): array
    {
        return [
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ];
    }

    protected function rulesPassword(): array
    {
        return [
            'current_password'          => 'required',
            'new_password'              => ['required', 'confirmed', Password::min(6)],
            'new_password_confirmation' => 'nullable',
        ];
    }

    protected $messages = [
        'name.required'             => 'ກະລຸນາໃສ່ຊື່',
        'email.required'            => 'ກະລຸນາໃສ່ອີເມລ',
        'email.email'               => 'ຮູບແບບອີເມລບໍ່ຖືກຕ້ອງ',
        'email.unique'              => 'ອີເມລນີ້ຖືກໃຊ້ແລ້ວ',
        'current_password.required' => 'ກະລຸນາໃສ່ລະຫັດຜ່ານປັດຈຸບັນ',
        'new_password.required'     => 'ກະລຸນາໃສ່ລະຫັດຜ່ານໃໝ່',
        'new_password.min'          => 'ລະຫັດຜ່ານໃໝ່ຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ',
        'new_password.confirmed'    => 'ລະຫັດຜ່ານໃໝ່ບໍ່ຕົງກັນ',
    ];

    public function saveInfo(): void
    {
        $this->validate($this->rulesInfo());

        auth()->user()->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        session()->flash('success', 'ບັນທຶກຂໍ້ມູນສ່ວນຕົວສຳເລັດ');
    }

    public function savePassword(): void
    {
        $this->validate($this->rulesPassword());

        if (! Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'ລະຫັດຜ່ານປັດຈຸບັນບໍ່ຖືກຕ້ອງ');
            return;
        }

        auth()->user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->current_password          = '';
        $this->new_password              = '';
        $this->new_password_confirmation = '';

        session()->flash('success', 'ປ່ຽນລະຫັດຜ່ານສຳເລັດ');
    }

    public function render()
    {
        return view('livewire.profile.profile-edit');
    }
}
