<div>
    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ການເງິນ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ແຈ້ງບິນຄ່າໄຟຟ້າ</h1>
            <p class="text-gray-400 text-sm mt-1">ບັນທຶກ ແລະ ຕິດຕາມໃບບິນຄ່າໄຟຟ້າປະຈຳເດືອນຂອງວັດ</p>
        </div>
        <div class="w-full sm:w-auto">
            <button wire:click="openCreate"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5
                           bg-brand-green text-white rounded-2xl text-sm font-semibold
                           shadow-lg shadow-brand-green/20 hover:bg-opacity-90
                           transition flex-shrink-0">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                ເພີ່ມໃບບິນ
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white card-shadow rounded-2xl p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text"
                   placeholder="ຄົ້ນຫາຊື່ ຫຼື ເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ..."
                   class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm
                          text-slate-800 placeholder:text-gray-400
                          focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
        </div>
        <select wire:model.live="filterProvince"
                class="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                       text-slate-800 sm:w-auto
                       focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
            <option value="">ທຸກແຂວງ</option>
            @foreach ($provinces as $p)
                <option value="{{ $p }}">{{ $p }}</option>
            @endforeach
        </select>
        <input wire:model.live="filterMonth" type="month"
               class="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                      text-slate-800 sm:w-auto
                      focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
        @if ($search !== '' || $filterProvince !== '' || $filterMonth !== '')
            <button type="button" wire:click="clearFilters"
                    class="flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium
                           text-slate-500 hover:bg-gray-100 transition-colors shrink-0">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                </svg>
                ລ້າງຕົວກອງ
            </button>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MOBILE: Compact list (visible below md)                       --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="md:hidden space-y-3">
        @forelse ($bills as $bill)
            <div class="bg-white rounded-2xl card-shadow overflow-hidden">
                <button wire:click="openView({{ $bill->id }})" class="w-full flex items-center gap-3 px-4 py-3.5 text-left">
                    <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                        <img src="{{ $bill->image_url }}" alt="{{ $bill->customer_name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800 text-sm leading-tight truncate min-w-0 flex-1">{{ $bill->customer_name }}</span>
                            <span class="text-brand-green font-bold text-sm flex-shrink-0">{{ number_format($bill->amount) }} ກີບ</span>
                        </div>
                        <p class="text-gray-400 text-xs mt-0.5 truncate">
                            {{ $bill->account_number }} &nbsp;·&nbsp; {{ $bill->province }} &nbsp;·&nbsp; {{ $bill->bill_month->translatedFormat('m/Y') }}
                        </p>
                    </div>
                </button>

                <div class="flex items-stretch border-t border-gray-100">
                    <button wire:click="duplicate({{ $bill->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors touch-manipulation">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 4h8a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2z"/>
                        </svg>
                        ຄັດລອກ
                    </button>
                    <div class="w-px bg-gray-100 self-stretch"></div>
                    <button wire:click="openEdit({{ $bill->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors touch-manipulation">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z"/>
                        </svg>
                        ແກ້ໄຂ
                    </button>
                    <div class="w-px bg-gray-100 self-stretch"></div>
                    <button wire:click="confirmDelete({{ $bill->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-red-500 hover:bg-red-50 py-2.5 transition-colors touch-manipulation">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5"/>
                        </svg>
                        ລຶບ
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl card-shadow py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີໃບບິນຄ່າໄຟຟ້າ</p>
                <p class="text-gray-400 text-sm">ກົດ "ເພີ່ມໃບບິນ" ເພື່ອບັນທຶກໃບບິນທຳອິດ</p>
            </div>
        @endforelse

        @if ($bills->hasPages())
            <div class="pt-1">{{ $bills->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- DESKTOP: Full table (visible md+)                              --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="hidden md:block bg-white rounded-2xl card-shadow overflow-hidden">
        <table class="w-full text-sm table-fixed">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="w-[68px] px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຮູບ</th>
                    <th class="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຜູ້ໃຊ້ໄຟຟ້າ</th>
                    <th class="w-32 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide hidden lg:table-cell">ແຂວງ</th>
                    <th class="w-24 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ເດືອນ</th>
                    <th class="w-32 px-4 py-3.5 text-right text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈຳນວນເງິນ</th>
                    <th class="w-28 px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bills as $bill)
                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5">
                            <button wire:click="openView({{ $bill->id }})" class="block w-11 h-11 rounded-xl overflow-hidden bg-gray-100">
                                <img src="{{ $bill->image_url }}" alt="{{ $bill->customer_name }}" class="w-full h-full object-cover">
                            </button>
                        </td>
                        <td class="px-4 py-3.5 overflow-hidden">
                            <button wire:click="openView({{ $bill->id }})" class="text-left block w-full min-w-0">
                                <p class="font-bold text-slate-800 leading-snug hover:text-brand-green transition-colors truncate">{{ $bill->customer_name }}</p>
                                <p class="text-gray-400 text-xs mt-0.5 truncate">{{ $bill->account_number }}</p>
                            </button>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 truncate hidden lg:table-cell">{{ $bill->province }}</td>
                        <td class="px-4 py-3.5 text-slate-600 whitespace-nowrap">{{ $bill->bill_month->translatedFormat('m/Y') }}</td>
                        <td class="px-4 py-3.5 text-right font-bold text-slate-800 whitespace-nowrap">{{ number_format($bill->amount) }} ກີບ</td>
                        <td class="px-4 py-3.5">
                            <div class="flex justify-center gap-1">
                                <button wire:click="duplicate({{ $bill->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                               text-slate-500 hover:bg-gray-100 transition-all" aria-label="ຄັດລອກ">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 4h8a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2z"/>
                                    </svg>
                                </button>
                                <button wire:click="openEdit({{ $bill->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                               text-slate-500 hover:bg-gray-100 transition-all" aria-label="ແກ້ໄຂ">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete({{ $bill->id }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                               text-red-500 hover:bg-red-50 transition-all" aria-label="ລຶບ">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີໃບບິນຄ່າໄຟຟ້າ</p>
                            <p class="text-gray-400 text-sm">ກົດ "ເພີ່ມໃບບິນ" ເພື່ອບັນທຶກໃບບິນທຳອິດ</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($bills->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                {{ $bills->links() }}
            </div>
        @endif
    </div>

    {{-- View Modal --}}
    @if ($showViewModal && $viewing)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
         wire:click.self="$set('showViewModal', false)">
        <div class="bg-white w-full sm:max-w-lg flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                    rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

            <div class="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            <div class="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລາຍລະອຽດໃບບິນ</p>
                <button wire:click="$set('showViewModal', false)" aria-label="ປິດ"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                               hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <img src="{{ $viewing->image_url }}" alt="{{ $viewing->customer_name }}" class="w-full aspect-[4/3] object-cover bg-gray-100">
                <div class="px-6 py-5 space-y-3">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 leading-snug">{{ $viewing->customer_name }}</h2>
                        <p class="text-gray-400 text-sm mt-0.5">{{ $viewing->province }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div class="bg-[#f8fafa] rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $viewing->account_number }}</p>
                        </div>
                        <div class="bg-[#f8fafa] rounded-xl p-3">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ໃບບິນປະຈຳເດືອນ</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $viewing->bill_month->translatedFormat('m/Y') }}</p>
                        </div>
                        <div class="bg-[#f8fafa] rounded-xl p-3 col-span-2">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຈຳນວນເງິນ</p>
                            <p class="text-lg font-bold text-brand-green">{{ number_format($viewing->amount) }} ກີບ</p>
                        </div>
                    </div>
                    <p class="text-gray-300 text-xs pt-1">
                        ບັນທຶກໂດຍ {{ $viewing->recordedBy?->name ?? 'ບໍ່ລະບຸ' }} &nbsp;·&nbsp; {{ $viewing->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Create / Edit Modal --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full sm:max-w-lg flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                    rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

            <div class="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            <div class="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນ' : ($isDuplicate ? 'ຄັດລອກລາຍການ' : 'ເພີ່ມໃໝ່') }}
                    </p>
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">
                        {{ $editId ? 'ແກ້ໄຂໃບບິນຄ່າໄຟຟ້າ' : 'ເພີ່ມໃບບິນຄ່າໄຟຟ້າ' }}
                    </h2>
                </div>
                <button wire:click="$set('showModal', false)" aria-label="ປິດ"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                               hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            @if ($isDuplicate)
                <div class="mx-6 mt-4 flex items-start gap-2.5 rounded-xl bg-brand-light-green px-3.5 py-3 text-xs text-brand-green">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>ດຶງຂໍ້ມູນຈາກລາຍການເກົ່າມາໃຫ້ແລ້ວ — ປ່ຽນ <strong>ເດືອນ</strong> ແລະ <strong>ຈຳນວນເງິນ</strong> ໃຫ້ຖືກຕ້ອງ ພ້ອມອັບໂຫລດຮູບໃບບິນໃໝ່</span>
                </div>
            @endif

            <form wire:submit="save" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="account_number" type="text" placeholder="ເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ"
                           class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                  text-slate-800 placeholder:text-gray-400
                                  focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                  @error('account_number') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                    @error('account_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ຊື່ຜູ້ໃຊ້ໄຟຟ້າ <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="customer_name" type="text" placeholder="ຊື່ຜູ້ໃຊ້ໄຟຟ້າ"
                           class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                  text-slate-800 placeholder:text-gray-400
                                  focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                  @error('customer_name') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                    @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ແຂວງ <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="province"
                            class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                   text-slate-800
                                   focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                   @error('province') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        <option value="">— ເລືອກແຂວງ —</option>
                        @foreach ($provinces as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                    @error('province') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                            ໃບບິນປະຈຳເດືອນ <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="bill_month" type="month"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                      text-slate-800
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('bill_month') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('bill_month') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                            ຈຳນວນເງິນ (ກີບ) <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="amount" type="number" step="0.01" min="0" placeholder="0"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                      text-slate-800 placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('amount') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ຮູບພາບບິນ <span class="text-red-500">*</span>
                    </label>

                    {{-- Current image (edit mode, no new file chosen yet) --}}
                    @if ($currentImage && ! $image)
                        <div class="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200 mb-2.5">
                            <img src="{{ asset('storage/' . $currentImage) }}" alt="ຮູບປັດຈຸບັນ"
                                 class="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-slate-800">ຮູບພາບປັດຈຸບັນ</p>
                                <p class="text-xs text-gray-400 mt-0.5">ເລືອກໄຟລ໌ໃໝ່ເພື່ອປ່ຽນແທນ</p>
                            </div>
                        </div>
                    @endif

                    {{-- Dropzone --}}
                    <div x-data="{ dragging: false }" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                         @drop.prevent="dragging = false; $refs.billImageInput.files = $event.dataTransfer.files; $refs.billImageInput.dispatchEvent(new Event('change'))"
                         :class="dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'"
                         class="relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer">

                        <input x-ref="billImageInput" wire:model="image" type="file" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="flex flex-col items-center justify-center py-6 px-4 text-center pointer-events-none">
                            <svg class="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <p class="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span class="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                            <p class="text-xs text-gray-400/70 mt-1">ຮູບໃບບິນຄ່າໄຟຟ້າ — JPG, PNG — ສູງສຸດ 4MB</p>
                        </div>

                        <div wire:loading wire:target="image"
                             class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 rounded-2xl">
                            <span class="text-xs font-medium text-brand-green">ກຳລັງອັບໂຫລດ...</span>
                        </div>
                    </div>

                    @error('image') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror

                    {{-- Preview of newly selected file --}}
                    @if ($image)
                        <div class="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200 mt-2.5">
                            <img src="{{ $image->temporaryUrl() }}" alt="ຕົວຢ່າງ" class="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-slate-800 truncate">{{ $image->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-400">{{ number_format($image->getSize() / 1024, 1) }} KB</p>
                            </div>
                            <button type="button" wire:click="$set('image', null)"
                                    class="flex-shrink-0 text-xs text-red-500 hover:text-red-600 font-medium transition-colors px-2 py-1">
                                ຍົກເລີກ
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-1 pb-safe">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600
                                   hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold
                                   shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition">
                        ບັນທຶກ
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirm Modal --}}
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full sm:max-w-sm rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

            <div class="sm:hidden flex justify-center pt-3 pb-1">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <h3 class="text-lg font-bold text-slate-800">ຢືນຢັນການລຶບ</h3>
                <button wire:click="$set('showDeleteModal', false)" aria-label="ປິດ"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                               hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-6 text-center pb-safe">
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <p class="text-slate-800 text-sm font-bold mb-1">ລຶບໃບບິນນີ້</p>
                <p class="text-gray-400 text-xs mb-6">ໃບບິນນີ້ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl
                                   text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button wire:click="delete"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl
                                   text-sm font-semibold hover:bg-red-600 transition-colors">
                        ລຶບໃບບິນ
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
