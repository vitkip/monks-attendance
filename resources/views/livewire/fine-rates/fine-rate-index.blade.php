<div>
    {{-- Page header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ການຈັດການ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ອັດຕາຄ່າປັບໃໝ</h1>
            <p class="text-gray-400 text-sm mt-1">ຕັ້ງຄ່າຈຳນວນເງິນສຳລັບແຕ່ລະປະເພດການຂາດ</p>
        </div>
        <button wire:click="openCreate"
                class="shrink-0 flex items-center gap-2 px-4 py-2.5 sm:px-5 sm:py-3 bg-brand-green text-white rounded-2xl text-sm font-semibold hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 touch-manipulation">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 4v12M4 10h12"/>
            </svg>
            <span class="hidden sm:inline">ເພີ່ມອັດຕາ</span>
            <span class="sm:hidden">ເພີ່ມ</span>
        </button>
    </div>

    {{-- Cards grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($rates as $rate)
            <div class="bg-white rounded-2xl card-interactive border border-gray-50 flex flex-col overflow-hidden">

                <div class="p-5 flex-1">
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ປະເພດຄ່າປັບ</p>
                    <h3 class="font-bold text-slate-800 text-base leading-snug">{{ $rate->name }}</h3>

                    {{-- Amount with Lao Kip coin mark --}}
                    <div class="flex items-center gap-2 mt-3">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-red-300 text-red-500 text-[9px] font-bold shrink-0 leading-none select-none">₭</span>
                        <span class="text-2xl font-bold text-red-500 tabular-nums">{{ number_format($rate->amount) }}</span>
                        <span class="text-sm text-gray-400">ກີບ</span>
                    </div>

                    @if ($rate->description)
                        <p class="text-sm text-slate-600 mt-2 leading-relaxed">{{ $rate->description }}</p>
                    @endif

                    <div class="flex items-center gap-1.5 mt-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" class="w-3.5 h-3.5 text-gray-300 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 4H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V6a2 2 0 00-2-2h-2M8 4a2 2 0 012-2 2 2 0 012 2M8 4h4"/>
                        </svg>
                        <span class="text-xs text-gray-400">ໃຊ້ {{ $rate->absences_count }} ຄັ້ງ</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 flex" style="min-height: 48px;">
                    <button wire:click="openEdit({{ $rate->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 text-sm text-slate-600 hover:bg-gray-50 py-3 transition-colors touch-manipulation">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z"/>
                        </svg>
                        ແກ້ໄຂ
                    </button>
                    <div class="w-px bg-gray-100 self-stretch"></div>
                    <button wire:click="delete({{ $rate->id }})"
                            wire:confirm="ຢືນຢັນການລຶບ '{{ $rate->name }}'?"
                            class="flex-1 flex items-center justify-center gap-1.5 text-sm text-red-500 hover:bg-red-50 py-3 transition-colors touch-manipulation">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5"/>
                        </svg>
                        ລຶບ
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl card-shadow flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mb-4">
                    <span class="text-2xl font-bold text-brand-green leading-none select-none">₭</span>
                </div>
                <p class="font-bold text-slate-800 mb-1">ຍັງບໍ່ມີອັດຕາຄ່າປັບ</p>
                <p class="text-gray-400 text-sm">ກົດ "ເພີ່ມອັດຕາ" ເພື່ອຕັ້ງຄ່າໃໝ່</p>
            </div>
        @endforelse
    </div>

    {{-- Modal: bottom-sheet on mobile, centered on sm+ --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
         wire:click.self="$set('showModal', false)">

        <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden max-h-[95dvh] sm:max-h-[90vh] flex flex-col">

            {{-- Modal header --}}
            <div class="px-6 py-4 flex items-center justify-between shrink-0 border-b border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່' }}
                    </p>
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">ອັດຕາຄ່າປັບໃໝ</h2>
                </div>
                <button wire:click="$set('showModal', false)"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-slate-700 hover:bg-gray-100 transition-colors touch-manipulation">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            {{-- Drag handle indicator on mobile --}}
            <div class="sm:hidden flex justify-center pt-3 pb-0 shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            {{-- Modal form body --}}
            <form wire:submit="save" class="px-6 py-5 space-y-5 overflow-y-auto">
                <div>
                    <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                        ຊື່ປະເພດ <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="name" type="text"
                           placeholder="ເຊັ່ນ: ຂາດການກວດສອບ, ຂາດຮຽນ..."
                           class="w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                  @error('name') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                        ຈຳນວນເງິນ <span class="text-[10px] normal-case text-gray-400">(ກີບ)</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-red-300 text-red-500 text-[9px] font-bold leading-none select-none">₭</span>
                        </div>
                        <input wire:model="amount" type="number" min="0" step="1000" placeholder="10000"
                               class="w-full bg-[#f8fafa] border rounded-xl pl-10 pr-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow tabular-nums
                                      @error('amount') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                    </div>
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ລາຍລະອຽດ</label>
                    <textarea wire:model="description" rows="2"
                              placeholder="ລາຍລະອຽດເພີ່ມເຕີມ..."
                              class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pb-1 pt-1">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm text-slate-600 hover:bg-gray-50 transition-colors touch-manipulation">
                        ຍົກເລີກ
                    </button>
                    <button type="submit"
                            class="flex items-center gap-2 px-5 py-2.5 bg-brand-green text-white rounded-xl text-sm font-semibold hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 touch-manipulation">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-4 h-4 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 3H7L3 7v10a1 1 0 001 1h13a1 1 0 001-1V4a1 1 0 00-1-1zm-3 14v-6H6v6"/>
                        </svg>
                        ບັນທຶກ
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
