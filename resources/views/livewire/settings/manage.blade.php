<div class="max-w-xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-xl font-semibold text-[#3A1C05]">ຕັ້ງຄ່າລະບົບ</h1>
        <p class="text-sm text-[#7A5A30] mt-0.5">ອັບໂຫລດ logo ສຳລັບໜ້າຫລັກ</p>
    </div>

    {{-- Logo Card --}}
    <div class="bg-white rounded-xl border border-[#D4A017]/20 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-[#D4A017]/15 bg-[#FAF6EE]">
            <h2 class="text-sm font-semibold text-[#3A1C05] uppercase tracking-wide">Logo ລະບົບ</h2>
        </div>

        <div class="px-6 py-6 space-y-5">

            {{-- Current logo preview --}}
            @if($currentLogo)
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-20 h-20 rounded-xl border-2 border-[#D4A017]/30 bg-[#1A0A02] flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('storage/' . $currentLogo) }}" alt="Logo" class="w-full h-full object-contain p-1">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#3A1C05]">Logo ປັດຈຸບັນ</p>
                        <p class="text-xs text-[#7A5A30] mt-0.5 break-all">{{ basename($currentLogo) }}</p>
                        <button wire:click="removeLogo" wire:confirm="ທ່ານຕ້ອງການລຶບ logo ນີ້ບໍ?"
                            class="mt-2 text-xs text-red-600 hover:text-red-700 font-medium transition-colors">
                            ລຶບ logo
                        </button>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 p-3 bg-[#FAF6EE] rounded-lg border border-[#D4A017]/15">
                    <span class="text-2xl select-none">☸️</span>
                    <p class="text-sm text-[#7A5A30]">ກຳລັງໃຊ້ icon ເລີ່ມຕົ້ນ (☸️) — ອັບໂຫລດ logo ເພື່ອປ່ຽນ</p>
                </div>
            @endif

            {{-- Upload form --}}
            <form wire:submit="save" class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-[#3A1C05] mb-1.5">
                        {{ $currentLogo ? 'ປ່ຽນ Logo ໃໝ່' : 'ອັບໂຫລດ Logo' }}
                    </label>

                    <div x-data="{ dragging: false }" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                        @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                        :class="dragging ? 'border-[#D4A017] bg-[#D4A017]/5' : 'border-[#D4A017]/30 bg-[#FAF6EE]'"
                        class="relative rounded-xl border-2 border-dashed transition-colors duration-150 cursor-pointer">

                        <input x-ref="fileInput" wire:model="logo" type="file" accept="image/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="flex flex-col items-center justify-center py-8 px-4 text-center pointer-events-none">
                            <svg class="w-8 h-8 text-[#D4A017]/60 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <p class="text-sm text-[#7A5A30]">ລາກໄຟລ໌ມາວາງ ຫຼື <span class="text-[#D4A017] font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                            <p class="text-xs text-[#7A5A30]/60 mt-1">PNG, JPG, SVG, GIF — ສູງສຸດ 2MB</p>
                        </div>
                    </div>

                    @error('logo')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Preview of newly selected file --}}
                @if($logo)
                    <div class="flex items-center gap-3 p-3 bg-[#FAF6EE] rounded-lg border border-[#D4A017]/15">
                        <img src="{{ $logo->temporaryUrl() }}" alt="Preview" class="w-12 h-12 object-contain rounded-lg border border-[#D4A017]/20">
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-[#3A1C05]">{{ $logo->getClientOriginalName() }}</p>
                            <p class="text-xs text-[#7A5A30]">{{ number_format($logo->getSize() / 1024, 1) }} KB</p>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-5 py-2 bg-[#D4A017] hover:bg-[#B8860B] text-white text-sm font-semibold rounded-lg transition-colors duration-150 shadow-sm">
                        <span wire:loading.remove wire:target="save">ບັນທຶກ</span>
                        <span wire:loading wire:target="save">ກຳລັງອັບໂຫລດ...</span>
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
