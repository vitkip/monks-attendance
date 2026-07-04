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

    public function mount(): void
    {
        $this->currentLogo = Setting::get('logo');
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
