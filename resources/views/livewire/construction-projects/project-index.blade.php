<div>
@if ($viewingProjectId)
    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- PROJECT LEDGER: running summary + chronological entries       --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <button wire:click="backToList" class="flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-brand-green transition-colors mb-4">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        ໂຄງການທັງໝົດ
    </button>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4 min-w-0">
            @if ($project->image_url)
                <img src="{{ $project->image_url }}" alt="{{ $project->name }}"
                     class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover shrink-0 border border-gray-100">
            @endif
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold
                        {{ $project->status === 'completed' ? 'bg-brand-light-green text-brand-green' : ($project->status === 'paused' ? 'bg-gray-100 text-gray-500' : 'bg-amber-50 text-amber-600') }}">
                        {{ $statuses[$project->status] ?? $project->status }}
                    </span>
                    @if ($project->start_date)
                        <span class="text-gray-300 text-xs">ເລີ່ມ {{ $project->start_date->translatedFormat('d M Y') }}</span>
                    @endif
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 truncate">{{ $project->name }}</h1>
            </div>
        </div>
        <div class="w-full sm:w-auto">
            <button wire:click="openTxCreate"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5
                           bg-brand-green text-white rounded-2xl text-sm font-semibold
                           shadow-lg shadow-brand-green/20 hover:bg-opacity-90
                           transition flex-shrink-0">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                ເພີ່ມລາຍການ
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-5 items-start">

        {{-- Summary board — the temple fundraising-board metaphor --}}
        <div class="bg-white card-shadow rounded-2xl p-5 lg:sticky lg:top-6">
            @if ($project->progress_percent !== null)
                <div class="flex items-baseline justify-between mb-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລະດົມທຶນໄດ້</span>
                    <span class="text-xl font-extrabold text-brand-green tabular-nums">{{ $project->progress_percent }}%</span>
                </div>
                <div class="relative h-3.5 rounded-full bg-brand-light-green mb-1.5">
                    <div class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#ffb366] to-brand-green transition-all duration-700 ease-out"
                         style="width: {{ $project->progress_percent }}%"></div>
                    <div class="absolute inset-y-0 left-1/4 w-px bg-white/60"></div>
                    <div class="absolute inset-y-0 left-1/2 w-px bg-white/60"></div>
                    <div class="absolute inset-y-0 left-3/4 w-px bg-white/60"></div>
                    <div class="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-white border-2 border-brand-green shadow-sm"
                         style="left: {{ $project->progress_percent }}%"></div>
                </div>
                <p class="text-[11px] text-gray-400 mb-5">ເປົ້າໝາຍ {{ number_format($project->target_amount) }} ກີບ</p>
            @endif

            <div class="space-y-3 {{ $project->progress_percent !== null ? 'border-t border-gray-100 pt-4' : '' }}">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">ໄດ້ຮັບ</span>
                    <span class="text-sm font-bold text-brand-green tabular-nums">{{ number_format($project->total_income) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">ຈ່າຍແລ້ວ</span>
                    <span class="text-sm font-bold text-rose-800 tabular-nums">{{ number_format($project->total_expense) }}</span>
                </div>
                <div class="flex items-center justify-between pt-2.5 border-t border-gray-100">
                    <span class="text-xs font-semibold text-slate-600">ຍອດເຫຼືອ</span>
                    <span class="text-base font-extrabold tabular-nums {{ $project->balance >= 0 ? 'text-slate-800' : 'text-rose-800' }}">{{ number_format($project->balance) }}</span>
                </div>
            </div>

            @if ($project->description)
                <p class="text-xs text-gray-400 leading-relaxed mt-5 pt-4 border-t border-gray-100">{{ $project->description }}</p>
            @endif
        </div>

        {{-- Ledger entries --}}
        <div class="bg-white card-shadow rounded-2xl overflow-hidden">
            <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3 border-b border-gray-100">
                <p class="text-xs font-semibold text-slate-600 flex-1">ບັນຊີລາຍການ</p>
                <select wire:model.live="filterType"
                        class="bg-[#f8fafa] border border-gray-200 rounded-xl px-3.5 py-2 text-xs
                               text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                    <option value="">ທຸກປະເພດ</option>
                    <option value="income">ລາຍຮັບ</option>
                    <option value="expense">ລາຍຈ່າຍ</option>
                </select>
                @if ($filterType !== '')
                    <button type="button" wire:click="$set('filterType', '')"
                            class="text-xs font-medium text-gray-400 hover:text-brand-green transition-colors shrink-0">
                        ລ້າງຕົວກອງ
                    </button>
                @endif
            </div>

            <div class="px-3 sm:px-5">
                @forelse ($transactions as $tx)
                    <div class="group flex items-center gap-3 sm:gap-4 py-3.5 border-b border-gray-100 last:border-0">
                        <div class="shrink-0 w-10 text-center">
                            <p class="text-[9px] font-bold uppercase tracking-wide text-gray-400 leading-none mb-0.5">{{ $tx->transaction_date->translatedFormat('M') }}</p>
                            <p class="text-base font-extrabold text-slate-800 leading-none tabular-nums">{{ $tx->transaction_date->format('d') }}</p>
                        </div>

                        <div class="shrink-0 w-10 h-10 rounded-xl overflow-hidden bg-[#f8fafa] border border-gray-100 flex items-center justify-center">
                            @if ($tx->image_url)
                                <a href="{{ $tx->image_url }}" target="_blank" class="w-full h-full block">
                                    <img src="{{ $tx->image_url }}" alt="ຮູບບິນ" class="w-full h-full object-cover">
                                </a>
                            @else
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-slate-800 text-sm truncate">{{ $tx->description ?: ($tx->type === 'income' ? 'ລາຍຮັບ' : 'ລາຍຈ່າຍ') }}</p>
                            <p class="text-[11px] text-gray-400 truncate">{{ $tx->recordedBy?->name ?? 'ບໍ່ລະບຸ' }}</p>
                        </div>

                        <p class="shrink-0 font-extrabold tabular-nums text-sm sm:text-base {{ $tx->type === 'income' ? 'text-brand-green' : 'text-rose-800' }}">
                            {{ $tx->type === 'income' ? '+' : '−' }}{{ number_format($tx->amount) }}
                        </p>

                        <div class="shrink-0 flex items-center gap-0.5">
                            <button wire:click="openTxEdit({{ $tx->id }})" aria-label="ແກ້ໄຂ"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-slate-600 hover:bg-gray-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button wire:click="confirmTxDelete({{ $tx->id }})" aria-label="ລຶບ"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-rose-700 hover:bg-rose-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center">
                        <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີລາຍການ</p>
                        <p class="text-gray-400 text-sm">ເພີ່ມລາຍຮັບ ຫຼື ລາຍຈ່າຍທຳອິດຂອງໂຄງການນີ້</p>
                    </div>
                @endforelse
            </div>

            @if ($transactions->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Transaction Create/Edit Modal --}}
    @if ($showTxModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full sm:max-w-lg flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                    rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
            <div class="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>
            <div class="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $editTxId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່' }}</p>
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">{{ $editTxId ? 'ແກ້ໄຂລາຍການ' : 'ເພີ່ມລາຍຈ່າຍ/ລາຍຮັບ' }}</h2>
                </div>
                <button wire:click="$set('showTxModal', false)" aria-label="ປິດ"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            <form wire:submit="saveTx" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ປະເພດ <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" wire:click="$set('tx_type', 'expense')"
                                class="px-4 py-2.5 rounded-xl text-sm font-semibold border transition-colors
                                       {{ $tx_type === 'expense' ? 'bg-rose-800 border-rose-800 text-white' : 'bg-[#f8fafa] border-gray-200 text-slate-600' }}">
                            ລາຍຈ່າຍ
                        </button>
                        <button type="button" wire:click="$set('tx_type', 'income')"
                                class="px-4 py-2.5 rounded-xl text-sm font-semibold border transition-colors
                                       {{ $tx_type === 'income' ? 'bg-brand-green border-brand-green text-white' : 'bg-[#f8fafa] border-gray-200 text-slate-600' }}">
                            ລາຍຮັບ
                        </button>
                    </div>
                    @error('tx_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຈຳນວນເງິນ (ກີບ) <span class="text-red-500">*</span></label>
                        <input wire:model="tx_amount" type="number" step="0.01" min="0" placeholder="0"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('tx_amount') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('tx_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ວັນທີ <span class="text-red-500">*</span></label>
                        <input wire:model="tx_transaction_date" type="date"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('tx_transaction_date') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('tx_transaction_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ລາຍລະອຽດ</label>
                    <input wire:model="tx_description" type="text" placeholder="ເຊັ່ນ: ຄ່າຊີມັງ, ບໍລິຈາກຈາກ..."
                           class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400
                                  focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                  @error('tx_description') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                    @error('tx_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຮູບພາບບິນ</label>

                    @if ($tx_currentImage && ! $tx_image)
                        <div class="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200 mb-2.5">
                            <img src="{{ asset('storage/' . $tx_currentImage) }}" alt="ຮູບປັດຈຸບັນ"
                                 class="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-slate-800">ຮູບພາບປັດຈຸບັນ</p>
                                <p class="text-xs text-gray-400 mt-0.5">ເລືອກໄຟລ໌ໃໝ່ເພື່ອປ່ຽນແທນ</p>
                            </div>
                        </div>
                    @endif

                    <div x-data="{ dragging: false }" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                         @drop.prevent="dragging = false; $refs.txImageInput.files = $event.dataTransfer.files; $refs.txImageInput.dispatchEvent(new Event('change'))"
                         :class="dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'"
                         class="relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer">

                        <input x-ref="txImageInput" wire:model="tx_image" type="file" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="flex flex-col items-center justify-center py-6 px-4 text-center pointer-events-none">
                            <svg class="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <p class="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span class="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                            <p class="text-xs text-gray-400/70 mt-1">ຮູບບິນ/ໃບຮັບເງິນ (ບໍ່ບັງຄັບ) — JPG, PNG — ສູງສຸດ 4MB</p>
                        </div>

                        <div wire:loading wire:target="tx_image"
                             class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 rounded-2xl">
                            <span class="text-xs font-medium text-brand-green">ກຳລັງອັບໂຫລດ...</span>
                        </div>
                    </div>

                    @error('tx_image') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror

                    @if ($tx_image)
                        <div class="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200 mt-2.5">
                            <img src="{{ $tx_image->temporaryUrl() }}" alt="ຕົວຢ່າງ" class="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-slate-800 truncate">{{ $tx_image->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-400">{{ number_format($tx_image->getSize() / 1024, 1) }} KB</p>
                            </div>
                            <button type="button" wire:click="$set('tx_image', null)"
                                    class="flex-shrink-0 text-xs text-red-500 hover:text-red-600 font-medium transition-colors px-2 py-1">
                                ຍົກເລີກ
                            </button>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-1 pb-safe">
                    <button type="button" wire:click="$set('showTxModal', false)"
                            class="px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition">
                        ບັນທຶກ
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Transaction Delete Confirm Modal --}}
    @if ($showTxDeleteModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full sm:max-w-sm rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
            <div class="sm:hidden flex justify-center pt-3 pb-1">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <h3 class="text-lg font-bold text-slate-800">ຢືນຢັນການລຶບ</h3>
                <button wire:click="$set('showTxDeleteModal', false)" aria-label="ປິດ"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
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
                <p class="text-slate-800 text-sm font-bold mb-1">ລຶບລາຍການນີ້</p>
                <p class="text-gray-400 text-xs mb-6">ລາຍການນີ້ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showTxDeleteModal', false)"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button wire:click="deleteTx"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl text-sm font-semibold hover:bg-red-600 transition-colors">
                        ລຶບລາຍການ
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

@else
    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- PROJECT LIST — the temple's building-fund board                --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ການເງິນ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ໂຄງການກໍ່ສ້າງ</h1>
            <p class="text-gray-400 text-sm mt-1">ຕິດຕາມທຶນບຸນ ລາຍຈ່າຍ ແລະ ຄວາມຄືບໜ້າແຕ່ລະໂຄງການ</p>
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
                ເພີ່ມໂຄງການ
            </button>
        </div>
    </div>

    {{-- Aggregate board across all projects --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        <div class="bg-white card-shadow rounded-2xl p-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ລວມທຶນທີ່ໄດ້ຮັບ</p>
            <p class="text-lg font-extrabold text-brand-green tabular-nums truncate">{{ number_format($totalIncomeAll) }}</p>
        </div>
        <div class="bg-white card-shadow rounded-2xl p-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ລວມລາຍຈ່າຍ</p>
            <p class="text-lg font-extrabold text-rose-800 tabular-nums truncate">{{ number_format($totalExpenseAll) }}</p>
        </div>
        <div class="bg-white card-shadow rounded-2xl p-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຍອດເຫຼືອສຸດທິ</p>
            <p class="text-lg font-extrabold tabular-nums truncate {{ ($totalIncomeAll - $totalExpenseAll) >= 0 ? 'text-slate-800' : 'text-rose-800' }}">{{ number_format($totalIncomeAll - $totalExpenseAll) }}</p>
        </div>
        <div class="bg-white card-shadow rounded-2xl p-4">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ກຳລັງດຳເນີນການ</p>
            <p class="text-lg font-extrabold text-slate-800 tabular-nums truncate">{{ $ongoingCount }} ໂຄງການ</p>
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
                   placeholder="ຄົ້ນຫາຊື່ໂຄງການ..."
                   class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm
                          text-slate-800 placeholder:text-gray-400
                          focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
        </div>
        <select wire:model.live="filterStatus"
                class="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                       text-slate-800 sm:w-auto
                       focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
            <option value="">ທຸກສະຖານະ</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
        @if ($search !== '' || $filterStatus !== '')
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

    {{-- Project cards — each a miniature fundraising board --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($projects as $project)
            @php
                $incomeSum = (float) ($project->income_sum ?? 0);
                $expenseSum = (float) ($project->expense_sum ?? 0);
                $balance = $incomeSum - $expenseSum;
                $percent = ($project->target_amount && (float) $project->target_amount > 0)
                    ? min(100, round($incomeSum / (float) $project->target_amount * 100, 1))
                    : null;
            @endphp
            <div class="bg-white rounded-2xl card-shadow overflow-hidden flex flex-col">
                @if ($project->image_url)
                    <div class="relative">
                        <img src="{{ $project->image_url }}" alt="{{ $project->name }}" class="w-full aspect-[16/10] object-cover">
                        <span class="absolute top-3 left-3 inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold backdrop-blur-sm
                            {{ $project->status === 'completed' ? 'bg-brand-green/90 text-white' : ($project->status === 'paused' ? 'bg-white/90 text-gray-600' : 'bg-amber-500/90 text-white') }}">
                            {{ $statuses[$project->status] ?? $project->status }}
                        </span>
                    </div>
                @else
                    <div class="h-1.5 w-full {{ $project->status === 'completed' ? 'bg-brand-green' : ($project->status === 'paused' ? 'bg-gray-300' : 'bg-gradient-to-r from-amber-400 to-brand-green-mid') }}"></div>
                @endif
                <button wire:click="viewProject({{ $project->id }})" class="text-left px-5 pt-4 pb-4 flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        @unless ($project->image_url)
                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold
                                {{ $project->status === 'completed' ? 'bg-brand-light-green text-brand-green' : ($project->status === 'paused' ? 'bg-gray-100 text-gray-500' : 'bg-amber-50 text-amber-600') }}">
                                {{ $statuses[$project->status] ?? $project->status }}
                            </span>
                        @endunless
                        @if ($project->start_date)
                            <span class="text-gray-300 text-[11px] truncate">ເລີ່ມ {{ $project->start_date->translatedFormat('d/m/Y') }}</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-slate-800 leading-snug hover:text-brand-green transition-colors mb-2">{{ $project->name }}</h3>

                    @if ($percent !== null)
                        <div class="flex items-baseline justify-between mb-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລະດົມທຶນໄດ້</span>
                            <span class="text-xs font-extrabold text-brand-green tabular-nums">{{ $percent }}%</span>
                        </div>
                        <div class="relative h-2 rounded-full bg-brand-light-green mb-3">
                            <div class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#ffb366] to-brand-green"
                                 style="width: {{ $percent }}%"></div>
                        </div>
                    @else
                        <div class="grid grid-cols-3 gap-2 pt-1 pb-1">
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ຮັບ</p>
                                <p class="text-xs font-bold text-brand-green truncate tabular-nums">{{ number_format($incomeSum) }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ຈ່າຍ</p>
                                <p class="text-xs font-bold text-rose-800 truncate tabular-nums">{{ number_format($expenseSum) }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ເຫຼືອ</p>
                                <p class="text-xs font-bold truncate tabular-nums {{ $balance >= 0 ? 'text-slate-800' : 'text-rose-800' }}">{{ number_format($balance) }}</p>
                            </div>
                        </div>
                    @endif
                </button>
                <div class="flex items-stretch border-t border-gray-100">
                    <button wire:click="viewProject({{ $project->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        ລາຍລະອຽດ
                    </button>
                    <div class="w-px bg-gray-100 self-stretch"></div>
                    <button wire:click="openEdit({{ $project->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z"/>
                        </svg>
                        ແກ້ໄຂ
                    </button>
                    <div class="w-px bg-gray-100 self-stretch"></div>
                    <button wire:click="confirmDelete({{ $project->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-rose-800 hover:bg-rose-50 py-2.5 transition-colors">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5"/>
                        </svg>
                        ລຶບ
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl card-shadow py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີໂຄງການກໍ່ສ້າງ</p>
                <p class="text-gray-400 text-sm">ເພີ່ມໂຄງການ ເພື່ອເລີ່ມບັນທຶກທຶນບຸນ ແລະ ຄວາມຄືບໜ້າ</p>
            </div>
        @endforelse
    </div>

    @if ($projects->hasPages())
        <div class="mt-6">{{ $projects->links() }}</div>
    @endif

    {{-- Project Create/Edit Modal --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full sm:max-w-lg flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                    rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
            <div class="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>
            <div class="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່' }}</p>
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">{{ $editId ? 'ແກ້ໄຂໂຄງການ' : 'ເພີ່ມໂຄງການກໍ່ສ້າງ' }}</h2>
                </div>
                <button wire:click="$set('showModal', false)" aria-label="ປິດ"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຊື່ໂຄງການ <span class="text-red-500">*</span></label>
                    <input wire:model="name" type="text" placeholder="ເຊັ່ນ: ກໍ່ສ້າງສາລາອະເນກປະສົງ"
                           class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400
                                  focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                  @error('name') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ລາຍລະອຽດ</label>
                    <textarea wire:model="description" rows="3" placeholder="ລາຍລະອຽດໂຄງການ..."
                              class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400
                                     focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                     @error('description') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror"></textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ວັນທີເລີ່ມ</label>
                        <input wire:model="start_date" type="date"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('start_date') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ເປົ້າໝາຍລະດົມທຶນ (ກີບ)</label>
                        <input wire:model="target_amount" type="number" step="0.01" min="0" placeholder="ບໍ່ກຳນົດ"
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('target_amount') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('target_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ສະຖານະ <span class="text-red-500">*</span></label>
                    <select wire:model="status"
                            class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800
                                   focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                   @error('status') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຮູບໂຄງການ</label>
                    <p class="text-[11px] text-gray-400 mb-2 -mt-1">ຮູບນີ້ຈະສະແດງໃນໜ້າສາທາລະນະ ໃຫ້ຍາດໂຍມເຫັນຄວາມຄືບໜ້າ</p>

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

                    <div x-data="{ dragging: false }" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                         @drop.prevent="dragging = false; $refs.projectImageInput.files = $event.dataTransfer.files; $refs.projectImageInput.dispatchEvent(new Event('change'))"
                         :class="dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'"
                         class="relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer">

                        <input x-ref="projectImageInput" wire:model="image" type="file" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="flex flex-col items-center justify-center py-6 px-4 text-center pointer-events-none">
                            <svg class="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <p class="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span class="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                            <p class="text-xs text-gray-400/70 mt-1">ຮູບໂຄງການ (ບໍ່ບັງຄັບ) — JPG, PNG — ສູງສຸດ 4MB</p>
                        </div>

                        <div wire:loading wire:target="image"
                             class="absolute inset-0 z-20 flex items-center justify-center bg-white/70 rounded-2xl">
                            <span class="text-xs font-medium text-brand-green">ກຳລັງອັບໂຫລດ...</span>
                        </div>
                    </div>

                    @error('image') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror

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
                            class="px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition">
                        ບັນທຶກ
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Project Delete Confirm Modal --}}
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full sm:max-w-sm rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
            <div class="sm:hidden flex justify-center pt-3 pb-1">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <h3 class="text-lg font-bold text-slate-800">ຢືນຢັນການລຶບ</h3>
                <button wire:click="$set('showDeleteModal', false)" aria-label="ປິດ"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
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
                <p class="text-slate-800 text-sm font-bold mb-1">ລຶບໂຄງການນີ້</p>
                <p class="text-gray-400 text-xs mb-6">ລາຍການລາຍຈ່າຍ/ລາຍຮັບທັງໝົດຂອງໂຄງການນີ້ຈະຖືກລຶບຖາວອນ</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button wire:click="delete"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl text-sm font-semibold hover:bg-red-600 transition-colors">
                        ລຶບໂຄງການ
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endif
</div>
