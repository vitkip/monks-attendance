import { router, useForm } from '@inertiajs/react';
import { useEffect, useMemo, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

function fmt(n) {
    return new Intl.NumberFormat('en-US').format(Math.round(n));
}

function parseYmd(s) {
    const [y, m, d] = s.split('-').map(Number);
    return new Date(y, m - 1, d);
}

function formatDmy(s) {
    const d = parseYmd(s);
    return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
}

function dateStatus(dateKey) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const d = parseYmd(dateKey);
    const diffDays = Math.round((d - today) / 86400000);
    return {
        isToday: diffDays === 0,
        isTomorrow: diffDays === 1,
        isFuture: diffDays > 0,
        isPast: diffDays < 0,
        daysOverdue: diffDays < 0 ? Math.abs(diffDays) : null,
    };
}

const typeClasses = {
    monk: 'bg-orange-50 text-orange-600',
    novice: 'bg-teal-50 text-teal-600',
    nun: 'bg-purple-50 text-purple-600',
};

const emptyForm = {
    monk_id: '',
    monk_ids: [],
    fine_rate_id: '',
    absent_date: new Date().toISOString().slice(0, 10),
    reason: '',
    fine_amount: 0,
    is_paid: false,
    note: '',
};

export default function AbsencesIndex({
    filters, absenceGroups, monks, fineRates, onceMonkGroups, weeklyMonkGroups, monksWithoutDuty, totalFine, unpaidFine,
}) {
    const [search, setSearch] = useState(filters.search);
    const [filterMonth, setFilterMonth] = useState(filters.month);
    const [filterPaid, setFilterPaid] = useState(filters.paid);
    const debounceRef = useRef(null);

    useEffect(() => {
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('absences.index'), { search, month: filterMonth, paid: filterPaid }, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            });
        }, 300);
        return () => clearTimeout(debounceRef.current);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, filterMonth, filterPaid]);

    const [showModal, setShowModal] = useState(false);
    const [editId, setEditId] = useState(null);
    const [monkSearch, setMonkSearch] = useState('');
    const form = useForm(emptyForm);

    function openCreate() {
        setEditId(null);
        form.reset();
        form.setData({ ...emptyForm, absent_date: new Date().toISOString().slice(0, 10) });
        form.clearErrors();
        setMonkSearch('');
        setShowModal(true);
    }

    function openEdit(item, dateKey) {
        setEditId(item.id);
        form.clearErrors();
        form.setData({
            monk_id: item.monk.id,
            monk_ids: [],
            fine_rate_id: item.fine_rate.id,
            absent_date: dateKey,
            reason: item.reason || '',
            fine_amount: item.fine_amount,
            is_paid: item.is_paid,
            note: item.note || '',
        });
        setShowModal(true);
    }

    function closeModal() {
        setShowModal(false);
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => setShowModal(false);
        if (editId) {
            form.put(route('absences.update', editId), { onSuccess, preserveScroll: true });
        } else {
            form.post(route('absences.store'), { onSuccess, preserveScroll: true });
        }
    }

    function markPaid(id) {
        router.patch(route('absences.mark-paid', id), {}, { preserveScroll: true });
    }

    function destroy(id) {
        if (! confirm('ຢືນຢັນການລຶບຂໍ້ມູນນີ້?')) return;
        router.delete(route('absences.destroy', id), { preserveScroll: true });
    }

    function onFineRateChange(value) {
        form.setData('fine_rate_id', value);
        const rate = fineRates.find((r) => String(r.id) === String(value));
        if (rate) form.setData('fine_amount', rate.amount);
    }

    function toggleMonkId(id) {
        const ids = form.data.monk_ids.includes(id)
            ? form.data.monk_ids.filter((x) => x !== id)
            : [...form.data.monk_ids, id];
        form.setData('monk_ids', ids);
    }

    const monkSearchLower = monkSearch.trim().toLowerCase();
    const matchesSearch = (name) => !monkSearchLower || name.toLowerCase().includes(monkSearchLower);

    const hasFilters = search !== '' || filterMonth !== '' || filterPaid !== '';
    const anyMonkVisible = useMemo(() => {
        if (!monkSearchLower) return true;
        const names = [
            ...onceMonkGroups.flatMap((g) => g.duties.map((d) => d.monk_name)),
            ...weeklyMonkGroups.flatMap((g) => g.duties.map((d) => d.monk_name)),
            ...monksWithoutDuty.map((m) => m.full_name),
        ];
        return names.some((n) => matchesSearch(n));
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [monkSearchLower, onceMonkGroups, weeklyMonkGroups, monksWithoutDuty]);

    return (
        <AppLayout title="ກວດສອບການຂາດລາ">
            <div className="flex items-start justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ບັນທຶກ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ກວດສອບການຂາດລາ</h1>
                    <p className="text-gray-400 text-sm mt-1">ບັນທຶກ ແລະ ຄຳນວນຄ່າປັບໃໝ</p>
                </div>
                <button onClick={openCreate}
                    className="shrink-0 flex items-center gap-2 px-4 py-2.5 sm:px-5 sm:py-3 bg-brand-green text-white rounded-2xl text-sm font-semibold hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 touch-manipulation">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-4 h-4 shrink-0">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M10 4v12M4 10h12" />
                    </svg>
                    <span className="hidden sm:inline">ບັນທຶກການຂາດ</span>
                    <span className="sm:hidden">ບັນທຶກ</span>
                </button>
            </div>

            <div className="grid grid-cols-2 gap-4 mb-6">
                <div className="bg-brand-green text-white rounded-3xl card-shadow p-5">
                    <p className="text-[10px] font-medium text-white/70 uppercase tracking-widest mb-1">ທັງໝົດ</p>
                    <p className="text-xs text-white/70 mb-2">ຄ່າປັບທັງໝົດ</p>
                    <div className="flex items-baseline gap-1.5 flex-wrap">
                        <span className="inline-flex items-center justify-center w-4 h-4 rounded-full border border-white/50 text-[8px] font-bold shrink-0 leading-none select-none">₭</span>
                        <span className="text-xl sm:text-2xl font-bold tabular-nums leading-none">{fmt(totalFine)}</span>
                        <span className="text-xs text-white/70">ກີບ</span>
                    </div>
                </div>
                <div className="bg-white rounded-3xl card-shadow border border-gray-50 p-5">
                    <p className="text-[10px] font-medium text-red-400 uppercase tracking-widest mb-1">ລໍຖ້າ</p>
                    <p className="text-xs text-gray-400 mb-2">ຍັງບໍ່ໄດ້ຈ່າຍ</p>
                    <div className="flex items-baseline gap-1.5 flex-wrap">
                        <span className="inline-flex items-center justify-center w-4 h-4 rounded-full border border-red-300 text-red-500 text-[8px] font-bold shrink-0 leading-none select-none">₭</span>
                        <span className="text-xl sm:text-2xl font-bold text-red-500 tabular-nums leading-none">{fmt(unpaidFine)}</span>
                        <span className="text-xs text-gray-400">ກີບ</span>
                    </div>
                </div>
            </div>

            <div className="bg-white card-shadow rounded-2xl p-4 mb-6">
                <div className="flex flex-col sm:flex-row gap-3">
                    <div className="relative sm:flex-1 sm:min-w-52">
                        <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4 text-gray-400">
                                <circle cx="8.5" cy="8.5" r="5.5" />
                                <path strokeLinecap="round" d="M14 14l3 3" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="ຄົ້ນຫາຊື່ພຣະ..."
                            className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow"
                        />
                    </div>
                    <div className="grid grid-cols-2 gap-3 sm:contents">
                        <input
                            type="month"
                            value={filterMonth}
                            onChange={(e) => setFilterMonth(e.target.value)}
                            className="bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow"
                        />
                        <select
                            value={filterPaid}
                            onChange={(e) => setFilterPaid(e.target.value)}
                            className="bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow"
                        >
                            <option value="">ທຸກສະຖານະ</option>
                            <option value="0">ຍັງບໍ່ຈ່າຍ</option>
                            <option value="1">ຈ່າຍແລ້ວ</option>
                        </select>
                    </div>
                </div>
            </div>

            {absenceGroups.length === 0 ? (
                <div className="bg-white rounded-3xl card-shadow flex flex-col items-center justify-center py-16 text-center">
                    <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mb-4">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#8f4e00" strokeWidth="1.5" className="w-7 h-7">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    {hasFilters ? (
                        <>
                            <p className="font-bold text-slate-800 mb-1">ບໍ່ພົບຂໍ້ມູນທີ່ຄົ້ນຫາ</p>
                            <p className="text-gray-400 text-sm mb-4">ລອງປ່ຽນຄຳຄົ້ນຫາ ຫຼື ຕົວກອງອື່ນ</p>
                            <button
                                onClick={() => { setSearch(''); setFilterMonth(''); setFilterPaid(''); }}
                                className="text-sm font-semibold text-brand-green hover:underline touch-manipulation"
                            >
                                ລ້າງຕົວກອງທັງໝົດ
                            </button>
                        </>
                    ) : (
                        <>
                            <p className="font-bold text-slate-800 mb-1">ຍັງບໍ່ມີຂໍ້ມູນການຂາດ</p>
                            <p className="text-gray-400 text-sm">ກົດ "ບັນທຶກ" ເພື່ອເພີ່ມຂໍ້ມູນ</p>
                        </>
                    )}
                </div>
            ) : (
                <div className="space-y-6">
                    {absenceGroups.map((group) => {
                        const { isToday, isTomorrow, isFuture, isPast, daysOverdue } = dateStatus(group.date);
                        return (
                            <div key={group.date}>
                                <div className="flex items-center gap-2.5 mb-3">
                                    {isToday ? (
                                        <div className="flex items-center gap-2 px-3 py-1.5 bg-red-500 rounded-full shrink-0">
                                            <div className="w-1.5 h-1.5 rounded-full bg-white"></div>
                                            <span className="text-xs font-bold text-white">{formatDmy(group.date)}</span>
                                            <span className="text-[10px] font-bold text-white/70 ml-0.5">ວັນນີ້</span>
                                        </div>
                                    ) : isTomorrow ? (
                                        <div className="flex items-center gap-2 px-3 py-1.5 bg-brand-green-dark rounded-full shrink-0">
                                            <div className="w-1.5 h-1.5 rounded-full bg-brand-bright-green"></div>
                                            <span className="text-xs font-bold text-white">{formatDmy(group.date)}</span>
                                            <span className="text-[10px] font-medium text-white/70 ml-0.5">ມື້ອື່ນ</span>
                                        </div>
                                    ) : isFuture ? (
                                        <div className="flex items-center gap-2 px-3 py-1.5 bg-brand-green-dark rounded-full shrink-0">
                                            <div className="w-1.5 h-1.5 rounded-full bg-brand-bright-green/60"></div>
                                            <span className="text-xs font-bold text-white">{formatDmy(group.date)}</span>
                                            <span className="text-[10px] font-medium text-white/60 ml-0.5">ກຳລັງຈະມາ</span>
                                        </div>
                                    ) : (
                                        <div className="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-full shrink-0">
                                            <div className="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                                            <span className="text-xs font-bold text-gray-500">{formatDmy(group.date)}</span>
                                        </div>
                                    )}
                                    <div className="flex-1 h-px bg-gray-200"></div>
                                    <span className="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full whitespace-nowrap">
                                        {group.items.length} ອົງ · {fmt(group.total)} ກີບ
                                    </span>
                                </div>

                                {/* Mobile cards */}
                                <div className="md:hidden space-y-3">
                                    {group.items.map((absence) => (
                                        <div key={absence.id} className="bg-white rounded-2xl card-shadow overflow-hidden">
                                            <div className="px-4 pt-4 pb-3 flex items-start justify-between gap-3">
                                                <div className="flex-1 min-w-0">
                                                    <p className="font-bold text-slate-800 text-base truncate">{absence.monk.full_name}</p>
                                                    <span className={`inline-block mt-1 px-2.5 py-1 rounded-full text-xs font-bold ${typeClasses[absence.monk.type] || 'bg-gray-50 text-gray-600'}`}>
                                                        {absence.monk.type_label}
                                                    </span>
                                                </div>
                                                <div className="text-right shrink-0">
                                                    <p className="text-[11px] text-gray-400">{absence.fine_rate.name}</p>
                                                </div>
                                            </div>
                                            <div className="px-4 pb-3 pt-3 flex items-center justify-between gap-3 border-t border-gray-100">
                                                <div className="flex-1 min-w-0">
                                                    <p className="text-sm text-slate-600 truncate">{absence.reason || 'ບໍ່ລະບຸເຫດຜົນ'}</p>
                                                    {!absence.is_paid && isPast && (
                                                        <p className="text-[10px] font-bold text-red-500 mt-1">ຄ້າງຈ່າຍ {daysOverdue} ວັນ</p>
                                                    )}
                                                </div>
                                                <div className="flex items-center gap-1.5 shrink-0">
                                                    <span className="inline-flex items-center justify-center w-4 h-4 rounded-full border border-red-300 text-red-500 text-[8px] font-bold leading-none select-none">₭</span>
                                                    <span className="font-bold text-red-500 tabular-nums text-sm">{fmt(absence.fine_amount)}</span>
                                                    <span className="text-xs text-gray-400">ກີບ</span>
                                                </div>
                                            </div>
                                            <div className="flex border-t border-gray-100" style={{ minHeight: 48 }}>
                                                {absence.is_paid ? (
                                                    <div className="flex-1 flex items-center justify-center gap-1.5 text-sm text-brand-green">
                                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-4 h-4 shrink-0">
                                                            <path strokeLinecap="round" strokeLinejoin="round" d="M4 10.5l5 5 7-7" />
                                                        </svg>
                                                        ຈ່າຍແລ້ວ
                                                    </div>
                                                ) : (
                                                    <button onClick={() => markPaid(absence.id)}
                                                        className="flex-1 flex items-center justify-center gap-1.5 text-sm text-orange-500 hover:bg-orange-50 transition-colors touch-manipulation">
                                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4 shrink-0">
                                                            <circle cx="10" cy="10" r="7.5" />
                                                            <path strokeLinecap="round" d="M10 6.5V10l2.5 2" />
                                                        </svg>
                                                        ໝາຍຈ່າຍ
                                                    </button>
                                                )}
                                                <div className="w-px self-stretch bg-gray-100"></div>
                                                <button onClick={() => openEdit(absence, group.date)} aria-label="ແກ້ໄຂ"
                                                    className="w-12 flex items-center justify-center text-slate-500 hover:bg-gray-50 transition-colors touch-manipulation">
                                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z" />
                                                    </svg>
                                                </button>
                                                <div className="w-px self-stretch bg-gray-100"></div>
                                                <button onClick={() => destroy(absence.id)} aria-label="ລຶບ"
                                                    className="w-12 flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors touch-manipulation">
                                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                {/* Desktop table */}
                                <div className="hidden md:block bg-white rounded-2xl card-shadow overflow-hidden">
                                    <table className="w-full text-sm">
                                        <thead>
                                            <tr className="bg-gray-50 border-b border-gray-100">
                                                <th className="px-4 py-3.5 text-left w-12 text-[11px] font-medium text-gray-400 uppercase tracking-wide">#</th>
                                                <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຊື່ພຣະສົງ / ສາມະເນນ</th>
                                                <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ເຫດຜົນ</th>
                                                <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ປະເພດ</th>
                                                <th className="px-4 py-3.5 text-right text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຄ່າປັບ</th>
                                                <th className="px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ສະຖານະ</th>
                                                <th className="px-4 py-3.5 text-center w-20 text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈັດການ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {group.items.map((absence) => (
                                                <tr key={absence.id} className="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                                    <td className="px-4 py-3.5 text-xs text-gray-300 tabular-nums">{absence.id}</td>
                                                    <td className="px-4 py-3.5">
                                                        <p className="font-bold text-slate-800">{absence.monk.full_name}</p>
                                                        <span className={`inline-block mt-1 px-2.5 py-1 rounded-full text-xs font-bold ${typeClasses[absence.monk.type] || 'bg-gray-50 text-gray-600'}`}>
                                                            {absence.monk.type_label}
                                                        </span>
                                                    </td>
                                                    <td className="px-4 py-3.5 text-gray-500">{absence.reason || '—'}</td>
                                                    <td className="px-4 py-3.5 text-gray-500">{absence.fine_rate.name}</td>
                                                    <td className="px-4 py-3.5 text-right whitespace-nowrap">
                                                        <span className="font-bold text-red-500 tabular-nums">{fmt(absence.fine_amount)}</span>
                                                        <span className="text-xs text-gray-400 ml-0.5">ກີບ</span>
                                                        {!absence.is_paid && isPast && (
                                                            <p className="text-[10px] font-bold text-red-500 mt-0.5">ຄ້າງ {daysOverdue} ວັນ</p>
                                                        )}
                                                    </td>
                                                    <td className="px-4 py-3.5 text-center">
                                                        {absence.is_paid ? (
                                                            <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-brand-light-green text-brand-green">
                                                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-3 h-3 shrink-0">
                                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M4 10.5l5 5 7-7" />
                                                                </svg>
                                                                ຈ່າຍແລ້ວ
                                                            </span>
                                                        ) : (
                                                            <button onClick={() => markPaid(absence.id)} title="ກົດເພື່ອໝາຍວ່າຈ່າຍແລ້ວ"
                                                                className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-500 hover:bg-orange-100 transition-colors touch-manipulation">
                                                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-3 h-3 shrink-0">
                                                                    <circle cx="10" cy="10" r="7.5" />
                                                                    <path strokeLinecap="round" d="M10 6.5V10l2.5 2" />
                                                                </svg>
                                                                ໝາຍຈ່າຍ
                                                            </button>
                                                        )}
                                                    </td>
                                                    <td className="px-4 py-3.5">
                                                        <div className="flex items-center justify-center gap-1">
                                                            <button onClick={() => openEdit(absence, group.date)} title="ແກ້ໄຂ" aria-label="ແກ້ໄຂ"
                                                                className="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-gray-100 transition-colors touch-manipulation">
                                                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4">
                                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z" />
                                                                </svg>
                                                            </button>
                                                            <button onClick={() => destroy(absence.id)} title="ລຶບ" aria-label="ລຶບ"
                                                                className="w-8 h-8 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 transition-colors touch-manipulation">
                                                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4">
                                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}

            {showModal && (
                <div onClick={(e) => e.target === e.currentTarget && closeModal()}
                    className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden max-h-[95dvh] sm:max-h-[90vh] flex flex-col">
                        <div className="px-6 py-4 flex items-center justify-between shrink-0 border-b border-gray-100">
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    {editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ບັນທຶກໃໝ່'}
                                </p>
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">ການຂາດລາ</h2>
                            </div>
                            <button onClick={closeModal}
                                className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-slate-700 hover:bg-gray-100 transition-colors touch-manipulation">
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-4 h-4">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <div className="sm:hidden flex justify-center pt-3 shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>

                        <form onSubmit={submit} className="px-6 py-5 space-y-5 overflow-y-auto">
                            <div>
                                {editId ? (
                                    <>
                                        <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                                            ພຣະສົງ / ສາມະເນນ <span className="text-red-500">*</span>
                                        </label>
                                        <select
                                            value={form.data.monk_id}
                                            onChange={(e) => form.setData('monk_id', e.target.value)}
                                            className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.monk_id ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`}
                                        >
                                            <option value="">— ເລືອກພຣະ —</option>
                                            {monks.map((m) => (
                                                <option key={m.id} value={m.id}>{m.full_name} ({m.type_label})</option>
                                            ))}
                                        </select>
                                        {form.errors.monk_id && <p className="text-red-500 text-xs mt-1.5">{form.errors.monk_id}</p>}
                                    </>
                                ) : (
                                    <>
                                        <div className="flex items-center justify-between mb-2">
                                            <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest">
                                                ພຣະສົງ / ສາມະເນນ <span className="text-red-500">*</span>{' '}
                                                <span className="normal-case font-normal text-gray-400">(ເລືອກໄດ້ຫຼາຍອົງ)</span>
                                            </label>
                                            <div className="flex items-center gap-2 text-[11px]">
                                                <button type="button" onClick={() => form.setData('monk_ids', monks.map((m) => m.id))}
                                                    className="text-brand-green hover:underline touch-manipulation">ເລືອກທັງໝົດ</button>
                                                <span className="text-gray-300">|</span>
                                                <button type="button" onClick={() => form.setData('monk_ids', [])}
                                                    className="text-red-500 hover:underline touch-manipulation">ລ້າງ</button>
                                            </div>
                                        </div>
                                        <div>
                                            <div className="relative mb-2">
                                                <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4 text-gray-400">
                                                        <circle cx="8.5" cy="8.5" r="5.5" />
                                                        <path strokeLinecap="round" d="M14 14l3 3" />
                                                    </svg>
                                                </div>
                                                <input type="text" value={monkSearch} onChange={(e) => setMonkSearch(e.target.value)}
                                                    placeholder="ຄົ້ນຫາໃນລາຍຊື່ນີ້..."
                                                    className="w-full bg-white border border-gray-200 rounded-xl pl-9 pr-3 py-2 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                                            </div>
                                            <div className={`max-h-64 overflow-y-auto bg-[#f8fafa] border rounded-xl divide-y divide-gray-100 ${form.errors.monk_ids ? 'border-red-300' : 'border-gray-200'}`}>
                                                {onceMonkGroups.map((g) => {
                                                    const visible = g.duties.filter((d) => matchesSearch(d.monk_name));
                                                    if (monkSearchLower && visible.length === 0) return null;
                                                    return (
                                                        <div key={`once-${g.date}`}>
                                                            <div className="px-3 py-1.5 bg-orange-50 text-[10px] font-bold text-orange-600 uppercase tracking-wide sticky top-0">
                                                                {formatDmy(g.date)} · ສະເພາະວັນ
                                                            </div>
                                                            {(monkSearchLower ? visible : g.duties).map((d) => (
                                                                <label key={`monk-once-${d.id}`} className="flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-gray-100 transition-colors touch-manipulation">
                                                                    <input type="checkbox" checked={form.data.monk_ids.includes(d.monk_id)} onChange={() => toggleMonkId(d.monk_id)}
                                                                        className="w-4 h-4 rounded border-gray-300 text-brand-green focus:ring-brand-green/30 shrink-0" />
                                                                    <span className="text-sm text-slate-800">{d.monk_name}</span>
                                                                    <span className="text-[10px] text-gray-400 ml-auto shrink-0 truncate max-w-[35%]">{d.duty_name}</span>
                                                                </label>
                                                            ))}
                                                        </div>
                                                    );
                                                })}

                                                {weeklyMonkGroups.map((g) => {
                                                    const visible = g.duties.filter((d) => matchesSearch(d.monk_name));
                                                    if (monkSearchLower && visible.length === 0) return null;
                                                    return (
                                                        <div key={`weekly-${g.day}`}>
                                                            <div className="px-3 py-1.5 bg-brand-light-green text-[10px] font-bold text-brand-green uppercase tracking-wide sticky top-0">
                                                                {g.day_name} · ໝຸນວຽນ
                                                            </div>
                                                            {(monkSearchLower ? visible : g.duties).map((d) => (
                                                                <label key={`monk-weekly-${d.id}`} className="flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-gray-100 transition-colors touch-manipulation">
                                                                    <input type="checkbox" checked={form.data.monk_ids.includes(d.monk_id)} onChange={() => toggleMonkId(d.monk_id)}
                                                                        className="w-4 h-4 rounded border-gray-300 text-brand-green focus:ring-brand-green/30 shrink-0" />
                                                                    <span className="text-sm text-slate-800">{d.monk_name}</span>
                                                                    <span className="text-[10px] text-gray-400 ml-auto shrink-0 truncate max-w-[35%]">{d.duty_name}</span>
                                                                </label>
                                                            ))}
                                                        </div>
                                                    );
                                                })}

                                                {monksWithoutDuty.length > 0 && (() => {
                                                    const visible = monksWithoutDuty.filter((m) => matchesSearch(m.full_name));
                                                    if (monkSearchLower && visible.length === 0) return null;
                                                    return (
                                                        <div>
                                                            <div className="px-3 py-1.5 bg-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wide sticky top-0">
                                                                ບໍ່ມີໜ້າທີ່ຮັບຜິດຊອບ
                                                            </div>
                                                            {(monkSearchLower ? visible : monksWithoutDuty).map((m) => (
                                                                <label key={`monk-none-${m.id}`} className="flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-gray-100 transition-colors touch-manipulation">
                                                                    <input type="checkbox" checked={form.data.monk_ids.includes(m.id)} onChange={() => toggleMonkId(m.id)}
                                                                        className="w-4 h-4 rounded border-gray-300 text-brand-green focus:ring-brand-green/30 shrink-0" />
                                                                    <span className="text-sm text-slate-800">{m.full_name}</span>
                                                                    <span className="text-xs text-gray-400 ml-auto shrink-0">{m.type_label}</span>
                                                                </label>
                                                            ))}
                                                        </div>
                                                    );
                                                })()}

                                                {onceMonkGroups.length === 0 && weeklyMonkGroups.length === 0 && monksWithoutDuty.length === 0 && (
                                                    <p className="px-3 py-3 text-sm text-gray-400">ບໍ່ມີພຣະສົງ/ສາມະເນນ</p>
                                                )}

                                                {monkSearchLower && !anyMonkVisible && (
                                                    <p className="px-3 py-3 text-sm text-gray-400 text-center">ບໍ່ພົບພຣະທີ່ຄົ້ນຫາ</p>
                                                )}
                                            </div>
                                        </div>
                                        {form.errors.monk_ids && <p className="text-red-500 text-xs mt-1.5">{form.errors.monk_ids}</p>}
                                        {form.data.monk_ids.length > 0 && (
                                            <p className="text-xs text-gray-400 mt-1.5">ເລືອກແລ້ວ {form.data.monk_ids.length} ອົງ</p>
                                        )}
                                    </>
                                )}
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                                        ວັນທີຂາດ <span className="text-red-500">*</span>
                                    </label>
                                    <input type="date" value={form.data.absent_date} onChange={(e) => form.setData('absent_date', e.target.value)}
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.absent_date ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.absent_date && <p className="text-red-500 text-xs mt-1.5">{form.errors.absent_date}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                                        ປະເພດຄ່າປັບ <span className="text-red-500">*</span>
                                    </label>
                                    <select value={form.data.fine_rate_id} onChange={(e) => onFineRateChange(e.target.value)}
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.fine_rate_id ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`}>
                                        <option value="">— ເລືອກ —</option>
                                        {fineRates.map((r) => (
                                            <option key={r.id} value={r.id}>{r.name} ({fmt(r.amount)} ₭)</option>
                                        ))}
                                    </select>
                                    {form.errors.fine_rate_id && <p className="text-red-500 text-xs mt-1.5">{form.errors.fine_rate_id}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">
                                        ຈຳນວນເງິນ <span className="text-[10px] normal-case font-normal text-gray-400">(ກີບ)</span>
                                    </label>
                                    <div className="relative">
                                        <div className="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                            <span className="inline-flex items-center justify-center w-4 h-4 rounded-full border border-gray-300 text-gray-400 text-[8px] font-bold leading-none select-none">₭</span>
                                        </div>
                                        <input type="number" min="0" step="1000" value={form.data.fine_amount}
                                            onChange={(e) => form.setData('fine_amount', e.target.value)}
                                            className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-9 pr-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent tabular-nums transition-shadow" />
                                    </div>
                                </div>
                                <div className="flex sm:items-end sm:pb-0.5">
                                    <label className="flex items-center gap-3 cursor-pointer w-full py-2 sm:py-0">
                                        <input type="checkbox" checked={form.data.is_paid} onChange={(e) => form.setData('is_paid', e.target.checked)} className="sr-only peer" />
                                        <div className="relative w-10 h-6 rounded-full transition-colors duration-200 bg-gray-200 peer-checked:bg-brand-green
                                                        after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:w-4 after:h-4
                                                        after:transition-transform after:duration-200 peer-checked:after:translate-x-4 shrink-0"></div>
                                        <span className="text-sm text-slate-600">ຈ່າຍແລ້ວ</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ເຫດຜົນ</label>
                                <input type="text" value={form.data.reason} onChange={(e) => form.setData('reason', e.target.value)} placeholder="ເຫດຜົນທີ່ຂາດ..."
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ໝາຍເຫດ</label>
                                <textarea rows="2" value={form.data.note} onChange={(e) => form.setData('note', e.target.value)} placeholder="ໝາຍເຫດ..."
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent resize-none transition-shadow"></textarea>
                            </div>

                            <div className="flex justify-end gap-3 pb-1 pt-1">
                                <button type="button" onClick={closeModal}
                                    className="px-5 py-2.5 border border-gray-200 rounded-xl text-sm text-slate-600 hover:bg-gray-50 transition-colors touch-manipulation">
                                    ຍົກເລີກ
                                </button>
                                <button type="submit" disabled={form.processing}
                                    className="flex items-center gap-2 px-5 py-2.5 bg-brand-green text-white rounded-xl text-sm font-semibold hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 touch-manipulation disabled:opacity-70">
                                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4 shrink-0">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M17 3H7L3 7v10a1 1 0 001 1h13a1 1 0 001-1V4a1 1 0 00-1-1zm-3 14v-6H6v6" />
                                    </svg>
                                    ບັນທຶກ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
