<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Settings/Index', [
            'currentLogo' => Setting::get('logo'),
            'contactWhatsapp' => Setting::get('contact_whatsapp'),
            'contactFacebook' => Setting::get('contact_facebook'),
            'contactEmail' => Setting::get('contact_email'),
            'contactYoutube' => Setting::get('contact_youtube'),
            'fundBankName' => Setting::get('fund_bank_name'),
            'fundAccountName' => Setting::get('fund_account_name'),
            'fundAccountNumber' => Setting::get('fund_account_number'),
        ]);
    }

    public function updateLogo(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
        ], [
            'logo.image' => 'ໄຟລ໌ຕ້ອງເປັນຮູບພາບ',
            'logo.mimes' => 'ຮອງຮັບສະເພາະ jpg, jpeg, png, gif, svg',
            'logo.max' => 'ຂະໜາດໄຟລ໌ສູງສຸດ 2MB',
        ]);

        $validator->validate();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');
            Setting::set('logo', $path);
        }

        return back()->with('success', 'ບັນທຶກການຕັ້ງຄ່າສຳເລັດ');
    }

    public function removeLogo(): RedirectResponse
    {
        $currentLogo = Setting::get('logo');

        if ($currentLogo) {
            Storage::disk('public')->delete($currentLogo);
        }

        Setting::set('logo', null);

        return back()->with('success', 'ລຶບ logo ສຳເລັດ');
    }

    public function updateContact(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'contactWhatsapp' => 'nullable|string|max:20',
            'contactFacebook' => 'nullable|url|max:255',
            'contactEmail' => 'nullable|email|max:255',
            'contactYoutube' => 'nullable|url|max:255',
        ], [
            'contactWhatsapp.max' => 'ເບີ WhatsApp ຍາວເກີນໄປ',
            'contactFacebook.url' => 'ລິ້ງ Facebook ບໍ່ຖືກຕ້ອງ',
            'contactEmail.email' => 'ອີເມວບໍ່ຖືກຕ້ອງ',
            'contactYoutube.url' => 'ລິ້ງ YouTube ບໍ່ຖືກຕ້ອງ',
        ])->validate();

        Setting::set('contact_whatsapp', $validated['contactWhatsapp'] ?? null);
        Setting::set('contact_facebook', $validated['contactFacebook'] ?? null);
        Setting::set('contact_email', $validated['contactEmail'] ?? null);
        Setting::set('contact_youtube', $validated['contactYoutube'] ?? null);

        return back()->with('success', 'ບັນທຶກຂໍ້ມູນຕິດຕໍ່ສຳເລັດ');
    }

    public function updateFund(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'fundBankName' => 'nullable|string|max:100',
            'fundAccountName' => 'nullable|string|max:150',
            'fundAccountNumber' => 'nullable|string|max:50',
        ], [
            'fundBankName.max' => 'ຊື່ທະນາຄານຍາວເກີນໄປ',
            'fundAccountName.max' => 'ຊື່ບັນຊີຍາວເກີນໄປ',
            'fundAccountNumber.max' => 'ເລກບັນຊີຍາວເກີນໄປ',
        ])->validate();

        Setting::set('fund_bank_name', $validated['fundBankName'] ?? null);
        Setting::set('fund_account_name', $validated['fundAccountName'] ?? null);
        Setting::set('fund_account_number', $validated['fundAccountNumber'] ?? null);

        return back()->with('success', 'ບັນທຶກຂໍ້ມູນບັນຊີກອງທຶນສຳເລັດ');
    }
}
