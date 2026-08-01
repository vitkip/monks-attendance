import { Link, usePage } from '@inertiajs/react';

export default function GuestLayout({ children }) {
    const { appLogo } = usePage().props;

    return (
        <div className="min-h-screen bg-[#faf8f2] text-slate-700 lg:grid lg:grid-cols-[minmax(0,7fr)_minmax(0,8fr)]">

            {/* Brand panel — temple identity */}
            <div className="relative overflow-hidden bg-brand-green-dark text-white flex flex-col justify-between
                        px-6 py-8 sm:px-10 sm:py-10 lg:px-14 lg:py-14 lg:min-h-screen">

                <div className="glow-dawn absolute left-1/2 bottom-10 sm:bottom-14 lg:bottom-20 -translate-x-1/2 w-[420px] h-[420px] rounded-full pointer-events-none" aria-hidden="true"></div>

                <Link href={route('news.public.index')} className="relative z-10 flex items-center gap-3 group w-fit">
                    {appLogo ? (
                        <span className="w-10 h-10 rounded-xl overflow-hidden bg-white/10 backdrop-blur-md flex items-center justify-center flex-shrink-0">
                            <img src={`/storage/${appLogo}`} alt="Logo" className="w-full h-full object-contain p-1" />
                        </span>
                    ) : (
                        <span className="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center flex-shrink-0">
                            <span className="text-xl select-none">☸</span>
                        </span>
                    )}
                    <span className="leading-tight">
                        <span className="block text-sm font-bold group-hover:text-brand-bright-green transition-colors">ວັດປ່າໜອງບົວທອງໃຕ້</span>
                        <span className="block text-[10px] text-white/50 uppercase tracking-widest group-hover:text-white/70 transition-colors">← ກັບໜ້າຫຼັກ</span>
                    </span>
                </Link>

                <div className="relative z-10 mt-8 lg:mt-0">
                    <p className="text-[11px] font-bold text-brand-bright-green uppercase tracking-[0.25em] mb-3">Monk Attendance System</p>
                    <h1 className="text-2xl sm:text-3xl font-bold leading-snug mb-5 max-w-xs">ລະບົບກວດສອບ<br />ການຂາດລາ</h1>
                    <ul className="hidden sm:flex flex-wrap gap-x-4 gap-y-2 text-xs text-white/60">
                        <li className="flex items-center gap-1.5"><span className="w-1 h-1 rounded-full bg-brand-bright-green"></span>ພຣະສົງ</li>
                        <li className="flex items-center gap-1.5"><span className="w-1 h-1 rounded-full bg-brand-bright-green"></span>ສາມະເນນ</li>
                        <li className="flex items-center gap-1.5"><span className="w-1 h-1 rounded-full bg-brand-bright-green"></span>ການຂາດລາ</li>
                        <li className="flex items-center gap-1.5"><span className="w-1 h-1 rounded-full bg-brand-bright-green"></span>ຄ່າປັບ</li>
                    </ul>
                </div>

                <svg className="relative z-10 w-full max-w-[340px] mx-auto lg:mx-0 h-auto mt-8 lg:mt-0" viewBox="0 0 400 170" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                    <defs>
                        <linearGradient id="roofGrad" x1="0" y1="170" x2="0" y2="0" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stopColor="#8f4e00" />
                            <stop offset="1" stopColor="#ff9933" />
                        </linearGradient>
                    </defs>
                    <polygon points="10,170 390,170 340,140 60,140" fill="url(#roofGrad)" opacity="0.5" />
                    <polygon points="60,140 340,140 295,112 105,112" fill="url(#roofGrad)" opacity="0.68" />
                    <polygon points="105,112 295,112 258,86 142,86" fill="url(#roofGrad)" opacity="0.84" />
                    <polygon points="142,86 258,86 228,60 172,60" fill="url(#roofGrad)" />
                    <rect x="197" y="24" width="6" height="38" fill="url(#roofGrad)" />
                    <circle cx="200" cy="18" r="7" fill="url(#roofGrad)" />
                </svg>
            </div>

            {/* Form panel */}
            <div className="flex flex-col justify-center px-6 py-10 sm:px-12 lg:px-16 xl:px-24">
                <div className="w-full max-w-sm mx-auto">
                    {children}
                </div>
            </div>

        </div>
    );
}
