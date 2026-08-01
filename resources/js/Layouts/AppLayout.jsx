import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import Toasts from '@/Components/Toasts';

function NavIcon({ path }) {
    return (
        <svg className="w-[18px] h-[18px] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="1.75">
            <path strokeLinecap="round" strokeLinejoin="round" d={path} />
        </svg>
    );
}

function NavLink({ href, active, onNavigate, icon, children }) {
    return (
        <Link
            href={href}
            onClick={onNavigate}
            className={`flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors ${
                active ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-100'
            }`}
        >
            <NavIcon path={icon} />
            <span>{children}</span>
        </Link>
    );
}

function NavGroupLabel({ children }) {
    return <p className="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 mt-6 px-4">{children}</p>;
}

const ICONS = {
    absences: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    balance: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
    electricity: 'M13 10V3L4 14h7v7l9-11h-7z',
    construction: 'M3 21h18M4 21V8l8-5 8 5v13M9 21v-6h6v6M9 12h.01M15 12h.01M9 9h.01M15 9h.01',
    fund: 'M3 10h18M5 6h14a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2zM12 15a2 2 0 100-4 2 2 0 000 4z',
    news: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6v-4H7v4z',
    chants: 'M12 6.253v13M12 6.253C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    heroSlides: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 8h16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z',
    category: 'M7 7h.01M7 3h5.586a1 1 0 01.707.293l6.414 6.414a1 1 0 010 1.414l-8.586 8.586a1 1 0 01-1.414 0l-6.414-6.414A1 1 0 013 12.586V7a4 4 0 014-4z',
    monks: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    duty: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
    fineRates: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    users: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
    settings: 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
    burger: 'M4 6h16M4 12h16M4 18h16',
    userCircle: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    logout: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
};

export default function AppLayout({ title, children }) {
    const { auth, appLogo, url } = usePage().props;
    const currentUrl = usePage().url;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const user = auth?.user;
    const isAdmin = !!user?.isAdmin;

    const isActive = (prefix) => currentUrl === prefix || currentUrl.startsWith(prefix + '/') || currentUrl.startsWith(prefix + '?');

    const closeSidebar = () => setSidebarOpen(false);

    function logout(e) {
        e.preventDefault();
        router.post(route('logout'));
    }

    return (
        <div className="bg-[#f1f3f4] font-sans text-slate-700">
            <Head title={title} />

            <div className="flex min-h-screen">

                {sidebarOpen && (
                    <div
                        onClick={closeSidebar}
                        className="fixed inset-0 bg-black/50 z-20 lg:hidden"
                    ></div>
                )}

                <aside
                    className={`fixed top-0 left-0 h-full w-64 z-30 flex flex-col bg-[#f8fafa] border-r border-gray-200
                                transition-transform duration-300 ease-in-out
                                ${sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}`}
                >
                    <div className="flex items-center gap-3 px-6 py-6">
                        {appLogo ? (
                            <div className="w-9 h-9 flex-shrink-0 rounded-lg overflow-hidden bg-brand-green flex items-center justify-center">
                                <img src={`/storage/${appLogo}`} alt="Logo" className="w-full h-full object-contain p-0.5" />
                            </div>
                        ) : (
                            <div className="w-9 h-9 flex-shrink-0 rounded-lg bg-brand-green flex items-center justify-center">
                                <span className="inline-block text-white text-base select-none">☸</span>
                            </div>
                        )}
                        <div className="min-w-0 leading-tight">
                            <div className="text-slate-800 font-bold text-sm truncate">ລະບົບບັນທຶກການຂາດລາ</div>
                            <div className="text-gray-400 text-[10px] uppercase tracking-widest mt-0.5">Monk Attendance</div>
                        </div>
                    </div>

                    <nav className="flex-1 px-4 pb-4 space-y-1 overflow-y-auto">
                        {user && (
                            <>
                                <p className="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-3 px-4">ເມນູ</p>

                                <NavLink href={route('absences.index')} active={isActive('/absences')} onNavigate={closeSidebar} icon={ICONS.absences}>ກວດສອບການຂາດລາ</NavLink>
                                <NavLink href={route('balance.index')} active={isActive('/balance')} onNavigate={closeSidebar} icon={ICONS.balance}>ຍອດຄ້າງຊຳລະ</NavLink>

                                {isAdmin && (
                                    <>
                                        <NavLink href={route('electricity-bills.index')} active={isActive('/electricity-bills')} onNavigate={closeSidebar} icon={ICONS.electricity}>ແຈ້ງບິນຄ່າໄຟຟ້າ</NavLink>
                                        <NavLink href={route('construction-projects.index')} active={isActive('/construction-projects')} onNavigate={closeSidebar} icon={ICONS.construction}>ໂຄງການກໍ່ສ້າງ</NavLink>
                                        <NavLink href={route('fund.index')} active={isActive('/fund')} onNavigate={closeSidebar} icon={ICONS.fund}>ລາຍຮັບ-ລາຍຈ່າຍກອງທຶນ</NavLink>
                                        <NavLink href={route('news.index')} active={isActive('/news')} onNavigate={closeSidebar} icon={ICONS.news}>ຂ່າວສານ</NavLink>
                                        <NavLink href={route('chants.index')} active={isActive('/chants')} onNavigate={closeSidebar} icon={ICONS.chants}>ບົດສູດມົນ</NavLink>

                                        <NavGroupLabel>ຈັດການເນື້ອຫາ</NavGroupLabel>
                                        <NavLink href={route('hero-slides.index')} active={isActive('/hero-slides')} onNavigate={closeSidebar} icon={ICONS.heroSlides}>ສະໄລ້ Hero</NavLink>
                                        <NavLink href={route('news-categories.index')} active={isActive('/news-categories')} onNavigate={closeSidebar} icon={ICONS.category}>ໝວດໝູ່ຂ່າວ</NavLink>
                                        <NavLink href={route('chant-categories.index')} active={isActive('/chant-categories')} onNavigate={closeSidebar} icon={ICONS.category}>ໝວດໝູ່ບົດສູດມົນ</NavLink>

                                        <NavGroupLabel>ຈັດການພຣະສົງ</NavGroupLabel>
                                        <NavLink href={route('monks.index')} active={isActive('/monks')} onNavigate={closeSidebar} icon={ICONS.monks}>ພຣະສົງ/ສາມະເນນ</NavLink>
                                        <NavLink href={route('duty-schedules.index')} active={isActive('/duty-schedules')} onNavigate={closeSidebar} icon={ICONS.duty}>ໜ້າທີ່ປະຈຳວັນ</NavLink>

                                        <NavGroupLabel>ລະບົບ</NavGroupLabel>
                                        <NavLink href={route('fine-rates.index')} active={isActive('/fine-rates')} onNavigate={closeSidebar} icon={ICONS.fineRates}>ອັດຕາຄ່າປັບ</NavLink>
                                        <NavLink href={route('users.index')} active={isActive('/users')} onNavigate={closeSidebar} icon={ICONS.users}>ຈັດການຜູ້ໃຊ້</NavLink>
                                        <NavLink href={route('settings.index')} active={isActive('/settings')} onNavigate={closeSidebar} icon={ICONS.settings}>ຕັ້ງຄ່າລະບົບ</NavLink>
                                    </>
                                )}
                            </>
                        )}
                    </nav>

                    {user && (
                        <div className="p-4 mt-auto">
                            <div className="bg-brand-green-dark text-white p-5 rounded-3xl relative overflow-hidden">
                                <div className="relative z-10">
                                    <Link href={route('profile.show')} onClick={closeSidebar} className="flex items-center gap-3 group">
                                        <div className="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-white/10">
                                            <NavIcon path={ICONS.userCircle} />
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm font-bold truncate group-hover:text-brand-bright-green transition-colors">{user.name}</p>
                                            <p className="text-[10px] text-gray-400">{isAdmin ? 'Admin' : 'Staff'}</p>
                                        </div>
                                    </Link>
                                    <form onSubmit={logout} className="mt-4">
                                        <button type="submit" className="w-full flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-[11px] font-bold py-2.5 rounded-xl transition-colors">
                                            <svg className="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                <path strokeLinecap="round" strokeLinejoin="round" d={ICONS.logout} />
                                            </svg>
                                            ອອກຈາກລະບົບ
                                        </button>
                                    </form>
                                </div>
                                <div className="absolute -bottom-10 -right-10 w-32 h-32 bg-brand-bright-green rounded-full opacity-20 blur-2xl"></div>
                            </div>
                        </div>
                    )}
                </aside>

                <div className="flex-1 flex flex-col min-h-screen lg:ml-64">
                    <header className="lg:hidden sticky top-0 z-10 flex items-center justify-between h-14 px-4 bg-white border-b border-gray-200">
                        <button
                            onClick={() => setSidebarOpen(true)}
                            className="text-gray-500 hover:text-brand-green transition-colors p-1.5 -ml-1.5 rounded-lg hover:bg-gray-100"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d={ICONS.burger} />
                            </svg>
                        </button>
                        <span className="text-slate-800 font-bold text-sm">ລະບົບບັນທຶກການຂາດລາ</span>
                        {appLogo ? (
                            <div className="w-7 h-7 rounded-md overflow-hidden bg-brand-green flex items-center justify-center">
                                <img src={`/storage/${appLogo}`} alt="Logo" className="w-full h-full object-contain p-0.5" />
                            </div>
                        ) : (
                            <div className="w-7 h-7 rounded-md bg-brand-green flex items-center justify-center">
                                <span className="text-white text-sm select-none">☸</span>
                            </div>
                        )}
                    </header>

                    <main className="flex-1 p-4 sm:p-6 lg:p-8">
                        {children}
                    </main>
                </div>

            </div>

            <Toasts />
        </div>
    );
}
