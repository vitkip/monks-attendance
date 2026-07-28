<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class Manage extends Component
{
    use WithFileUploads;

    public $logo;
    public ?string $currentLogo = null;

    public ?string $contactWhatsapp = null;
    public ?string $contactFacebook = null;
    public ?string $contactEmail = null;
    public ?string $contactYoutube = null;

    public function mount(): void
    {
        $this->currentLogo = Setting::get('logo');
        $this->contactWhatsapp = Setting::get('contact_whatsapp');
        $this->contactFacebook = Setting::get('contact_facebook');
        $this->contactEmail = Setting::get('contact_email');
        $this->contactYoutube = Setting::get('contact_youtube');
    }

    public function save(): void
    {
        $this->validate([
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
        ], [
            'logo.image' => 'ໄຟລ໌ຕ້ອງເປັນຮູບພາບ',
            'logo.mimes' => 'ຮອງຮັບສະເພາະ jpg, jpeg, png, gif, svg',
            'logo.max' => 'ຂະໜາດໄຟລ໌ສູງສຸດ 2MB',
        ]);

        if ($this->logo) {
            $path = $this->logo->store('settings', 'public');
            Setting::set('logo', $path);
            $this->currentLogo = $path;
        }

        session()->flash('success', 'ບັນທຶກການຕັ້ງຄ່າສຳເລັດ');
    }

    public function saveContact(): void
    {
        $validated = $this->validate([
            'contactWhatsapp' => 'nullable|string|max:20',
            'contactFacebook' => 'nullable|url|max:255',
            'contactEmail' => 'nullable|email|max:255',
            'contactYoutube' => 'nullable|url|max:255',
        ], [
            'contactWhatsapp.max' => 'ເບີ WhatsApp ຍາວເກີນໄປ',
            'contactFacebook.url' => 'ລິ້ງ Facebook ບໍ່ຖືກຕ້ອງ',
            'contactEmail.email' => 'ອີເມວບໍ່ຖືກຕ້ອງ',
            'contactYoutube.url' => 'ລິ້ງ YouTube ບໍ່ຖືກຕ້ອງ',
        ]);

        Setting::set('contact_whatsapp', $validated['contactWhatsapp']);
        Setting::set('contact_facebook', $validated['contactFacebook']);
        Setting::set('contact_email', $validated['contactEmail']);
        Setting::set('contact_youtube', $validated['contactYoutube']);

        session()->flash('success', 'ບັນທຶກຂໍ້ມູນຕິດຕໍ່ສຳເລັດ');
    }

    public function removeLogo(): void
    {
        if ($this->currentLogo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->currentLogo);
        }
        Setting::set('logo', null);
        $this->currentLogo = null;
        $this->logo = null;

        session()->flash('success', 'ລຶບ logo ສຳເລັດ');
    }

    public function render()
    {
        return view('livewire.settings.manage');
    }
}
