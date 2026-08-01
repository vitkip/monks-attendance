import { router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

const emptyForm = { name: '' };

export default function NewsCategoriesIndex({ categories }) {
    const [showModal, setShowModal] = useState(false);
    const [editId, setEditId] = useState(null);
    const form = useForm(emptyForm);

    function openCreate() {
        setEditId(null);
        form.clearErrors();
        form.setData({ ...emptyForm });
        setShowModal(true);
    }

    function openEdit(category) {
        setEditId(category.id);
        form.clearErrors();
        form.setData({ name: category.name });
        setShowModal(true);
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => setShowModal(false);
        if (editId) {
            form.put(route('news-categories.update', editId), { onSuccess, preserveScroll: true });
        } else {
            form.post(route('news-categories.store'), { onSuccess, preserveScroll: true });
        }
    }

    function destroy(category) {
        if (!confirm(`ຢືນຢັນການລຶບໝວດໝູ່ '${category.name}'?`)) return;
        router.delete(route('news-categories.destroy', category.id), { preserveScroll: true });
    }

    return (
        <AppLayout title="ໝວດໝູ່ຂ່າວ">
            <div className="flex items-start justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ການຈັດການ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ໝວດໝູ່ຂ່າວ</h1>
                    <p className="text-gray-400 text-sm mt-1">ຈັດການໝວດໝູ່ສຳລັບຈັດປະເພດຂ່າວສານ</p>
                </div>
                <button onClick={openCreate}
                    className="shrink-0 flex items-center gap-2 px-4 py-2.5 sm:px-5 sm:py-3 bg-brand-green text-white rounded-2xl text-sm font-semibold hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 touch-manipulation">
                    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-4 h-4 shrink-0">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M10 4v12M4 10h12" />
                    </svg>
                    <span className="hidden sm:inline">ເພີ່ມໝວດໝູ່</span>
                    <span className="sm:hidden">ເພີ່ມ</span>
                </button>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {categories.length === 0 ? (
                    <div className="col-span-full bg-white rounded-3xl card-shadow flex flex-col items-center justify-center py-16 text-center">
                        <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mb-4">
                            <svg className="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z" />
                            </svg>
                        </div>
                        <p className="font-bold text-slate-800 mb-1">ຍັງບໍ່ມີໝວດໝູ່ຂ່າວ</p>
                        <p className="text-gray-400 text-sm">ກົດ "ເພີ່ມໝວດໝູ່" ເພື່ອຕັ້ງຄ່າໃໝ່</p>
                    </div>
                ) : categories.map((category) => (
                    <div key={category.id} className="bg-white rounded-2xl card-interactive border border-gray-50 flex flex-col overflow-hidden">
                        <div className="p-5 flex-1">
                            <p className="text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ໝວດໝູ່ຂ່າວ</p>
                            <h3 className="font-bold text-slate-800 text-base leading-snug">{category.name}</h3>

                            <div className="flex items-center gap-1.5 mt-3">
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.5" className="w-3.5 h-3.5 text-gray-300 shrink-0">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z" />
                                </svg>
                                <span className="text-xs text-gray-400">ໃຊ້ {category.news_count} ຂ່າວ</span>
                            </div>
                        </div>
                        <div className="border-t border-gray-100 flex" style={{ minHeight: 48 }}>
                            <button onClick={() => openEdit(category)} className="flex-1 flex items-center justify-center gap-1.5 text-sm text-slate-600 hover:bg-gray-50 py-3 transition-colors touch-manipulation">
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4 shrink-0">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13.586 3.586a2 2 0 112.828 2.828l-8 8a1 1 0 01-.464.263l-3 .75a.5.5 0 01-.607-.607l.75-3a1 1 0 01.263-.464l8-8z" />
                                </svg>
                                ແກ້ໄຂ
                            </button>
                            <div className="w-px bg-gray-100 self-stretch"></div>
                            <button onClick={() => destroy(category)} className="flex-1 flex items-center justify-center gap-1.5 text-sm text-red-500 hover:bg-red-50 py-3 transition-colors touch-manipulation">
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.75" className="w-4 h-4 shrink-0">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 3h2M3 6h14m-2 0V17a1 1 0 01-1 1H6a1 1 0 01-1-1V6h10zm-6 4v5m4-5v5" />
                                </svg>
                                ລຶບ
                            </button>
                        </div>
                    </div>
                ))}
            </div>

            {showModal && (
                <div onClick={(e) => e.target === e.currentTarget && setShowModal(false)}
                    className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden max-h-[95dvh] sm:max-h-[90vh] flex flex-col">
                        <div className="px-6 py-4 flex items-center justify-between shrink-0 border-b border-gray-100">
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່'}</p>
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">ໝວດໝູ່ຂ່າວ</h2>
                            </div>
                            <button onClick={() => setShowModal(false)} aria-label="ປິດ" className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-slate-700 hover:bg-gray-100 transition-colors touch-manipulation">
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-4 h-4">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <div className="sm:hidden flex justify-center pt-3 pb-0 shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>

                        <form onSubmit={submit} className="px-6 py-5 space-y-5 overflow-y-auto">
                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ຊື່ໝວດໝູ່ <span className="text-red-500">*</span></label>
                                <input type="text" value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} placeholder="ເຊັ່ນ: ຂ່າວທົ່ວໄປ, ກິດຈະກຳວັດ, ປະກາດ..."
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.name ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                {form.errors.name && <p className="text-red-500 text-xs mt-1.5">{form.errors.name}</p>}
                            </div>

                            <div className="flex justify-end gap-3 pb-1 pt-1">
                                <button type="button" onClick={() => setShowModal(false)}
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
