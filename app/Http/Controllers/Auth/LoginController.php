<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'ກະລຸນາໃສ່ອີເມລ',
            'email.email' => 'ຮູບແບບອີເມລບໍ່ຖືກຕ້ອງ',
            'password.required' => 'ກະລຸນາໃສ່ລະຫັດຜ່ານ',
            'password.min' => 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'ອີເມລ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route('absences.index');
    }
}
