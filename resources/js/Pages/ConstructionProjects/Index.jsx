import { Link, router, useForm } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';

function fmt(n) {
    return new Intl.NumberFormat('en-US').format(Math.round(n));
}

const statusPillClasses = {
    completed: 'bg-brand-light-green text-brand-green',
    paused: 'bg-gray-100 text-gray-500',
    ongoing: 'bg-amber-50 text-amber-600',
};

const statusPillOverlayClasses = {
    completed: 'bg-brand-green/90 text-white',
    paused: 'bg-white/90 text-gray-600',
    ongoing: 'bg-amber-500/90 text-white',
};

const emptyForm = { name: '', description: '', start_date: '', target_amount: '', status: 'ongoing', image: null };

export default function ConstructionProjectsIndex({ filters, projects, statuses, totalIncomeAll, totalExpenseAll, ongoingCount }) {
    const [search, setSearch] = useState(filters.search);
    const [filterStatus, setFilterStatus] = useState(filters.status);
    const debounceRef = useRef(null);

    useEffect(() => {
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('construction-projects.index'), { search, status: filterStatus }, { preserveState: true, preserveScroll: true, replace: true });
        }, 300);
        return () => clearTimeout(debounceRef.current);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, filterStatus]);

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
        form.setData({ ...emptyForm });
        setShowModal(true);
    }

    function openEdit(project) {
        setEditId(project.id);
        setCurrentImageUrl(project.image_url || '');
        form.clearErrors();
        form.setData({
            name: project.name,
            description: project.description || '',
            start_date: project.start_date || '',
            target_amount: project.target_amount ?? '',
            status: project.status,
            image: null,
        });
        setShowModal(true);
    }

    function onFileSelected(file) {
        form.setData('image', file);
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => setShowModal(false);
        if (editId) {
            form.transform((data) => ({ ...data, _method: 'put' }));
            form.post(route('construction-projects.update', editId), { onSuccess, preserveScroll: true, forceFormData: true });
        } else {
            form.transform((data) => data);
            form.post(route('construction-projects.store'), { onSuccess, preserveScroll: true, forceFormData: true });
        }
    }

    function confirmDelete(id) {
        setDeleteId(id);
        setShowDeleteModal(true);
    }

    function doDelete() {
        if (!deleteId) return;
        router.delete(route('construction-projects.destroy', deleteId), {
            onSuccess: () => { setShowDeleteModal(false); setDeleteId(null); },
        });
    }

    return (
        <AppLayout title="ໂຄງການກໍ່ສ້າງ">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ການເງິນ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ໂຄງການກໍ່ສ້າງ</h1>
                    <p className="text-gray-400 text-sm mt-1">ຕິດຕາມທຶນບຸນ ລາຍຈ່າຍ ແລະ ຄວາມຄືບໜ້າແຕ່ລະໂຄງການ</p>
                </div>
                <div className="w-full sm:w-auto">
                    <button onClick={openCreate}
                        className="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition flex-shrink-0">
                        <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        ເພີ່ມໂຄງການ
                    </button>
                </div>
            </div>

            <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <div className="bg-white card-shadow rounded-2xl p-4">
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ລວມທຶນທີ່ໄດ້ຮັບ</p>
                    <p className="text-lg font-extrabold text-brand-green tabular-nums truncate">{fmt(totalIncomeAll)}</p>
                </div>
                <div className="bg-white card-shadow rounded-2xl p-4">
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ລວມລາຍຈ່າຍ</p>
                    <p className="text-lg font-extrabold text-rose-800 tabular-nums truncate">{fmt(totalExpenseAll)}</p>
                </div>
                <div className="bg-white card-shadow rounded-2xl p-4">
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຍອດເຫຼືອສຸດທິ</p>
                    <p className={`text-lg font-extrabold tabular-nums truncate ${(totalIncomeAll - totalExpenseAll) >= 0 ? 'text-slate-800' : 'text-rose-800'}`}>{fmt(totalIncomeAll - totalExpenseAll)}</p>
                </div>
                <div className="bg-white card-shadow rounded-2xl p-4">
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ກຳລັງດຳເນີນການ</p>
                    <p className="text-lg font-extrabold text-slate-800 tabular-nums truncate">{ongoingCount} ໂຄງການ</p>
                </div>
            </div>

            <div className="bg-white card-shadow rounded-2xl p-4 mb-6 flex flex-col sm:flex-row gap-3">
                <div className="flex-1 relative">
                    <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="ຄົ້ນຫາຊື່ໂຄງການ..."
                        className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                </div>
                <select value={filterStatus} onChange={(e) => setFilterStatus(e.target.value)}
                    className="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 sm:w-auto focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                    <option value="">ທຸກສະຖານະ</option>
                    {Object.entries(statuses).map(([key, label]) => <option key={key} value={key}>{label}</option>)}
                </select>
                {(search !== '' || filterStatus !== '') && (
                    <button type="button" onClick={() => { setSearch(''); setFilterStatus(''); }}
                        className="flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:bg-gray-100 transition-colors shrink-0">
                        <svg className="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                        </svg>
                        ລ້າງຕົວກອງ
                    </button>
                )}
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {projects.data.length === 0 ? (
                    <div className="col-span-full bg-white rounded-3xl card-shadow py-16 text-center">
                        <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                            <svg className="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີໂຄງການກໍ່ສ້າງ</p>
                        <p className="text-gray-400 text-sm">ເພີ່ມໂຄງການ ເພື່ອເລີ່ມບັນທຶກທຶນບຸນ ແລະ ຄວາມຄືບໜ້າ</p>
                    </div>
                ) : projects.data.map((project) => (
                    <div key={project.id} className="bg-white rounded-2xl card-shadow overflow-hidden flex flex-col">
                        {project.image_url ? (
                            <div className="relative">
                                <img src={project.image_url} alt={project.name} className="w-full aspect-[16/10] object-cover" />
                                <span className={`absolute top-3 left-3 inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold backdrop-blur-sm ${statusPillOverlayClasses[project.status] || 'bg-white/90 text-gray-600'}`}>
                                    {statuses[project.status] || project.status}
                                </span>
                            </div>
                        ) : (
                            <div className={`h-1.5 w-full ${project.status === 'completed' ? 'bg-brand-green' : project.status === 'paused' ? 'bg-gray-300' : 'bg-gradient-to-r from-amber-400 to-brand-green-mid'}`}></div>
                        )}
                        <Link href={route('construction-projects.show', project.id)} className="text-left px-5 pt-4 pb-4 flex-1 block">
                            <div className="flex items-center gap-2 mb-2">
                                {!project.image_url && (
                                    <span className={`inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold ${statusPillClasses[project.status] || 'bg-gray-50 text-gray-600'}`}>
                                        {statuses[project.status] || project.status}
                                    </span>
                                )}
                                {project.start_date_label && <span className="text-gray-300 text-[11px] truncate">ເລີ່ມ {project.start_date_label}</span>}
                            </div>
                            <h3 className="font-bold text-slate-800 leading-snug hover:text-brand-green transition-colors mb-2">{project.name}</h3>

                            {project.percent !== null ? (
                                <>
                                    <div className="flex items-baseline justify-between mb-1">
                                        <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລະດົມທຶນໄດ້</span>
                                        <span className="text-xs font-extrabold text-brand-green tabular-nums">{project.percent}%</span>
                                    </div>
                                    <div className="relative h-2 rounded-full bg-brand-light-green mb-3">
                                        <div className="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#ffb366] to-brand-green" style={{ width: `${project.percent}%` }}></div>
                                    </div>
                                </>
                            ) : (
                                <div className="grid grid-cols-3 gap-2 pt-1 pb-1">
                                    <div>
                                        <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ຮັບ</p>
                                        <p className="text-xs font-bold text-brand-green truncate tabular-nums">{fmt(project.income_sum)}</p>
                                    </div>
                                    <div>
                                        <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ຈ່າຍ</p>
                                        <p className="text-xs font-bold text-rose-800 truncate tabular-nums">{fmt(project.expense_sum)}</p>
                                    </div>
                                    <div>
                                        <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ເຫຼືອ</p>
                                        <p className={`text-xs font-bold truncate tabular-nums ${project.balance >= 0 ? 'text-slate-800' : 'text-rose-800'}`}>{fmt(project.balance)}</p>
                                    </div>
                                </div>
                            )}
                        </Link>
                        <div className="flex items-stretch border-t border-gray-100">
                            <Link href={route('construction-projects.show', project.id)}
                                className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors">
                                <svg className="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                ລາຍລະອຽດ
                            </Link>
                            <div className="w-px bg-gray-100 self-stretch"></div>
                            <button onClick={() => openEdit(project)}
                                className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors">
                                <svg className="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="1.75">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z" />
                                </svg>
                                ແກ້ໄຂ
                            </button>
                            <div className="w-px bg-gray-100 self-stretch"></div>
                            <button onClick={() => confirmDelete(project.id)}
                                className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-rose-800 hover:bg-rose-50 py-2.5 transition-colors">
                                <svg className="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="1.75">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5" />
                                </svg>
                                ລຶບ
                            </button>
                        </div>
                    </div>
                ))}
            </div>

            {projects.data.length > 0 && <div className="mt-6"><Pagination links={projects.links} /></div>}

            {showModal && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-lg flex flex-col max-h-[95dvh] sm:max-h-[90dvh] rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
                        <div className="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>
                        <div className="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່'}</p>
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">{editId ? 'ແກ້ໄຂໂຄງການ' : 'ເພີ່ມໂຄງການກໍ່ສ້າງ'}</h2>
                            </div>
                            <button onClick={() => setShowModal(false)} className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <form onSubmit={submit} className="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຊື່ໂຄງການ <span className="text-red-500">*</span></label>
                                <input type="text" value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} placeholder="ເຊັ່ນ: ກໍ່ສ້າງສາລາອະເນກປະສົງ"
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.name ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                {form.errors.name && <p className="text-red-500 text-xs mt-1">{form.errors.name}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ລາຍລະອຽດ</label>
                                <textarea rows="3" value={form.data.description} onChange={(e) => form.setData('description', e.target.value)} placeholder="ລາຍລະອຽດໂຄງການ..."
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow"></textarea>
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ວັນທີເລີ່ມ</label>
                                    <input type="date" value={form.data.start_date} onChange={(e) => form.setData('start_date', e.target.value)}
                                        className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                                </div>
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ເປົ້າໝາຍລະດົມທຶນ (ກີບ)</label>
                                    <input type="number" step="0.01" min="0" placeholder="ບໍ່ກຳນົດ" value={form.data.target_amount} onChange={(e) => form.setData('target_amount', e.target.value)}
                                        className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                                </div>
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ສະຖານະ <span className="text-red-500">*</span></label>
                                <select value={form.data.status} onChange={(e) => form.setData('status', e.target.value)}
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                                    {Object.entries(statuses).map(([key, label]) => <option key={key} value={key}>{label}</option>)}
                                </select>
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຮູບໂຄງການ</label>
                                <p className="text-[11px] text-gray-400 mb-2 -mt-1">ຮູບນີ້ຈະສະແດງໃນໜ້າສາທາລະນະ ໃຫ້ຍາດໂຍມເຫັນຄວາມຄືບໜ້າ</p>

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
                                    onDrop={(e) => { e.preventDefault(); setDragging(false); const f = e.dataTransfer.files?.[0]; if (f) onFileSelected(f); }}
                                    onClick={() => fileInputRef.current?.click()}
                                    className={`relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer ${dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'}`}
                                >
                                    <input ref={fileInputRef} type="file" accept="image/*" className="hidden" onChange={(e) => onFileSelected(e.target.files?.[0] || null)} />
                                    <div className="flex flex-col items-center justify-center py-6 px-4 text-center pointer-events-none">
                                        <svg className="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        <p className="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span className="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                                        <p className="text-xs text-gray-400/70 mt-1">ຮູບໂຄງການ (ບໍ່ບັງຄັບ) — JPG, PNG — ສູງສຸດ 4MB</p>
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
                                        <button type="button" onClick={() => onFileSelected(null)} className="flex-shrink-0 text-xs text-red-500 hover:text-red-600 font-medium transition-colors px-2 py-1">
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
                            <p className="text-slate-800 text-sm font-bold mb-1">ລຶບໂຄງການນີ້</p>
                            <p className="text-gray-400 text-xs mb-6">ລາຍການລາຍຈ່າຍ/ລາຍຮັບທັງໝົດຂອງໂຄງການນີ້ຈະຖືກລຶບຖາວອນ</p>
                            <div className="flex justify-center gap-3">
                                <button onClick={() => setShowDeleteModal(false)}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                                    ຍົກເລີກ
                                </button>
                                <button onClick={doDelete}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl text-sm font-semibold hover:bg-red-600 transition-colors">
                                    ລຶບໂຄງການ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
