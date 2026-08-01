import { router, useForm, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import RichEditor from '@/Components/RichEditor';

const emptyForm = {
    title: '',
    excerpt: '',
    content: '',
    image: null,
    category_id: '',
    publishNow: true,
    removeExistingImage: false,
};

function StatusBadge({ status }) {
    return (
        <span className={`px-2.5 py-1 rounded-full text-xs font-bold ${status ? 'bg-brand-light-green text-brand-green' : 'bg-gray-100 text-gray-500'}`}>
            {status ? 'ເຜີຍແຜ່ແລ້ວ' : 'ຮ່າງ'}
        </span>
    );
}

function ImageThumb({ item, className }) {
    return item.image_url ? (
        <img src={item.image_url} alt={item.title} className={`${className} object-cover`} />
    ) : (
        <div className={`${className} flex items-center justify-center bg-brand-light-green`}>
            <svg className="w-4 h-4 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                <path strokeLinecap="round" strokeLinejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z" />
            </svg>
        </div>
    );
}

function TogglePublishIcon({ status }) {
    return status ? (
        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="1.75">
            <path strokeLinecap="round" strokeLinejoin="round" d="M3 12s3-7 7-7 7 7 7 7-3 7-7 7-7-7-7-7z" />
            <circle cx="10" cy="12" r="2.5" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
    ) : (
        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="1.75">
            <path strokeLinecap="round" strokeLinejoin="round" d="M3.5 3.5l13 13M8.3 5.2A7.7 7.7 0 0110 5c4 0 7 7 7 7a13 13 0 01-2.3 3.1M6.2 6.2C4 7.8 3 10 3 10s3 7 7 7c1 0 1.9-.2 2.7-.5" />
        </svg>
    );
}

function EmptyState({ isAdmin }) {
    return (
        <>
            <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                <svg className="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                    <path strokeLinecap="round" strokeLinejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z" />
                </svg>
            </div>
            <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີຂ່າວ</p>
            <p className="text-gray-400 text-sm">
                {isAdmin ? 'ກົດ "ເພີ່ມຂ່າວ" ເພື່ອປະກາດຂ່າວໃໝ່' : 'ຍັງບໍ່ມີການປະກາດຂ່າວໃນຂະນະນີ້'}
            </p>
        </>
    );
}

export default function NewsIndex({ filters, news, categories }) {
    const { auth } = usePage().props;
    const isAdmin = !!auth?.user?.isAdmin;

    const [search, setSearch] = useState(filters.search);
    const [filterCategory, setFilterCategory] = useState(filters.category);
    const [filterStatus, setFilterStatus] = useState(filters.status);
    const debounceRef = useRef(null);

    useEffect(() => {
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('news.index'), { search, category: filterCategory, status: filterStatus }, { preserveState: true, preserveScroll: true, replace: true });
        }, 300);
        return () => clearTimeout(debounceRef.current);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, filterCategory, filterStatus]);

    const [showModal, setShowModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [editId, setEditId] = useState(null);
    const [viewingItem, setViewingItem] = useState(null);
    const [deleteId, setDeleteId] = useState(null);
    const [existingImageUrl, setExistingImageUrl] = useState('');
    const [imagePreview, setImagePreview] = useState('');
    const [dragging, setDragging] = useState(false);
    const [editorNonce, setEditorNonce] = useState(0);
    const fileInputRef = useRef(null);

    const form = useForm(emptyForm);

    function openCreate() {
        setEditId(null);
        setExistingImageUrl('');
        setImagePreview('');
        form.clearErrors();
        form.setData({ ...emptyForm });
        setEditorNonce((n) => n + 1);
        setShowModal(true);
    }

    function openEdit(item) {
        setEditId(item.id);
        setExistingImageUrl(item.image_url || '');
        setImagePreview('');
        form.clearErrors();
        form.setData({
            title: item.title,
            excerpt: item.excerpt || '',
            content: item.content || '',
            image: null,
            category_id: item.category_id ? String(item.category_id) : '',
            publishNow: !!item.status,
            removeExistingImage: false,
        });
        setEditorNonce((n) => n + 1);
        setShowModal(true);
    }

    function onImageChange(file) {
        form.setData('image', file);
        form.setData('removeExistingImage', false);
        setImagePreview(file ? URL.createObjectURL(file) : '');
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

    function removeCurrentImage() {
        setExistingImageUrl('');
        form.setData('removeExistingImage', true);
    }

    function cancelNewImage() {
        form.setData('image', null);
        setImagePreview('');
        if (fileInputRef.current) fileInputRef.current.value = '';
    }

    function openView(item) {
        setViewingItem(item);
        setShowViewModal(true);
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => setShowModal(false);
        if (editId) {
            form.post(route('news.update', editId), { onSuccess, preserveScroll: true, forceFormData: true });
        } else {
            form.post(route('news.store'), { onSuccess, preserveScroll: true, forceFormData: true });
        }
    }

    function confirmDelete(id) {
        setDeleteId(id);
        setShowDeleteModal(true);
    }

    function doDelete() {
        if (!deleteId) return;
        router.delete(route('news.destroy', deleteId), {
            preserveScroll: true,
            onSuccess: () => { setShowDeleteModal(false); setDeleteId(null); },
        });
    }

    function togglePublish(id) {
        router.patch(route('news.toggle-publish', id), {}, { preserveScroll: true });
    }

    const colCount = isAdmin ? 6 : 5;

    return (
        <AppLayout title="ຂ່າວ ແລະ ປະກາດ">
            {/* Page header */}
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຂ່າວສານ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ຂ່າວ ແລະ ປະກາດ</h1>
                    <p className="text-gray-400 text-sm mt-1">ຂ່າວສານ ແລະ ປະກາດຕ່າງໆພາຍໃນວັດ</p>
                </div>
                <div className="w-full sm:w-auto flex items-center gap-2.5">
                    <a href={route('news.public.index')} target="_blank" rel="noopener"
                        className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-slate-600 rounded-2xl text-sm font-semibold hover:bg-gray-50 transition flex-shrink-0">
                        <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        ເປີດໜ້າ Frontend
                    </a>
                    {isAdmin && (
                        <button onClick={openCreate}
                            className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-green text-white rounded-2xl text-sm font-semibold shadow-lg shadow-brand-green/20 hover:bg-opacity-90 transition flex-shrink-0">
                            <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            ເພີ່ມຂ່າວ
                        </button>
                    )}
                </div>
            </div>

            {/* Filters */}
            <div className="bg-white card-shadow rounded-2xl p-4 mb-6 flex flex-col sm:flex-row gap-3">
                <div className="flex-1 relative">
                    <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="ຄົ້ນຫາຫົວຂໍ້ຂ່າວ..."
                        className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                </div>
                <select value={filterCategory} onChange={(e) => setFilterCategory(e.target.value)}
                    className="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 sm:w-auto focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                    <option value="">ທຸກໝວດໝູ່</option>
                    {categories.map((category) => (
                        <option key={category.id} value={category.id}>{category.name}</option>
                    ))}
                </select>
                {isAdmin && (
                    <select value={filterStatus} onChange={(e) => setFilterStatus(e.target.value)}
                        className="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 sm:w-auto focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                        <option value="">ທຸກສະຖານະ</option>
                        <option value="1">ເຜີຍແຜ່ແລ້ວ</option>
                        <option value="0">ຮ່າງ</option>
                    </select>
                )}
            </div>

            {/* Mobile compact list */}
            <div className="md:hidden space-y-3">
                {news.data.length === 0 ? (
                    <div className="bg-white rounded-3xl card-shadow py-16 text-center">
                        <EmptyState isAdmin={isAdmin} />
                    </div>
                ) : news.data.map((item) => (
                    <div key={item.id} className="bg-white rounded-2xl card-shadow overflow-hidden">
                        <button onClick={() => openView(item)} className="w-full flex items-center gap-3 px-4 py-3.5 text-left">
                            <div className="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                                <ImageThumb item={item} className="w-full h-full" />
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2">
                                    <span className="font-bold text-slate-800 text-sm leading-tight truncate min-w-0 flex-1">{item.title}</span>
                                    {isAdmin && (
                                        <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold flex-shrink-0 ${item.status ? 'bg-brand-light-green text-brand-green' : 'bg-gray-100 text-gray-500'}`}>
                                            {item.status ? 'ເຜີຍແຜ່ແລ້ວ' : 'ຮ່າງ'}
                                        </span>
                                    )}
                                </div>
                                <p className="text-gray-400 text-xs mt-0.5 truncate">
                                    {item.date_label}
                                    &nbsp;·&nbsp;{item.author_name || 'ບໍ່ລະບຸ'}
                                    {item.category && <>&nbsp;·&nbsp;{item.category.name}</>}
                                </p>
                            </div>
                        </button>

                        {isAdmin && (
                            <div className="flex items-stretch border-t border-gray-100">
                                <button onClick={() => togglePublish(item.id)}
                                    className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors touch-manipulation">
                                    <TogglePublishIcon status={item.status} />
                                    {item.status ? 'ເກັບຮ່າງ' : 'ເຜີຍແຜ່'}
                                </button>
                                <div className="w-px bg-gray-100 self-stretch"></div>
                                <button onClick={() => openEdit(item)}
                                    className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-600 hover:bg-gray-50 py-2.5 transition-colors touch-manipulation">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-3.5 h-3.5 shrink-0">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z" />
                                    </svg>
                                    ແກ້ໄຂ
                                </button>
                                <div className="w-px bg-gray-100 self-stretch"></div>
                                <button onClick={() => confirmDelete(item.id)}
                                    className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-red-500 hover:bg-red-50 py-2.5 transition-colors touch-manipulation">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-3.5 h-3.5 shrink-0">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5" />
                                    </svg>
                                    ລຶບ
                                </button>
                            </div>
                        )}
                    </div>
                ))}

                {news.data.length > 0 && <div className="pt-1"><Pagination links={news.links} /></div>}
            </div>

            {/* Desktop table */}
            <div className="hidden md:block bg-white rounded-2xl card-shadow overflow-hidden">
                <table className="w-full text-sm table-fixed">
                    <thead>
                        <tr className="bg-gray-50 border-b border-gray-100">
                            <th className="w-[68px] px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຮູບ</th>
                            <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຫົວຂໍ້ຂ່າວ</th>
                            {isAdmin && <th className="w-32 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ສະຖານະ</th>}
                            <th className="w-36 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide hidden lg:table-cell">ຜູ້ຂຽນ</th>
                            <th className="w-24 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ວັນທີ</th>
                            <th className="w-32 px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        {news.data.length === 0 ? (
                            <tr>
                                <td colSpan={colCount} className="px-4 py-16 text-center">
                                    <EmptyState isAdmin={isAdmin} />
                                </td>
                            </tr>
                        ) : news.data.map((item) => (
                            <tr key={item.id} className="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                <td className="px-4 py-3.5">
                                    <button onClick={() => openView(item)} className="block w-11 h-11 rounded-xl overflow-hidden bg-gray-100">
                                        <ImageThumb item={item} className="w-full h-full" />
                                    </button>
                                </td>
                                <td className="px-4 py-3.5 overflow-hidden">
                                    <button onClick={() => openView(item)} className="text-left block w-full min-w-0">
                                        {item.category && (
                                            <span className="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 mb-1">
                                                {item.category.name}
                                            </span>
                                        )}
                                        <p className="font-bold text-slate-800 leading-snug hover:text-brand-green transition-colors truncate">{item.title}</p>
                                        <p className="text-gray-400 text-xs mt-0.5 truncate">{item.excerpt_or_summary}</p>
                                    </button>
                                </td>
                                {isAdmin && (
                                    <td className="px-4 py-3.5">
                                        <StatusBadge status={item.status} />
                                    </td>
                                )}
                                <td className="px-4 py-3.5 text-slate-600 truncate hidden lg:table-cell">{item.author_name || 'ບໍ່ລະບຸ'}</td>
                                <td className="px-4 py-3.5 text-slate-600 whitespace-nowrap">{item.date_label}</td>
                                <td className="px-4 py-3.5">
                                    <div className="flex justify-center gap-1">
                                        {isAdmin ? (
                                            <>
                                                <button onClick={() => togglePublish(item.id)}
                                                    className="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:bg-gray-100 transition-all"
                                                    aria-label={item.status ? 'ເກັບຮ່າງ' : 'ເຜີຍແຜ່'}>
                                                    <TogglePublishIcon status={item.status} />
                                                </button>
                                                <button onClick={() => openEdit(item)}
                                                    className="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:bg-gray-100 transition-all" aria-label="ແກ້ໄຂ">
                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button onClick={() => confirmDelete(item.id)}
                                                    className="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg text-red-500 hover:bg-red-50 transition-all" aria-label="ລຶບ">
                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </>
                                        ) : (
                                            <button onClick={() => openView(item)}
                                                className="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg text-slate-500 hover:bg-gray-100 transition-all">
                                                ອ່ານ
                                            </button>
                                        )}
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                {news.data.length > 0 && (
                    <div className="px-4 py-3 border-t border-gray-100 bg-gray-50">
                        <Pagination links={news.links} />
                    </div>
                )}
            </div>

            {/* View Modal */}
            {showViewModal && viewingItem && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
                    onClick={(e) => { if (e.target === e.currentTarget) setShowViewModal(false); }}>
                    <div className="bg-white w-full sm:max-w-2xl flex flex-col max-h-[95dvh] sm:max-h-[90dvh] rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
                        <div className="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>

                        <div className="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລາຍລະອຽດຂ່າວ</p>
                            <button onClick={() => setShowViewModal(false)} aria-label="ປິດ"
                                className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <div className="flex-1 overflow-y-auto">
                            {viewingItem.image_url && (
                                <img src={viewingItem.image_url} alt={viewingItem.title} className="w-full aspect-[16/9] object-cover" />
                            )}
                            <div className="px-6 py-5">
                                <div className="flex items-center gap-2 flex-wrap mb-2">
                                    {viewingItem.category && (
                                        <span className="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600">
                                            {viewingItem.category.name}
                                        </span>
                                    )}
                                    {isAdmin && <StatusBadge status={viewingItem.status} />}
                                    <span className="text-xs text-gray-400">
                                        {viewingItem.date_label}
                                        &nbsp;·&nbsp;{viewingItem.author_name || 'ບໍ່ລະບຸ'}
                                    </span>
                                </div>
                                <h2 className="text-xl font-bold text-slate-800 leading-snug">{viewingItem.title}</h2>
                                <div className="prose prose-sm max-w-none mt-4 text-slate-600" dangerouslySetInnerHTML={{ __html: viewingItem.content }} />
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Create / Edit Modal */}
            {showModal && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-5xl flex flex-col max-h-[95dvh] sm:max-h-[90dvh] rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
                        <div className="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>

                        <div className="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່'}</p>
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">{editId ? 'ແກ້ໄຂຂ່າວ' : 'ເພີ່ມຂ່າວໃໝ່'}</h2>
                            </div>
                            <button onClick={() => setShowModal(false)} aria-label="ປິດ"
                                className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <form onSubmit={submit} className="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ຫົວຂໍ້ຂ່າວ <span className="text-red-500">*</span>
                                </label>
                                <input type="text" value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} placeholder="ຫົວຂໍ້ຂ່າວ"
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.title ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                {form.errors.title && <p className="text-red-500 text-xs mt-1">{form.errors.title}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ໝວດໝູ່ <span className="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                                </label>
                                <select value={form.data.category_id} onChange={(e) => form.setData('category_id', e.target.value)}
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                                    <option value="">— ບໍ່ມີໝວດໝູ່ —</option>
                                    {categories.map((category) => (
                                        <option key={category.id} value={category.id}>{category.name}</option>
                                    ))}
                                </select>
                                {form.errors.category_id && <p className="text-red-500 text-xs mt-1">{form.errors.category_id}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ຄຳໂປຍຫຍໍ້ <span className="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                                </label>
                                <textarea value={form.data.excerpt} onChange={(e) => form.setData('excerpt', e.target.value)} rows={2} placeholder="ສະຫຼຸບຫຍໍ້ຂອງຂ່າວ..."
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow resize-none"></textarea>
                                {form.errors.excerpt && <p className="text-red-500 text-xs mt-1">{form.errors.excerpt}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ເນື້ອຫາ <span className="text-red-500">*</span>
                                </label>
                                <RichEditor
                                    key={`news-content-editor-${editorNonce}`}
                                    value={form.data.content}
                                    onChange={(html) => form.setData('content', html)}
                                    placeholder="ເນື້ອຫາຂ່າວ..."
                                />
                                {form.errors.content && <p className="text-red-500 text-xs mt-1">{form.errors.content}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ຮູບພາບປົກ <span className="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                                </label>

                                {existingImageUrl && !form.data.image && (
                                    <div className="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200 mb-2.5">
                                        <img src={existingImageUrl} alt="ຮູບປັດຈຸບັນ" className="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0" />
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs font-medium text-slate-800">ຮູບພາບປັດຈຸບັນ</p>
                                            <p className="text-xs text-gray-400 mt-0.5">ເລືອກໄຟລ໌ໃໝ່ເພື່ອປ່ຽນແທນ</p>
                                        </div>
                                        <button type="button" onClick={removeCurrentImage}
                                            className="flex-shrink-0 text-xs text-red-500 hover:text-red-600 font-medium transition-colors px-2 py-1">
                                            ລຶບຮູບ
                                        </button>
                                    </div>
                                )}

                                <div
                                    onDragOver={(e) => { e.preventDefault(); setDragging(true); }}
                                    onDragLeave={(e) => { e.preventDefault(); setDragging(false); }}
                                    onDrop={handleDrop}
                                    className={`relative rounded-2xl border-2 border-dashed transition-colors duration-150 cursor-pointer ${dragging ? 'border-brand-green bg-brand-light-green' : 'border-gray-200 bg-[#f8fafa]'}`}
                                >
                                    <input ref={fileInputRef} type="file" accept="image/*" onChange={handleFileInput}
                                        className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />

                                    <div className="flex flex-col items-center justify-center py-6 px-4 text-center pointer-events-none">
                                        <svg className="w-7 h-7 text-gray-400 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                        <p className="text-sm text-gray-400">ລາກໄຟລ໌ມາວາງ ຫຼື <span className="text-brand-green font-medium">ຄລິກເລືອກໄຟລ໌</span></p>
                                        <p className="text-xs text-gray-400/70 mt-1">JPG, PNG, GIF — ສູງສຸດ 2MB</p>
                                    </div>
                                </div>

                                {form.errors.image && <p className="text-red-500 text-xs mt-1.5">{form.errors.image}</p>}

                                {form.data.image && (
                                    <div className="flex items-center gap-3 p-3 bg-[#f8fafa] rounded-xl border border-gray-200 mt-2.5">
                                        <img src={imagePreview} alt="ຕົວຢ່າງ" className="w-14 h-14 object-cover rounded-lg border border-gray-200 flex-shrink-0" />
                                        <div className="flex-1 min-w-0">
                                            <p className="text-xs font-medium text-slate-800 truncate">{form.data.image.name}</p>
                                            <p className="text-xs text-gray-400">{(form.data.image.size / 1024).toFixed(1)} KB</p>
                                        </div>
                                        <button type="button" onClick={cancelNewImage}
                                            className="flex-shrink-0 text-xs text-red-500 hover:text-red-600 font-medium transition-colors px-2 py-1">
                                            ຍົກເລີກ
                                        </button>
                                    </div>
                                )}
                            </div>

                            <label className="flex items-center justify-between gap-3 cursor-pointer w-full p-3.5 rounded-2xl border border-gray-200 bg-[#f8fafa]">
                                <span>
                                    <span className="block text-sm font-medium text-slate-800">ເຜີຍແຜ່ທັນທີ</span>
                                    <span className="block text-xs text-gray-400 mt-0.5">
                                        {form.data.publishNow ? 'ຂ່າວຈະສະແດງຢູ່ໜ້າ Frontend ທັນທີທີ່ບັນທຶກ' : 'ບັນທຶກໄວ້ເປັນຮ່າງ, ຍັງບໍ່ເຜີຍແຜ່'}
                                    </span>
                                </span>
                                <span className="inline-flex flex-shrink-0">
                                    <input type="checkbox" className="sr-only peer" checked={form.data.publishNow}
                                        onChange={(e) => form.setData('publishNow', e.target.checked)} />
                                    <div className="relative w-10 h-6 rounded-full transition-colors duration-200 bg-gray-200 peer-checked:bg-brand-green after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:w-4 after:h-4 after:transition-transform after:duration-200 peer-checked:after:translate-x-4 shrink-0"></div>
                                </span>
                            </label>

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
                                className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
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
                            <p className="text-slate-800 text-sm font-bold mb-1">ລຶບຂ່າວນີ້</p>
                            <p className="text-gray-400 text-xs mb-6">ຂ່າວນີ້ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                            <div className="flex justify-center gap-3">
                                <button onClick={() => setShowDeleteModal(false)}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                                    ຍົກເລີກ
                                </button>
                                <button onClick={doDelete}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl text-sm font-semibold hover:bg-red-600 transition-colors">
                                    ລຶບຂ່າວ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
