import { useState } from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';

export default function CbfolCommitteesIndex({
    filters,
    committees,
    categories,
    terms,
    stats,
}) {
    const [modalOpen, setModalOpen] = useState(false);
    const [editingItem, setEditingItem] = useState(null);
    const [deleteConfirmItem, setDeleteConfirmItem] = useState(null);
    const [photoPreview, setPhotoPreview] = useState(null);

    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({
        name: '',
        title: '',
        category: 'president',
        term: 'ສະໄໝທີ IX',
        term_year: '23 ທັນວາ 2568 (ຄສ 2025)',
        organization: '',
        phone: '',
        photo: null,
        photo_url_input: '',
        remove_photo: false,
        bio: '',
        sort_order: 1,
        status: 'active',
    });

    const openCreateModal = () => {
        setEditingItem(null);
        reset();
        clearErrors();
        setData({
            name: '',
            title: '',
            category: filters.category || 'president',
            term: filters.term || 'ສະໄໝທີ IX',
            term_year: '23 ທັນວາ 2568 (ຄສ 2025)',
            organization: '',
            phone: '',
            photo: null,
            photo_url_input: '',
            remove_photo: false,
            bio: '',
            sort_order: 1,
            status: 'active',
        });
        setPhotoPreview(null);
        setModalOpen(true);
    };

    const formatPhotoUrl = (url) => {
        if (!url) return '';
        const trimmed = url.trim();
        const driveMatch = trimmed.match(/drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?(?:.*&)?id=)([a-zA-Z0-9_-]+)/);
        if (driveMatch && driveMatch[1]) {
            return `https://lh3.googleusercontent.com/d/${driveMatch[1]}`;
        }
        return trimmed;
    };

    const compressImage = (file, maxWidth = 1400, maxHeight = 1400, quality = 0.85) => {
        return new Promise((resolve) => {
            if (!file || !file.type || !file.type.startsWith('image/')) {
                resolve(file);
                return;
            }
            if (file.type === 'image/svg+xml') {
                resolve(file);
                return;
            }
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    let { width, height } = img;
                    if (width <= maxWidth && height <= maxHeight && file.size <= 1.5 * 1024 * 1024) {
                        resolve(file);
                        return;
                    }
                    if (width > height) {
                        if (width > maxWidth) {
                            height = Math.round((height * maxWidth) / width);
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width = Math.round((width * maxHeight) / height);
                            height = maxHeight;
                        }
                    }
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    canvas.toBlob(
                        (blob) => {
                            if (!blob) {
                                resolve(file);
                                return;
                            }
                            const compressed = new File([blob], file.name.replace(/\.[^.]+$/, '.jpg'), {
                                type: 'image/jpeg',
                                lastModified: Date.now(),
                            });
                            resolve(compressed);
                        },
                        'image/jpeg',
                        quality
                    );
                };
                img.onerror = () => resolve(file);
            };
            reader.onerror = () => resolve(file);
        });
    };

    const openEditModal = (item) => {
        setEditingItem(item);
        clearErrors();
        setData({
            name: item.name || '',
            title: item.title || '',
            category: item.category || 'president',
            term: item.term || 'ສະໄໝທີ IX',
            term_year: item.term_year || '',
            organization: item.organization || '',
            phone: item.phone || '',
            photo: null,
            photo_url_input: (item.photo && item.photo.startsWith('http')) ? item.photo : '',
            remove_photo: false,
            bio: item.bio || '',
            sort_order: item.sort_order ?? 1,
            status: item.status || 'active',
        });
        setPhotoPreview(item.photo_url || null);
        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setEditingItem(null);
        setPhotoPreview(null);
        reset();
        clearErrors();
    };

    const handlePhotoChange = async (e) => {
        const file = e.target.files?.[0];
        if (file) {
            try {
                const processed = await compressImage(file);
                setData((prev) => ({
                    ...prev,
                    photo: processed,
                    photo_url_input: '',
                    remove_photo: false,
                }));
                setPhotoPreview(URL.createObjectURL(processed));
            } catch (err) {
                setData((prev) => ({
                    ...prev,
                    photo: file,
                    photo_url_input: '',
                    remove_photo: false,
                }));
                setPhotoPreview(URL.createObjectURL(file));
            }
        }
    };

    const handleUrlChange = (e) => {
        const val = e.target.value;
        const formatted = formatPhotoUrl(val);
        setData((prev) => ({
            ...prev,
            photo_url_input: val,
            photo: null,
            remove_photo: false,
        }));
        if (formatted) {
            setPhotoPreview(formatted);
        } else if (!data.photo) {
            setPhotoPreview(null);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editingItem) {
            post(route('cbfol-committees.update', editingItem.id), {
                forceFormData: true,
                onSuccess: () => closeModal(),
            });
        } else {
            post(route('cbfol-committees.store'), {
                forceFormData: true,
                onSuccess: () => closeModal(),
            });
        }
    };

    const handleDelete = (item) => {
        router.delete(route('cbfol-committees.destroy', item.id), {
            onSuccess: () => setDeleteConfirmItem(null),
        });
    };

    const handleFilterChange = (key, value) => {
        router.get(
            route('cbfol-committees.index'),
            {
                ...filters,
                [key]: value || undefined,
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    };

    const categoryBadge = (catKey) => {
        const cat = categories[catKey];
        if (!cat) return null;
        return (
            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${cat.badge_color}`}>
                {cat.label}
            </span>
        );
    };

    return (
        <AppLayout title="ຈັດການຄະນະກຳມະການສູນກາງ ອພສ">
            <div className="space-y-6">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-3">
                            <img src="/logo.png" alt="Logo" className="w-14 h-14 sm:w-16 sm:h-16 object-contain flex-shrink-0" />
                            <h1 className="text-xl sm:text-2xl font-bold text-slate-800">
                                ຄະນະກຳມະການສູນກາງ ອພສ
                            </h1>
                        </div>
                        <p className="text-xs sm:text-sm text-gray-500 mt-1">
                            ສູນກາງອົງການພຸດທະສາສະໜາສັມພັນ ແຫ່ງ ສປປ ລາວ (CBFOL)
                        </p>
                    </div>

                    <div className="flex items-center gap-2">
                        <a
                            href={route('cbfol-committees.public.index')}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white border border-gray-200 text-slate-700 hover:bg-gray-50 transition-colors shadow-sm"
                        >
                            <span>ເບິ່ງໜ້າສະແດງຜົນ</span>
                            <svg className="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>

                        <button
                            type="button"
                            onClick={openCreateModal}
                            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-brand-green text-white hover:bg-brand-green/90 transition-colors shadow-sm"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>ເພີ່ມກຳມະການໃໝ່</span>
                        </button>
                    </div>
                </div>

                {/* Stat Cards */}
                <div className="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    <button
                        type="button"
                        onClick={() => handleFilterChange('category', '')}
                        className={`p-3.5 rounded-2xl text-left border transition-all ${
                            !filters.category ? 'bg-brand-green text-white border-brand-green shadow-md' : 'bg-white border-gray-100 hover:border-gray-200 text-slate-700'
                        }`}
                    >
                        <p className={`text-[11px] font-medium ${!filters.category ? 'text-white/80' : 'text-gray-400'}`}>ທັງໝົດ</p>
                        <p className="text-xl font-bold mt-0.5">{stats.total}</p>
                    </button>

                    <button
                        type="button"
                        onClick={() => handleFilterChange('category', 'president')}
                        className={`p-3.5 rounded-2xl text-left border transition-all ${
                            filters.category === 'president' ? 'bg-amber-600 text-white border-amber-600 shadow-md' : 'bg-white border-gray-100 hover:border-gray-200 text-slate-700'
                        }`}
                    >
                        <p className={`text-[11px] font-medium ${filters.category === 'president' ? 'text-white/80' : 'text-gray-400'}`}>ປະທານ-ຮອງປະທານ</p>
                        <p className="text-xl font-bold mt-0.5">{stats.president}</p>
                    </button>

                    <button
                        type="button"
                        onClick={() => handleFilterChange('category', 'standing')}
                        className={`p-3.5 rounded-2xl text-left border transition-all ${
                            filters.category === 'standing' ? 'bg-orange-600 text-white border-orange-600 shadow-md' : 'bg-white border-gray-100 hover:border-gray-200 text-slate-700'
                        }`}
                    >
                        <p className={`text-[11px] font-medium ${filters.category === 'standing' ? 'text-white/80' : 'text-gray-400'}`}>ຄະນະປະຈຳການ</p>
                        <p className="text-xl font-bold mt-0.5">{stats.standing}</p>
                    </button>

                    <button
                        type="button"
                        onClick={() => handleFilterChange('category', 'member')}
                        className={`p-3.5 rounded-2xl text-left border transition-all ${
                            filters.category === 'member' ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white border-gray-100 hover:border-gray-200 text-slate-700'
                        }`}
                    >
                        <p className={`text-[11px] font-medium ${filters.category === 'member' ? 'text-white/80' : 'text-gray-400'}`}>ຄະນະກຳມະການ</p>
                        <p className="text-xl font-bold mt-0.5">{stats.member}</p>
                    </button>

                    <button
                        type="button"
                        onClick={() => handleFilterChange('category', 'advisor')}
                        className={`p-3.5 rounded-2xl text-left border transition-all ${
                            filters.category === 'advisor' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'bg-white border-gray-100 hover:border-gray-200 text-slate-700'
                        }`}
                    >
                        <p className={`text-[11px] font-medium ${filters.category === 'advisor' ? 'text-white/80' : 'text-gray-400'}`}>ຄະນະທີ່ປຶກສາ</p>
                        <p className="text-xl font-bold mt-0.5">{stats.advisor}</p>
                    </button>
                </div>

                {/* Filter Bar */}
                <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                    <div className="relative flex-1">
                        <input
                            type="text"
                            placeholder="ຄົ້ນຫາຕາມຊື່, ຕຳແໜ່ງ, ຫຼື ພາກສ່ວນ..."
                            defaultValue={filters.search}
                            onKeyDown={(e) => {
                                if (e.key === 'Enter') handleFilterChange('search', e.target.value);
                            }}
                            onBlur={(e) => handleFilterChange('search', e.target.value)}
                            className="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green"
                        />
                        <svg className="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <div className="flex flex-wrap items-center gap-2">
                        <select
                            value={filters.category || ''}
                            onChange={(e) => handleFilterChange('category', e.target.value)}
                            className="text-xs py-2 px-3 rounded-xl border border-gray-200 bg-white focus:outline-none focus:border-brand-green"
                        >
                            <option value="">ທຸກໝວດໝູ່</option>
                            {Object.entries(categories).map(([k, cat]) => (
                                <option key={k} value={k}>{cat.label}</option>
                            ))}
                        </select>

                        <select
                            value={filters.term || ''}
                            onChange={(e) => handleFilterChange('term', e.target.value)}
                            className="text-xs py-2 px-3 rounded-xl border border-gray-200 bg-white focus:outline-none focus:border-brand-green"
                        >
                            <option value="">ທຸກສະໄໝ</option>
                            {terms.map((t) => (
                                <option key={t} value={t}>{t}</option>
                            ))}
                        </select>

                        <select
                            value={filters.status || ''}
                            onChange={(e) => handleFilterChange('status', e.target.value)}
                            className="text-xs py-2 px-3 rounded-xl border border-gray-200 bg-white focus:outline-none focus:border-brand-green"
                        >
                            <option value="">ທຸກສະຖານະ</option>
                            <option value="active">ກຳລັງດຳລົງຕຳແໜ່ງ</option>
                            <option value="inactive">ພົ້ນຕຳແໜ່ງ</option>
                        </select>
                    </div>
                </div>

                {/* Table */}
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr className="border-b border-gray-100 bg-[#fbfbfa] text-gray-500 font-semibold uppercase text-[10px] tracking-wider">
                                    <th className="py-3 px-4 w-12 text-center">ລຳດັບ</th>
                                    <th className="py-3 px-4">ຮູບພາບ & ຊື່/ສາຍາ</th>
                                    <th className="py-3 px-4">ຕຳແໜ່ງ</th>
                                    <th className="py-3 px-4">ໝວດໝູ່</th>
                                    <th className="py-3 px-4">ສະໄໝ</th>
                                    <th className="py-3 px-4 text-center">ສະຖານະ</th>
                                    <th className="py-3 px-4 text-right">ຈັດການ</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 text-slate-700">
                                {committees.data.length === 0 ? (
                                    <tr>
                                        <td colSpan="7" className="py-12 text-center text-gray-400">
                                            ບໍ່ພົບຂໍ້ມູນຄະນະກຳມະການ
                                        </td>
                                    </tr>
                                ) : (
                                    committees.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-gray-50/70 transition-colors">
                                            <td className="py-3 px-4 text-center text-gray-400 font-medium">
                                                {item.sort_order}
                                            </td>
                                            <td className="py-3 px-4">
                                                <div className="flex items-center gap-3">
                                                    <div className="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200/60 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                                        {item.photo_url ? (
                                                            <img
                                                                src={item.photo_url}
                                                                alt={item.name}
                                                                className="w-full h-full object-cover"
                                                                onError={(e) => {
                                                                    e.target.style.display = 'none';
                                                                }}
                                                            />
                                                        ) : (
                                                            <span className="text-amber-700 text-sm font-bold">
                                                                {item.name.charAt(0)}
                                                            </span>
                                                        )}
                                                    </div>
                                                    <div>
                                                        <p className="font-semibold text-slate-800 text-xs sm:text-sm">
                                                            {item.name}
                                                        </p>
                                                        {item.organization && (
                                                            <p className="text-[11px] text-gray-400 truncate max-w-xs">
                                                                {item.organization}
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="py-3 px-4 text-slate-600 font-medium">
                                                {item.title || '-'}
                                            </td>
                                            <td className="py-3 px-4">
                                                {categoryBadge(item.category)}
                                            </td>
                                            <td className="py-3 px-4 text-gray-500">
                                                <span>{item.term}</span>
                                            </td>
                                            <td className="py-3 px-4 text-center">
                                                <span className={`inline-block px-2 py-0.5 rounded-full text-[10px] font-medium ${
                                                    item.status === 'active'
                                                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                                        : 'bg-gray-100 text-gray-500'
                                                }`}>
                                                    {item.status_label}
                                                </span>
                                            </td>
                                            <td className="py-3 px-4 text-right">
                                                <div className="inline-flex items-center gap-1.5">
                                                    <button
                                                        type="button"
                                                        onClick={() => openEditModal(item)}
                                                        className="p-1.5 text-gray-500 hover:text-brand-green hover:bg-brand-light-green rounded-lg transition-colors"
                                                        title="ແກ້ໄຂ"
                                                    >
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() => setDeleteConfirmItem(item)}
                                                        className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="ລຶບ"
                                                    >
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {committees.links && committees.links.length > 3 && (
                        <div className="p-4 border-t border-gray-100 flex justify-center">
                            <Pagination links={committees.links} />
                        </div>
                    )}
                </div>
            </div>

            {/* Create / Edit Modal */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto">
                    <div className="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between pb-4 border-b border-gray-100 mb-6">
                            <h3 className="text-lg font-bold text-slate-800">
                                {editingItem ? 'ແກ້ໄຂຂໍ້ມູນຄະນະກຳມະການ' : 'ເພີ່ມຄະນະກຳມະການໃໝ່'}
                            </h3>
                            <button
                                type="button"
                                onClick={closeModal}
                                className="text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100"
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form onSubmit={handleSubmit} className="space-y-4">
                            {/* Category & Term */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 mb-1">
                                        ໝວດໝູ່ / ຕຳແໜ່ງ <span className="text-red-500">*</span>
                                    </label>
                                    <select
                                        value={data.category}
                                        onChange={(e) => setData('category', e.target.value)}
                                        className="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-brand-green"
                                    >
                                        {Object.entries(categories).map(([k, cat]) => (
                                            <option key={k} value={k}>{cat.label}</option>
                                        ))}
                                    </select>
                                    {errors.category && <p className="text-red-500 text-[10px] mt-1">{errors.category}</p>}
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 mb-1">
                                        ສະໄໝທີ <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        value={data.term}
                                        onChange={(e) => setData('term', e.target.value)}
                                        placeholder="ເຊັ່ນ: ສະໄໝທີ IX"
                                        className="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-brand-green"
                                    />
                                    {errors.term && <p className="text-red-500 text-[10px] mt-1">{errors.term}</p>}
                                </div>
                            </div>

                            {/* Name & Title */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 mb-1">
                                        ຊື່ ແລະ ສາຍາ/ນາມສະກຸນ <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        placeholder="ເຊັ່ນ: ພຣະອາຈານໃຫຍ່ ມະຫາເຫວດ ມະເສໄນ"
                                        className="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-brand-green"
                                    />
                                    {errors.name && <p className="text-red-500 text-[10px] mt-1">{errors.name}</p>}
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 mb-1">
                                        ຕຳແໜ່ງຮັບຜິດຊອບ
                                    </label>
                                    <input
                                        type="text"
                                        value={data.title}
                                        onChange={(e) => setData('title', e.target.value)}
                                        placeholder="ເຊັ່ນ: ປະທານ ສູນກາງ ອພສ, ຮອງປະທານ..."
                                        className="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-brand-green"
                                    />
                                    {errors.title && <p className="text-red-500 text-[10px] mt-1">{errors.title}</p>}
                                </div>
                            </div>

                            {/* Organization & Phone */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 mb-1">
                                        ພາກສ່ວນ / ກຳມາທິການ / ວັດ
                                    </label>
                                    <input
                                        type="text"
                                        value={data.organization}
                                        onChange={(e) => setData('organization', e.target.value)}
                                        placeholder="ເຊັ່ນ: ກຳມາທິການສາທາຣະນຸປະການ"
                                        className="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-brand-green"
                                    />
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 mb-1">
                                        ວັນທີຮັບຮອງ / ປີ
                                    </label>
                                    <input
                                        type="text"
                                        value={data.term_year}
                                        onChange={(e) => setData('term_year', e.target.value)}
                                        placeholder="ເຊັ່ນ: 23 ທັນວາ 2568 (ຄສ 2025)"
                                        className="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-brand-green"
                                    />
                                </div>
                            </div>

                            {/* Sort order & Status */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 mb-1">
                                        ລຳດັບການສະແດງ (Sort Order)
                                    </label>
                                    <input
                                        type="number"
                                        value={data.sort_order}
                                        onChange={(e) => setData('sort_order', parseInt(e.target.value) || 0)}
                                        className="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-brand-green"
                                    />
                                </div>

                                <div>
                                    <label className="block text-xs font-semibold text-slate-700 mb-1">
                                        ສະຖານະ
                                    </label>
                                    <select
                                        value={data.status}
                                        onChange={(e) => setData('status', e.target.value)}
                                        className="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-brand-green"
                                    >
                                        <option value="active">ກຳລັງດຳລົງຕຳແໜ່ງ (Active)</option>
                                        <option value="inactive">ພົ້ນຕຳແໜ່ງ (Inactive)</option>
                                    </select>
                                </div>
                            </div>

                            {/* Photo Upload & Remote URL */}
                            <div className="border border-gray-100 rounded-2xl p-4 bg-gray-50/50 space-y-3">
                                <label className="block text-xs font-semibold text-slate-700">
                                    ຮູບພາບປະຈຳໂຕ
                                </label>

                                <div className="flex items-center gap-4">
                                    <div className="w-16 h-16 rounded-2xl bg-white border border-gray-200 overflow-hidden flex items-center justify-center flex-shrink-0 shadow-sm">
                                        {photoPreview ? (
                                            <img src={photoPreview} alt="Preview" className="w-full h-full object-cover" />
                                        ) : (
                                            <img src="/logo.png" alt="Logo" className="w-8 h-8 object-contain opacity-40 select-none" />
                                        )}
                                    </div>

                                    <div className="flex-1 space-y-2">
                                        <div>
                                            <label className="inline-block px-3 py-1.5 rounded-xl text-xs font-semibold bg-white border border-gray-200 text-slate-700 hover:bg-gray-50 cursor-pointer shadow-sm">
                                                <span>ເລືອກໄຟລ໌ຮູບພາບ</span>
                                                <input
                                                    type="file"
                                                    accept="image/*"
                                                    onChange={handlePhotoChange}
                                                    className="hidden"
                                                />
                                            </label>
                                            {photoPreview && (
                                                <button
                                                    type="button"
                                                    onClick={() => {
                                                        setData('photo', null);
                                                        setData('photo_url_input', '');
                                                        setData('remove_photo', true);
                                                        setPhotoPreview(null);
                                                    }}
                                                    className="ml-2 text-xs text-red-500 hover:underline"
                                                >
                                                    ລຶບຮູບພາບ
                                                </button>
                                            )}
                                        </div>

                                        <div>
                                            <input
                                                type="text"
                                                value={data.photo_url_input}
                                                onChange={handleUrlChange}
                                                placeholder="ຫຼື ວາງລິ້ງຮູບພາບອອນລາຍ (Google Drive, URL)..."
                                                className="w-full text-xs p-2 rounded-xl border border-gray-200 bg-white focus:outline-none focus:border-brand-green"
                                            />
                                        </div>

                                        {errors.photo && (
                                            <p className="text-xs text-red-500 font-medium">{errors.photo}</p>
                                        )}
                                        {errors.photo_url_input && (
                                            <p className="text-xs text-red-500 font-medium">{errors.photo_url_input}</p>
                                        )}
                                        <p className="text-[10px] text-gray-400">
                                            ຮອງຮັບໄຟລ໌ຮູບ JPG, PNG, WebP ຫຼື ລິ້ງ (Google Drive, ເວັບໄຊທ໌)
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* Bio / Description */}
                            <div>
                                <label className="block text-xs font-semibold text-slate-700 mb-1">
                                    ປະຫວັດຫຍໍ້ ຫຼື ໝາຍເຫດ
                                </label>
                                <textarea
                                    rows="3"
                                    value={data.bio}
                                    onChange={(e) => setData('bio', e.target.value)}
                                    placeholder="ລາຍລະອຽດ ຫຼື ປະຫວັດຫຍໍ້..."
                                    className="w-full text-xs p-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-brand-green"
                                ></textarea>
                            </div>

                            {/* Submit Buttons */}
                            <div className="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                                <button
                                    type="button"
                                    onClick={closeModal}
                                    className="px-4 py-2 rounded-xl text-xs font-semibold text-gray-500 hover:bg-gray-100 transition-colors"
                                >
                                    ຍົກເລີກ
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-5 py-2 rounded-xl text-xs font-semibold bg-brand-green text-white hover:bg-brand-green/90 transition-colors shadow-sm disabled:opacity-50"
                                >
                                    {processing ? 'ກຳລັງບັນທຶກ...' : 'ບັນທຶກຂໍ້ມູນ'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Delete Confirmation Modal */}
            {deleteConfirmItem && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                    <div className="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
                        <div className="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h4 className="text-base font-bold text-slate-800">ຢືນຢັນການລຶບ</h4>
                            <p className="text-xs text-gray-500 mt-1">
                                ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບ <strong>{deleteConfirmItem.name}</strong> ອອກຈາກລະບົບ?
                            </p>
                        </div>
                        <div className="flex items-center justify-center gap-2 pt-2">
                            <button
                                type="button"
                                onClick={() => setDeleteConfirmItem(null)}
                                className="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100"
                            >
                                ຍົກເລີກ
                            </button>
                            <button
                                type="button"
                                onClick={() => handleDelete(deleteConfirmItem)}
                                className="px-4 py-2 rounded-xl text-xs font-semibold bg-red-600 text-white hover:bg-red-700 shadow-sm"
                            >
                                ລຶບທັນທີ
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
