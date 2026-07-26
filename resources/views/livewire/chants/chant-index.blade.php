<div>
    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຄຳສອນ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ບົດສູດມົນ</h1>
            <p class="text-gray-400 text-sm mt-1">ຈັດການບົດສູດມົນ ແລະ ໝວດໝູ່ຕ່າງໆ</p>
        </div>
        <div class="w-full sm:w-auto flex items-center gap-2.5">
            <a href="{{ route('chants.public.index') }}" target="_blank" rel="noopener"
               class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5
                      bg-white border border-gray-200 text-slate-600 rounded-2xl text-sm font-semibold
                      hover:bg-gray-50 transition flex-shrink-0">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                ເປີດໜ້າ Frontend
            </a>
            @if ($this->isAdmin())
                <button wire:click="openCreate"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5
                               bg-brand-green text-white rounded-2xl text-sm font-semibold
                               shadow-lg shadow-brand-green/20 hover:bg-opacity-90
                               transition flex-shrink-0">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    ເພີ່ມບົດສູດມົນ
                </button>
            @endif
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
                   placeholder="ຄົ້ນຫາຫົວຂໍ້ບົດສູດມົນ..."
                   class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm
                          text-slate-800 placeholder:text-gray-400
                          focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
        </div>
        <select wire:model.live="filterCategory"
                class="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                       text-slate-800 sm:w-auto
                       focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
            <option value="">ທຸກໝວດໝູ່</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ str_repeat('— ', $category->depth) }}{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MOBILE: Compact list (visible below md)                       --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="md:hidden space-y-3">
        @forelse ($chants as $item)
            <div class="bg-white rounded-2xl card-shadow overflow-hidden">

                <button wire:click="openView({{ $item->id }})" class="w-full flex items-center gap-3 px-4 py-3.5 text-left">
                    <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 bg-brand-light-green flex items-center justify-center">
                        <span class="text-lg text-brand-green">☸</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="font-bold text-slate-800 text-sm leading-tight truncate block">{{ $item->title }}</span>
                        <p class="text-gray-400 text-xs mt-0.5 truncate">
                            {{ $item->category?->name ?? 'ບໍ່ມີໝວດໝູ່' }}
                        </p>
                    </div>
                </button>

                @if ($this->isAdmin())
                    <div class="flex items-stretch border-t border-gray-100">
                        <button wire:click="openEdit({{ $item->id }})"
                                class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors touch-manipulation">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z"/>
                            </svg>
                            ແກ້ໄຂ
                        </button>
                        <div class="w-px bg-gray-100 self-stretch"></div>
                        <button wire:click="confirmDelete({{ $item->id }})"
                                class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-red-500 hover:bg-red-50 py-2.5 transition-colors touch-manipulation">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5"/>
                            </svg>
                            ລຶບ
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-3xl card-shadow py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl text-brand-green">☸</span>
                </div>
                <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີບົດສູດມົນ</p>
                <p class="text-gray-400 text-sm">
                    @if ($this->isAdmin())
                        ກົດ "ເພີ່ມບົດສູດມົນ" ເພື່ອເພີ່ມໃໝ່
                    @else
                        ຍັງບໍ່ມີບົດສູດມົນໃນຂະນະນີ້
                    @endif
                </p>
            </div>
        @endforelse

        @if ($chants->hasPages())
            <div class="pt-1">{{ $chants->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- DESKTOP: Full table (visible md+)                              --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="hidden md:block bg-white rounded-2xl card-shadow overflow-hidden">
        <table class="w-full text-sm table-fixed">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຫົວຂໍ້ບົດສູດມົນ</th>
                    <th class="w-48 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ໝວດໝູ່</th>
                    <th class="w-32 px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($chants as $item)
                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5 overflow-hidden">
                            <button wire:click="openView({{ $item->id }})" class="text-left block w-full min-w-0">
                                <p class="font-bold text-slate-800 leading-snug hover:text-brand-green transition-colors truncate">{{ $item->title }}</p>
                            </button>
                        </td>
                        <td class="px-4 py-3.5">
                            @if ($item->category)
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600">
                                    {{ $item->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">ບໍ່ມີໝວດໝູ່</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex justify-center gap-1">
                                @if ($this->isAdmin())
                                    <button wire:click="openEdit({{ $item->id }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                                   text-slate-500 hover:bg-gray-100 transition-all" aria-label="ແກ້ໄຂ">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $item->id }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                                   text-red-500 hover:bg-red-50 transition-all" aria-label="ລຶບ">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @else
                                    <button wire:click="openView({{ $item->id }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                                   text-slate-500 hover:bg-gray-100 transition-all">
                                        ອ່ານ
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-16 text-center">
                            <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl text-brand-green">☸</span>
                            </div>
                            <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີບົດສູດມົນ</p>
                            <p class="text-gray-400 text-sm">
                                @if ($this->isAdmin())
                                    ກົດ "ເພີ່ມບົດສູດມົນ" ເພື່ອເພີ່ມໃໝ່
                                @else
                                    ຍັງບໍ່ມີບົດສູດມົນໃນຂະນະນີ້
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($chants->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                {{ $chants->links() }}
            </div>
        @endif
    </div>

    {{-- View Modal --}}
    @if ($showViewModal && $viewing)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
         wire:click.self="$set('showViewModal', false)">
        <div class="bg-white w-full sm:max-w-5xl flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                    rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

            <div class="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            <div class="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລາຍລະອຽດບົດສູດມົນ</p>
                <button wire:click="$set('showViewModal', false)" aria-label="ປິດ"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                               hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="px-6 py-5">
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        @if ($viewing->category)
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600">
                                {{ $viewing->category->name }}
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-slate-800 leading-snug">{{ $viewing->title }}</h2>
                    <div class="prose prose-sm max-w-none mt-4 text-slate-600 leading-loose">{!! $viewing->content_html !!}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Create / Edit Modal --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white w-full sm:max-w-5xl flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                    rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

            <div class="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            <div class="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່' }}
                    </p>
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">
                        {{ $editId ? 'ແກ້ໄຂບົດສູດມົນ' : 'ເພີ່ມບົດສູດມົນໃໝ່' }}
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

            <form wire:submit="save" class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ຫົວຂໍ້ບົດສູດມົນ <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="title" type="text" placeholder="ຫົວຂໍ້ບົດສູດມົນ"
                           class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                  text-slate-800 placeholder:text-gray-400
                                  focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                  @error('title') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ໝວດໝູ່ <span class="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                    </label>
                    <select wire:model="category_id"
                            class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                                   text-slate-800
                                   focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                        <option value="">— ບໍ່ມີໝວດໝູ່ —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ str_repeat('— ', $category->depth) }}{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ເນື້ອໃນ <span class="text-red-500">*</span>
                    </label>
                    <x-rich-editor name="content" :value="$content" placeholder="ເນື້ອໃນບົດສູດມົນ..."
                                   :wire-key="'chant-content-editor-' . $editorNonce" />
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
        <div class="bg-white w-full sm:max-w-5xl rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

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
                <p class="text-slate-800 text-sm font-bold mb-1">ລຶບບົດສູດມົນນີ້</p>
                <p class="text-gray-400 text-xs mb-6">ບົດສູດມົນນີ້ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl
                                   text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button wire:click="delete"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl
                                   text-sm font-semibold hover:bg-red-600 transition-colors">
                        ລຶບ
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
