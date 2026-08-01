import { router, useForm, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';

const emptyForm = { name: '', email: '', role: 'staff', password: '', password_confirmation: '' };

export default function UsersIndex({ filters, users, adminCount }) {
    const { auth } = usePage().props;
    const currentUserId = auth.user.id;

    const [search, setSearch] = useState(filters.search);
    const debounceRef = useRef(null);

    useEffect(() => {
        clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            router.get(route('users.index'), { search }, { preserveState: true, preserveScroll: true, replace: true });
        }, 300);
        return () => clearTimeout(debounceRef.current);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    const [showModal, setShowModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteId, setDeleteId] = useState(null);
    const [editId, setEditId] = useState(null);
    const form = useForm(emptyForm);

    function openCreate() {
        setEditId(null);
        form.clearErrors();
        form.setData({ ...emptyForm });
        setShowModal(true);
    }

    function openEdit(user) {
        setEditId(user.id);
        form.clearErrors();
        form.setData({
            name: user.name,
            email: user.email,
            role: user.role,
            password: '',
            password_confirmation: '',
        });
        setShowModal(true);
    }

    function submit(e) {
        e.preventDefault();
        const onSuccess = () => setShowModal(false);
        if (editId) {
            form.put(route('users.update', editId), { onSuccess, preserveScroll: true });
        } else {
            form.post(route('users.store'), { onSuccess, preserveScroll: true });
        }
    }

    function confirmDelete(id) {
        setDeleteId(id);
        setShowDeleteModal(true);
    }

    function doDelete() {
        if (!deleteId) {
            setShowDeleteModal(false);
            return;
        }
        router.delete(route('users.destroy', deleteId), {
            preserveScroll: true,
            onSuccess: () => { setShowDeleteModal(false); setDeleteId(null); },
        });
    }

    function cannotDelete(user) {
        return user.id === currentUserId || (user.is_admin && adminCount <= 1);
    }

    return (
        <AppLayout title="ຈັດການຜູ້ໃຊ້ລະບົບ">
            {/* Page Header */}
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ລະບົບ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ຈັດການຜູ້ໃຊ້ລະບົບ</h1>
                    <p className="text-gray-400 text-sm mt-1">ເພີ່ມ, ແກ້ໄຂ ແລະ ລຶບຜູ້ໃຊ້</p>
                </div>
                <button onClick={openCreate}
                    className="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5
                               bg-brand-green text-white rounded-2xl text-sm font-semibold
                               hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 flex-shrink-0">
                    <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    ເພີ່ມຜູ້ໃຊ້
                </button>
            </div>

            {/* Search */}
            <div className="bg-white card-shadow rounded-2xl p-4 mb-5">
                <div className="relative">
                    <svg className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" value={search} onChange={(e) => setSearch(e.target.value)}
                        placeholder="ຄົ້ນຫາຊື່ ຫຼື ອີເມລ..."
                        className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm
                                   text-slate-800 placeholder:text-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                </div>
            </div>

            {/* Mobile Cards */}
            <div className="md:hidden space-y-3">
                {users.data.length === 0 ? (
                    <div className="text-center py-16 text-gray-400 text-sm">ບໍ່ພົບຂໍ້ມູນ</div>
                ) : users.data.map((user) => (
                    <div key={user.id} className="bg-white rounded-2xl card-shadow overflow-hidden">
                        <div className="flex items-center gap-3 px-4 py-3.5">
                            <div className={`w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 ${user.is_admin ? 'bg-brand-light-green' : 'bg-gray-100'}`}>
                                <svg className={`w-5 h-5 ${user.is_admin ? 'text-brand-green' : 'text-gray-500'}`}
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                                    <path strokeLinecap="round" strokeLinejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2 flex-wrap">
                                    <span className="font-semibold text-slate-800 text-sm">{user.name}</span>
                                    {user.id === currentUserId && (
                                        <span className="px-1.5 py-0.5 rounded text-[9px] font-medium bg-brand-light-green text-brand-green">ຂ້ອຍ</span>
                                    )}
                                </div>
                                <p className="text-gray-400 text-xs mt-0.5 truncate">{user.email}</p>
                            </div>
                            <span className={`px-2.5 py-1 rounded-full text-[10px] font-bold flex-shrink-0 ${user.is_admin ? 'bg-brand-light-green text-brand-green' : 'bg-gray-100 text-gray-500'}`}>
                                {user.is_admin ? 'Admin' : 'Staff'}
                            </span>
                        </div>
                        <div className="flex border-t border-gray-100">
                            <button onClick={() => openEdit(user)}
                                className="flex-1 py-2.5 text-xs text-slate-600 hover:bg-gray-50 transition-colors border-r border-gray-100 font-medium">
                                ແກ້ໄຂ
                            </button>
                            <button onClick={() => confirmDelete(user.id)}
                                disabled={cannotDelete(user)}
                                className={`flex-1 py-2.5 text-xs font-medium transition-colors ${cannotDelete(user) ? 'text-gray-300 cursor-not-allowed' : 'text-red-500 hover:bg-red-50'}`}>
                                ລຶບ
                            </button>
                        </div>
                    </div>
                ))}
                {users.data.length > 0 && <div className="pt-1"><Pagination links={users.links} /></div>}
            </div>

            {/* Desktop Table */}
            <div className="hidden md:block bg-white rounded-2xl card-shadow overflow-hidden">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="bg-gray-50 border-b border-gray-100">
                            <th className="text-left px-4 py-3.5 text-[11px] font-medium text-gray-400 uppercase tracking-wide">#</th>
                            <th className="text-left px-4 py-3.5 text-[11px] font-medium text-gray-400 uppercase tracking-wide">ຊື່</th>
                            <th className="text-left px-4 py-3.5 text-[11px] font-medium text-gray-400 uppercase tracking-wide">ອີເມລ</th>
                            <th className="text-left px-4 py-3.5 text-[11px] font-medium text-gray-400 uppercase tracking-wide">ສິດ</th>
                            <th className="text-left px-4 py-3.5 text-[11px] font-medium text-gray-400 uppercase tracking-wide">ວັນທີສ້າງ</th>
                            <th className="px-4 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {users.data.length === 0 ? (
                            <tr>
                                <td colSpan={6} className="px-5 py-16 text-center text-gray-400">ບໍ່ພົບຂໍ້ມູນ</td>
                            </tr>
                        ) : users.data.map((user, i) => (
                            <tr key={user.id} className="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                                <td className="px-4 py-3.5 text-gray-300 text-xs">{i + 1 + (users.current_page - 1) * users.per_page}</td>
                                <td className="px-4 py-3.5">
                                    <div className="flex items-center gap-2.5">
                                        <div className={`w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 ${user.is_admin ? 'bg-brand-light-green' : 'bg-gray-100'}`}>
                                            <svg className={`w-4 h-4 ${user.is_admin ? 'text-brand-green' : 'text-gray-500'}`}
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                                                <path strokeLinecap="round" strokeLinejoin="round"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <span className="font-medium text-slate-800">{user.name}</span>
                                        {user.id === currentUserId && (
                                            <span className="px-1.5 py-0.5 rounded text-[9px] font-medium bg-brand-light-green text-brand-green">ຂ້ອຍ</span>
                                        )}
                                    </div>
                                </td>
                                <td className="px-4 py-3.5 text-slate-600">{user.email}</td>
                                <td className="px-4 py-3.5">
                                    <span className={`inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold ${user.is_admin ? 'bg-brand-light-green text-brand-green' : 'bg-gray-100 text-gray-500'}`}>
                                        {user.is_admin ? 'Admin' : 'Staff'}
                                    </span>
                                </td>
                                <td className="px-4 py-3.5 text-gray-400 text-xs">{user.created_at}</td>
                                <td className="px-4 py-3.5">
                                    <div className="flex items-center justify-end gap-2">
                                        <button onClick={() => openEdit(user)}
                                            className="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600
                                                       border border-gray-200 hover:bg-gray-50
                                                       transition-all duration-150">
                                            ແກ້ໄຂ
                                        </button>
                                        <button onClick={() => confirmDelete(user.id)}
                                            disabled={cannotDelete(user)}
                                            className={`px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-150 ${cannotDelete(user) ? 'text-gray-300 border border-gray-100 cursor-not-allowed' : 'text-red-500 border border-red-200 hover:bg-red-50'}`}>
                                            ລຶບ
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                {users.data.length > 0 && (
                    <div className="px-5 py-4 border-t border-gray-100">
                        <Pagination links={users.links} />
                    </div>
                )}
            </div>

            {/* Create / Edit Modal */}
            {showModal && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
                    onClick={(e) => e.target === e.currentTarget && setShowModal(false)}>
                    <div className="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden max-h-[95dvh] sm:max-h-[90vh] flex flex-col">

                        <div className="px-6 py-4 flex items-center justify-between shrink-0 border-b border-gray-100">
                            <div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    {editId ? 'ແກ້ໄຂຂໍ້ມູນ' : 'ເພີ່ມໃໝ່'}
                                </p>
                                <h2 className="text-lg font-bold text-slate-800 mt-0.5">ຜູ້ໃຊ້ງານ</h2>
                            </div>
                            <button onClick={() => setShowModal(false)}
                                className="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-slate-700 hover:bg-gray-100 transition-colors touch-manipulation">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="2" className="w-4 h-4">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M6 6l8 8M14 6l-8 8" />
                                </svg>
                            </button>
                        </div>

                        {/* Drag handle (mobile only) */}
                        <div className="sm:hidden flex justify-center pt-3 shrink-0">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>

                        <form onSubmit={submit} className="px-6 py-5 space-y-4 overflow-y-auto">

                            {/* Name */}
                            <div>
                                <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ຊື່ - ນາມສະກຸນ</label>
                                <input type="text" value={form.data.name} onChange={(e) => form.setData('name', e.target.value)}
                                    placeholder="ຊື່ ນາມສະກຸນ"
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                               placeholder:text-gray-400 outline-none transition-shadow
                                               ${form.errors.name ? 'border-red-300 focus:ring-2 focus:ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-brand-green/30 focus:border-transparent'}`} />
                                {form.errors.name && <p className="mt-1 text-red-500 text-xs">{form.errors.name}</p>}
                            </div>

                            {/* Email */}
                            <div>
                                <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ອີເມລ</label>
                                <input type="email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)}
                                    placeholder="example@email.com"
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                               placeholder:text-gray-400 outline-none transition-shadow
                                               ${form.errors.email ? 'border-red-300 focus:ring-2 focus:ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-brand-green/30 focus:border-transparent'}`} />
                                {form.errors.email && <p className="mt-1 text-red-500 text-xs">{form.errors.email}</p>}
                            </div>

                            {/* Role */}
                            <div>
                                <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ສິດການໃຊ້ງານ</label>
                                <select value={form.data.role} onChange={(e) => form.setData('role', e.target.value)}
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                               outline-none transition-shadow
                                               ${form.errors.role ? 'border-red-300 focus:ring-2 focus:ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-brand-green/30 focus:border-transparent'}`}>
                                    <option value="staff">Staff — ບັນທຶກຂາດໄດ້ຢ່າງດຽວ</option>
                                    <option value="admin">Admin — ເຮັດໄດ້ທຸກຢ່າງ</option>
                                </select>
                                {form.errors.role && <p className="mt-1 text-red-500 text-xs">{form.errors.role}</p>}
                            </div>

                            {/* Password */}
                            <div>
                                <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">
                                    ລະຫັດຜ່ານ
                                    {editId && <span className="font-normal text-gray-400 normal-case tracking-normal"> (ປ່ອຍວ່າງ = ບໍ່ປ່ຽນ)</span>}
                                </label>
                                <input type="password" value={form.data.password} onChange={(e) => form.setData('password', e.target.value)}
                                    placeholder="••••••••"
                                    className={`w-full px-3.5 py-2.5 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                               placeholder:text-gray-400 outline-none transition-shadow
                                               ${form.errors.password ? 'border-red-300 focus:ring-2 focus:ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-brand-green/30 focus:border-transparent'}`} />
                                {form.errors.password && <p className="mt-1 text-red-500 text-xs">{form.errors.password}</p>}
                            </div>

                            {/* Confirm Password */}
                            <div>
                                <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ຢືນຢັນລະຫັດຜ່ານ</label>
                                <input type="password" value={form.data.password_confirmation} onChange={(e) => form.setData('password_confirmation', e.target.value)}
                                    placeholder="••••••••"
                                    className="w-full px-3.5 py-2.5 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                               placeholder:text-gray-400 outline-none transition-shadow border-gray-200
                                               focus:ring-2 focus:ring-brand-green/30 focus:border-transparent" />
                            </div>

                            <div className="flex gap-3 pt-1">
                                <button type="button" onClick={() => setShowModal(false)}
                                    className="flex-1 py-2.5 rounded-2xl border border-gray-200 text-sm text-slate-600
                                               hover:bg-gray-50 transition-colors font-medium">
                                    ຍົກເລີກ
                                </button>
                                <button type="submit" disabled={form.processing}
                                    className="flex-1 py-2.5 rounded-2xl bg-brand-green text-white text-sm font-semibold
                                               hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 disabled:opacity-70">
                                    {form.processing ? 'ກຳລັງບັນທຶກ...' : (editId ? 'ບັນທຶກ' : 'ເພີ່ມ')}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            )}

            {/* Delete Confirm Modal */}
            {showDeleteModal && (
                <div className="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
                    onClick={(e) => e.target === e.currentTarget && setShowDeleteModal(false)}>
                    <div className="bg-white w-full sm:max-w-sm rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">
                        {/* Drag handle (mobile only) */}
                        <div className="sm:hidden flex justify-center pt-3">
                            <div className="w-10 h-1 rounded-full bg-gray-200"></div>
                        </div>
                        <div className="px-6 py-6 text-center">
                            <div className="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                                <svg className="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                </svg>
                            </div>
                            <h3 className="text-lg font-bold text-slate-800 mb-2">ຢືນຢັນການລຶບ</h3>
                            <p className="text-gray-400 text-sm mb-6">ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບຜູ້ໃຊ້ນີ້? ການດຳເນີນການນີ້ບໍ່ສາມາດຍ້ອນກັບໄດ້</p>
                            <div className="flex gap-3">
                                <button onClick={() => setShowDeleteModal(false)}
                                    className="flex-1 py-2.5 rounded-2xl border border-gray-200 text-sm text-slate-600
                                               hover:bg-gray-50 transition-colors font-medium">
                                    ຍົກເລີກ
                                </button>
                                <button onClick={doDelete}
                                    className="flex-1 py-2.5 rounded-2xl bg-red-500 text-white text-sm font-semibold
                                               hover:bg-red-600 transition-colors">
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
