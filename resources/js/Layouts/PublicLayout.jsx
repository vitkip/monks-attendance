import { Link, usePage } from '@inertiajs/react';

const NAV = [
    ['news.public.index', 'ຂ່າວສານ', '/'],
    ['monks.public.index', 'ພຣະສົງ', '/info/monks'],
    ['chants.public.index', 'ບົດສູດມົນ', '/info/chants'],
    ['electricity-bills.public.index', 'ລາຍການຄ່າໄຟຟ້າ', '/info/electricity-bills'],
    ['construction-projects.public.index', 'ໂຄງການກໍ່ສ້າງ', '/info/construction-projects'],
];

function isActivePath(currentUrl, prefix) {
    if (prefix === '/') return currentUrl === '/';
    return currentUrl === prefix || currentUrl.startsWith(prefix + '/') || currentUrl.startsWith(prefix + '?');
}

export default function PublicLayout({ children }) {
    const { auth, appLogo, contact } = usePage().props;
    const currentUrl = usePage().url;
    const hasSocial = contact?.whatsapp || contact?.facebook || contact?.email || contact?.youtube;

    return (
        <div className="bg-[#faf8f2] font-sans text-slate-700 min-h-screen flex flex-col">
            <header className="bg-brand-green-dark text-white sticky top-0 z-20">
                <div className="max-w-6xl mx-auto px-5 sm:px-8 h-16 flex items-center justify-between gap-4">
                    <Link href={route('news.public.index')} className="flex items-center gap-3 group shrink-0">
                        {appLogo ? (
                            <span className="w-9 h-9 rounded-lg overflow-hidden bg-white/10 flex items-center justify-center flex-shrink-0">
                                <img src={`/storage/${appLogo}`} alt="Logo" className="w-full h-full object-contain p-1" />
                            </span>
                        ) : (
                            <span className="w-9 h-9 rounded-lg bg-brand-green flex items-center justify-center flex-shrink-0">
                                <span className="text-white text-base select-none">☸</span>
                            </span>
                        )}
                        <span className="leading-tight hidden sm:block">
                            <span className="block text-sm font-bold group-hover:text-brand-bright-green transition-colors">ວັດປ່າໜອງບົວທອງໃຕ້</span>
                            <span className="block text-[10px] text-white/50 uppercase tracking-widest">ວັດ · ຂ່າວສານຊຸມຊົນ</span>
                        </span>
                    </Link>

                    <nav className="flex items-center gap-1 overflow-x-auto no-scrollbar">
                        {NAV.map(([routeName, label, prefix]) => (
                            <Link
                                key={routeName}
                                href={route(routeName)}
                                className={`shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors ${
                                    isActivePath(currentUrl, prefix) ? 'bg-white/15 text-white' : 'text-white/60 hover:text-white'
                                }`}
                            >
                                {label}
                            </Link>
                        ))}
                    </nav>

                    <Link
                        href={auth?.user ? route('absences.index') : route('login')}
                        className="text-xs font-semibold text-white/70 hover:text-white transition-colors flex items-center gap-1.5 shrink-0"
                    >
                        {auth?.user ? 'ໄປໜ້າລະບົບ' : 'ເຂົ້າສູ່ລະບົບ'}
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M8 4l6 6-6 6" />
                        </svg>
                    </Link>
                </div>
                <div className="h-[3px] bg-gradient-to-r from-transparent via-brand-bright-green/70 to-transparent" aria-hidden="true"></div>
            </header>

            <main className="flex-1">
                {children}
            </main>

            <footer className="border-t border-black/5 mt-16">
                <div className="max-w-6xl mx-auto px-5 sm:px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <Link href={route('news.public.index')} className="flex items-center gap-3 shrink-0">
                        <span className="w-9 h-9 rounded-lg bg-brand-green flex items-center justify-center">
                            <span className="text-white text-base select-none">☸</span>
                        </span>
                        <span className="leading-tight">
                            <span className="block text-sm font-bold text-slate-700">ວັດປ່າໜອງບົວທອງໃຕ້</span>
                            <span className="block text-[11px] text-slate-400">ວັດ · ຂ່າວສານຊຸມຊົນ</span>
                        </span>
                    </Link>

                    <nav className="flex items-center gap-5 text-xs font-semibold text-slate-500">
                        <Link href={route('news.public.index')} className="hover:text-brand-green transition-colors">ຂ່າວສານ</Link>
                        <Link href={route('monks.public.index')} className="hover:text-brand-green transition-colors">ພຣະສົງ</Link>
                        <Link href={route('chants.public.index')} className="hover:text-brand-green transition-colors">ບົດສູດມົນ</Link>
                        <Link href={route('electricity-bills.public.index')} className="hover:text-brand-green transition-colors">ຄ່າໄຟຟ້າ</Link>
                        <Link href={route('construction-projects.public.index')} className="hover:text-brand-green transition-colors">ກໍ່ສ້າງ</Link>
                    </nav>

                    {hasSocial && (
                        <div className="flex items-center gap-3 shrink-0">
                            {contact.whatsapp && (
                                <a href={`https://wa.me/${contact.whatsapp.replace(/\D/g, '')}`} target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"
                                    className="w-8 h-8 rounded-full bg-[#f8fafa] flex items-center justify-center text-slate-400 hover:text-brand-green hover:bg-brand-light-green transition-colors">
                                    <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 004.74 1.2h.005c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.87 9.87 0 0012.04 2zm5.8 14.05c-.24.68-1.4 1.3-1.93 1.38-.5.08-1.12.11-1.8-.11-.42-.13-.95-.31-1.64-.6-2.88-1.24-4.76-4.14-4.9-4.33-.14-.19-1.17-1.56-1.17-2.98 0-1.42.74-2.11 1-2.4.26-.29.57-.36.76-.36.19 0 .38 0 .55.01.18.01.41-.07.64.49.24.58.81 2 .88 2.15.07.15.12.32.02.51-.09.19-.14.31-.28.48-.14.17-.29.37-.42.5-.14.14-.28.29-.12.57.16.29.72 1.19 1.55 1.93 1.07.95 1.96 1.25 2.25 1.39.29.14.46.12.63-.07.17-.19.71-.83.9-1.11.19-.29.38-.24.63-.14.26.1 1.65.78 1.93.92.29.14.48.21.55.33.07.12.07.68-.17 1.36z" /></svg>
                                </a>
                            )}
                            {contact.facebook && (
                                <a href={contact.facebook} target="_blank" rel="noopener noreferrer" aria-label="Facebook"
                                    className="w-8 h-8 rounded-full bg-[#f8fafa] flex items-center justify-center text-slate-400 hover:text-brand-green hover:bg-brand-light-green transition-colors">
                                    <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5.02 3.66 9.18 8.44 9.94v-7.03H7.9v-2.91h2.54V9.85c0-2.51 1.49-3.9 3.77-3.9 1.09 0 2.24.2 2.24.2v2.47h-1.26c-1.24 0-1.63.78-1.63 1.57v1.88h2.78l-.44 2.91h-2.34V22c4.78-.76 8.44-4.92 8.44-9.94z" /></svg>
                                </a>
                            )}
                            {contact.youtube && (
                                <a href={contact.youtube} target="_blank" rel="noopener noreferrer" aria-label="YouTube"
                                    className="w-8 h-8 rounded-full bg-[#f8fafa] flex items-center justify-center text-slate-400 hover:text-brand-green hover:bg-brand-light-green transition-colors">
                                    <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.5 6.19a3.02 3.02 0 00-2.12-2.14C19.51 3.5 12 3.5 12 3.5s-7.51 0-9.38.55A3.02 3.02 0 00.5 6.19 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.81 3.02 3.02 0 002.12 2.14c1.87.55 9.38.55 9.38.55s7.51 0 9.38-.55a3.02 3.02 0 002.12-2.14A31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.81zM9.6 15.5v-7l6.2 3.5-6.2 3.5z" /></svg>
                                </a>
                            )}
                            {contact.email && (
                                <a href={`mailto:${contact.email}`} aria-label="Email"
                                    className="w-8 h-8 rounded-full bg-[#f8fafa] flex items-center justify-center text-slate-400 hover:text-brand-green hover:bg-brand-light-green transition-colors">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </a>
                            )}
                        </div>
                    )}

                    <span className="text-xs text-slate-400 shrink-0">© {new Date().getFullYear()} ວັດປ່າໜອງບົວທອງໃຕ້</span>
                </div>
            </footer>
        </div>
    );
}
