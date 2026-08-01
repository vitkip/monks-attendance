import { router, useForm } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';

function fmt(n) {
    return new Intl.NumberFormat('en-US').format(Math.round(n));
}

const typeClasses = {
    monk: 'bg-orange-50 text-orange-600',
    novice: 'bg-teal-50 text-teal-600',
    nun: 'bg-purple-50 text-purple-600',
};

const typeOptions = [
    ['monk', 'ພຣະສົງ'],
    ['novice', 'ສາມະເນນ'],
    ['nun', 'ແມ່ຂາວ'],
];

const typeActiveClasses = {
    monk: 'bg-orange-50 text-orange-600 border-orange-200',
    novice: 'bg-teal-50 text-teal-600 border-teal-200',
    nun: 'bg-purple-50 text-purple-600 border-purple-200',
};

const emptyForm = {
    name: '', surname: '', type: 'monk', birth_date: '', ordination_date: '', temple: '', photo: null,
};

function pansaFor(ordinationDate) {
    if (!ordinationDate) return 0;
    const ord = new Date(ordinationDate);
    if (ord > new Date()) return 0;
    const now = new Date();
    let years = now.getFullYear() - ord.getFullYear();
    const m = now.getMonth() - ord.getMonth();
    if (m < 0 || (m === 0 && now.getDate() < ord.getDate())) years--;
    return Math.max(0, years);
}

function ageFor(birthDate) {
    if (!birthDate) return null;
    return pansaFor(birthDate);
}

export default function MonksIndex({ filters, monks, totalCount, monkCount, noviceCount, nunCount }) {
    const [search, setSearch] = useState(filters.search);
    const [filterType, setFilterType] = useState(filters.type);
    const debounceRef = useRef(null);

    useEffect(() => {
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('monks.index'), { search, type: filterType }, { preserveState: true, preserveScroll: true, replace: true });
        }, 300);
        return () => clearTimeout(debounceRef.current);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, filterType]);

    const [showModal, setShowModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteId, setDeleteId] = useState(null);
    const [editId, setEditId] = useState(null);
    const [existingPhotoUrl, setExistingPhotoUrl] = useState('');
    const [photoPreview, setPhotoPreview] = useState('');
    const form = useForm(emptyForm);

    function openCreate() {
        setEditId(null);
        setExistingPhotoUrl('');
        setPhotoPreview('');
        form.reset();
        form.clearErrors();
        form.setData({ ...emptyForm });
        setShowModal(true);
    }

    function openEdit(monk) {
        setEditId(monk.id);
        setExistingPhotoUrl(monk.photo_url);
        setPhotoPreview('');
        form.clearErrors();
        form.setData({
            name: monk.name,
            surname: monk.surname,
            type: monk.type,
            birth_date: monk.birth_date || '',
            ordination_date: monk.ordination_date || '',
            temple: monk.temple || '',
            photo: null,
        });
        setShowModal(true);
    }

    function onPhotoChange(e) {
        const file = e.target.files?.[0] || null;
        form.setData('photo', file);
        setPhotoPreview(file ? URL.createObjectURL(file) : '');
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => setShowModal(false);
        if (editId) {
            form.post(route('monks.update', editId), { onSuccess, preserveScroll: true, forceFormData: true });
        } else {
            form.post(route('monks.store'), { onSuccess, preserveScroll: true, forceFormData: true });
        }
    }

    function confirmDelete(id) {
        setDeleteId(id);
        setShowDeleteModal(true);
    }

    function doDelete() {
        if (!deleteId) return;
        router.delete(route('monks.destroy', deleteId), {
            preserveScroll: true,
            onSuccess: () => { setShowDeleteModal(false); setDeleteId(null); },
        });
    }

    const photoSrc = photoPreview || existingPhotoUrl;

    return (
        <AppLayout title="ພຣະສົງ ແລະ ສາມະເນນ">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ທະບຽນ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ພຣະສົງ ແລະ ສາມະເນນ</h1>
                    <p className="text-gray-400 text-sm mt-1">ຈັດການຂໍ້ມູນສະມາຊິກຂອງວັດທັງໝົດ</p>
                </div>
                <div className="w-full sm:w-auto flex items-center gap-2.5">
                    <a href={route('monks.public.index')} target="_blank" rel="noopener"
                        className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-slate-600 rounded-2xl text-sm font-semibold hover:bg-gray-50 transition flex-shrink-0">
                        <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        ເປີດໜ້າ Frontend
                    </a>
                    <button onClick={openCreate}
                        className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition flex-shrink-0">
                        <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        ເພີ່ມສະມາຊິກ
                    </button>
                </div>
            </div>

            <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div className="bg-brand-green text-white rounded-2xl card-shadow p-4">
                    <div className="flex items-center gap-1.5 mb-2">
                        <span className="w-1.5 h-1.5 rounded-full bg-white"></span>
                        <span className="text-[10px] font-medium text-white/70 uppercase tracking-widest">ທັງໝົດ</span>
                    </div>
                    <p className="text-2xl font-bold tabular-nums leading-none">{totalCount}</p>
                    <p className="text-[11px] text-white/60 mt-1.5">ຄົນ</p>
                </div>
                <div className="bg-white rounded-2xl card-shadow p-4">
                    <div className="flex items-center gap-1.5 mb-2">
                        <span className="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                        <span className="text-[10px] font-medium text-gray-400 uppercase tracking-widest">ພຣະສົງ</span>
                    </div>
                    <p className="text-2xl font-bold text-slate-800 tabular-nums leading-none">{monkCount}</p>
                    <p className="text-[11px] text-gray-300 mt-1.5">ຄົນ</p>
                </div>
                <div className="bg-white rounded-2xl card-shadow p-4">
                    <div className="flex items-center gap-1.5 mb-2">
                        <span className="w-1.5 h-1.5 rounded-full bg-teal-400"></span>
                        <span className="text-[10px] font-medium text-gray-400 uppercase tracking-widest">ສາມະເນນ</span>
                    </div>
                    <p className="text-2xl font-bold text-slate-800 tabular-nums leading-none">{noviceCount}</p>
                    <p className="text-[11px] text-gray-300 mt-1.5">ຄົນ</p>
                </div>
                <div className="bg-white rounded-2xl card-shadow p-4">
                    <div className="flex items-center gap-1.5 mb-2">
                        <span className="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                        <span className="text-[10px] font-medium text-gray-400 uppercase tracking-widest">ແມ່ຂາວ</span>
                    </div>
                    <p className="text-2xl font-bold text-slate-800 tabular-nums leading-none">{nunCount}</p>
                    <p className="text-[11px] text-gray-300 mt-1.5">ຄົນ</p>
                </div>
            </div>

            <div className="bg-white card-shadow rounded-2xl p-4 mb-6 flex flex-col sm:flex-row gap-3">
                <div className="flex-1 relative">
                    <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="ຄົ້ນຫາຊື່, ນາມສະກຸນ, ວັດ..."
                        className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                </div>
                <select value={filterType} onChange={(e) => setFilterType(e.target.value)}
                    className="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 sm:w-auto focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                    <option value="">ທຸກປະເພດ</option>
                    <option value="monk">ພຣະສົງ</option>
                    <option value="novice">ສາມະເນນ</option>
                    <option value="nun">ແມ່ຂາວ</option>
                </select>
            </div>

            {/* Mobile cards */}
            <div className="md:hidden space-y-3">
                {monks.data.length === 0 ? (
                    <div className="bg-white rounded-3xl card-shadow py-16 text-center">
                        <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                            <span className="text-2xl text-brand-green select-none">☸</span>
                        </div>
                        <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີຂໍ້ມູນພຣະສົງ/ສາມະເນນ</p>
                        <p className="text-gray-400 text-sm">ກົດ "ເພີ່ມສະມາຊິກ" ເພື່ອເລີ່ມຕົ້ນ</p>
                    </div>
                ) : monks.data.map((monk) => (
                    <div key={monk.id} className="bg-white rounded-2xl card-shadow overflow-hidden">
                        <div className="flex items-center gap-3 px-4 py-3.5">
                            <img src={monk.photo_url} alt={monk.full_name} className="w-11 h-11 rounded-full object-cover flex-shrink-0 ring-2 ring-brand-green/30" />
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2 flex-wrap">
                                    <span className="font-bold text-slate-800 text-sm leading-tight">{monk.full_name}</span>
                                    <span className={`px-2.5 py-1 rounded-full text-xs font-bold flex-shrink-0 ${typeClasses[monk.type] || 'bg-gray-50 text-gray-600'}`}>
                                        {monk.type_label}
                                    </span>
                                </div>
                                <p className="text-gray-400 text-xs mt-0.5 truncate">
                                    {monk.temple || '—'}&nbsp;·&nbsp;{monk.pansa} ພັນສາ&nbsp;·&nbsp;ອາຍຸ {monk.age ?? '—'}
                                </p>
                            </div>
                        </div>
                        <div className="flex items-stretch border-t border-gray-100">
                            <div className="flex-1 flex flex-col items-center justify-center py-2.5 border-r border-gray-100">
                                <span className="text-[9px] text-gray-400 uppercase tracking-wider mb-0.5">ຂາດ</span>
                                {monk.absences_count > 0 ? <span className="text-sm font-bold text-red-500">{monk.absences_count}</span> : <span className="text-sm text-gray-300">—</span>}
                            </div>
                            <div className="flex-1 flex flex-col items-center justify-center py-2.5 border-r border-gray-100 px-2">
                                <span className="text-[9px] text-gray-400 uppercase tracking-wider mb-0.5">ຄ່າປັບ</span>
                                <span className={`text-xs font-medium ${monk.absences_sum_fine_amount > 0 ? 'text-red-500' : 'text-gray-300'}`}>
                                    {monk.absences_sum_fine_amount > 0 ? fmt(monk.absences_sum_fine_amount) : '—'}
                                </span>
                            </div>
                            <div className="flex items-center gap-0.5 px-3">
                                <button onClick={() => openEdit(monk)} className="p-2.5 rounded-lg text-slate-500 hover:bg-gray-100 transition-all" aria-label="ແກ້ໄຂ">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onClick={() => confirmDelete(monk.id)} className="p-2.5 rounded-lg text-red-500 hover:bg-red-50 transition-all" aria-label="ລຶບ">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
                {monks.data.length > 0 && <div className="pt-1"><Pagination links={monks.links} /></div>}
            </div>

            {/* Desktop table */}
            <div className="hidden md:block bg-white rounded-2xl card-shadow overflow-hidden">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="bg-gray-50 border-b border-gray-100">
                            <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">#</th>
                            <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຮູບ</th>
                            <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຊື່-ນາມສະກຸນ</th>
                            <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ປະເພດ</th>
                            <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide hidden lg:table-cell">ພັນສາ</th>
                            <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide hidden lg:table-cell">ອາຍຸ</th>
                            <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide hidden lg:table-cell">ວັດ</th>
                            <th className="px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຂາດ</th>
                            <th className="px-4 py-3.5 text-right text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຄ່າປັບ</th>
                            <th className="px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        {monks.data.length === 0 ? (
                            <tr>
                                <td colSpan={10} className="px-4 py-16 text-center">
                                    <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                                        <span className="text-2xl text-brand-green select-none">☸</span>
                                    </div>
                                    <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີຂໍ້ມູນພຣະສົງ/ສາມະເນນ</p>
                                    <p className="text-gray-400 text-sm">ກົດ "ເພີ່ມສະມາຊິກ" ເພື່ອເລີ່ມຕົ້ນ</p>
                                </td>
                            </tr>
                        ) : monks.data.map((monk) => (
                            <tr key={monk.id} className="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                <td className="px-4 py-3.5 text-xs text-gray-300 tabular-nums">{monk.id}</td>
                                <td className="px-4 py-3.5">
                                    <img src={monk.photo_url} alt={monk.full_name} className="w-9 h-9 rounded-full object-cover ring-2 ring-brand-green/30" />
                                </td>
                                <td className="px-4 py-3.5 font-bold text-slate-800">{monk.full_name}</td>
                                <td className="px-4 py-3.5">
                                    <span className={`px-2.5 py-1 rounded-full text-xs font-bold ${typeClasses[monk.type] || 'bg-gray-50 text-gray-600'}`}>
                                        {monk.type_label}
                                    </span>
                                </td>
                                <td className="px-4 py-3.5 text-slate-600 hidden lg:table-cell">{monk.pansa} ພັນສາ</td>
                                <td className="px-4 py-3.5 text-slate-600 hidden lg:table-cell">{monk.age ?? '—'}</td>
                                <td className="px-4 py-3.5 text-slate-600 hidden lg:table-cell">{monk.temple || '—'}</td>
                                <td className="px-4 py-3.5 text-center">
                                    {monk.absences_count > 0 ? (
                                        <span className="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-red-50 text-red-500">{monk.absences_count}</span>
                                    ) : <span className="text-gray-300 text-xs">—</span>}
                                </td>
                                <td className={`px-4 py-3.5 text-right font-medium ${monk.absences_sum_fine_amount > 0 ? 'text-red-500' : 'text-gray-300'}`}>
                                    {monk.absences_sum_fine_amount > 0 ? `${fmt(monk.absences_sum_fine_amount)} ກີບ` : '—'}
                                </td>
                                <td className="px-4 py-3.5">
                                    <div className="flex justify-center gap-1">
                                        <button onClick={() => openEdit(monk)} className="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:bg-gray-100 transition-all">
                                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            ແກ້ໄຂ
                                        </button>
                                        <button onClick={() => confirmDelete(monk.id)} className="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg text-red-500 hover:bg-red-50 transition-all">
                                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            ລຶບ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    <Pagination links={monks.links} />
                </div>
            </div>

            {showModal && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-5xl flex flex-col max-h-[95dvh] sm:max-h-[90dvh] rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
                        <div className="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>

                        <div className="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່'}</p>
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">{editId ? 'ແກ້ໄຂຂໍ້ມູນສະມາຊິກ' : 'ເພີ່ມສະມາຊິກໃໝ່'}</h2>
                            </div>
                            <button onClick={() => setShowModal(false)} className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <form onSubmit={submit} className="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                            <div className="flex flex-col items-center gap-2 pb-1">
                                <label htmlFor="monk-photo-input" className="relative cursor-pointer group flex-shrink-0">
                                    <div className="w-24 h-24 rounded-full overflow-hidden bg-[#f8fafa] ring-4 ring-[#f8fafa] flex items-center justify-center relative">
                                        {photoSrc ? (
                                            <img src={photoSrc} alt="ຮູບຕົວຢ່າງ" className="w-full h-full object-cover" />
                                        ) : (
                                            <svg className="w-9 h-9 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z" />
                                            </svg>
                                        )}
                                    </div>
                                    <span className="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-brand-green text-white flex items-center justify-center ring-2 ring-white group-hover:bg-opacity-90 transition">
                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 17a3.5 3.5 0 100-7 3.5 3.5 0 000 7z" />
                                        </svg>
                                    </span>
                                    <input id="monk-photo-input" type="file" accept="image/*" className="hidden" onChange={onPhotoChange} />
                                </label>
                                <p className="text-[11px] text-gray-400">ກົດຮູບເພື່ອເລືອກ/ປ່ຽນຮູບພາບ</p>
                                {form.errors.photo && <p className="text-red-500 text-xs">{form.errors.photo}</p>}
                            </div>

                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest pt-1 border-t border-gray-100">ຂໍ້ມູນສ່ວນຕົວ</p>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຊື່ <span className="text-red-500">*</span></label>
                                    <input type="text" value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} placeholder="ຊື່"
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.name ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.name && <p className="text-red-500 text-xs mt-1">{form.errors.name}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ນາມສະກຸນ <span className="text-red-500">*</span></label>
                                    <input type="text" value={form.data.surname} onChange={(e) => form.setData('surname', e.target.value)} placeholder="ນາມສະກຸນ"
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.surname ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.surname && <p className="text-red-500 text-xs mt-1">{form.errors.surname}</p>}
                                </div>
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ປະເພດ</label>
                                <div className="grid grid-cols-3 gap-2">
                                    {typeOptions.map(([value, label]) => (
                                        <button key={value} type="button" onClick={() => form.setData('type', value)}
                                            className={`px-3 py-2.5 rounded-xl text-sm font-semibold border transition-all ${form.data.type === value ? typeActiveClasses[value] : 'bg-[#f8fafa] text-gray-400 border-gray-200 hover:bg-gray-100 hover:text-slate-600'}`}>
                                            {label}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest pt-3 border-t border-gray-100">ວັນທີ</p>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ວັນເດືອນປີເກີດ</label>
                                    <input type="date" value={form.data.birth_date} onChange={(e) => form.setData('birth_date', e.target.value)}
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.birth_date ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.birth_date && <p className="text-red-500 text-xs mt-1">{form.errors.birth_date}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ວັນເດືອນປີບວດ</label>
                                    <input type="date" value={form.data.ordination_date} onChange={(e) => form.setData('ordination_date', e.target.value)}
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.ordination_date ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.ordination_date && <p className="text-red-500 text-xs mt-1">{form.errors.ordination_date}</p>}
                                </div>
                            </div>

                            <div className="flex gap-2">
                                <div className="flex-1 bg-brand-light-green rounded-xl px-3 py-2.5">
                                    <p className="text-[9px] font-medium text-brand-green/70 uppercase tracking-widest mb-0.5">ອາຍຸ</p>
                                    <p className="text-sm font-bold text-brand-green">{form.data.birth_date ? `${ageFor(form.data.birth_date)} ປີ` : '—'}</p>
                                </div>
                                <div className="flex-1 bg-gray-100 rounded-xl px-3 py-2.5">
                                    <p className="text-[9px] font-medium text-gray-400 uppercase tracking-widest mb-0.5">ພັນສາ (ອັດຕະໂນມັດ)</p>
                                    <p className="text-sm font-bold text-slate-700">{pansaFor(form.data.ordination_date)}</p>
                                </div>
                            </div>

                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest pt-3 border-t border-gray-100">ວັດ</p>

                            <div>
                                <input type="text" value={form.data.temple} onChange={(e) => form.setData('temple', e.target.value)} placeholder="ຊື່ວັດ"
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                            </div>

                            <div className="flex justify-end gap-3 pt-1 pb-safe">
                                <button type="button" onClick={() => setShowModal(false)}
                                    className="px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                                    ຍົກເລີກ
                                </button>
                                <button type="submit" disabled={form.processing}
                                    className="px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                                    {form.processing ? (
                                        <>
                                            <svg className="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                            </svg>
                                            ກຳລັງບັນທຶກ...
                                        </>
                                    ) : 'ບັນທຶກ'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {showDeleteModal && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-sm rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
                        <div className="sm:hidden flex justify-center pt-3 pb-1">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>
                        <div className="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <h3 className="text-lg font-bold text-slate-800">ຢືນຢັນການລຶບ</h3>
                            <button onClick={() => setShowDeleteModal(false)} className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>
                        <div className="px-6 py-6 text-center pb-safe">
                            <div className="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                                <svg className="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <p className="text-slate-800 text-sm font-bold mb-1">ລຶບຂໍ້ມູນສະມາຊິກ</p>
                            <p className="text-gray-400 text-xs mb-6">ຂໍ້ມູນການຂາດລາທີ່ກ່ຽວຂ້ອງທັງໝົດ<br />ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                            <div className="flex justify-center gap-3">
                                <button onClick={() => setShowDeleteModal(false)}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                                    ຍົກເລີກ
                                </button>
                                <button onClick={doDelete}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl text-sm font-semibold hover:bg-red-600 transition-colors">
                                    ລຶບຂໍ້ມູນ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
