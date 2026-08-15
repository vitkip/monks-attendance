import { router, useForm } from '@inertiajs/react';
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

const emptyForm = {
    account_number: '', province: '', customer_name: '', bill_month: '', amount: '', image: null,
};

export default function ElectricityBillsIndex({ filters, bills, provinces }) {
    const [search, setSearch] = useState(filters.search);
    const [filterProvince, setFilterProvince] = useState(filters.province);
    const [filterMonth, setFilterMonth] = useState(filters.month);
    const debounceRef = useRef(null);
    const firstRun = useRef(true);

    useEffect(() => {
        if (firstRun.current) {
            firstRun.current = false;
            return;
        }
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('electricity-bills.index'), { search, province: filterProvince, month: filterMonth }, { preserveState: true, preserveScroll: true, replace: true });
        }, 300);
        return () => clearTimeout(debounceRef.current);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, filterProvince, filterMonth]);

    function clearFilters() {
        setSearch('');
        setFilterProvince('');
        setFilterMonth('');
    }

    const [showModal, setShowModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [editId, setEditId] = useState(null);
    const [deleteId, setDeleteId] = useState(null);
    const [viewingBill, setViewingBill] = useState(null);
    const [isDuplicate, setIsDuplicate] = useState(false);
    const [existingImageUrl, setExistingImageUrl] = useState('');
    const [imagePreview, setImagePreview] = useState('');
    const [dragging, setDragging] = useState(false);
    const [isScanningAi, setIsScanningAi] = useState(false);
    const [aiMessage, setAiMessage] = useState('');
    const [aiError, setAiError] = useState('');
    const form = useForm(emptyForm);

    function resetAiState() {
        setAiMessage('');
        setAiError('');
        setIsScanningAi(false);
    }

    function openCreate() {
        setEditId(null);
        setIsDuplicate(false);
        setExistingImageUrl('');
        setImagePreview('');
        resetAiState();
        form.clearErrors();
        form.setData({ ...emptyForm });
        setShowModal(true);
    }

    function openEdit(bill) {
        setEditId(bill.id);
        setIsDuplicate(false);
        setExistingImageUrl(bill.image_url || '');
        setImagePreview('');
        resetAiState();
        form.clearErrors();
        form.setData({
            account_number: bill.account_number,
            province: bill.province,
            customer_name: bill.customer_name,
            bill_month: bill.bill_month,
            amount: String(bill.amount),
            image: null,
        });
        setShowModal(true);
    }

    function openDuplicate(bill) {
        setEditId(null);
        setIsDuplicate(true);
        setExistingImageUrl('');
        setImagePreview('');
        resetAiState();
        form.clearErrors();
        form.setData({
            account_number: bill.account_number,
            province: bill.province,
            customer_name: bill.customer_name,
            bill_month: '',
            amount: '',
            image: null,
        });
        setShowModal(true);
    }

    function openView(bill) {
        setViewingBill(bill);
        setShowViewModal(true);
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

    async function scanBillWithAi() {
        if (!form.data.image) return;
        setIsScanningAi(true);
        setAiMessage('');
        setAiError('');

        const formData = new FormData();
        formData.append('image', form.data.image);

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || (document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ? decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1]) : '');

        try {
            const response = await fetch(route('electricity-bills.scan-ai'), {
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
                    account_number: data.account_number || prev.account_number,
                    customer_name: data.customer_name || prev.customer_name,
                    province: data.province || prev.province,
                    bill_month: data.bill_month || prev.bill_month,
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
        setIsDuplicate(false);
        resetAiState();
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => closeModal();
        if (editId) {
            form.post(route('electricity-bills.update', editId), { onSuccess, preserveScroll: true, forceFormData: true });
        } else {
            form.post(route('electricity-bills.store'), { onSuccess, preserveScroll: true, forceFormData: true });
        }
    }

    function confirmDelete(id) {
        setDeleteId(id);
        setShowDeleteModal(true);
    }

    function doDelete() {
        if (!deleteId) return;
        router.delete(route('electricity-bills.destroy', deleteId), {
            preserveScroll: true,
            onSuccess: () => { setShowDeleteModal(false); setDeleteId(null); },
        });
    }

    const hasFilters = search !== '' || filterProvince !== '' || filterMonth !== '';

    return (
        <AppLayout title="ແຈ້ງບິນຄ່າໄຟຟ້າ">
            {/* Page header */}
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ການເງິນ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ແຈ້ງບິນຄ່າໄຟຟ້າ</h1>
                    <p className="text-gray-400 text-sm mt-1">ບັນທຶກ ແລະ ຕິດຕາມໃບບິນຄ່າໄຟຟ້າປະຈຳເດືອນຂອງວັດ</p>
                </div>
                <div className="w-full sm:w-auto">
                    <button onClick={openCreate}
                        className="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5
                                   bg-brand-green text-white rounded-2xl text-sm font-semibold
                                   shadow-lg shadow-brand-green/20 hover:bg-opacity-90
                                   transition flex-shrink-0">
                        <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        ເພີ່ມໃບບິນ
                    </button>
                </div>
            </div>

            {/* Filters */}
            <div className="bg-white card-shadow rounded-2xl p-4 mb-6 flex flex-col sm:flex-row gap-3">
                <div className="flex-1 relative">
                    <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)}
                        placeholder="ຄົ້ນຫາຊື່ ຫຼື ເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ..."
                        className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm
                                   text-slate-800 placeholder:text-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                </div>
                <select value={filterProvince} onChange={(e) => setFilterProvince(e.target.value)}
                    className="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                               text-slate-800 sm:w-auto
                               focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                    <option value="">ທຸກແຂວງ</option>
                    {provinces.map((p) => <option key={p} value={p}>{p}</option>)}
                </select>
                <input type="month" value={filterMonth} onChange={(e) => setFilterMonth(e.target.value)}
                    className="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                               text-slate-800 sm:w-auto
                               focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                {hasFilters && (
                    <button type="button" onClick={clearFilters}
                        className="flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium
                                   text-slate-500 hover:bg-gray-100 transition-colors shrink-0">
                        <svg className="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                        </svg>
                        ລ້າງຕົວກອງ
                    </button>
                )}
            </div>

            {/* MOBILE: Compact list */}
            <div className="md:hidden space-y-3">
                {bills.data.length === 0 ? (
                    <div className="bg-white rounded-3xl card-shadow py-16 text-center">
                        <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                            <svg className="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີໃບບິນຄ່າໄຟຟ້າ</p>
                        <p className="text-gray-400 text-sm">ກົດ "ເພີ່ມໃບບິນ" ເພື່ອບັນທຶກໃບບິນທຳອິດ</p>
                    </div>
                ) : bills.data.map((bill) => (
                    <div key={bill.id} className="bg-white rounded-2xl card-shadow overflow-hidden">
                        <button onClick={() => openView(bill)} className="w-full flex items-center gap-3 px-4 py-3.5 text-left">
                            <div className="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100 flex items-center justify-center">
                                {isPdf(bill.image) ? (
                                    <svg className="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1c.55 0 1 .45 1 1s-.45 1-1 1h-.5v1.5a.5.5 0 01-1 0V13.5a.5.5 0 01.5-.5zm3.5 0h1c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-1a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5zm1 3c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5h-.5v2h.5zm2.5-3h1.5a.5.5 0 010 1H16v.5h1a.5.5 0 010 1h-1v1a.5.5 0 01-1 0v-3a.5.5 0 01.5-.5z" />
                                    </svg>
                                ) : (
                                    <img src={bill.image_url} alt={bill.customer_name} className="w-full h-full object-cover" />
                                )}
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2">
                                    <span className="font-bold text-slate-800 text-sm leading-tight truncate min-w-0 flex-1">{bill.customer_name}</span>
                                    <span className="text-brand-green font-bold text-sm flex-shrink-0">{fmt(bill.amount)} ກີບ</span>
                                </div>
                                <p className="text-gray-400 text-xs mt-0.5 truncate">
                                    {bill.account_number} &nbsp;·&nbsp; {bill.province} &nbsp;·&nbsp; {bill.bill_month_label}
                                </p>
                            </div>
                        </button>

                        <div className="flex items-stretch border-t border-gray-100">
                            <button onClick={() => openDuplicate(bill)}
                                className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors touch-manipulation">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-3.5 h-3.5 shrink-0">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 4h8a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2z" />
                                </svg>
                                ຄັດລອກ
                            </button>
                            <div className="w-px bg-gray-100 self-stretch"></div>
                            <button onClick={() => openEdit(bill)}
                                className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors touch-manipulation">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-3.5 h-3.5 shrink-0">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z" />
                                </svg>
                                ແກ້ໄຂ
                            </button>
                            <div className="w-px bg-gray-100 self-stretch"></div>
                            <button onClick={() => confirmDelete(bill.id)}
                                className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-red-500 hover:bg-red-50 py-2.5 transition-colors touch-manipulation">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-3.5 h-3.5 shrink-0">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5" />
                                </svg>
                                ລຶບ
                            </button>
                        </div>
                    </div>
                ))}

                {bills.data.length > 0 && bills.links.length > 3 && <div className="pt-1"><Pagination links={bills.links} /></div>}
            </div>

            {/* DESKTOP: Full table */}
            <div className="hidden md:block bg-white rounded-2xl card-shadow overflow-hidden">
                <table className="w-full text-sm table-fixed">
                    <thead>
                        <tr className="bg-gray-50 border-b border-gray-100">
                            <th className="w-[68px] px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຮູບ</th>
                            <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຜູ້ໃຊ້ໄຟຟ້າ</th>
                            <th className="w-32 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide hidden lg:table-cell">ແຂວງ</th>
                            <th className="w-24 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ເດືອນ</th>
                            <th className="w-32 px-4 py-3.5 text-right text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈຳນວນເງິນ</th>
                            <th className="w-28 px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        {bills.data.length === 0 ? (
                            <tr>
                                <td colSpan={6} className="px-4 py-16 text-center">
                                    <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                                        <svg className="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີໃບບິນຄ່າໄຟຟ້າ</p>
                                    <p className="text-gray-400 text-sm">ກົດ "ເພີ່ມໃບບິນ" ເພື່ອບັນທຶກໃບບິນທຳອິດ</p>
                                </td>
                            </tr>
                        ) : bills.data.map((bill) => (
                            <tr key={bill.id} className="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                <td className="px-4 py-3.5">
                                    <button onClick={() => openView(bill)} className="block w-11 h-11 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
                                        {isPdf(bill.image) ? (
                                            <svg className="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1c.55 0 1 .45 1 1s-.45 1-1 1h-.5v1.5a.5.5 0 01-1 0V13.5a.5.5 0 01.5-.5zm3.5 0h1c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-1a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5zm1 3c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5h-.5v2h.5zm2.5-3h1.5a.5.5 0 010 1H16v.5h1a.5.5 0 010 1h-1v1a.5.5 0 01-1 0v-3a.5.5 0 01.5-.5z" />
                                            </svg>
                                        ) : (
                                            <img src={bill.image_url} alt={bill.customer_name} className="w-full h-full object-cover" />
                                        )}
                                    </button>
                                </td>
                                <td className="px-4 py-3.5 overflow-hidden">
                                    <button onClick={() => openView(bill)} className="text-left block w-full min-w-0">
                                        <p className="font-bold text-slate-800 leading-snug hover:text-brand-green transition-colors truncate">{bill.customer_name}</p>
                                        <p className="text-gray-400 text-xs mt-0.5 truncate">{bill.account_number}</p>
                                    </button>
                                </td>
                                <td className="px-4 py-3.5 text-slate-600 truncate hidden lg:table-cell">{bill.province}</td>
                                <td className="px-4 py-3.5 text-slate-600 whitespace-nowrap">{bill.bill_month_label}</td>
                                <td className="px-4 py-3.5 text-right font-bold text-slate-800 whitespace-nowrap">{fmt(bill.amount)} ກີບ</td>
                                <td className="px-4 py-3.5">
                                    <div className="flex justify-center gap-1">
                                        <button onClick={() => openDuplicate(bill)}
                                            className="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                                       text-slate-500 hover:bg-gray-100 transition-all" aria-label="ຄັດລອກ">
                                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                <path strokeLinecap="round" strokeLinejoin="round"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 4h8a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2v-8a2 2 0 012-2z" />
                                            </svg>
                                        </button>
                                        <button onClick={() => openEdit(bill)}
                                            className="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                                       text-slate-500 hover:bg-gray-100 transition-all" aria-label="ແກ້ໄຂ">
                                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                <path strokeLinecap="round" strokeLinejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button onClick={() => confirmDelete(bill.id)}
                                            className="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg
                                                       text-red-500 hover:bg-red-50 transition-all" aria-label="ລຶບ">
                                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                <path strokeLinecap="round" strokeLinejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {bills.links.length > 3 && (
                    <div className="px-4 py-3 border-t border-gray-100 bg-gray-50">
                        <Pagination links={bills.links} />
                    </div>
                )}
            </div>

            {/* View Modal */}
            {showViewModal && viewingBill && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
                    onClick={(e) => { if (e.target === e.currentTarget) setShowViewModal(false); }}>
                    <div className="bg-white w-full sm:max-w-lg flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                                rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

                        <div className="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>

                        <div className="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລາຍລະອຽດໃບບິນ</p>
                            <button onClick={() => setShowViewModal(false)} aria-label="ປິດ"
                                className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                                           hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <div className="flex-1 overflow-y-auto">
                            {isPdf(viewingBill.image) ? (
                                <div className="w-full bg-gray-100">
                                    <div className="flex items-center gap-2 px-4 py-2 bg-gray-50 border-b border-gray-200">
                                        <svg className="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1c.55 0 1 .45 1 1s-.45 1-1 1h-.5v1.5a.5.5 0 01-1 0V13.5a.5.5 0 01.5-.5zm3.5 0h1c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-1a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5zm1 3c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5h-.5v2h.5zm2.5-3h1.5a.5.5 0 010 1H16v.5h1a.5.5 0 010 1h-1v1a.5.5 0 01-1 0v-3a.5.5 0 01.5-.5z" />
                                        </svg>
                                        <span className="text-xs font-medium text-slate-600 truncate flex-1">ໄຟລ໌ PDF</span>
                                        <a href={viewingBill.image_url} target="_blank" rel="noopener noreferrer"
                                            className="text-xs font-medium text-brand-green hover:underline flex-shrink-0"
                                            onClick={(e) => e.stopPropagation()}>ເປີດໃນແຖບໃໝ່</a>
                                    </div>
                                    <iframe src={viewingBill.image_url} className="w-full aspect-[4/3]" title="ໃບບິນ PDF" />
                                </div>
                            ) : (
                                <img src={viewingBill.image_url} alt={viewingBill.customer_name} className="w-full aspect-[4/3] object-cover bg-gray-100" />
                            )}
                            <div className="px-6 py-5 space-y-3">
                                <div>
                                    <h2 className="text-xl font-bold text-slate-800 leading-snug">{viewingBill.customer_name}</h2>
                                    <p className="text-gray-400 text-sm mt-0.5">{viewingBill.province}</p>
                                </div>
                                <div className="grid grid-cols-2 gap-3 pt-2">
                                    <div className="bg-[#f8fafa] rounded-xl p-3">
                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ</p>
                                        <p className="text-sm font-semibold text-slate-800">{viewingBill.account_number}</p>
                                    </div>
                                    <div className="bg-[#f8fafa] rounded-xl p-3">
                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ໃບບິນປະຈຳເດືອນ</p>
                                        <p className="text-sm font-semibold text-slate-800">{viewingBill.bill_month_label}</p>
                                    </div>
                                    <div className="bg-[#f8fafa] rounded-xl p-3 col-span-2">
                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຈຳນວນເງິນ</p>
                                        <p className="text-lg font-bold text-brand-green">{fmt(viewingBill.amount)} ກີບ</p>
                                    </div>
                                </div>
                                <p className="text-gray-300 text-xs pt-1">
                                    ບັນທຶກໂດຍ {viewingBill.recorded_by_name || 'ບໍ່ລະບຸ'} &nbsp;·&nbsp; {viewingBill.created_at}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Create / Edit Modal */}
            {showModal && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-lg flex flex-col max-h-[95dvh] sm:max-h-[90dvh]
                                rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

                        <div className="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>

                        <div className="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    {editId ? 'ແກ້ໄຂຂໍ້ມູນ' : (isDuplicate ? 'ຄັດລອກລາຍການ' : 'ເພີ່ມໃໝ່')}
                                </p>
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">
                                    {editId ? 'ແກ້ໄຂໃບບິນຄ່າໄຟຟ້າ' : 'ເພີ່ມໃບບິນຄ່າໄຟຟ້າ'}
                                </h2>
                            </div>
                            <button onClick={closeModal} aria-label="ປິດ"
                                className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                                           hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        {isDuplicate && (
                            <div className="mx-6 mt-4 flex items-start gap-2.5 rounded-xl bg-brand-light-green px-3.5 py-3 text-xs text-brand-green">
                                <svg className="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>ດຶງຂໍ້ມູນຈາກລາຍການເກົ່າມາໃຫ້ແລ້ວ — ປ່ຽນ <strong>ເດືອນ</strong> ແລະ <strong>ຈຳນວນເງິນ</strong> ໃຫ້ຖືກຕ້ອງ ພ້ອມອັບໂຫລດຮູບໃບບິນໃໝ່</span>
                            </div>
                        )}

                        <form onSubmit={submit} className="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ <span className="text-red-500">*</span>
                                </label>
                                <input type="text" value={form.data.account_number} onChange={(e) => form.setData('account_number', e.target.value)}
                                    placeholder="ເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ"
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                                text-slate-800 placeholder:text-gray-400
                                                focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                                ${form.errors.account_number ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                {form.errors.account_number && <p className="text-red-500 text-xs mt-1">{form.errors.account_number}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ຊື່ຜູ້ໃຊ້ໄຟຟ້າ <span className="text-red-500">*</span>
                                </label>
                                <input type="text" value={form.data.customer_name} onChange={(e) => form.setData('customer_name', e.target.value)}
                                    placeholder="ຊື່ຜູ້ໃຊ້ໄຟຟ້າ"
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                                text-slate-800 placeholder:text-gray-400
                                                focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                                ${form.errors.customer_name ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                {form.errors.customer_name && <p className="text-red-500 text-xs mt-1">{form.errors.customer_name}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ແຂວງ <span className="text-red-500">*</span>
                                </label>
                                <select value={form.data.province} onChange={(e) => form.setData('province', e.target.value)}
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                                text-slate-800
                                                focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                                ${form.errors.province ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`}>
                                    <option value="">— ເລືອກແຂວງ —</option>
                                    {provinces.map((p) => <option key={p} value={p}>{p}</option>)}
                                </select>
                                {form.errors.province && <p className="text-red-500 text-xs mt-1">{form.errors.province}</p>}
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                        ໃບບິນປະຈຳເດືອນ <span className="text-red-500">*</span>
                                    </label>
                                    <input type="month" value={form.data.bill_month} onChange={(e) => form.setData('bill_month', e.target.value)}
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                                    text-slate-800
                                                    focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                                    ${form.errors.bill_month ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.bill_month && <p className="text-red-500 text-xs mt-1">{form.errors.bill_month}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                        ຈຳນວນເງິນ (ກີບ) <span className="text-red-500">*</span>
                                    </label>
                                    <input type="number" step="0.01" min="0" value={form.data.amount} onChange={(e) => form.setData('amount', e.target.value)}
                                        placeholder="0"
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm
                                                    text-slate-800 placeholder:text-gray-400
                                                    focus:outline-none focus:ring-2 focus:border-transparent transition-shadow
                                                    ${form.errors.amount ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.amount && <p className="text-red-500 text-xs mt-1">{form.errors.amount}</p>}
                                </div>
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ຮູບພາບ / PDF ໃບບິນ <span className="text-red-500">*</span>
                                </label>

                                {/* Current image (edit mode, no new file chosen yet) */}
                                {existingImageUrl && !form.data.image && (
                                    <div className="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200 mb-2.5">
                                        {isPdf(existingImageUrl) ? (
                                            <div className="w-14 h-14 rounded-lg border border-gray-200 flex-shrink-0 bg-white flex items-center justify-center">
                                                <svg className="w-7 h-7 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l5 5h-5V4zM8.5 13h1c.55 0 1 .45 1 1s-.45 1-1 1h-.5v1.5a.5.5 0 01-1 0V13.5a.5.5 0 01.5-.5zm3.5 0h1c.83 0 1.5.67 1.5 1.5v1c0 .83-.67 1.5-1.5 1.5h-1a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5zm1 3c.28 0 .5-.22.5-.5v-1c0-.28-.22-.5-.5-.5h-.5v2h.5zm2.5-3h1.5a.5.5 0 010 1H16v.5h1a.5.5 0 010 1h-1v1a.5.5 0 01-1 0v-3a.5.5 0 01.5-.5z" />
                                                </svg>
                                            </div>
                                        ) : (
                                            <img src={existingImageUrl} alt="ຮູບປັດຈຸບັນ"
                                                className="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0" />
                                        )}
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs font-medium text-slate-800">{isPdf(existingImageUrl) ? 'ໄຟລ໌ PDF ປັດຈຸບັນ' : 'ຮູບພາບປັດຈຸບັນ'}</p>
                                            <p className="text-xs text-gray-400 mt-0.5">ເລືອກໄຟລ໌ໃໝ່ເພື່ອປ່ຽນແທນ</p>
                                        </div>
                                    </div>
                                )}

                                {/* Dropzone */}
                                <label
                                    onDragOver={(e) => { e.preventDefault(); setDragging(true); }}
                                    onDragLeave={(e) => { e.preventDefault(); setDragging(false); }}
                                    onDrop={handleDrop}
                                    className={`relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer block
                                                ${dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'}`}>

                                    <input type="file" accept="image/*,.pdf" onChange={handleFileInput}
                                        className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />

                                    <div className="flex flex-col items-center justify-center py-6 px-4 text-center pointer-events-none">
                                        <svg className="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        <p className="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span className="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                                        <p className="text-xs text-gray-400/70 mt-1">ຮູບໃບບິນ ຫຼື PDF — JPG, PNG, PDF — ສູງສຸດ 4MB</p>
                                    </div>
                                </label>

                                {form.errors.image && <p className="text-red-500 text-xs mt-1.5">{form.errors.image}</p>}

                                {/* Preview of newly selected file */}
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
                                            <button type="button" onClick={clearImage}
                                                className="flex-shrink-0 text-xs text-red-500 hover:text-red-600 font-medium transition-colors px-2 py-1">
                                                ຍົກເລີກ
                                            </button>
                                        </div>

                                        <button
                                            type="button"
                                            onClick={scanBillWithAi}
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
                                                    <span>✨ ສະແກນບິນດ້ວຍ AI (ດຶງຈຳນວນເງິນ & ຂໍ້ມູນອັດໂນມັດ)</span>
                                                </>
                                            )}
                                        </button>
                                    </div>
                                )}

                                {aiMessage && (
                                    <div className="mt-2.5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2">
                                        <svg className="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>{aiMessage}</span>
                                    </div>
                                )}
                                {aiError && (
                                    <div className="mt-2.5 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs flex items-center gap-2">
                                        <svg className="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span>{aiError}</span>
                                    </div>
                                )}
                            </div>

                            <div className="flex justify-end gap-3 pt-1 pb-safe">
                                <button type="button" onClick={closeModal}
                                    className="px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600
                                               hover:bg-gray-50 transition-colors">
                                    ຍົກເລີກ
                                </button>
                                <button type="submit" disabled={form.processing}
                                    className="px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold
                                               shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
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

            {/* Delete Confirm Modal */}
            {showDeleteModal && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-sm rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

                        <div className="sm:hidden flex justify-center pt-3 pb-1">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>

                        <div className="px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <h3 className="text-lg font-bold text-slate-800">ຢືນຢັນການລຶບ</h3>
                            <button onClick={() => setShowDeleteModal(false)} aria-label="ປິດ"
                                className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400
                                           hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <div className="px-6 py-6 text-center pb-safe">
                            <div className="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                                <svg className="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                                    <path strokeLinecap="round" strokeLinejoin="round"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <p className="text-slate-800 text-sm font-bold mb-1">ລຶບໃບບິນນີ້</p>
                            <p className="text-gray-400 text-xs mb-6">ໃບບິນນີ້ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                            <div className="flex justify-center gap-3">
                                <button onClick={() => setShowDeleteModal(false)}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl
                                               text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                                    ຍົກເລີກ
                                </button>
                                <button onClick={doDelete}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl
                                               text-sm font-semibold hover:bg-red-600 transition-colors">
                                    ລຶບໃບບິນ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
