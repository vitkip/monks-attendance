<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Profile/Show');
    }

    public function updateInfo(Request $request): RedirectResponse
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ], [
            'name.required' => 'ກະລຸນາໃສ່ຊື່',
            'email.required' => 'ກະລຸນາໃສ່ອີເມລ',
            'email.email' => 'ຮູບແບບອີເມລບໍ່ຖືກຕ້ອງ',
            'email.unique' => 'ອີເມລນີ້ຖືກໃຊ້ແລ້ວ',
        ])->validate();

        auth()->user()->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        return back()->with('success', 'ບັນທຶກຂໍ້ມູນສ່ວນຕົວສຳເລັດ');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(6)],
            'new_password_confirmation' => 'nullable',
        ], [
            'current_password.required' => 'ກະລຸນາໃສ່ລະຫັດຜ່ານປັດຈຸບັນ',
            'new_password.required' => 'ກະລຸນາໃສ່ລະຫັດຜ່ານໃໝ່',
            'new_password.min' => 'ລະຫັດຜ່ານໃໝ່ຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ',
            'new_password.confirmed' => 'ລະຫັດຜ່ານໃໝ່ບໍ່ຕົງກັນ',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (! Hash::check((string) $request->input('current_password'), auth()->user()->password)) {
                $validator->errors()->add('current_password', 'ລະຫັດຜ່ານປັດຈຸບັນບໍ່ຖືກຕ້ອງ');
            }
        });

        $data = $validator->validate();

        auth()->user()->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return back()->with('success', 'ປ່ຽນລະຫັດຜ່ານສຳເລັດ');
    }
}
