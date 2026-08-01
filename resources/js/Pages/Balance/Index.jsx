import { router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

function fmt(n) {
    return new Intl.NumberFormat('en-US').format(Math.round(n));
}

const typeClasses = {
    monk: 'bg-orange-50 text-orange-600',
    novice: 'bg-teal-50 text-teal-600',
    nun: 'bg-purple-50 text-purple-600',
};

export default function BalanceIndex({ filters, monks, totalUnpaid, monksWithDebt, unpaidCount }) {
    const [search, setSearch] = useState(filters.search);
    const [filterType, setFilterType] = useState(filters.type);
    const [onlyDebt, setOnlyDebt] = useState(filters.onlyDebt);
    const [expanded, setExpanded] = useState([]);
    const debounceRef = useRef(null);

    useEffect(() => {
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('balance.index'), { search, type: filterType, onlyDebt: onlyDebt ? '1' : '0' }, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            });
        }, 300);
        return () => clearTimeout(debounceRef.current);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, filterType, onlyDebt]);

    function toggleExpand(monkId) {
        setExpanded((prev) => (prev.includes(monkId) ? prev.filter((id) => id !== monkId) : [...prev, monkId]));
    }

    function markAllPaid(monk) {
        if (! confirm(`ຢືນຢັນໝາຍຈ່າຍທັງໝົດ ${monk.full_name}?`)) return;
        router.patch(route('balance.mark-all-paid', monk.id), {}, { preserveScroll: true });
    }

    function markPaid(absenceId) {
        router.patch(route('absences.mark-paid', absenceId), {}, { preserveScroll: true });
    }

    return (
        <AppLayout title="ຍອດຄ້າງຊຳລະ">
            <div className="mb-5">
                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ການເງິນ</p>
                <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ຍອດຄ້າງຊຳລະ</h1>
                <p className="text-gray-400 text-sm mt-1">ລາຍຊື່ພຣະສົງ ແລະ ສາມະເນນ ທີ່ຍັງຄ້າງຄ່າປັບ</p>
            </div>

            <div className="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-5">
                <div className="col-span-2 sm:col-span-1 bg-brand-green text-white rounded-3xl card-shadow p-4">
                    <p className="text-[10px] font-medium text-white/70 uppercase tracking-widest mb-1">ຍອດລວມຄ້າງ</p>
                    <p className="text-xs text-white/70 mb-2">ຄ່າປັບທີ່ຍັງບໍ່ໄດ້ຊຳລະ</p>
                    <div className="flex items-baseline gap-1.5 flex-wrap">
                        <span className="inline-flex items-center justify-center w-4 h-4 rounded-full border border-white/50 text-[8px] font-bold shrink-0 leading-none select-none">₭</span>
                        <span className="text-2xl sm:text-3xl font-bold tabular-nums leading-none">{fmt(totalUnpaid)}</span>
                        <span className="text-xs text-white/70">ກີບ</span>
                    </div>
                </div>

                <div className="bg-white rounded-3xl card-shadow border border-gray-50 p-4">
                    <p className="text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-1">ຈຳນວນ</p>
                    <p className="text-xs text-gray-400 mb-2">ພຣະ/ສາມະເນນ</p>
                    <div className="flex items-baseline gap-1">
                        <span className="text-2xl font-bold text-slate-800 tabular-nums leading-none">{monksWithDebt}</span>
                        <span className="text-xs text-gray-400 ml-1">ອົງ</span>
                    </div>
                </div>

                <div className="bg-white rounded-3xl card-shadow border border-gray-50 p-4">
                    <p className="text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-1">ຄ້າງ</p>
                    <p className="text-xs text-gray-400 mb-2">ຈຳນວນຄັ້ງທີ່ຂາດ</p>
                    <div className="flex items-baseline gap-1">
                        <span className="text-2xl font-bold text-slate-800 tabular-nums leading-none">{unpaidCount}</span>
                        <span className="text-xs text-gray-400 ml-1">ຄັ້ງ</span>
                    </div>
                </div>
            </div>

            <div className="bg-white card-shadow rounded-2xl p-4 mb-5">
                <div className="flex flex-col sm:flex-row gap-3">
                    <div className="relative flex-1">
                        <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4 text-gray-400">
                                <circle cx="8.5" cy="8.5" r="5.5" />
                                <path strokeLinecap="round" d="M14 14l3 3" />
                            </svg>
                        </div>
                        <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="ຄົ້ນຫາຊື່ພຣະ..."
                            className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                    </div>

                    <select value={filterType} onChange={(e) => setFilterType(e.target.value)}
                        className="bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow sm:w-40">
                        <option value="">ທຸກປະເພດ</option>
                        <option value="monk">ພຣະສົງ</option>
                        <option value="novice">ສາມະເນນ</option>
                        <option value="nun">ແມ່ຂາວ</option>
                    </select>

                    <label className="flex items-center gap-2.5 cursor-pointer py-1 sm:py-0">
                        <input type="checkbox" checked={onlyDebt} onChange={(e) => setOnlyDebt(e.target.checked)} className="sr-only peer" />
                        <div className="relative w-9 h-5 rounded-full transition-colors duration-200 bg-gray-200 peer-checked:bg-brand-green
                                        after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:w-4 after:h-4
                                        after:transition-transform after:duration-200 peer-checked:after:translate-x-4 shrink-0"></div>
                        <span className="text-sm text-slate-600 whitespace-nowrap">ສະເພາະທີ່ຄ້າງ</span>
                    </label>
                </div>
            </div>

            <div className="space-y-3">
                {monks.length === 0 ? (
                    <div className="bg-white rounded-3xl card-shadow flex flex-col items-center justify-center py-20 text-center">
                        {onlyDebt ? (
                            <>
                                <div className="w-16 h-16 rounded-full bg-brand-light-green flex items-center justify-center mb-4">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#8f4e00" strokeWidth="1.5" className="w-8 h-8">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p className="font-bold text-slate-800 mb-1">ບໍ່ມີຍອດຄ້າງ</p>
                                <p className="text-gray-400 text-sm">ພຣະສົງທຸກອົງຊຳລະຄ່າປັບຄົບຖ້ວນແລ້ວ</p>
                            </>
                        ) : (
                            <>
                                <div className="w-16 h-16 rounded-full bg-brand-light-green flex items-center justify-center mb-4">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="#8f4e00" strokeWidth="1.5" className="w-8 h-8">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <p className="font-bold text-slate-800 mb-1">ບໍ່ພົບຂໍ້ມູນ</p>
                                <p className="text-gray-400 text-sm">ລອງປ່ຽນເງື່ອນໄຂການຄົ້ນຫາ</p>
                            </>
                        )}
                    </div>
                ) : (
                    monks.map((monk) => {
                        const isExpanded = expanded.includes(monk.id);
                        return (
                            <div key={monk.id} className="bg-white rounded-2xl card-shadow overflow-hidden">
                                <div className="px-4 pt-4 pb-4 flex items-start gap-4">
                                    <img src={monk.photo_url} alt={monk.full_name}
                                        className="w-12 h-12 rounded-full object-cover shrink-0 mt-0.5 border-2 border-brand-green/20" />

                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-start gap-2 flex-wrap">
                                            <span className="font-semibold text-slate-800 text-base leading-tight">{monk.full_name}</span>
                                            <span className={`shrink-0 mt-0.5 px-2.5 py-1 rounded-full text-xs font-bold ${typeClasses[monk.type] || 'bg-gray-50 text-gray-600'}`}>
                                                {monk.type_label}
                                            </span>
                                        </div>
                                        <p className="text-xs text-gray-400 mt-0.5">
                                            {monk.temple ? `${monk.temple} · ` : ''}{monk.pansa} ພັນສາ
                                        </p>

                                        <div className="mt-3 pt-3 flex items-end justify-between gap-3 border-t border-gray-100">
                                            <div className="min-w-0">
                                                <p className="text-[10px] font-medium text-red-400 uppercase tracking-widest mb-0.5 select-none">ຍອດຄ້າງຊຳລະ</p>
                                                <div className="flex items-baseline gap-1.5">
                                                    <span className="text-[11px] font-semibold text-red-400 select-none">₭</span>
                                                    <span className="text-[2rem] sm:text-[2.5rem] font-bold text-red-500 tabular-nums leading-none">
                                                        {monk.unpaid_fine ? fmt(monk.unpaid_fine) : '0'}
                                                    </span>
                                                    <span className="text-xs text-gray-400">ກີບ</span>
                                                </div>
                                                <p className="text-[11px] text-gray-400 mt-1">
                                                    ຄ້າງ <span className="font-medium text-slate-600">{monk.unpaid_count}</span> ຄັ້ງ
                                                    {monk.total_count > monk.unpaid_count && (
                                                        <>&nbsp;·&nbsp; ຊຳລະແລ້ວ {monk.total_count - monk.unpaid_count} ຄັ້ງ</>
                                                    )}
                                                </p>
                                            </div>

                                            <div className="flex flex-col items-end gap-2 shrink-0">
                                                {monk.unpaid_count > 0 ? (
                                                    <button onClick={() => markAllPaid(monk)}
                                                        className="flex items-center gap-1.5 px-3 py-1.5 bg-brand-green text-white rounded-lg text-xs font-semibold shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition touch-manipulation whitespace-nowrap">
                                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-3.5 h-3.5 shrink-0">
                                                            <path strokeLinecap="round" strokeLinejoin="round" d="M4 10.5l5 5 7-7" />
                                                        </svg>
                                                        ຈ່າຍໝົດ
                                                    </button>
                                                ) : (
                                                    <span className="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-light-green text-brand-green">
                                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-3.5 h-3.5 shrink-0">
                                                            <path strokeLinecap="round" strokeLinejoin="round" d="M4 10.5l5 5 7-7" />
                                                        </svg>
                                                        ຊຳລະແລ້ວ
                                                    </span>
                                                )}

                                                <button onClick={() => toggleExpand(monk.id)}
                                                    className="flex items-center gap-1 text-xs text-gray-400 hover:text-slate-600 transition-colors touch-manipulation">
                                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75"
                                                        className={`w-3.5 h-3.5 transition-transform duration-200 ${isExpanded ? 'rotate-180' : ''}`}>
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 7.5l5 5 5-5" />
                                                    </svg>
                                                    {isExpanded ? 'ຫຍໍ້' : 'ລາຍລະອຽດ'}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {isExpanded && (
                                    <div className="border-t border-gray-100 bg-gray-50">
                                        {monk.unpaid_absences.length > 0 ? (
                                            monk.unpaid_absences.map((absence, i) => (
                                                <div key={absence.id} className={`flex items-center gap-3 px-4 py-3 touch-manipulation ${i !== monk.unpaid_absences.length - 1 ? 'border-b border-gray-100' : ''}`}>
                                                    <div className="flex-1 min-w-0">
                                                        <div className="flex items-center gap-2 flex-wrap">
                                                            <span className="text-sm font-medium text-slate-800 tabular-nums">{absence.absent_date}</span>
                                                            <span className="text-[11px] text-gray-400 px-1.5 py-0.5 rounded bg-gray-100">{absence.fine_rate_name}</span>
                                                        </div>
                                                        {absence.reason && <p className="text-xs text-gray-400 mt-0.5 truncate">{absence.reason}</p>}
                                                    </div>
                                                    <div className="flex items-center gap-2 shrink-0">
                                                        <div className="text-right">
                                                            <span className="font-bold text-red-500 tabular-nums text-sm">{fmt(absence.fine_amount)}</span>
                                                            <span className="text-[11px] text-gray-400 ml-0.5">ກີບ</span>
                                                        </div>
                                                        <button onClick={() => markPaid(absence.id)}
                                                            className="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] font-medium text-orange-500 border border-gray-200 hover:bg-orange-50 transition-colors touch-manipulation whitespace-nowrap">
                                                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-3 h-3 shrink-0">
                                                                <path strokeLinecap="round" strokeLinejoin="round" d="M4 10.5l5 5 7-7" />
                                                            </svg>
                                                            ໝາຍຈ່າຍ
                                                        </button>
                                                    </div>
                                                </div>
                                            ))
                                        ) : (
                                            <div className="px-4 py-4 text-center">
                                                <p className="text-sm text-brand-green">ຊຳລະທັງໝົດແລ້ວ</p>
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>
                        );
                    })
                )}
            </div>
        </AppLayout>
    );
}
