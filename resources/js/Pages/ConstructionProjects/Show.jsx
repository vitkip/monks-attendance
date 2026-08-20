import { Link, router, useForm } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';

function fmt(n) {
    return new Intl.NumberFormat('en-US').format(Math.round(n));
}

function isPdf(fileOrUrl) {
    if (!fileOrUrl) return false;
    if (fileOrUrl instanceof File) return fileOrUrl.type === 'application/pdf';
    if (typeof fileOrUrl === 'string') return fileOrUrl.toLowerCase().endsWith('.pdf');
    return false;
}

const statusPillClasses = {
    completed: 'bg-brand-light-green text-brand-green',
    paused: 'bg-gray-100 text-gray-500',
    ongoing: 'bg-amber-50 text-amber-600',
};

const emptyForm = { type: 'expense', amount: '', transaction_date: new Date().toISOString().slice(0, 10), description: '', image: null };

export default function ConstructionProjectShow({ project, transactions, statuses, filters }) {
    const [filterType, setFilterType] = useState(filters.type);

    useEffect(() => {
        router.get(route('construction-projects.show', project.id), filterType ? { type: filterType } : {}, {
            preserveState: true, preserveScroll: true, replace: true,
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [filterType]);

    const [showModal, setShowModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteId, setDeleteId] = useState(null);
    const [editId, setEditId] = useState(null);
    const [currentImageUrl, setCurrentImageUrl] = useState('');
    const [imagePreview, setImagePreview] = useState('');
    const [dragging, setDragging] = useState(false);
    const [isScanningAi, setIsScanningAi] = useState(false);
    const [aiMessage, setAiMessage] = useState('');
    const [aiError, setAiError] = useState('');
    const fileInputRef = useRef(null);
    const form = useForm(emptyForm);

    function resetAiState() {
        setAiMessage('');
        setAiError('');
        setIsScanningAi(false);
    }

    function openCreate() {
        setEditId(null);
        setCurrentImageUrl('');
        setImagePreview('');
        resetAiState();
        form.clearErrors();
        form.setData({ ...emptyForm, transaction_date: new Date().toISOString().slice(0, 10) });
        setShowModal(true);
    }

    function openEdit(tx) {
        setEditId(tx.id);
        setCurrentImageUrl(tx.image_url || '');
        setImagePreview('');
        resetAiState();
        form.clearErrors();
        form.setData({
            type: tx.type,
            amount: String(tx.amount),
            transaction_date: tx.transaction_date,
            description: tx.description || '',
            image: null,
        });
        setShowModal(true);
    }

    function onImageChange(file) {
        form.setData('image', file);
        resetAiState();
        if (file && !isPdf(file)) {
            setImagePreview(URL.createObjectURL(file));
        } else {
            setImagePreview('');
        }
    }

    function handleFileInput(e) {
        onImageChange(e.target.files?.[0] || null);
    }

    function handleDrop(e) {
        e.preventDefault();
        setDragging(false);
        const file = e.dataTransfer.files?.[0] || null;
        if (file) onImageChange(file);
    }

    function clearImage() {
        onImageChange(null);
    }

    async function scanTransactionWithAi() {
        if (!form.data.image) return;
        setIsScanningAi(true);
        setAiMessage('');
        setAiError('');

        const formData = new FormData();
        formData.append('image', form.data.image);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || (document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ? decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1]) : '');

        try {
            const response = await fetch(route('construction-projects.scan-ai'), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-XSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const resData = await response.json();

            if (resData?.success) {
                const data = resData.data;
                form.setData((prev) => ({
                    ...prev,
                    amount: data.amount ? String(data.amount) : prev.amount,
                    transaction_date: data.transaction_date || prev.transaction_date,
                    description: data.description || prev.description,
                    type: data.type || prev.type,
                }));
                setAiMessage('✨ AI ດຶງຂໍ້ມູນສຳເລັດ! ກະລຸນາກວດສອບຄວາມຖືກຕ້ອງກ່ອນບັນທຶກ.');
            } else {
                setAiError(resData?.message || 'ບໍ່ສາມາດດຶງຂໍ້ມູນຈາກ AI ໄດ້');
            }
        } catch (err) {
            setAiError('ເກີດຂໍ້ຜິດພາດໃນການສະແກນ');
        } finally {
            setIsScanningAi(false);
        }
    }

    function closeModal() {
        setShowModal(false);
        resetAiState();
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => closeModal();
        if (editId) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(route('construction-projects.transactions.update', [project.id, editId]), { onSuccess, preserveScroll: true, forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post(route('construction-projects.transactions.store', project.id), { onSuccess, preserveScroll: true, forceFormData: true });
        }
    }

    function confirmDelete(id) {
        setDeleteId(id);
        setShowDeleteModal(true);
    }

    function doDelete() {
        if (!deleteId) return;
        router.delete(route('construction-projects.transactions.destroy', [project.id, deleteId]), {
            preserveScroll: true,
            onSuccess: () => { setShowDeleteModal(false); setDeleteId(null); },
        });
    }

    function toggleShowTransactions() {
        router.patch(route('construction-projects.toggle-transactions', project.id), {}, { preserveScroll: true });
    }

    return (
        <AppLayout title={project.name}>
            <Link href={route('construction-projects.index')} className="flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-brand-green transition-colors mb-4">
                <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                ໂຄງການທັງໝົດ
            </Link>

            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div className="flex items-center gap-4 min-w-0">
                    {project.image_url && (
                        <img src={project.image_url} alt={project.name} className="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover shrink-0 border border-gray-100" />
                    )}
                    <div className="min-w-0">
                        <div className="flex items-center gap-2 mb-1.5">
                            <span className={`inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold ${statusPillClasses[project.status] || 'bg-gray-50 text-gray-600'}`}>
                                {statuses[project.status] || project.status}
                            </span>
                            {project.start_date_label && <span className="text-gray-300 text-xs">ເລີ່ມ {project.start_date_label}</span>}
                        </div>
                        <h1 className="text-2xl sm:text-3xl font-bold text-slate-800 truncate">{project.name}</h1>
                    </div>
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

            <div className="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-5 items-start">
                <div className="bg-white card-shadow rounded-2xl p-5 lg:sticky lg:top-6">
                    {project.progress_percent !== null && (
                        <>
                            <div className="flex items-baseline justify-between mb-2">
                                <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລະດົມທຶນໄດ້</span>
                                <span className="text-xl font-extrabold text-brand-green tabular-nums">{project.progress_percent}%</span>
                            </div>
                            <div className="relative h-3.5 rounded-full bg-brand-light-green mb-1.5">
                                <div className="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#ffb366] to-brand-green transition-all duration-700 ease-out" style={{ width: `${project.progress_percent}%` }}></div>
                                <div className="absolute inset-y-0 left-1/4 w-px bg-white/60"></div>
                                <div className="absolute inset-y-0 left-1/2 w-px bg-white/60"></div>
                                <div className="absolute inset-y-0 left-3/4 w-px bg-white/60"></div>
                                <div className="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-white border-2 border-brand-green shadow-sm" style={{ left: `${project.progress_percent}%` }}></div>
                            </div>
                            <p className="text-[11px] text-gray-400 mb-5">ເປົ້າໝາຍ {fmt(project.target_amount)} ກີບ</p>
                        </>
                    )}

                    <div className={`space-y-3 ${project.progress_percent !== null ? 'border-t border-gray-100 pt-4' : ''}`}>
                        <div className="flex items-center justify-between">
                            <span className="text-xs text-gray-400">ໄດ້ຮັບ</span>
                            <span className="text-sm font-bold text-brand-green tabular-nums">{fmt(project.total_income)}</span>
                        </div>
                        <div className="flex items-center justify-between">
                            <span className="text-xs text-gray-400">ຈ່າຍແລ້ວ</span>
                            <span className="text-sm font-bold text-rose-800 tabular-nums">{fmt(project.total_expense)}</span>
                        </div>
                        <div className="flex items-center justify-between pt-2.5 border-t border-gray-100">
                            <span className="text-xs font-semibold text-slate-600">ຍອດເຫຼືອ</span>
                            <span className={`text-base font-extrabold tabular-nums ${project.balance >= 0 ? 'text-slate-800' : 'text-rose-800'}`}>{fmt(project.balance)}</span>
                        </div>
                    </div>

                    <div className="flex items-center justify-between gap-3 mt-5 pt-4 border-t border-gray-100">
                        <div className="min-w-0">
                            <p className="text-xs font-semibold text-slate-600">ສະແດງລາຍການ</p>
                            <p className="text-[11px] text-gray-400">ໃນໜ້າສາທາລະນະ</p>
                        </div>
                        <button type="button" onClick={toggleShowTransactions} aria-pressed={project.show_transactions}
                            className={`relative shrink-0 w-11 h-6 rounded-full transition-colors ${project.show_transactions ? 'bg-brand-green' : 'bg-gray-200'}`}>
                            <span className={`absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white shadow-sm transition-transform ${project.show_transactions ? 'translate-x-5' : 'translate-x-0'}`}></span>
                        </button>
                    </div>

                    {project.description && (
                        <p className="text-xs text-gray-400 leading-relaxed mt-5 pt-4 border-t border-gray-100">{project.description}</p>
                    )}
                </div>

                <div className="bg-white card-shadow rounded-2xl overflow-hidden">
                    <div className="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3 border-b border-gray-100">
                        <p className="text-xs font-semibold text-slate-600 flex-1">ບັນຊີລາຍການ</p>
                        <select value={filterType} onChange={(e) => setFilterType(e.target.value)}
                            className="bg-[#f8fafa] border border-gray-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                            <option value="">ທຸກປະເພດ</option>
                            <option value="income">ລາຍຮັບ</option>
                            <option value="expense">ລາຍຈ່າຍ</option>
                        </select>
                        {filterType !== '' && (
                            <button type="button" onClick={() => setFilterType('')} className="text-xs font-medium text-gray-400 hover:text-brand-green transition-colors shrink-0">
                                ລ້າງຕົວກອງ
                            </button>
                        )}
                    </div>

                    <div className="px-3 sm:px-5">
                        {transactions.data.length === 0 ? (
                            <div className="py-16 text-center">
                                <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                                    <svg className="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີລາຍການ</p>
                                <p className="text-gray-400 text-sm">ເພີ່ມລາຍຮັບ ຫຼື ລາຍຈ່າຍທຳອິດຂອງໂຄງການນີ້</p>
                            </div>
                        ) : transactions.data.map((tx, i) => (
                            <div key={tx.id} className={`group flex items-center gap-3 sm:gap-4 py-3.5 ${i !== transactions.data.length - 1 ? 'border-b border-gray-100' : ''}`}>
                                <div className="shrink-0 w-10 text-center">
                                    <p className="text-[9px] font-bold uppercase tracking-wide text-gray-400 leading-none mb-0.5">{tx.transaction_date_month}</p>
                                    <p className="text-base font-extrabold text-slate-800 leading-none tabular-nums">{tx.transaction_date_day}</p>
                                </div>

                                <div className="shrink-0 w-10 h-10 rounded-xl overflow-hidden bg-[#f8fafa] border border-gray-100 flex items-center justify-center">
                                    {tx.image_url ? (
                                        <a href={tx.image_url} target="_blank" rel="noopener noreferrer" className="w-full h-full flex items-center justify-center">
                                            {isPdf(tx.image_url || tx.image) ? (
                                                <svg className="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1c.55 0 1 .45 1 1s-.45 1-1 1h-.5v1.5a.5.5 0 01-1 0V13.5a.5.5 0 01.5-.5zm3.5 0h1c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-1a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5zm1 3c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5h-.5v2h.5zm2.5-3h1.5a.5.5 0 010 1H16v.5h1a.5.5 0 010 1h-1v1a.5.5 0 01-1 0v-3a.5.5 0 01.5-.5z" />
                                                </svg>
                                            ) : (
                                                <img src={tx.image_url} alt="ຮູບບິນ" className="w-full h-full object-cover" />
                                            )}
                                        </a>
                                    ) : (
                                        <svg className="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    )}
                                </div>

                                <div className="flex-1 min-w-0">
                                    <p className="font-semibold text-slate-800 text-sm truncate">{tx.description || (tx.type === 'income' ? 'ລາຍຮັບ' : 'ລາຍຈ່າຍ')}</p>
                                    <p className="text-[11px] text-gray-400 truncate">{tx.recorded_by_name || 'ບໍ່ລະບຸ'}</p>
                                </div>

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
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ລາຍລະອຽດ</label>
                                <input type="text" value={form.data.description} onChange={(e) => form.setData('description', e.target.value)} placeholder="ເຊັ່ນ: ຄ່າຊີມັງ, ບໍລິຈາກຈາກ..."
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ຮູບພາບ / PDF ໃບບິນ
                                </label>

                                {currentImageUrl && !form.data.image && (
                                    <div className="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200 mb-2.5">
                                        {isPdf(currentImageUrl) ? (
                                            <div className="w-14 h-14 rounded-lg border border-gray-200 flex-shrink-0 bg-white flex items-center justify-center">
                                                <svg className="w-7 h-7 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1c.55 0 1 .45 1 1s-.45 1-1 1h-.5v1.5a.5.5 0 01-1 0V13.5a.5.5 0 01.5-.5zm3.5 0h1c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-1a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5zm1 3c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5h-.5v2h.5zm2.5-3h1.5a.5.5 0 010 1H16v.5h1a.5.5 0 010 1h-1v1a.5.5 0 01-1 0v-3a.5.5 0 01.5-.5z" />
                                                </svg>
                                            </div>
                                        ) : (
                                            <img src={currentImageUrl} alt="ຮູບປັດຈຸບັນ" className="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0" />
                                        )}
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs font-medium text-slate-800">{isPdf(currentImageUrl) ? 'ໄຟລ໌ PDF ປັດຈຸບັນ' : 'ຮູບພາບປັດຈຸບັນ'}</p>
                                            <p className="text-xs text-gray-400 mt-0.5">ເລືອກໄຟລ໌ໃໝ່ເພື່ອປ່ຽນແທນ</p>
                                        </div>
                                    </div>
                                )}

                                <label
                                    onDragOver={(e) => { e.preventDefault(); setDragging(true); }}
                                    onDragLeave={(e) => { e.preventDefault(); setDragging(false); }}
                                    onDrop={handleDrop}
                                    className={`relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer block ${dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'}`}
                                >
                                    <input ref={fileInputRef} type="file" accept="image/*,.pdf" className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onChange={handleFileInput} />
                                    <div className="flex flex-col items-center justify-center py-6 px-4 text-center pointer-events-none">
                                        <svg className="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        <p className="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span className="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                                        <p className="text-xs text-gray-400/70 mt-1">ຮູບໃບບິນ ຫຼື PDF — JPG, PNG, PDF — ສູງສຸດ 4MB</p>
                                    </div>
                                </label>
                                {form.errors.image && <p className="text-red-500 text-xs mt-1.5">{form.errors.image}</p>}

                                {form.data.image && (
                                    <div className="space-y-2.5 mt-2.5">
                                        <div className="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200">
                                            {isPdf(form.data.image) ? (
                                                <div className="w-14 h-14 rounded-lg border border-gray-200 flex-shrink-0 bg-white flex items-center justify-center">
                                                    <svg className="w-7 h-7 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1c.55 0 1 .45 1 1s-.45 1-1 1h-.5v1.5a.5.5 0 01-1 0V13.5a.5.5 0 01.5-.5zm3.5 0h1c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-1a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5zm1 3c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5h-.5v2h.5zm2.5-3h1.5a.5.5 0 010 1H16v.5h1a.5.5 0 010 1h-1v1a.5.5 0 01-1 0v-3a.5.5 0 01.5-.5z" />
                                                    </svg>
                                                </div>
                                            ) : (
                                                <img src={imagePreview} alt="ຕົວຢ່າງ" className="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0" />
                                            )}
                                            <div className="flex-1 min-w-0">
                                                <p className="text-xs font-medium text-slate-800 truncate">{form.data.image.name}</p>
                                                <p className="text-xs text-gray-400">{(form.data.image.size / 1024).toFixed(1)} KB</p>
                                            </div>
                                            <button type="button" onClick={clearImage} className="flex-shrink-0 text-xs text-red-500 hover:text-red-600 font-medium transition-colors px-2 py-1">
                                                ຍົກເລີກ
                                            </button>
                                        </div>

                                        <button
                                            type="button"
                                            onClick={scanTransactionWithAi}
                                            disabled={isScanningAi}
                                            className="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-purple-500/20 transition-all disabled:opacity-60"
                                        >
                                            {isScanningAi ? (
                                                <>
                                                    <svg className="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span>ກຳລັງສະແກນ ແລະ ດຶງຂໍ້ມູນດ້ວຍ AI...</span>
                                                </>
                                            ) : (
                                                <>
                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                    </svg>
                                                    <span>✨ ສະແກນບິນດ້ວຍ AI (ດຶງຈຳນວນເງິນ, ວັນທີ & ລາຍລະອຽດ)</span>
                                                </>
                                            )}
                                        </button>
                                    </div>
                                )}

                                {aiMessage && (
                                    <div className="mt-2.5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium flex items-center gap-2">
                                        <svg className="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>{aiMessage}</span>
                                    </div>
                                )}

                                {aiError && (
                                    <div className="mt-2.5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2">
                                        <svg className="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>{aiError}</span>
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
