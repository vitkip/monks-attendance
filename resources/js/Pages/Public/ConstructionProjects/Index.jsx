import { Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';
import Pagination from '@/Components/Pagination';

function fmt(n) {
    return new Intl.NumberFormat('en-US').format(Math.round(n));
}

const statusPillClasses = {
    completed: 'bg-brand-light-green text-brand-green',
    paused: 'bg-gray-100 text-gray-500',
    ongoing: 'bg-amber-50 text-amber-600',
};

const statusPillOverlayClasses = {
    completed: 'bg-brand-green/90 text-white',
    paused: 'bg-white/90 text-gray-600',
    ongoing: 'bg-amber-500/90 text-white',
};

const barClasses = {
    completed: 'bg-brand-green',
    paused: 'bg-gray-300',
    ongoing: 'bg-gradient-to-r from-amber-400 to-brand-green-mid',
};

const FILTERS = [
    ['', 'ທັງໝົດ'],
    ['ongoing', 'ກຳລັງດຳເນີນການ'],
    ['completed', 'ສຳເລັດແລ້ວ'],
];

export default function ConstructionProjectsPublicIndex({ status, projects, statuses, totalIncomeAll, ongoingCount, featured }) {
    return (
        <PublicLayout>
            <SeoHead
                title="ໂຄງການກໍ່ສ້າງ"
                description="ຕິດຕາມໂຄງການກໍ່ສ້າງ ແລະ ຄວາມຄືບໜ້າພາຍໃນວັດປ່າໜອງບົວທອງໃຕ້"
            />

            {/* Hero */}
            <section className="relative overflow-hidden bg-brand-green-dark">
                <span
                    className="absolute -right-6 -top-14 text-[240px] leading-none text-white/[0.04] select-none pointer-events-none"
                    aria-hidden="true">🏗️</span>

                <div className="relative max-w-6xl mx-auto px-5 sm:px-8 pt-16 pb-10 sm:pt-20 sm:pb-12">
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                        <span aria-hidden="true">🏗️</span> ຄວາມໂປ່ງໃສການກໍ່ສ້າງ
                    </span>

                    <h1 className="text-white text-3xl sm:text-4xl font-bold leading-tight">ຮ່ວມສ້າງບຸນ ກໍ່ສ້າງວັດ</h1>
                    <p className="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                        ຕິດຕາມຄວາມຄືບໜ້າ ທຶນບຸນ ແລະ ລາຍຈ່າຍຂອງແຕ່ລະໂຄງການກໍ່ສ້າງຂອງວັດ ເປີດເຜີຍໃຫ້ຍາດໂຍມ ແລະ ຜູ້ມີສັດທາຮ່ວມຕິດຕາມໄດ້
                    </p>

                    <div className="flex flex-wrap items-center gap-3 mt-7">
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-white tabular-nums leading-none">{fmt(totalIncomeAll)}</span>
                            <span className="text-xs text-white/60">ກີບ · ທຶນບຸນທີ່ໄດ້ຮັບທັງໝົດ</span>
                        </div>
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{ongoingCount}</span>
                            <span className="text-xs text-white/60">ໂຄງການ · ກຳລັງດຳເນີນການ</span>
                        </div>
                    </div>

                    {/* Featured project — the flagship fundraising board */}
                    {featured && (
                        <div className="mt-9 bg-white/[0.04] rounded-2xl px-5 sm:px-6 py-6">
                            <div className="flex items-center justify-between gap-3 mb-4">
                                <div className="flex items-center gap-3 min-w-0">
                                    {featured.image_url && (
                                        <img src={featured.image_url} alt={featured.name}
                                            className="w-12 h-12 rounded-xl object-cover shrink-0 border border-white/10" />
                                    )}
                                    <div className="min-w-0">
                                        <p className="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-1">ໂຄງການທີ່ກຳລັງລະດົມທຶນ</p>
                                        <p className="text-white font-bold truncate">{featured.name}</p>
                                    </div>
                                </div>
                                <Link href={route('construction-projects.public.show', featured.id)}
                                    className="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-brand-bright-green hover:text-white transition-colors">
                                    ເບິ່ງລາຍລະອຽດ
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M8 4l6 6-6 6" />
                                    </svg>
                                </Link>
                            </div>

                            <div className="relative h-3.5 rounded-full bg-white/10 mb-2">
                                <div className="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-amber-400 to-brand-bright-green"
                                    style={{ width: `${featured.progress_percent}%` }}></div>
                                <div className="absolute inset-y-0 left-1/4 w-px bg-black/10"></div>
                                <div className="absolute inset-y-0 left-1/2 w-px bg-black/10"></div>
                                <div className="absolute inset-y-0 left-3/4 w-px bg-black/10"></div>
                                <div className="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-white border-2 border-brand-bright-green shadow-sm"
                                    style={{ left: `${featured.progress_percent}%` }}></div>
                            </div>
                            <div className="flex items-center justify-between text-xs">
                                <span className="text-white/70 font-semibold tabular-nums">{fmt(featured.total_income)} ກີບ</span>
                                <span className="text-brand-bright-green font-bold tabular-nums">{featured.progress_percent}%</span>
                                <span className="text-white/40 tabular-nums">ເປົ້າ {fmt(featured.target_amount)} ກີບ</span>
                            </div>
                        </div>
                    )}
                </div>
            </section>

            <div className="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

                {/* Status filter */}
                <nav aria-label="ເລືອກສະຖານະ"
                    className="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5">
                    <div className="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                        {FILTERS.map(([value, label]) => (
                            <Link
                                key={value}
                                href={value ? route('construction-projects.public.index', { status: value }) : route('construction-projects.public.index')}
                                className={`shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                                          ${status === value ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50'}`}>
                                {label}
                            </Link>
                        ))}
                    </div>
                </nav>

                {projects.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center text-center py-24">
                        <span className="text-5xl text-brand-green/30 mb-4">🏗️</span>
                        <p className="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີໂຄງການໃນໝວດນີ້</p>
                        <p className="text-slate-400 text-sm">ກະລຸນາເລືອກໝວດອື່ນ ຫຼື ກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
                    </div>
                ) : (
                    <>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            {projects.data.map((project) => (
                                <Link key={project.id} href={route('construction-projects.public.show', project.id)}
                                    className="group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden flex flex-col
                                              transition-all duration-300 hover:-translate-y-1 hover:shadow-xl
                                              focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                                    {project.image_url ? (
                                        <div className="relative overflow-hidden">
                                            <img src={project.image_url} alt={project.name}
                                                className="w-full aspect-[16/10] object-cover transition-transform duration-300 group-hover:scale-105" />
                                            <span className={`absolute top-3 left-3 inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold backdrop-blur-sm ${statusPillOverlayClasses[project.status] || 'bg-white/90 text-gray-600'}`}>
                                                {statuses[project.status] || project.status}
                                            </span>
                                        </div>
                                    ) : (
                                        <div className={`h-1.5 w-full ${barClasses[project.status] || barClasses.ongoing}`}></div>
                                    )}
                                    <div className="px-5 pt-4 pb-5 flex-1">
                                        <div className="flex items-center gap-2 mb-2">
                                            {!project.image_url && (
                                                <span className={`inline-flex px-2 py-0.5 rounded-lg text-[11px] font-semibold ${statusPillClasses[project.status] || 'bg-gray-50 text-gray-600'}`}>
                                                    {statuses[project.status] || project.status}
                                                </span>
                                            )}
                                            {project.start_date_label && <span className="text-gray-300 text-[11px] truncate">ເລີ່ມ {project.start_date_label}</span>}
                                        </div>
                                        <h3 className="font-bold text-slate-800 leading-snug group-hover:text-brand-green transition-colors mb-3">{project.name}</h3>

                                        {project.percent !== null ? (
                                            <>
                                                <div className="flex items-baseline justify-between mb-1">
                                                    <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ລະດົມທຶນໄດ້</span>
                                                    <span className="text-xs font-extrabold text-brand-green tabular-nums">{project.percent}%</span>
                                                </div>
                                                <div className="relative h-2 rounded-full bg-brand-light-green mb-2">
                                                    <div className="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-[#ffb366] to-brand-green"
                                                        style={{ width: `${project.percent}%` }}></div>
                                                </div>
                                                <p className="text-[11px] text-gray-400">{fmt(project.income_sum)} / {fmt(project.target_amount)} ກີບ</p>
                                            </>
                                        ) : (
                                            <div className="grid grid-cols-2 gap-2">
                                                <div>
                                                    <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ໄດ້ຮັບ</p>
                                                    <p className="text-xs font-bold text-brand-green truncate tabular-nums">{fmt(project.income_sum)} ກີບ</p>
                                                </div>
                                                <div>
                                                    <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">ຈ່າຍແລ້ວ</p>
                                                    <p className="text-xs font-bold text-rose-800 truncate tabular-nums">{fmt(project.expense_sum)} ກີບ</p>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </Link>
                            ))}
                        </div>

                        {projects.data.length > 0 && <div className="mt-8"><Pagination links={projects.links} /></div>}
                    </>
                )}
            </div>
        </PublicLayout>
    );
}
