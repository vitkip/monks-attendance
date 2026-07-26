<div>
    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ໜ້າຫຼັກ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ສະໄລ້ Hero</h1>
            <p class="text-gray-400 text-sm mt-1">ຈັດການຮູບພາບ ແລະ ຂໍ້ຄວາມທີ່ສະແດງເປັນສະໄລ້ຢູ່ໜ້າຫຼັກ Frontend</p>
        </div>
        <div class="w-full sm:w-auto flex items-center gap-2.5">
            <a href="{{ route('news.public.index') }}" target="_blank" rel="noopener"
               class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5
                      bg-white border border-gray-200 text-slate-600 rounded-2xl text-sm font-semibold
                      hover:bg-gray-50 transition flex-shrink-0">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                ເປີດໜ້າ Frontend
            </a>
            <button wire:click="openCreate"
                    class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5
                           bg-brand-green text-white rounded-2xl text-sm font-semibold
                           shadow-lg shadow-brand-green/20 hover:bg-opacity-90
                           transition flex-shrink-0">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                ເພີ່ມສະໄລ້
            </button>
        </div>
    </div>

    {{-- Slide list --}}
    <div class="bg-white rounded-2xl card-shadow overflow-hidden">
        @forelse ($slides as $slide)
            <div class="flex items-center gap-4 px-4 sm:px-5 py-4 {{ ! $loop->last ? 'border-b border-gray-100' : '' }}">

                {{-- Reorder controls --}}
                <div class="flex flex-col gap-1 shrink-0">
                    <button wire:click="moveUp({{ $slide->id }})" @if($loop->first) disabled @endif
                            class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-slate-700 transition-colors disabled:opacity-30 disabled:pointer-events-none"
                            aria-label="ຍ້າຍຂຶ້ນ">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l5-5 5 5"/>
                        </svg>
                    </button>
                    <button wire:click="moveDown({{ $slide->id }})" @if($loop->last) disabled @endif
                            class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-slate-700 transition-colors disabled:opacity-30 disabled:pointer-events-none"
                            aria-label="ຍ້າຍລົງ">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8l5 5 5-5"/>
                        </svg>
                    </button>
                </div>

                {{-- Thumbnail --}}
                <div class="w-20 h-14 sm:w-24 sm:h-16 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                    <img src="{{ $slide->image_url }}" alt="{{ $slide->title }}" class="w-full h-full object-cover">
                </div>

                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-bold text-slate-800 text-sm truncate">{{ $slide->title }}</p>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0
                            {{ $slide->status ? 'bg-brand-light-green text-brand-green' : 'bg-gray-100 text-gray-500' }}">
                            {{ $slide->status ? 'ເຜີຍແຜ່ແລ້ວ' : 'ຮ່າງ' }}
                        </span>
                    </div>
                    @if ($slide->subtitle)
                        <p class="text-gray-400 text-xs mt-0.5 truncate">{{ $slide->subtitle }}</p>
                    @endif
                    @if ($slide->link_url)
                        <p class="text-gray-300 text-[11px] mt-0.5 truncate">{{ $slide->link_url }}</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1 shrink-0">
                    <button wire:click="togglePublish({{ $slide->id }})"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                   text-slate-500 hover:bg-gray-100 transition-all"
                            aria-label="{{ $slide->status ? 'ເກັບຮ່າງ' : 'ເຜີຍແຜ່' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="1.75">
                            @if ($slide->status)
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12s3-7 7-7 7 7 7 7-3 7-7 7-7-7-7-7z"/>
                                <circle cx="10" cy="12" r="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.5 3.5l13 13M8.3 5.2A7.7 7.7 0 0110 5c4 0 7 7 7 7a13 13 0 01-2.3 3.1M6.2 6.2C4 7.8 3 10 3 10s3 7 7 7c1 0 1.9-.2 2.7-.5"/>
                            @endif
                        </svg>
                    </button>
                    <button wire:click="openEdit({{ $slide->id }})"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                   text-slate-500 hover:bg-gray-100 transition-all" aria-label="ແກ້ໄຂ">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button wire:click="confirmDelete({{ $slide->id }})"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                   text-red-500 hover:bg-red-50 transition-all" aria-label="ລຶບ">
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z"/>
                    </svg>
                </div>
                <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີສະໄລ້</p>
                <p class="text-gray-400 text-sm">ກົດ "ເພີ່ມສະໄລ້" ເພື່ອສ້າງສະໄລ້ທຳອິດສຳລັບໜ້າຫຼັກ</p>
            </div>
        @endforelse
    </div>

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
                        {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່' }}
                    </p>
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">
                        {{ $editId ? 'ແກ້ໄຂສະໄລ້' : 'ເພີ່ມສະໄລ້ໃໝ່' }}
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
                        ຫົວຂໍ້ <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="title" type="text" placeholder="ຫົວຂໍ້ສະໄລ້"
                           class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                  text-slate-800 placeholder:text-gray-400
                                  focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                  @error('title') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ຄຳບັນຍາຍ <span class="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                    </label>
                    <textarea wire:model="subtitle" rows="2" placeholder="ຂໍ້ຄວາມສັ້ນໆໃຕ້ຫົວຂໍ້..."
                              class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                                     text-slate-800 placeholder:text-gray-400
                                     focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow resize-none"></textarea>
                    @error('subtitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                            ລິ້ງ <span class="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                        </label>
                        <input wire:model="link_url" type="text" placeholder="https://..."
                               class="w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                      text-slate-800 placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                      @error('link_url') border-red-300 focus:ring-red-200 @else border-gray-200 focus:ring-brand-green/30 @enderror">
                        @error('link_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                            ຂໍ້ຄວາມປຸ່ມ <span class="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                        </label>
                        <input wire:model="button_text" type="text" placeholder="ອ່ານຕໍ່"
                               class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                                      text-slate-800 placeholder:text-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ຮູບພາບສະໄລ້ <span class="text-red-500">*</span>
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
                         @drop.prevent="dragging = false; $refs.heroSlideImageInput.files = $event.dataTransfer.files; $refs.heroSlideImageInput.dispatchEvent(new Event('change'))"
                         :class="dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'"
                         class="relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer">

                        <input x-ref="heroSlideImageInput" wire:model="image" type="file" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="flex flex-col items-center justify-center py-6 px-4 text-center pointer-events-none">
                            <svg class="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <p class="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span class="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                            <p class="text-xs text-gray-400/70 mt-1">ແນະນຳອັດຕາສ່ວນ 16:9 — JPG, PNG — ສູງສຸດ 2MB</p>
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

                <label class="flex items-center justify-between gap-3 cursor-pointer w-full p-3.5 rounded-2xl border border-gray-200 bg-[#f8fafa]">
                    <span>
                        <span class="block text-sm font-medium text-slate-800">ເຜີຍແຜ່ທັນທີ</span>
                        <span class="block text-xs text-gray-400 mt-0.5">
                            {{ $publishNow ? 'ສະໄລ້ຈະສະແດງຢູ່ໜ້າຫຼັກທັນທີທີ່ບັນທຶກ' : 'ບັນທຶກໄວ້ເປັນຮ່າງ, ຍັງບໍ່ສະແດງ' }}
                        </span>
                    </span>
                    <span class="inline-flex flex-shrink-0">
                        <input wire:model="publishNow" type="checkbox" class="sr-only peer" @checked($publishNow)>
                        <div class="relative w-10 h-6 rounded-full transition-colors duration-200
                                    bg-gray-200 peer-checked:bg-brand-green
                                    after:content-[''] after:absolute after:top-1 after:left-1
                                    after:bg-white after:rounded-full after:w-4 after:h-4
                                    after:transition-transform after:duration-200
                                    peer-checked:after:translate-x-4 shrink-0"></div>
                    </span>
                </label>

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
                <p class="text-slate-800 text-sm font-bold mb-1">ລຶບສະໄລ້ນີ້</p>
                <p class="text-gray-400 text-xs mb-6">ສະໄລ້ນີ້ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl
                                   text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button wire:click="delete"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl
                                   text-sm font-semibold hover:bg-red-600 transition-colors">
                        ລຶບສະໄລ້
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
