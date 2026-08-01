import { router, useForm } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';

function fmt(n) {
    return new Intl.NumberFormat('en-US').format(Math.round(n));
}

const emptyForm = {
    type: 'expense',
    amount: '',
    transaction_date: new Date().toISOString().slice(0, 10),
    source_type: 'other',
    monk_id: '',
    party_name: '',
    description: '',
    image: null,
};

export default function FundIndex({ filters, transactions, types, monks, totalIncomeAll, totalExpenseAll }) {
    const [search, setSearch] = useState(filters.search);
    const [filterType, setFilterType] = useState(filters.type);
    const [filterMonth, setFilterMonth] = useState(filters.month);
    const debounceRef = useRef(null);

    useEffect(() => {
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('fund.index'), { search, type: filterType, month: filterMonth }, { preserveState: true, preserveScroll: true, replace: true });
        }, 300);
        return () => clearTimeout(debounceRef.current);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, filterType, filterMonth]);

    const [showModal, setShowModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteId, setDeleteId] = useState(null);
    const [editId, setEditId] = useState(null);
    const [currentImageUrl, setCurrentImageUrl] = useState('');
    const [dragging, setDragging] = useState(false);
    const fileInputRef = useRef(null);
    const form = useForm(emptyForm);

    function openCreate() {
        setEditId(null);
        setCurrentImageUrl('');
        form.clearErrors();
        form.setData({ ...emptyForm, transaction_date: new Date().toISOString().slice(0, 10) });
        setShowModal(true);
    }

    function openEdit(tx) {
        setEditId(tx.id);
        setCurrentImageUrl(tx.image_url || '');
        form.clearErrors();
        form.setData({
            type: tx.type,
            amount: tx.amount,
            transaction_date: tx.transaction_date,
            source_type: tx.monk ? 'monk' : 'other',
            monk_id: tx.monk?.id ?? '',
            party_name: tx.monk ? '' : (tx.party_name || ''),
            description: tx.description || '',
            image: null,
        });
        setShowModal(true);
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => setShowModal(false);
        if (editId) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(route('fund.update', editId), { onSuccess, preserveScroll: true, forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post(route('fund.store'), { onSuccess, preserveScroll: true, forceFormData: true });
        }
    }

    function confirmDelete(id) {
        setDeleteId(id);
        setShowDeleteModal(true);
    }

    function doDelete() {
        if (!deleteId) return;
        router.delete(route('fund.destroy', deleteId), {
            preserveScroll: true,
            onSuccess: () => { setShowDeleteModal(false); setDeleteId(null); },
        });
    }

    const balanceAll = totalIncomeAll - totalExpenseAll;
    const hasFilters = search !== '' || filterType !== '' || filterMonth !== '';

    return (
        <AppLayout title="ລາຍຮັບ-ລາຍຈ່າຍກອງທຶນ">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ການເງິນ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ກອງທຶນ</h1>
                    <p className="text-gray-400 text-sm mt-1">ບັນທຶກລາຍຮັບ-ລາຍຈ່າຍຂອງກອງທຶນວັດ ຜູກກັບພຣະສົງ/ສາມະເນນ ຫຼື ບຸກຄົນອື່ນໆ</p>
                </div>
                <div className="w-full sm:w-auto">
                    <button onClick={openCreate}
                        className="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition flex-shrink-0">
                        <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        ເພີ່ມລາຍການ
                    </button>
                </div>
            </div>

            <div className="grid grid-cols-3 gap-3 mb-6">
                <div className="bg-white card-shadow rounded-2xl p-4">
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ລວມລາຍຮັບ</p>
                    <p className="text-lg font-extrabold text-brand-green tabular-nums truncate">{fmt(totalIncomeAll)}</p>
                </div>
                <div className="bg-white card-shadow rounded-2xl p-4">
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ລວມລາຍຈ່າຍ</p>
                    <p className="text-lg font-extrabold text-rose-800 tabular-nums truncate">{fmt(totalExpenseAll)}</p>
                </div>
                <div className="bg-white card-shadow rounded-2xl p-4">
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຍອດເຫຼືອສຸດທິ</p>
                    <p className={`text-lg font-extrabold tabular-nums truncate ${balanceAll >= 0 ? 'text-slate-800' : 'text-rose-800'}`}>{fmt(balanceAll)}</p>
                </div>
            </div>

            <div className="bg-white card-shadow rounded-2xl p-4 mb-6 flex flex-col sm:flex-row gap-3">
                <div className="flex-1 relative">
                    <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="ຄົ້ນຫາຊື່ພຣະ, ຜູ້ບໍລິຈາກ, ລາຍລະອຽດ..."
                        className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                </div>
                <select value={filterType} onChange={(e) => setFilterType(e.target.value)}
                    className="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 sm:w-auto focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                    <option value="">ທຸກປະເພດ</option>
                    {Object.entries(types).map(([key, label]) => <option key={key} value={key}>{label}</option>)}
                </select>
                <input type="month" value={filterMonth} onChange={(e) => setFilterMonth(e.target.value)}
                    className="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 sm:w-auto focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                {hasFilters && (
                    <button type="button" onClick={() => { setSearch(''); setFilterType(''); setFilterMonth(''); }}
                        className="flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:bg-gray-100 transition-colors shrink-0">
                        <svg className="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                        </svg>
                        ລ້າງຕົວກອງ
                    </button>
                )}
            </div>

            <div className="bg-white card-shadow rounded-2xl overflow-hidden">
                <div className="px-3 sm:px-5">
                    {transactions.data.length === 0 ? (
                        <div className="py-16 text-center">
                            <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                                <svg className="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີລາຍການ</p>
                            <p className="text-gray-400 text-sm">ເພີ່ມລາຍຮັບ ຫຼື ລາຍຈ່າຍທຳອິດຂອງກອງທຶນ</p>
                        </div>
                    ) : transactions.data.map((tx, i) => (
                        <div key={tx.id} className={`group flex items-center gap-3 sm:gap-4 py-3.5 ${i !== transactions.data.length - 1 ? 'border-b border-gray-100' : ''}`}>
                            <div className="shrink-0 w-10 text-center">
                                <p className="text-[9px] font-bold uppercase tracking-wide text-gray-400 leading-none mb-0.5">{tx.transaction_date_month}</p>
                                <p className="text-base font-extrabold text-slate-800 leading-none tabular-nums">{tx.transaction_date_day}</p>
                            </div>

                            <div className="shrink-0 w-10 h-10 rounded-full overflow-hidden bg-[#f8fafa] border border-gray-100 flex items-center justify-center">
                                {tx.monk ? (
                                    <img src={tx.monk.photo_url} alt={tx.monk.full_name} className="w-full h-full object-cover" />
                                ) : (
                                    <svg className="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                )}
                            </div>

                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-1.5">
                                    <p className="font-semibold text-slate-800 text-sm truncate">{tx.party_label}</p>
                                    {tx.monk && <span className="shrink-0 text-[10px] font-semibold text-brand-green bg-brand-light-green px-1.5 py-0.5 rounded-md">{tx.monk.type_label}</span>}
                                </div>
                                <p className="text-[11px] text-gray-400 truncate">{tx.description || (tx.type === 'income' ? 'ລາຍຮັບ' : 'ລາຍຈ່າຍ')} · {tx.recorded_by_name || 'ບໍ່ລະບຸ'}</p>
                            </div>

                            {tx.image_url && (
                                <a href={tx.image_url} target="_blank" rel="noopener noreferrer" aria-label="ເບິ່ງໃບຮັບເງິນ"
                                    className="shrink-0 w-8 h-8 rounded-lg overflow-hidden border border-gray-200">
                                    <img src={tx.image_url} alt="ໃບຮັບເງິນ" className="w-full h-full object-cover" />
                                </a>
                            )}

                            <p className={`shrink-0 font-extrabold tabular-nums text-sm sm:text-base ${tx.type === 'income' ? 'text-brand-green' : 'text-rose-800'}`}>
                                {tx.type === 'income' ? '+' : '−'}{fmt(tx.amount)}
                            </p>

                            <div className="shrink-0 flex items-center gap-0.5">
                                <button onClick={() => openEdit(tx)} aria-label="ແກ້ໄຂ" className="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-slate-600 hover:bg-gray-100 transition-colors">
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onClick={() => confirmDelete(tx.id)} aria-label="ລຶບ" className="w-7 h-7 flex items-center justify-center rounded-lg text-gray-300 hover:text-rose-700 hover:bg-rose-50 transition-colors">
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    ))}
                </div>

                {transactions.data.length > 0 && (
                    <div className="px-4 py-3 border-t border-gray-100 bg-gray-50">
                        <Pagination links={transactions.links} />
                    </div>
                )}
            </div>

            {showModal && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-lg flex flex-col max-h-[95dvh] sm:max-h-[90dvh] rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
                        <div className="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>
                        <div className="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່'}</p>
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">{editId ? 'ແກ້ໄຂລາຍການ' : 'ເພີ່ມລາຍຈ່າຍ/ລາຍຮັບ'}</h2>
                            </div>
                            <button onClick={() => setShowModal(false)} className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <form onSubmit={submit} className="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ປະເພດ <span className="text-red-500">*</span></label>
                                <div className="grid grid-cols-2 gap-2">
                                    <button type="button" onClick={() => form.setData('type', 'expense')}
                                        className={`px-4 py-2.5 rounded-xl text-sm font-semibold border transition-colors ${form.data.type === 'expense' ? 'bg-rose-800 border-rose-800 text-white' : 'bg-[#f8fafa] border-gray-200 text-slate-600'}`}>
                                        ລາຍຈ່າຍ
                                    </button>
                                    <button type="button" onClick={() => form.setData('type', 'income')}
                                        className={`px-4 py-2.5 rounded-xl text-sm font-semibold border transition-colors ${form.data.type === 'income' ? 'bg-brand-green border-brand-green text-white' : 'bg-[#f8fafa] border-gray-200 text-slate-600'}`}>
                                        ລາຍຮັບ
                                    </button>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຈຳນວນເງິນ (ກີບ) <span className="text-red-500">*</span></label>
                                    <input type="number" step="0.01" min="0" placeholder="0" value={form.data.amount} onChange={(e) => form.setData('amount', e.target.value)}
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.amount ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.amount && <p className="text-red-500 text-xs mt-1">{form.errors.amount}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ວັນທີ <span className="text-red-500">*</span></label>
                                    <input type="date" value={form.data.transaction_date} onChange={(e) => form.setData('transaction_date', e.target.value)}
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.transaction_date ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.transaction_date && <p className="text-red-500 text-xs mt-1">{form.errors.transaction_date}</p>}
                                </div>
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຜູ້ບໍລິຈາກ / ຜູ້ຮັບເງິນ <span className="text-red-500">*</span></label>
                                <div className="grid grid-cols-2 gap-2 mb-2.5">
                                    <button type="button" onClick={() => form.setData('source_type', 'monk')}
                                        className={`px-4 py-2.5 rounded-xl text-sm font-semibold border transition-colors ${form.data.source_type === 'monk' ? 'bg-slate-800 border-slate-800 text-white' : 'bg-[#f8fafa] border-gray-200 text-slate-600'}`}>
                                        ພຣະສົງ/ສາມະເນນ
                                    </button>
                                    <button type="button" onClick={() => form.setData('source_type', 'other')}
                                        className={`px-4 py-2.5 rounded-xl text-sm font-semibold border transition-colors ${form.data.source_type === 'other' ? 'bg-slate-800 border-slate-800 text-white' : 'bg-[#f8fafa] border-gray-200 text-slate-600'}`}>
                                        ອື່ນໆ
                                    </button>
                                </div>

                                {form.data.source_type === 'monk' ? (
                                    <>
                                        <select value={form.data.monk_id} onChange={(e) => form.setData('monk_id', e.target.value)}
                                            className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.monk_id ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`}>
                                            <option value="">— ເລືອກພຣະ —</option>
                                            {monks.map((m) => (
                                                <option key={m.id} value={m.id}>{m.full_name} ({m.type_label})</option>
                                            ))}
                                        </select>
                                        {form.errors.monk_id && <p className="text-red-500 text-xs mt-1.5">{form.errors.monk_id}</p>}
                                    </>
                                ) : (
                                    <>
                                        <input type="text" value={form.data.party_name} onChange={(e) => form.setData('party_name', e.target.value)} placeholder="ເຊັ່ນ: ຍາດໂຍມ, ຮ້ານຄ້າ, ວັດອື່ນ..."
                                            className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.party_name ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                        {form.errors.party_name && <p className="text-red-500 text-xs mt-1.5">{form.errors.party_name}</p>}
                                    </>
                                )}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ລາຍລະອຽດ</label>
                                <input type="text" value={form.data.description} onChange={(e) => form.setData('description', e.target.value)} placeholder="ເຊັ່ນ: ບໍລິຈາກປັດໄຈ, ຄ່າຢາ, ຄ່າຂອງໃຊ້ວັດ..."
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຮູບໃບຮັບເງິນ</label>

                                {currentImageUrl && !form.data.image && (
                                    <div className="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200 mb-2.5">
                                        <img src={currentImageUrl} alt="ຮູບປັດຈຸບັນ" className="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0" />
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs font-medium text-slate-800">ຮູບພາບປັດຈຸບັນ</p>
                                            <p className="text-xs text-gray-400 mt-0.5">ເລືອກໄຟລ໌ໃໝ່ເພື່ອປ່ຽນແທນ</p>
                                        </div>
                                    </div>
                                )}

                                <div
                                    onDragOver={(e) => { e.preventDefault(); setDragging(true); }}
                                    onDragLeave={(e) => { e.preventDefault(); setDragging(false); }}
                                    onDrop={(e) => { e.preventDefault(); setDragging(false); const f = e.dataTransfer.files?.[0]; if (f) form.setData('image', f); }}
                                    onClick={() => fileInputRef.current?.click()}
                                    className={`relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer ${dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'}`}
                                >
                                    <input ref={fileInputRef} type="file" accept="image/*" className="hidden" onChange={(e) => form.setData('image', e.target.files?.[0] || null)} />
                                    <div className="flex flex-col items-center justify-center py-6 px-4 text-center pointer-events-none">
                                        <svg className="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        <p className="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span className="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                                        <p className="text-xs text-gray-400/70 mt-1">ຮູບໃບຮັບເງິນ (ບໍ່ບັງຄັບ) — JPG, PNG — ສູງສຸດ 4MB</p>
                                    </div>
                                </div>
                                {form.errors.image && <p className="text-red-500 text-xs mt-1.5">{form.errors.image}</p>}

                                {form.data.image && (
                                    <div className="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200 mt-2.5">
                                        <img src={URL.createObjectURL(form.data.image)} alt="ຕົວຢ່າງ" className="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0" />
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs font-medium text-slate-800 truncate">{form.data.image.name}</p>
                                            <p className="text-xs text-gray-400">{(form.data.image.size / 1024).toFixed(1)} KB</p>
                                        </div>
                                        <button type="button" onClick={() => form.setData('image', null)} className="flex-shrink-0 text-xs text-red-500 hover:text-red-600 font-medium transition-colors px-2 py-1">
                                            ຍົກເລີກ
                                        </button>
                                    </div>
                                )}
                            </div>

                            <div className="flex justify-end gap-3 pt-1 pb-safe">
                                <button type="button" onClick={() => setShowModal(false)}
                                    className="px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                                    ຍົກເລີກ
                                </button>
                                <button type="submit" disabled={form.processing}
                                    className="px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition disabled:opacity-70">
                                    ບັນທຶກ
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
                            <p className="text-slate-800 text-sm font-bold mb-1">ລຶບລາຍການນີ້</p>
                            <p className="text-gray-400 text-xs mb-6">ລາຍການນີ້ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                            <div className="flex justify-center gap-3">
                                <button onClick={() => setShowDeleteModal(false)}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                                    ຍົກເລີກ
                                </button>
                                <button onClick={doDelete}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl text-sm font-semibold hover:bg-red-600 transition-colors">
                                    ລຶບລາຍການ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
