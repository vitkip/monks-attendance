<div>
    {{-- Page header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ການຈັດການ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ໝວດໝູ່ບົດສູດມົນ</h1>
            <p class="text-gray-400 text-sm mt-1">ຈັດການໝວດໝູ່ ແລະ ໝວດໝູ່ຍ່ອຍຂອງບົດສູດມົນ</p>
        </div>
        <button wire:click="openCreate"
                class="shrink-0 flex items-center gap-2 px-4 py-2.5 sm:px-5 sm:py-3 bg-brand-green text-white rounded-2xl text-sm font-semibold hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 touch-manipulation">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 4v12M4 10h12"/>
            </svg>
            <span class="hidden sm:inline">ເພີ່ມໝວດໝູ່</span>
            <span class="sm:hidden">ເພີ່ມ</span>
        </button>
    </div>

    {{-- Tree list --}}
    <div class="bg-white rounded-2xl card-shadow border border-gray-50 divide-y divide-gray-100 overflow-hidden">
        @forelse ($categories as $category)
            <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 transition-colors"
                 style="padding-left: {{ 1.25 + $category->depth * 1.5 }}rem">

                @if ($category->depth > 0)
                    <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 3v10a2 2 0 002 2h6M6 8h4"/>
                    </svg>
                @endif

                <div class="min-w-0 flex-1">
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $category->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">ໃຊ້ {{ $category->chants_count }} ບົດ</p>
                </div>

                <div class="flex items-center gap-1 shrink-0">
                    <button wire:click="openCreate({{ $category->id }})"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:bg-gray-100 transition-all" aria-label="ເພີ່ມໝວດໝູ່ຍ່ອຍ">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 4v12M4 10h12"/>
                        </svg>
                        ຍ່ອຍ
                    </button>
                    <button wire:click="openEdit({{ $category->id }})"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:bg-gray-100 transition-all" aria-label="ແກ້ໄຂ">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z"/>
                        </svg>
                    </button>
                    <button wire:click="delete({{ $category->id }})"
                            wire:confirm="ຢືນຢັນການລຶບໝວດໝູ່ '{{ $category->name }}'?"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg text-red-500 hover:bg-red-50 transition-all" aria-label="ລຶບ">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z"/>
                    </svg>
                </div>
                <p class="font-bold text-slate-800 mb-1">ຍັງບໍ່ມີໝວດໝູ່ບົດສູດມົນ</p>
                <p class="text-gray-400 text-sm">ກົດ "ເພີ່ມໝວດໝູ່" ເພື່ອຕັ້ງຄ່າໃໝ່</p>
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
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">ໝວດໝູ່ບົດສູດມົນ</h2>
                </div>
                <button wire:click="$set('showModal', false)" aria-label="ປິດ"
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
                        ຊື່ໝວດໝູ່ <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="name" type="text"
                           placeholder="ເຊັ່ນ: ບົດສູດ, ບົດທີ 1, ສາມທະຍາຍທັມແປ..."
                           class="w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                  @error('name') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                        ໝວດໝູ່ແມ່ <span class="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                    </label>
                    <select wire:model="parent_id"
                            class="w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                   @error('parent_id') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        <option value="">— ໝວດໝູ່ຫຼັກ —</option>
                        @foreach ($parentOptions as $option)
                            <option value="{{ $option->id }}">
                                {{ str_repeat('— ', $option->depth) }}{{ $option->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
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
