import { Head, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    function submit(e) {
        e.preventDefault();
        post(route('login.store'));
    }

    return (
        <GuestLayout>
            <Head title="ເຂົ້າສູ່ລະບົບ" />

            <h2 className="text-2xl font-bold text-slate-800 mb-2">ເຂົ້າສູ່ລະບົບ</h2>
            <p className="text-slate-400 text-sm mb-8">ກະລຸນາໃສ່ອີເມລ ແລະ ລະຫັດຜ່ານ ເພື່ອເຂົ້າສູ່ລະບົບ</p>

            <form onSubmit={submit} noValidate className="space-y-5">

                {/* Email */}
                <div>
                    <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ອີເມລ</label>
                    <div className="relative">
                        <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 6.75c0-.621.504-1.125 1.125-1.125h17.25c.621 0 1.125.504 1.125 1.125v10.5c0 .621-.504 1.125-1.125 1.125H3.375A1.125 1.125 0 012.25 17.25V6.75zm0 0L12 13.5l9.75-6.75" />
                            </svg>
                        </span>
                        <input
                            type="email"
                            autoComplete="email"
                            placeholder="admin@example.com"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            className={`w-full pl-11 pr-4 py-3 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                        placeholder-gray-400 outline-none transition-all
                                        ${errors.email ? 'border-red-300 ring-1 ring-red-200' : 'border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20'}`}
                        />
                    </div>
                    {errors.email && <p className="mt-1.5 text-red-500 text-xs">{errors.email}</p>}
                </div>

                {/* Password */}
                <div>
                    <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">ລະຫັດຜ່ານ</label>
                    <div className="relative">
                        <span className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="1.75">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 0h10.5a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5h-10.5a1.5 1.5 0 01-1.5-1.5v-7.5a1.5 1.5 0 011.5-1.5z" />
                            </svg>
                        </span>
                        <input
                            type="password"
                            autoComplete="current-password"
                            placeholder="••••••••"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            className={`w-full pl-11 pr-4 py-3 rounded-xl border text-sm bg-[#f8fafa] text-slate-800
                                        placeholder-gray-400 outline-none transition-all
                                        ${errors.password ? 'border-red-300 ring-1 ring-red-200' : 'border-gray-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20'}`}
                        />
                    </div>
                    {errors.password && <p className="mt-1.5 text-red-500 text-xs">{errors.password}</p>}
                </div>

                {/* Remember */}
                <div className="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="remember"
                        checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)}
                        className="w-4 h-4 rounded border-gray-300 accent-brand-green cursor-pointer"
                    />
                    <label htmlFor="remember" className="text-slate-600 text-sm cursor-pointer select-none">ຈື່ການເຂົ້າລະບົບ</label>
                </div>

                {/* Submit */}
                <button
                    type="submit"
                    disabled={processing}
                    className="w-full py-3.5 rounded-xl bg-brand-green hover:bg-opacity-90
                               text-white font-semibold text-sm
                               transition shadow-lg shadow-brand-green/20
                               focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-green focus-visible:ring-offset-2
                               disabled:opacity-70 disabled:cursor-not-allowed
                               flex items-center justify-center gap-2"
                >
                    {processing ? (
                        <span className="flex items-center gap-2">
                            <svg className="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            ກຳລັງກວດສອບ...
                        </span>
                    ) : (
                        <span>ເຂົ້າລະບົບ</span>
                    )}
                </button>

            </form>

            <p className="text-center text-gray-300 text-xs mt-8">&copy; {new Date().getFullYear()} ວັດປ່າໜອງບົວທອງໃຕ້</p>
        </GuestLayout>
    );
}
