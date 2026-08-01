import { router, useForm, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import RichEditor from '@/Components/RichEditor';

const emptyForm = { title: '', content: '', category_id: '' };

export default function ChantsIndex({ filters, chants, categories }) {
    const { auth } = usePage().props;
    const isAdmin = !!auth?.user?.isAdmin;

    const [search, setSearch] = useState(filters.search);
    const [filterCategory, setFilterCategory] = useState(filters.category);
    const debounceRef = useRef(null);
    const firstRun = useRef(true);

    useEffect(() => {
        if (firstRun.current) {
            firstRun.current = false;
            return;
        }
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('chants.index'), { search, category: filterCategory }, { preserveState: true, preserveScroll: true, replace: true });
        }, 300);
        return () => clearTimeout(debounceRef.current);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, filterCategory]);

    const [showModal, setShowModal] = useState(false);
    const [showViewModal, setShowViewModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [editId, setEditId] = useState(null);
    const [viewing, setViewing] = useState(null);
    const [deleteId, setDeleteId] = useState(null);
    const [editorNonce, setEditorNonce] = useState(0);
    const form = useForm(emptyForm);

    function openCreate() {
        setEditId(null);
        form.clearErrors();
        form.setData({ ...emptyForm });
        setEditorNonce((n) => n + 1);
        setShowModal(true);
    }

    function openEdit(item) {
        setEditId(item.id);
        form.clearErrors();
        form.setData({
            title: item.title,
            content: item.content_html,
            category_id: item.category_id ?? '',
        });
        setEditorNonce((n) => n + 1);
        setShowModal(true);
    }

    function openView(item) {
        setViewing(item);
        setShowViewModal(true);
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => setShowModal(false);
        if (editId) {
            form.put(route('chants.update', editId), { onSuccess, preserveScroll: true });
        } else {
            form.post(route('chants.store'), { onSuccess, preserveScroll: true });
        }
    }

    function confirmDelete(id) {
        setDeleteId(id);
        setShowDeleteModal(true);
    }

    function doDelete() {
        if (!deleteId) return;
        router.delete(route('chants.destroy', deleteId), {
            preserveScroll: true,
            onSuccess: () => { setShowDeleteModal(false); setDeleteId(null); },
        });
    }

    return (
        <AppLayout title="ບົດສູດມົນ">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ຄຳສອນ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ບົດສູດມົນ</h1>
                    <p className="text-gray-400 text-sm mt-1">ຈັດການບົດສູດມົນ ແລະ ໝວດໝູ່ຕ່າງໆ</p>
                </div>
                <div className="w-full sm:w-auto flex items-center gap-2.5">
                    <a href={route('chants.public.index')} target="_blank" rel="noopener"
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
                            ເພີ່ມບົດສູດມົນ
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
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="ຄົ້ນຫາຫົວຂໍ້ບົດສູດມົນ..."
                        className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                </div>
                <select value={filterCategory} onChange={(e) => setFilterCategory(e.target.value)}
                    className="bg-[#f8fafa] border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 sm:w-auto focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow">
                    <option value="">ທຸກໝວດໝູ່</option>
                    {categories.map((category) => (
                        <option key={category.id} value={category.id}>
                            {'— '.repeat(category.depth)}{category.name}
                        </option>
                    ))}
                </select>
            </div>

            {/* ══════════════════════════════════════════════════════════ */}
            {/* MOBILE: Compact list (visible below md)                    */}
            {/* ══════════════════════════════════════════════════════════ */}
            <div className="md:hidden space-y-3">
                {chants.data.length === 0 ? (
                    <div className="bg-white rounded-3xl card-shadow py-16 text-center">
                        <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                            <span className="text-lg text-brand-green select-none">☸</span>
                        </div>
                        <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີບົດສູດມົນ</p>
                        <p className="text-gray-400 text-sm">
                            {isAdmin ? 'ກົດ "ເພີ່ມບົດສູດມົນ" ເພື່ອເພີ່ມໃໝ່' : 'ຍັງບໍ່ມີບົດສູດມົນໃນຂະນະນີ້'}
                        </p>
                    </div>
                ) : chants.data.map((item) => (
                    <div key={item.id} className="bg-white rounded-2xl card-shadow overflow-hidden">
                        <button onClick={() => openView(item)} className="w-full flex items-center gap-3 px-4 py-3.5 text-left">
                            <div className="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 bg-brand-light-green flex items-center justify-center">
                                <span className="text-lg text-brand-green">☸</span>
                            </div>
                            <div className="flex-1 min-w-0">
                                <span className="font-bold text-slate-800 text-sm leading-tight truncate block">{item.title}</span>
                                <p className="text-gray-400 text-xs mt-0.5 truncate">
                                    {item.category?.name ?? 'ບໍ່ມີໝວດໝູ່'}
                                </p>
                            </div>
                        </button>

                        {isAdmin && (
                            <div className="flex items-stretch border-t border-gray-100">
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

                {chants.data.length > 0 && <div className="pt-1"><Pagination links={chants.links} /></div>}
            </div>

            {/* ══════════════════════════════════════════════════════════ */}
            {/* DESKTOP: Full table (visible md+)                          */}
            {/* ══════════════════════════════════════════════════════════ */}
            <div className="hidden md:block bg-white rounded-2xl card-shadow overflow-hidden">
                <table className="w-full text-sm table-fixed">
                    <thead>
                        <tr className="bg-gray-50 border-b border-gray-100">
                            <th className="px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຫົວຂໍ້ບົດສູດມົນ</th>
                            <th className="w-48 px-4 py-3.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wide">ໝວດໝູ່</th>
                            <th className="w-32 px-4 py-3.5 text-center text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຈັດການ</th>
                        </tr>
                    </thead>
                    <tbody>
                        {chants.data.length === 0 ? (
                            <tr>
                                <td colSpan={3} className="px-4 py-16 text-center">
                                    <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mx-auto mb-4">
                                        <span className="text-2xl text-brand-green select-none">☸</span>
                                    </div>
                                    <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີບົດສູດມົນ</p>
                                    <p className="text-gray-400 text-sm">
                                        {isAdmin ? 'ກົດ "ເພີ່ມບົດສູດມົນ" ເພື່ອເພີ່ມໃໝ່' : 'ຍັງບໍ່ມີບົດສູດມົນໃນຂະນະນີ້'}
                                    </p>
                                </td>
                            </tr>
                        ) : chants.data.map((item) => (
                            <tr key={item.id} className="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                <td className="px-4 py-3.5 overflow-hidden">
                                    <button onClick={() => openView(item)} className="text-left block w-full min-w-0">
                                        <p className="font-bold text-slate-800 leading-snug hover:text-brand-green transition-colors truncate">{item.title}</p>
                                    </button>
                                </td>
                                <td className="px-4 py-3.5">
                                    {item.category ? (
                                        <span className="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600">
                                            {item.category.name}
                                        </span>
                                    ) : (
                                        <span className="text-gray-400 text-xs">ບໍ່ມີໝວດໝູ່</span>
                                    )}
                                </td>
                                <td className="px-4 py-3.5">
                                    <div className="flex justify-center gap-1">
                                        {isAdmin ? (
                                            <>
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
                <div className="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    <Pagination links={chants.links} />
                </div>
            </div>

            {/* View Modal */}
            {showViewModal && viewing && (
                <div onClick={(e) => e.target === e.currentTarget && setShowViewModal(false)}
                    className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-5xl flex flex-col max-h-[95dvh] sm:max-h-[90dvh] rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

                        <div className="sm:hidden flex justify-center pt-3 pb-1 flex-shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>

                        <div className="flex-shrink-0 px-6 py-4 flex items-center justify-between border-b border-gray-100">
                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລາຍລະອຽດບົດສູດມົນ</p>
                            <button onClick={() => setShowViewModal(false)} aria-label="ປິດ"
                                className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <div className="flex-1 overflow-y-auto">
                            <div className="px-6 py-5">
                                <div className="flex items-center gap-2 flex-wrap mb-2">
                                    {viewing.category && (
                                        <span className="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600">
                                            {viewing.category.name}
                                        </span>
                                    )}
                                </div>
                                <h2 className="text-xl font-bold text-slate-800 leading-snug">{viewing.title}</h2>
                                <div className="prose prose-sm max-w-none mt-4 text-slate-600 leading-loose"
                                    dangerouslySetInnerHTML={{ __html: viewing.content_html }} />
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
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    {editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່'}
                                </p>
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">
                                    {editId ? 'ແກ້ໄຂບົດສູດມົນ' : 'ເພີ່ມບົດສູດມົນໃໝ່'}
                                </h2>
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
                                    ຫົວຂໍ້ບົດສູດມົນ <span className="text-red-500">*</span>
                                </label>
                                <input type="text" value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} placeholder="ຫົວຂໍ້ບົດສູດມົນ"
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.title ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                {form.errors.title && <p className="text-red-500 text-xs mt-1">{form.errors.title}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ໝວດໝູ່ <span className="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                                </label>
                                <select value={form.data.category_id ?? ''}
                                    onChange={(e) => form.setData('category_id', e.target.value ? Number(e.target.value) : '')}
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.category_id ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`}>
                                    <option value="">— ບໍ່ມີໝວດໝູ່ —</option>
                                    {categories.map((category) => (
                                        <option key={category.id} value={category.id}>
                                            {'— '.repeat(category.depth)}{category.name}
                                        </option>
                                    ))}
                                </select>
                                {form.errors.category_id && <p className="text-red-500 text-xs mt-1">{form.errors.category_id}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ເນື້ອໃນ <span className="text-red-500">*</span>
                                </label>
                                <RichEditor
                                    key={editorNonce}
                                    value={form.data.content}
                                    onChange={(html) => form.setData('content', html)}
                                    placeholder="ເນື້ອໃນບົດສູດມົນ..."
                                />
                                {form.errors.content && <p className="text-red-500 text-xs mt-1">{form.errors.content}</p>}
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

            {/* Delete Confirm Modal */}
            {showDeleteModal && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-5xl rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

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
                            <p className="text-slate-800 text-sm font-bold mb-1">ລຶບບົດສູດມົນນີ້</p>
                            <p className="text-gray-400 text-xs mb-6">ບົດສູດມົນນີ້ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                            <div className="flex justify-center gap-3">
                                <button onClick={() => setShowDeleteModal(false)}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                                    ຍົກເລີກ
                                </button>
                                <button onClick={doDelete}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl text-sm font-semibold hover:bg-red-600 transition-colors">
                                    ລຶບ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
