<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required|min:6',
    ];

    protected $messages = [
        'email.required'    => 'ກະລຸນາໃສ່ອີເມລ',
        'email.email'       => 'ຮູບແບບອີເມລບໍ່ຖືກຕ້ອງ',
        'password.required' => 'ກະລຸນາໃສ່ລະຫັດຜ່ານ',
        'password.min'      => 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ',
    ];

    public function login(): void
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            $this->redirect(route('absences.index'), navigate: true);
        } else {
            $this->addError('email', 'ອີເມລ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ');
        }
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest');
    }
}
