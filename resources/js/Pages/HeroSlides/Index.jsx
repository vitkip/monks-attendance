import { router, useForm } from '@inertiajs/react';
import { useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

const emptyForm = { title: '', subtitle: '', link_url: '', button_text: '', image: null, status: true };

export default function HeroSlidesIndex({ slides }) {
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

    function openEdit(slide) {
        setEditId(slide.id);
        setCurrentImageUrl(slide.image_url || '');
        form.clearErrors();
        form.setData({
            title: slide.title,
            subtitle: slide.subtitle || '',
            link_url: slide.link_url || '',
            button_text: slide.button_text || '',
            image: null,
            status: slide.status,
        });
        setShowModal(true);
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => setShowModal(false);
        if (editId) {
            form.transform((data) => ({ ...data, _method: 'put' })).post(route('hero-slides.update', editId), { onSuccess, preserveScroll: true, forceFormData: true });
        } else {
            form.post(route('hero-slides.store'), { onSuccess, preserveScroll: true, forceFormData: true });
        }
    }

    function togglePublish(slide) {
        router.patch(route('hero-slides.toggle-publish', slide.id), {}, { preserveScroll: true });
    }

    function moveUp(slide) {
        router.patch(route('hero-slides.move-up', slide.id), {}, { preserveScroll: true });
    }

    function moveDown(slide) {
        router.patch(route('hero-slides.move-down', slide.id), {}, { preserveScroll: true });
    }

    function confirmDelete(id) {
        setDeleteId(id);
        setShowDeleteModal(true);
    }

    function doDelete() {
        if (!deleteId) return;
        router.delete(route('hero-slides.destroy', deleteId), {
            preserveScroll: true,
            onSuccess: () => { setShowDeleteModal(false); setDeleteId(null); },
        });
    }

    return (
        <AppLayout title="ສະໄລ້ Hero">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ໜ້າຫຼັກ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ສະໄລ້ Hero</h1>
                    <p className="text-gray-400 text-sm mt-1">ຈັດການຮູບພາບ ແລະ ຂໍ້ຄວາມທີ່ສະແດງເປັນສະໄລ້ຢູ່ໜ້າຫຼັກ Frontend</p>
                </div>
                <div className="w-full sm:w-auto flex items-center gap-2.5">
                    <a href={route('news.public.index')} target="_blank" rel="noopener"
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
                        ເພີ່ມສະໄລ້
                    </button>
                </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {slides.length === 0 ? (
                    <div className="col-span-full bg-white rounded-3xl card-shadow flex flex-col items-center justify-center py-16 text-center">
                        <div className="w-14 h-14 rounded-full bg-brand-light-green flex items-center justify-center mb-4">
                            <svg className="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" />
                            </svg>
                        </div>
                        <p className="text-slate-800 font-bold mb-1">ຍັງບໍ່ມີສະໄລ້</p>
                        <p className="text-gray-400 text-sm">ກົດ "ເພີ່ມສະໄລ້" ເພື່ອສ້າງສະໄລ້ທຳອິດສຳລັບໜ້າຫຼັກ</p>
                    </div>
                ) : slides.map((slide, i) => (
                    <div key={slide.id} className="bg-white rounded-2xl card-interactive border border-gray-50 flex flex-col overflow-hidden">
                        <div className="relative aspect-video w-full bg-gray-100 overflow-hidden">
                            <img src={slide.image_url} alt={slide.title} className="w-full h-full object-cover" />

                            <span className="absolute bottom-2.5 left-2.5 w-6 h-6 flex items-center justify-center rounded-full bg-black/50 backdrop-blur-sm text-white text-[11px] font-bold">
                                {i + 1}
                            </span>

                            <span className={`absolute bottom-2.5 right-2.5 px-2.5 py-1 rounded-full text-[10px] font-bold shadow-sm ${slide.status ? 'bg-brand-light-green text-brand-green' : 'bg-white/90 text-gray-500'}`}>
                                {slide.status ? 'ເຜີຍແຜ່ແລ້ວ' : 'ຮ່າງ'}
                            </span>

                            <div className="absolute top-2.5 right-2.5 flex flex-col gap-1.5">
                                <button onClick={() => moveUp(slide)} disabled={i === 0} aria-label="ຍ້າຍຂຶ້ນ"
                                    className="w-7 h-7 flex items-center justify-center rounded-lg bg-white/90 backdrop-blur-sm text-slate-500 shadow-sm hover:bg-white hover:text-slate-700 transition-colors disabled:opacity-40 disabled:pointer-events-none">
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 12l5-5 5 5" />
                                    </svg>
                                </button>
                                <button onClick={() => moveDown(slide)} disabled={i === slides.length - 1} aria-label="ຍ້າຍລົງ"
                                    className="w-7 h-7 flex items-center justify-center rounded-lg bg-white/90 backdrop-blur-sm text-slate-500 shadow-sm hover:bg-white hover:text-slate-700 transition-colors disabled:opacity-40 disabled:pointer-events-none">
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 8l5 5 5-5" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div className="p-4 flex-1">
                            <p className="font-bold text-slate-800 text-sm leading-snug">{slide.title}</p>
                            {slide.subtitle && <p className="text-gray-400 text-xs mt-1 line-clamp-2">{slide.subtitle}</p>}
                            {slide.link_url && <p className="text-gray-300 text-[11px] mt-1.5 truncate">{slide.link_url}</p>}
                        </div>

                        <div className="border-t border-gray-100 flex" style={{ minHeight: 44 }}>
                            <button onClick={() => togglePublish(slide)} aria-label={slide.status ? 'ເກັບຮ່າງ' : 'ເຜີຍແຜ່'}
                                className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-500 hover:bg-gray-50 py-2.5 transition-colors touch-manipulation">
                                <svg className="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="1.75">
                                    {slide.status ? (
                                        <>
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M3 12s3-7 7-7 7 7 7 7-3 7-7 7-7-7-7-7z" />
                                            <circle cx="10" cy="12" r="2.5" strokeLinecap="round" strokeLinejoin="round" />
                                        </>
                                    ) : (
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M3.5 3.5l13 13M8.3 5.2A7.7 7.7 0 0110 5c4 0 7 7 7 7a13 13 0 01-2.3 3.1M6.2 6.2C4 7.8 3 10 3 10s3 7 7 7c1 0 1.9-.2 2.7-.5" />
                                    )}
                                </svg>
                                {slide.status ? 'ເກັບຮ່າງ' : 'ເຜີຍແຜ່'}
                            </button>
                            <div className="w-px bg-gray-100 self-stretch"></div>
                            <button onClick={() => openEdit(slide)} aria-label="ແກ້ໄຂ"
                                className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-slate-500 hover:bg-gray-50 py-2.5 transition-colors touch-manipulation">
                                <svg className="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                ແກ້ໄຂ
                            </button>
                            <div className="w-px bg-gray-100 self-stretch"></div>
                            <button onClick={() => confirmDelete(slide.id)} aria-label="ລຶບ"
                                className="flex-1 flex items-center justify-center gap-1.5 text-xs font-medium text-red-500 hover:bg-red-50 py-2.5 transition-colors touch-manipulation">
                                <svg className="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                ລຶບ
                            </button>
                        </div>
                    </div>
                ))}
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
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">{editId ? 'ແກ້ໄຂສະໄລ້' : 'ເພີ່ມສະໄລ້ໃໝ່'}</h2>
                            </div>
                            <button onClick={() => setShowModal(false)} aria-label="ປິດ" className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        <form onSubmit={submit} className="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຫົວຂໍ້ <span className="text-red-500">*</span></label>
                                <input type="text" value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} placeholder="ຫົວຂໍ້ສະໄລ້"
                                    className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.title ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                {form.errors.title && <p className="text-red-500 text-xs mt-1">{form.errors.title}</p>}
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                    ຄຳບັນຍາຍ <span className="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                                </label>
                                <textarea rows="2" value={form.data.subtitle} onChange={(e) => form.setData('subtitle', e.target.value)} placeholder="ຂໍ້ຄວາມສັ້ນໆໃຕ້ຫົວຂໍ້..."
                                    className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow resize-none"></textarea>
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                        ລິ້ງ <span className="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                                    </label>
                                    <input type="text" value={form.data.link_url} onChange={(e) => form.setData('link_url', e.target.value)} placeholder="https://..."
                                        className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition-shadow ${form.errors.link_url ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                                    {form.errors.link_url && <p className="text-red-500 text-xs mt-1">{form.errors.link_url}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">
                                        ຂໍ້ຄວາມປຸ່ມ <span className="text-[10px] normal-case text-gray-400">(ບໍ່ບັງຄັບ)</span>
                                    </label>
                                    <input type="text" value={form.data.button_text} onChange={(e) => form.setData('button_text', e.target.value)} placeholder="ອ່ານຕໍ່"
                                        className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                                </div>
                            </div>

                            <div>
                                <label className="block text-[10px] font-medium text-gray-400 mb-1.5 uppercase tracking-widest">ຮູບພາບສະໄລ້ <span className="text-red-500">*</span></label>

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
                                        <p className="text-xs text-gray-400/70 mt-1">ແນະນຳອັດຕາສ່ວນ 16:9 — JPG, PNG — ສູງສຸດ 2MB</p>
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

                            <label className="flex items-center justify-between gap-3 cursor-pointer w-full p-3.5 rounded-2xl border border-gray-200 bg-[#f8fafa]">
                                <span>
                                    <span className="block text-sm font-medium text-slate-800">ເຜີຍແຜ່ທັນທີ</span>
                                    <span className="block text-xs text-gray-400 mt-0.5">
                                        {form.data.status ? 'ສະໄລ້ຈະສະແດງຢູ່ໜ້າຫຼັກທັນທີທີ່ບັນທຶກ' : 'ບັນທຶກໄວ້ເປັນຮ່າງ, ຍັງບໍ່ສະແດງ'}
                                    </span>
                                </span>
                                <span className="inline-flex flex-shrink-0">
                                    <input type="checkbox" checked={form.data.status} onChange={(e) => form.setData('status', e.target.checked)} className="sr-only peer" />
                                    <div className="relative w-10 h-6 rounded-full transition-colors duration-200 bg-gray-200 peer-checked:bg-brand-green
                                                    after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:w-4 after:h-4
                                                    after:transition-transform after:duration-200 peer-checked:after:translate-x-4 shrink-0"></div>
                                </span>
                            </label>

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
                            <button onClick={() => setShowDeleteModal(false)} aria-label="ປິດ" className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 transition-all">
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
                            <p className="text-slate-800 text-sm font-bold mb-1">ລຶບສະໄລ້ນີ້</p>
                            <p className="text-gray-400 text-xs mb-6">ສະໄລ້ນີ້ຈະຖືກລຶບອອກຈາກລະບົບຢ່າງຖາວອນ</p>
                            <div className="flex justify-center gap-3">
                                <button onClick={() => setShowDeleteModal(false)}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm text-slate-600 hover:bg-gray-50 transition-colors">
                                    ຍົກເລີກ
                                </button>
                                <button onClick={doDelete}
                                    className="flex-1 sm:flex-none px-5 py-2.5 bg-red-500 text-white rounded-2xl text-sm font-semibold hover:bg-red-600 transition-colors">
                                    ລຶບສະໄລ້
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
