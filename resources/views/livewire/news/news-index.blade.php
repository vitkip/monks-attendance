<div>
    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຂ່າວສານ</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">ຂ່າວ ແລະ ປະກາດ</h1>
            <p class="text-gray-400 text-sm mt-1">ຂ່າວສານ ແລະ ປະກາດຕ່າງໆພາຍໃນວັດ</p>
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
            @if ($this->isAdmin())
                <button wire:click="openCreate"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5
                               bg-brand-green text-white rounded-2xl text-sm font-semibold
                               shadow-lg shadow-brand-green/20 hover:bg-opacity-90
                               transition flex-shrink-0">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    ເພີ່ມຂ່າວ
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
                   placeholder="ຄົ້ນຫາຫົວຂໍ້ຂ່າວ..."
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
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        @if ($this->isAdmin())
            <select wire:model.live="filterStatus"
                    class="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                           text-slate-800 sm:w-auto
                           focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                <option value="">ທຸກສະຖານະ</option>
                <option value="1">ເຜີຍແຜ່ແລ້ວ</option>
                <option value="0">ຮ່າງ</option>
            </select>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- MOBILE: Compact list (visible below md)                       --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div class="md:hidden space-y-3">
        @forelse ($news as $item)
            <div class="bg-white rounded-2xl card-shadow overflow-hidden">

                <button wire:click="openView({{ $item->id }})" class="w-full flex items-center gap-3 px-4 py-3.5 text-left">
                    <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-brand-light-green">
                                <svg class="w-5 h-5 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-800 text-sm leading-tight truncate min-w-0 flex-1">{{ $item->title }}</span>
                            @if ($this->isAdmin())
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold flex-shrink-0
                                    {{ $item->status ? 'bg-brand-light-green text-brand-green' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $item->status ? 'ເຜີຍແຜ່ແລ້ວ' : 'ຮ່າງ' }}
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-400 text-xs mt-0.5 truncate">
                            {{ $item->published_at?->format('d/m/Y') ?? $item->created_at->format('d/m/Y') }}
                            &nbsp;·&nbsp;{{ $item->author?->name ?? 'ບໍ່ລະບຸ' }}
                            @if ($item->category)
                                &nbsp;·&nbsp;{{ $item->category->name }}
                            @endif
                        </p>
                    </div>
                </button>

                @if ($this->isAdmin())
                    <div class="flex items-stretch border-t border-gray-100">
                        <button wire:click="togglePublish({{ $item->id }})"
                                class="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors touch-manipulation">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" class="w-3.5 h-3.5 shrink-0">
                                @if ($item->status)
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12s3-7 7-7 7 7 7 7-3 7-7 7-7-7-7-7z"/>
                                    <circle cx="10" cy="12" r="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.5 3.5l13 13M8.3 5.2A7.7 7.7 0 0110 5c4 0 7 7 7 7a13 13 0 01-2.3 3.1M6.2 6.2C4 7.8 3 10 3 10s3 7 7 7c1 0 1.9-.2 2.7-.5"/>
                                @endif
                            </svg>
                            {{ $item->status ? 'ເກັບຮ່າງ' : 'ເຜີຍແຜ່' }}
                        </button>
                        <div class="w-px bg-gray-100 self-stretch"></div>
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
                    <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z"/>
                    </svg>
                </div>
                <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີຂ່າວ</p>
                <p class="text-gray-400 text-sm">
                    @if ($this->isAdmin())
                        ກົດ "ເພີ່ມຂ່າວ" ເພື່ອປະກາດຂ່າວໃໝ່
                    @else
                        ຍັງບໍ່ມີການປະກາດຂ່າວໃນຂະນະນີ້
                    @endif
                </p>
            </div>
        @endforelse

        @if ($news->hasPages())
            <div class="pt-1">{{ $news->links() }}</div>
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
                    <th class="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຫົວຂໍ້ຂ່າວ</th>
                    @if ($this->isAdmin())
                        <th class="w-32 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ສະຖານະ</th>
                    @endif
                    <th class="w-36 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide hidden lg:table-cell">ຜູ້ຂຽນ</th>
                    <th class="w-24 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ວັນທີ</th>
                    <th class="w-32 px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($news as $item)
                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5">
                            <button wire:click="openView({{ $item->id }})" class="block w-11 h-11 rounded-xl overflow-hidden bg-gray-100">
                                @if ($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-brand-light-green">
                                        <svg class="w-4 h-4 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z"/>
                                        </svg>
                                    </div>
                                @endif
                            </button>
                        </td>
                        <td class="px-4 py-3.5 overflow-hidden">
                            <button wire:click="openView({{ $item->id }})" class="text-left block w-full min-w-0">
                                @if ($item->category)
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 mb-1">
                                        {{ $item->category->name }}
                                    </span>
                                @endif
                                <p class="font-bold text-slate-800 leading-snug hover:text-brand-green transition-colors truncate">{{ $item->title }}</p>
                                <p class="text-gray-400 text-xs mt-0.5 truncate">{{ $item->excerpt_or_summary }}</p>
                            </button>
                        </td>
                        @if ($this->isAdmin())
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                    {{ $item->status ? 'bg-brand-light-green text-brand-green' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $item->status ? 'ເຜີຍແຜ່ແລ້ວ' : 'ຮ່າງ' }}
                                </span>
                            </td>
                        @endif
                        <td class="px-4 py-3.5 text-slate-600 truncate hidden lg:table-cell">{{ $item->author?->name ?? 'ບໍ່ລະບຸ' }}</td>
                        <td class="px-4 py-3.5 text-slate-600 whitespace-nowrap">{{ $item->published_at?->format('d/m/Y') ?? $item->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex justify-center gap-1">
                                @if ($this->isAdmin())
                                    <button wire:click="togglePublish({{ $item->id }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                                   text-slate-500 hover:bg-gray-100 transition-all"
                                            aria-label="{{ $item->status ? 'ເກັບຮ່າງ' : 'ເຜີຍແຜ່' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="1.75">
                                            @if ($item->status)
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12s3-7 7-7 7 7 7 7-3 7-7 7-7-7-7-7z"/>
                                                <circle cx="10" cy="12" r="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.5 3.5l13 13M8.3 5.2A7.7 7.7 0 0110 5c4 0 7 7 7 7a13 13 0 01-2.3 3.1M6.2 6.2C4 7.8 3 10 3 10s3 7 7 7c1 0 1.9-.2 2.7-.5"/>
                                            @endif
                                        </svg>
                                    </button>
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
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z"/>
                                </svg>
                            </div>
                            <p class="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີຂ່າວ</p>
                            <p class="text-gray-400 text-sm">
                                @if ($this->isAdmin())
                                    ກົດ "ເພີ່ມຂ່າວ" ເພື່ອປະກາດຂ່າວໃໝ່
                                @else
                                    ຍັງບໍ່ມີການປະກາດຂ່າວໃນຂະນະນີ້
                                @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($news->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                {{ $news->links() }}
            </div>
        @endif
    </div>

    {{-- View Modal --}}
    @if ($showViewModal && $viewing)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
         wire:click.self="$set('showViewModal', false)">
        <div class="bg-white w-full sm:max-w-2xl flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                    rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

            <div class="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                <div class="w-10 h-1 rounded-full bg-gray-200"></div>
            </div>

            <div class="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລາຍລະອຽດຂ່າວ</p>
                <button wire:click="$set('showViewModal', false)" aria-label="ປິດ"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                               hover:bg-gray-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l8 8M14 6l-8 8"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                @if ($viewing->image_url)
                    <img src="{{ $viewing->image_url }}" alt="{{ $viewing->title }}" class="w-full aspect-[16/9] object-cover">
                @endif
                <div class="px-6 py-5">
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        @if ($viewing->category)
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600">
                                {{ $viewing->category->name }}
                            </span>
                        @endif
                        @if ($this->isAdmin())
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $viewing->status ? 'bg-brand-light-green text-brand-green' : 'bg-gray-100 text-gray-500' }}">
                                {{ $viewing->status ? 'ເຜີຍແຜ່ແລ້ວ' : 'ຮ່າງ' }}
                            </span>
                        @endif
                        <span class="text-xs text-gray-400">
                            {{ $viewing->published_at?->format('d/m/Y') ?? $viewing->created_at->format('d/m/Y') }}
                            &nbsp;·&nbsp;{{ $viewing->author?->name ?? 'ບໍ່ລະບຸ' }}
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800 leading-snug">{{ $viewing->title }}</h2>
                    <div class="prose prose-sm max-w-none mt-4 text-slate-600">{!! $viewing->content_html !!}</div>
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
                        {{ $editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່' }}
                    </p>
                    <h2 class="text-lg font-bold text-slate-800 mt-0.5">
                        {{ $editId ? 'ແກ້ໄຂຂ່າວ' : 'ເພີ່ມຂ່າວໃໝ່' }}
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
                        ຫົວຂໍ້ຂ່າວ <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="title" type="text" placeholder="ຫົວຂໍ້ຂ່າວ"
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
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ຄຳໂປຍຫຍໍ້ <span class="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                    </label>
                    <textarea wire:model="excerpt" rows="2" placeholder="ສະຫຼຸບຫຍໍ້ຂອງຂ່າວ..."
                              class="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm
                                     text-slate-800 placeholder:text-gray-400
                                     focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow resize-none"></textarea>
                    @error('excerpt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ເນື້ອຫາ <span class="text-red-500">*</span>
                    </label>
                    <x-rich-editor name="content" :value="$content" placeholder="ເນື້ອຫາຂ່າວ..."
                                   :wire-key="'news-content-editor-' . $editorNonce" />
                </div>

                <div>
                    <label class="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                        ຮູບພາບປົກ <span class="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
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
                            <button type="button" wire:click="removeCurrentImage"
                                    class="flex-shrink-0 text-xs text-red-500 hover:text-red-600 font-medium transition-colors px-2 py-1">
                                ລຶບຮູບ
                            </button>
                        </div>
                    @endif

                    {{-- Dropzone --}}
                    <div x-data="{ dragging: false }" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                         @drop.prevent="dragging = false; $refs.newsImageInput.files = $event.dataTransfer.files; $refs.newsImageInput.dispatchEvent(new Event('change'))"
                         :class="dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'"
                         class="relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer">

                        <input x-ref="newsImageInput" wire:model="image" type="file" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                        <div class="flex flex-col items-center justify-center py-6 px-4 text-center pointer-events-none">
                            <svg class="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>
                            <p class="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span class="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                            <p class="text-xs text-gray-400/70 mt-1">JPG, PNG, GIF — ສູງສຸດ 2MB</p>
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
                            {{ $publishNow ? 'ຂ່າວຈະສະແດງຢູ່ໜ້າ Frontend ທັນທີທີ່ບັນທຶກ' : 'ບັນທຶກໄວ້ເປັນຮ່າງ, ຍັງບໍ່ເຜີຍແຜ່' }}
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
                <p class="text-slate-800 text-sm font-bold mb-1">ລຶບຂ່າວນີ້</p>
                <p class="text-gray-400 text-xs mb-6">ຂ່າວນີ້ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl
                                   text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                        ຍົກເລີກ
                    </button>
                    <button wire:click="delete"
                            class="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl
                                   text-sm font-semibold hover:bg-red-600 transition-colors">
                        ລຶບຂ່າວ
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
