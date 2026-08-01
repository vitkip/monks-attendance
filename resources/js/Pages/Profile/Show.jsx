import { Head, useForm, usePage, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function ProfileShow() {
    const { auth } = usePage().props;
    const user = auth?.user;
    const isAdmin = !!user?.isAdmin;

    const infoForm = useForm({
        name: user?.name || '',
        email: user?.email || '',
    });

    const passwordForm = useForm({
        current_password: '',
        new_password: '',
        new_password_confirmation: '',
    });

    function submitInfo(e) {
        e.preventDefault();
        infoForm.post(route('profile.update-info'), { preserveScroll: true });
    }

    function submitPassword(e) {
        e.preventDefault();
        passwordForm.post(route('profile.update-password'), {
            preserveScroll: true,
            onSuccess: () => passwordForm.reset('current_password', 'new_password', 'new_password_confirmation'),
        });
    }

    function logout(e) {
        e.preventDefault();
        router.post(route('logout'));
    }

    return (
        <AppLayout title="ຂໍ້ມູນສ່ວນຕົວ">
            <Head title="ຂໍ້ມູນສ່ວນຕົວ" />

            <div className="max-w-xl mx-auto space-y-6">

                {/* Page Header */}
                <div>
                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">ບັນຊີ</p>
                    <h1 className="text-2xl sm:text-3xl font-bold text-slate-800">ຂໍ້ມູນສ່ວນຕົວ</h1>
                    <p className="text-gray-400 text-sm mt-1">ແກ້ໄຂຊື່, ອີເມລ ແລະ ລະຫັດຜ່ານຂອງທ່ານ</p>
                </div>

                {/* Profile Card */}
                <div className="bg-white rounded-3xl card-shadow overflow-hidden">

                    {/* Avatar strip */}
                    <div className="flex items-center gap-4 px-6 py-5 border-b border-gray-100 bg-[#f8fafa]">
                        <div className={`w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0
                                        ${isAdmin ? 'bg-brand-light-green' : 'bg-gray-100'}`}>
                            <svg className={`w-7 h-7 ${isAdmin ? 'text-brand-green' : 'text-slate-500'}`}
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.5">
                                <path strokeLinecap="round" strokeLinejoin="round"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p className="text-slate-800 font-semibold text-base">{user?.name}</p>
                            <span className={`inline-flex mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                ${isAdmin ? 'bg-brand-light-green text-brand-green' : 'bg-gray-100 text-slate-500'}`}>
                                {isAdmin ? 'Admin' : 'Staff'}
                            </span>
                        </div>
                    </div>

                    {/* Info form */}
                    <form onSubmit={submitInfo} className="px-6 py-5 space-y-4">
                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ຂໍ້ມູນທົ່ວໄປ</p>

                        <div>
                            <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ຊື່ - ນາມສະກຸນ</label>
                            <input type="text" placeholder="ຊື່ ນາມສະກຸນ"
                                   value={infoForm.data.name}
                                   onChange={(e) => infoForm.setData('name', e.target.value)}
                                   className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 outline-none focus:ring-2 focus:border-transparent transition-shadow
                                              ${infoForm.errors.name ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                            {infoForm.errors.name && <p className="mt-1.5 text-red-500 text-xs">{infoForm.errors.name}</p>}
                        </div>

                        <div>
                            <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ອີເມລ</label>
                            <input type="email" placeholder="example@email.com"
                                   value={infoForm.data.email}
                                   onChange={(e) => infoForm.setData('email', e.target.value)}
                                   className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 outline-none focus:ring-2 focus:border-transparent transition-shadow
                                              ${infoForm.errors.email ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                            {infoForm.errors.email && <p className="mt-1.5 text-red-500 text-xs">{infoForm.errors.email}</p>}
                        </div>

                        <div className="flex justify-end pt-1">
                            <button type="submit" disabled={infoForm.processing}
                                    className="px-6 py-2.5 rounded-2xl bg-brand-green text-white text-sm font-semibold
                                               hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 disabled:opacity-70 disabled:cursor-not-allowed">
                                {infoForm.processing ? 'ກຳລັງບັນທຶກ...' : 'ບັນທຶກຂໍ້ມູນ'}
                            </button>
                        </div>
                    </form>
                </div>

                {/* Password Card */}
                <div className="bg-white rounded-3xl card-shadow overflow-hidden">

                    <div className="px-6 py-4 border-b border-gray-100 bg-[#f8fafa]">
                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ປ່ຽນລະຫັດຜ່ານ</p>
                    </div>

                    <form onSubmit={submitPassword} className="px-6 py-5 space-y-4">

                        <div>
                            <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ລະຫັດຜ່ານປັດຈຸບັນ</label>
                            <input type="password" placeholder="••••••••"
                                   value={passwordForm.data.current_password}
                                   onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                   className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 outline-none focus:ring-2 focus:border-transparent transition-shadow
                                              ${passwordForm.errors.current_password ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                            {passwordForm.errors.current_password && <p className="mt-1.5 text-red-500 text-xs">{passwordForm.errors.current_password}</p>}
                        </div>

                        <div>
                            <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ລະຫັດຜ່ານໃໝ່</label>
                            <input type="password" placeholder="••••••••"
                                   value={passwordForm.data.new_password}
                                   onChange={(e) => passwordForm.setData('new_password', e.target.value)}
                                   className={`w-full bg-[#f8fafa] border rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 outline-none focus:ring-2 focus:border-transparent transition-shadow
                                              ${passwordForm.errors.new_password ? 'border-red-300 focus:ring-red-200' : 'border-gray-200 focus:ring-brand-green/30'}`} />
                            {passwordForm.errors.new_password && <p className="mt-1.5 text-red-500 text-xs">{passwordForm.errors.new_password}</p>}
                        </div>

                        <div>
                            <label className="block text-[10px] font-medium text-gray-400 uppercase tracking-widest mb-2">ຢືນຢັນລະຫັດຜ່ານໃໝ່</label>
                            <input type="password" placeholder="••••••••"
                                   value={passwordForm.data.new_password_confirmation}
                                   onChange={(e) => passwordForm.setData('new_password_confirmation', e.target.value)}
                                   className="w-full bg-[#f8fafa] border border-gray-200 rounded-xl px-3 py-3 text-sm text-slate-800 placeholder:text-gray-400 outline-none
                                              focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow" />
                        </div>

                        <div className="flex justify-end pt-1">
                            <button type="submit" disabled={passwordForm.processing}
                                    className="px-6 py-2.5 rounded-2xl bg-brand-green text-white text-sm font-semibold
                                               hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20 disabled:opacity-70 disabled:cursor-not-allowed">
                                {passwordForm.processing ? 'ກຳລັງບັນທຶກ...' : 'ປ່ຽນລະຫັດຜ່ານ'}
                            </button>
                        </div>
                    </form>
                </div>

                {/* Logout */}
                <div className="bg-white rounded-3xl card-shadow overflow-hidden">

                    <div className="px-6 py-4 border-b border-gray-100 bg-[#f8fafa]">
                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ອອກຈາກລະບົບ</p>
                    </div>

                    <div className="px-6 py-5">
                        <p className="text-gray-400 text-sm mb-4">ອອກຈາກລະບົບໃນທຸກອຸປະກອນຂອງທ່ານ</p>
                        <form onSubmit={logout}>
                            <button type="submit"
                                    className="flex items-center gap-2 px-5 py-2.5 rounded-2xl text-sm font-semibold
                                               text-red-500 border border-red-200 hover:bg-red-50
                                               hover:border-red-300 transition-all duration-150">
                                <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                ອອກຈາກລະບົບ
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </AppLayout>
    );
}
