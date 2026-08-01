import { router, useForm } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

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
    return { isToday: diffDays === 0, isTomorrow: diffDays === 1, isFuture: diffDays > 0, isPast: diffDays < 0 };
}

const dayShort = { 1: 'ຈ', 2: 'ອ', 3: 'ພ', 4: 'ພຫ', 5: 'ສ', 6: 'ເສ', 7: 'ທ' };

const emptyForm = { monk_id: '', schedule_type: 'once', duty_name: '', duty_date: new Date().toISOString().slice(0, 10), day_of_week: '', description: '' };

function DutyCard({ duty, badgeClass, onEdit, onDelete, dimmed }) {
    return (
        <div className={`bg-white rounded-2xl card-interactive overflow-hidden ${dimmed ? 'opacity-60' : ''}`}>
            <div className="p-4">
                <div className="flex items-start gap-3">
                    <img src={duty.monk.photo_url} alt={duty.monk.full_name} className="w-9 h-9 rounded-full object-cover shrink-0 border border-gray-200" />
                    <div className="flex-1 min-w-0">
                        <p className="font-semibold text-slate-800 text-sm leading-snug">{duty.monk.full_name}</p>
                        <p className="text-[11px] text-gray-400 mt-0.5">{duty.monk.type_label}</p>
                    </div>
                </div>
                <div className="mt-3 flex items-start gap-2 flex-wrap">
                    <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium ${badgeClass}`}>
                        {duty.duty_name}
                    </span>
                    {duty.description && <p className="text-[11px] text-gray-400 leading-tight w-full mt-1">{duty.description}</p>}
                </div>
            </div>
            <div className="flex border-t border-gray-100" style={{ minHeight: 40 }}>
                <button onClick={onEdit} className="flex-1 flex items-center justify-center gap-1.5 text-xs text-slate-500 hover:bg-gray-50 transition-colors touch-manipulation py-2.5">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-3.5 h-3.5 shrink-0">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z" />
                    </svg>
                    ແກ້ໄຂ
                </button>
                <div className="w-px self-stretch bg-gray-100"></div>
                <button onClick={onDelete} className="flex-1 flex items-center justify-center gap-1.5 text-xs text-red-500 hover:bg-red-50 transition-colors touch-manipulation py-2.5">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-3.5 h-3.5 shrink-0">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5" />
                    </svg>
                    ລຶບ
                </button>
            </div>
        </div>
    );
}

export default function DutySchedulesIndex({ filters, weeklyGroups, onceGroups, monks, totalWeekly, totalOnce, dayNames }) {
    const [search, setSearch] = useState(filters.search);
    const [filterType, setFilterType] = useState(filters.type);
    const debounceRef = useRef(null);

    useEffect(() => {
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('duty-schedules.index'), { search, type: filterType }, { preserveState: true, preserveScroll: true, replace: true });
        }, 300);
        return () => clearTimeout(debounceRef.current);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, filterType]);

    const [showModal, setShowModal] = useState(false);
    const [editId, setEditId] = useState(null);
    const form = useForm(emptyForm);

    function openCreate() {
        setEditId(null);
        form.clearErrors();
        form.setData({ ...emptyForm, duty_date: new Date().toISOString().slice(0, 10) });
        setShowModal(true);
    }

    function openEdit(duty) {
        setEditId(duty.id);
        form.clearErrors();
        form.setData({
            monk_id: duty.monk_id,
            schedule_type: duty.schedule_type,
            duty_name: duty.duty_name,
            duty_date: duty.duty_date || new Date().toISOString().slice(0, 10),
            day_of_week: duty.day_of_week || '',
            description: duty.description || '',
        });
        setShowModal(true);
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => setShowModal(false);
        if (editId) {
            form.put(route('duty-schedules.update', editId), { onSuccess, preserveScroll: true });
        } else {
            form.post(route('duty-schedules.store'), { onSuccess, preserveScroll: true });
        }
    }

    function destroy(id) {
        if (!confirm('ຢືນຢັນການລຶບໜ້າທີ່ນີ້?')) return;
        router.delete(route('duty-schedules.destroy', id), { preserveScroll: true });
    }

    const hasAny = weeklyGroups.length > 0 || onceGroups.length > 0;

    return (
        <AppLayout title="ໜ້າທີ່ຮັບຜິດຊອບ">
            <div className="flex items-start justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຕາຕະລາງ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ໜ້າທີ່ຮັບຜິດຊອບ</h1>
                    <p className="text-gray-400 text-sm mt-1">ຕາຕະລາງໜ້າທີ່ — ພຣະທີ່ມີໜ້າທີ່ຈະບໍ່ສາມາດໝາຍຂາດໄດ້</p>
                </div>
                <div className="flex items-center gap-2 shrink-0">
                    <a href={route('duty-schedules.report', filterType ? { type: filterType } : {})} target="_blank" rel="noopener"
                        className="flex items-center gap-2 px-4 py-2.5 bg-white text-slate-600 border border-gray-200 rounded-2xl text-sm font-medium hover:bg-gray-50 transition-colors touch-manipulation">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4 shrink-0">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M5 7V4a1 1 0 011-1h8a1 1 0 011 1v3M5 13H3a1 1 0 01-1-1V9a1 1 0 011-1h14a1 1 0 011 1v3a1 1 0 01-1 1h-2M6 13v4h8v-4H6z" />
                        </svg>
                        <span className="hidden sm:inline">ລາຍງານ PDF</span>
                        <span className="sm:hidden">PDF</span>
                    </a>
                    <button onClick={openCreate}
                        className="flex items-center gap-2 px-4 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 touch-manipulation">
                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-4 h-4 shrink-0">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M10 4v12M4 10h12" />
                        </svg>
                        <span className="hidden sm:inline">ເພີ່ມໜ້າທີ່</span>
                        <span className="sm:hidden">ເພີ່ມ</span>
                    </button>
                </div>
            </div>

            <div className="grid grid-cols-2 gap-4 mb-6">
                <div className="bg-brand-green text-white rounded-3xl card-shadow p-5">
                    <p className="text-[10px] font-medium text-white/70 uppercase tracking-widest mb-1">ໝຸນວຽນ</p>
                    <p className="text-xs text-white/70 mb-2">ໜ້າທີ່ປະຈຳອາທິດ</p>
                    <div className="flex items-baseline gap-1.5">
                        <span className="text-xl sm:text-2xl font-bold tabular-nums leading-none">{totalWeekly}</span>
                        <span className="text-xs text-white/70">ລາຍການ</span>
                    </div>
                </div>
                <div className="bg-white rounded-3xl card-shadow border border-gray-50 p-5">
                    <p className="text-[10px] font-medium text-orange-400 uppercase tracking-widest mb-1">ສະເພາະ</p>
                    <p className="text-xs text-gray-400 mb-2">ໜ້າທີ່ວັນສະເພາະ</p>
                    <div className="flex items-baseline gap-1.5">
                        <span className="text-xl sm:text-2xl font-bold text-slate-800 tabular-nums leading-none">{totalOnce}</span>
                        <span className="text-xs text-gray-400">ລາຍການ</span>
                    </div>
                </div>
            </div>

            <div className="flex items-start gap-3 bg-brand-light-green rounded-2xl px-4 py-3 mb-6">
                <svg viewBox="0 0 20 20" fill="none" stroke="#8f4e00" strokeWidth="1.75" className="w-4 h-4 mt-0.5 shrink-0">
                    <circle cx="10" cy="10" r="8" />
                    <path strokeLinecap="round" d="M10 6v4.5M10 13.5v.5" />
                </svg>
                <p className="text-xs text-slate-600 leading-relaxed">
                    ພຣະທີ່ຖືກມອບໝາຍໜ້າທີ່ (ທັງສະເພາະວັນ ແລະ ໜ້າທີ່ໝຸນວຽນ) <strong className="text-slate-800">ຈະບໍ່ສາມາດໝາຍຂາດໃນວັນນັ້ນ</strong> ໄດ້
                </p>
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
                        <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="ຄົ້ນຫາຊື່ພຣະ ຫຼື ໜ້າທີ່..."
                            className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                    </div>
                    <select value={filterType} onChange={(e) => setFilterType(e.target.value)}
                        className="bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                        <option value="">ທຸກປະເພດ</option>
                        <option value="weekly">ໝຸນວຽນ (ປະຈຳອາທິດ)</option>
                        <option value="once">ສະເພາະວັນ</option>
                    </select>
                </div>
            </div>

            {!hasAny ? (
                <div className="bg-white rounded-3xl card-shadow flex flex-col items-center justify-center py-16 text-center">
                    <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mb-4">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#8f4e00" strokeWidth="1.5" className="w-7 h-7">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <p className="font-bold text-slate-800 mb-1">ຍັງບໍ່ມີໜ້າທີ່</p>
                    <p className="text-gray-400 text-sm">ກົດ "ເພີ່ມໜ້າທີ່" ເພື່ອມອບໝາຍໜ້າທີ່</p>
                </div>
            ) : (
                <>
                    {weeklyGroups.length > 0 && (
                        <div className="mb-8">
                            <div className="flex items-center gap-3 mb-5">
                                <div className="flex items-center gap-2.5 shrink-0">
                                    <div className="w-8 h-8 rounded-lg bg-brand-light-green flex items-center justify-center">
                                        <svg viewBox="0 0 20 20" fill="none" stroke="#8f4e00" strokeWidth="1.75" className="w-4 h-4">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M10 4v3M10 13v3M4 10h3M13 10h3M5.636 5.636l2.121 2.121M12.243 12.243l2.121 2.121M12.243 7.757l2.121-2.121M5.636 14.364l2.121-2.121" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p className="text-sm font-bold text-slate-800 leading-none">ໝຸນວຽນ</p>
                                        <p className="text-[10px] text-gray-400 mt-0.5">ໜ້າທີ່ປະຈຳອາທິດ</p>
                                    </div>
                                </div>
                                <div className="flex-1 h-px bg-gray-200"></div>
                                <span className="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full">
                                    {weeklyGroups.reduce((n, g) => n + g.duties.length, 0)} ລາຍ
                                </span>
                            </div>

                            <div className="space-y-6">
                                {weeklyGroups.map((g) => (
                                    <div key={g.day}>
                                        <div className="flex items-center gap-2.5 mb-3">
                                            <div className="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-full shrink-0">
                                                <div className="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                                                <span className="text-xs font-bold text-gray-500">{g.day_name}</span>
                                            </div>
                                            <div className="flex-1 h-px bg-gray-200"></div>
                                            <span className="text-[11px] font-medium text-gray-400">{g.duties.length} ລາຍ</span>
                                        </div>
                                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 pl-2">
                                            {g.duties.map((duty) => (
                                                <DutyCard key={duty.id} duty={duty} badgeClass="bg-brand-light-green text-brand-green"
                                                    onEdit={() => openEdit(duty)} onDelete={() => destroy(duty.id)} />
                                            ))}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {weeklyGroups.length > 0 && onceGroups.length > 0 && (
                        <div className="flex items-center gap-3 mb-7">
                            <div className="flex-1 h-px bg-gray-200"></div>
                        </div>
                    )}

                    {onceGroups.length > 0 && (
                        <div>
                            <div className="flex items-center gap-3 mb-5">
                                <div className="flex items-center gap-2.5 shrink-0">
                                    <div className="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                                        <svg viewBox="0 0 20 20" fill="none" stroke="#f97316" strokeWidth="1.75" className="w-4 h-4">
                                            <rect x="3" y="4" width="14" height="13" rx="2" />
                                            <path strokeLinecap="round" d="M3 9h14M7 4V2M13 4V2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p className="text-sm font-bold text-slate-800 leading-none">ສະເພາະວັນ</p>
                                        <p className="text-[10px] text-gray-400 mt-0.5">ໜ້າທີ່ວັນສະເພາະ</p>
                                    </div>
                                </div>
                                <div className="flex-1 h-px bg-gray-200"></div>
                                <span className="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full">
                                    {onceGroups.reduce((n, g) => n + g.duties.length, 0)} ລາຍ
                                </span>
                            </div>

                            <div className="space-y-6">
                                {onceGroups.map((g) => {
                                    const { isToday, isTomorrow, isFuture, isPast } = dateStatus(g.date);
                                    return (
                                        <div key={g.date}>
                                            <div className="flex items-center gap-2.5 mb-3">
                                                {isToday ? (
                                                    <div className="flex items-center gap-2 px-3 py-1.5 bg-red-500 rounded-full shrink-0">
                                                        <div className="w-1.5 h-1.5 rounded-full bg-white"></div>
                                                        <span className="text-xs font-bold text-white">{formatDmy(g.date)}</span>
                                                        <span className="text-[10px] font-bold text-white/70 ml-0.5">ວັນນີ້</span>
                                                    </div>
                                                ) : isTomorrow ? (
                                                    <div className="flex items-center gap-2 px-3 py-1.5 bg-brand-green-dark rounded-full shrink-0">
                                                        <div className="w-1.5 h-1.5 rounded-full bg-brand-bright-green"></div>
                                                        <span className="text-xs font-bold text-white">{formatDmy(g.date)}</span>
                                                        <span className="text-[10px] font-medium text-white/70 ml-0.5">ມື້ອື່ນ</span>
                                                    </div>
                                                ) : isFuture ? (
                                                    <div className="flex items-center gap-2 px-3 py-1.5 bg-brand-green-dark rounded-full shrink-0">
                                                        <div className="w-1.5 h-1.5 rounded-full bg-brand-bright-green/60"></div>
                                                        <span className="text-xs font-bold text-white">{formatDmy(g.date)}</span>
                                                        <span className="text-[10px] font-medium text-white/60 ml-0.5">ກຳລັງຈະມາ</span>
                                                    </div>
                                                ) : (
                                                    <div className="flex items-center gap-2 px-3 py-1.5 bg-gray-100 rounded-full shrink-0">
                                                        <div className="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                                                        <span className="text-xs font-bold text-gray-500">{formatDmy(g.date)}</span>
                                                        <span className="text-[10px] font-medium text-gray-400 ml-0.5">ຜ່ານໄປ</span>
                                                    </div>
                                                )}
                                                <div className="flex-1 h-px bg-gray-200"></div>
                                                <span className="text-[11px] text-gray-400 font-medium">{g.duties.length} ລາຍ</span>
                                            </div>
                                            <div className={`grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 pl-2 ${isPast ? 'opacity-60' : ''}`}>
                                                {g.duties.map((duty) => (
                                                    <DutyCard key={duty.id} duty={duty} badgeClass="bg-orange-50 text-orange-600"
                                                        onEdit={() => openEdit(duty)} onDelete={() => destroy(duty.id)} />
                                                ))}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </>
            )}

            {showModal && (
                <div onClick={(e) => e.target === e.currentTarget && setShowModal(false)}
                    className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden max-h-[95dvh] sm:max-h-[90vh] flex flex-col">
                        <div className="px-6 py-4 flex items-center justify-between shrink-0 border-b border-gray-100">
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ມອບໝາຍໜ້າທີ່'}</p>
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">ໜ້າທີ່ຮັບຜິດຊອບ</h2>
                            </div>
                            <button onClick={() => setShowModal(false)} className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-slate-700 hover:bg-gray-100 transition-colors touch-manipulation">
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
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ພຣະສົງ / ສາມະເນນ <span className="text-red-500">*</span></label>
                                <select value={form.data.monk_id} onChange={(e) => form.setData('monk_id', e.target.value)}
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.monk_id ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`}>
                                    <option value="">— ເລືອກພຣະ —</option>
                                    {monks.map((m) => <option key={m.id} value={m.id}>{m.full_name} ({m.type_label})</option>)}
                                </select>
                                {form.errors.monk_id && <p className="text-red-500 text-xs mt-1.5">{form.errors.monk_id}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ຊື່ໜ້າທີ່ <span className="text-red-500">*</span></label>
                                <input type="text" value={form.data.duty_name} onChange={(e) => form.setData('duty_name', e.target.value)} placeholder="ເຊັ່ນ: ຮັບໃຊ້ອາຫານ, ທຳຄວາມສະອາດ, ອ່ານພຣະ..."
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.duty_name ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                {form.errors.duty_name && <p className="text-red-500 text-xs mt-1.5">{form.errors.duty_name}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ຮູບແບບໜ້າທີ່</label>
                                <div className="grid grid-cols-2 gap-2">
                                    <button type="button" onClick={() => form.setData('schedule_type', 'once')}
                                        className={`flex items-center gap-2.5 px-4 py-3 rounded-xl border text-sm font-medium transition-all ${form.data.schedule_type === 'once' ? 'bg-orange-50 text-orange-600 border-orange-200' : 'bg-white text-slate-600 border-gray-200 hover:border-gray-300'}`}>
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4 shrink-0">
                                            <rect x="3" y="4" width="14" height="13" rx="2" />
                                            <path strokeLinecap="round" d="M3 9h14M7 4V2M13 4V2" />
                                        </svg>
                                        ສະເພາະວັນ
                                    </button>
                                    <button type="button" onClick={() => form.setData('schedule_type', 'weekly')}
                                        className={`flex items-center gap-2.5 px-4 py-3 rounded-xl border text-sm font-medium transition-all ${form.data.schedule_type === 'weekly' ? 'bg-brand-light-green text-brand-green border-brand-green/30' : 'bg-white text-slate-600 border-gray-200 hover:border-gray-300'}`}>
                                        <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4 shrink-0">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M10 4v3M10 13v3M4 10h3M13 10h3M5.636 5.636l2.121 2.121M12.243 12.243l2.121 2.121M12.243 7.757l2.121-2.121M5.636 14.364l2.121-2.121" />
                                        </svg>
                                        ໝຸນວຽນ
                                    </button>
                                </div>
                            </div>

                            {form.data.schedule_type === 'once' ? (
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ວັນທີ <span className="text-red-500">*</span></label>
                                    <input type="date" value={form.data.duty_date} onChange={(e) => form.setData('duty_date', e.target.value)}
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.duty_date ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.duty_date && <p className="text-red-500 text-xs mt-1.5">{form.errors.duty_date}</p>}
                                </div>
                            ) : (
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ວັນໃນອາທິດ <span className="text-red-500">*</span></label>
                                    <div className="grid grid-cols-7 gap-1.5">
                                        {Object.entries(dayShort).map(([num, short]) => {
                                            const isSelected = Number(form.data.day_of_week) === Number(num);
                                            return (
                                                <button key={num} type="button" onClick={() => form.setData('day_of_week', Number(num))}
                                                    className={`flex flex-col items-center justify-center py-2.5 rounded-xl border text-xs font-semibold transition-all ${isSelected ? 'bg-brand-green text-white border-brand-green shadow-sm' : 'bg-[#f8fafa] text-slate-600 border-gray-200 hover:border-gray-300'}`}>
                                                    <span className="text-[11px] font-bold">{short}</span>
                                                    <span className="text-[9px] mt-0.5 opacity-70">{num}</span>
                                                </button>
                                            );
                                        })}
                                    </div>
                                    {form.data.day_of_week && (
                                        <p className="text-xs mt-2 font-medium text-brand-green">
                                            ✓ ເລືອກ: {dayNames[form.data.day_of_week]} (ທຸກໆອາທິດ)
                                        </p>
                                    )}
                                    {form.errors.day_of_week && <p className="text-red-500 text-xs mt-1.5">{form.errors.day_of_week}</p>}
                                </div>
                            )}

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ລາຍລະອຽດ</label>
                                <textarea rows="2" value={form.data.description} onChange={(e) => form.setData('description', e.target.value)} placeholder="ລາຍລະອຽດໜ້າທີ່..."
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent resize-none transition-shadow"></textarea>
                            </div>

                            <div className="flex justify-end gap-3 pb-1 pt-1">
                                <button type="button" onClick={() => setShowModal(false)}
                                    className="px-5 py-2.5 border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors touch-manipulation">
                                    ຍົກເລີກ
                                </button>
                                <button type="submit" disabled={form.processing}
                                    className="flex items-center gap-2 px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 touch-manipulation disabled:opacity-70">
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
