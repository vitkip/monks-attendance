import { Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';

const SECTIONS = [
    {
        key: 'monk',
        title: 'ພຣະສົງ',
        subtitle: 'Monks',
        unit: 'ອົງ',
        iconBg: 'bg-brand-light-green',
        iconColor: 'text-brand-green',
        badgeBg: 'bg-brand-light-green',
        badgeColor: 'text-brand-green',
    },
    {
        key: 'novice',
        title: 'ສາມະເນນ',
        subtitle: 'Novices',
        unit: 'ອົງ',
        iconBg: 'bg-orange-50',
        iconColor: 'text-orange-500',
        badgeBg: 'bg-orange-50',
        badgeColor: 'text-orange-600',
    },
    {
        key: 'nun',
        title: 'ແມ່ຂາວ',
        subtitle: 'Nuns',
        unit: 'ຄົນ',
        iconBg: 'bg-purple-50',
        iconColor: 'text-purple-500',
        badgeBg: 'bg-purple-50',
        badgeColor: 'text-purple-600',
    },
];

function MonkCard({ monk, badgeBg, badgeColor }) {
    return (
        <div className="bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden p-4 flex items-start gap-3 transition-shadow duration-300 hover:shadow-lg">
            <img
                src={monk.photo_url}
                alt={monk.full_name}
                loading="lazy"
                className="w-12 h-12 rounded-full object-cover shrink-0 border border-gray-200"
            />
            <div className="flex-1 min-w-0">
                <p className="font-semibold text-slate-800 text-sm leading-snug">{monk.full_name}</p>
                {monk.temple && <p className="text-[11px] text-gray-400 mt-0.5">{monk.temple}</p>}
                <span className={`inline-flex items-center mt-2 px-2.5 py-1 rounded-full text-[11px] font-medium ${badgeBg} ${badgeColor}`}>
                    ພັນສາ {monk.pansa}
                </span>
            </div>
        </div>
    );
}

export default function Index({ monkGroup, noviceGroup, nunGroup, totalMonks, totalNovices, totalNuns, type }) {
    const groups = { monk: monkGroup, novice: noviceGroup, nun: nunGroup };
    const isEmpty = monkGroup.length === 0 && noviceGroup.length === 0 && nunGroup.length === 0;

    const tabs = [
        { value: null, label: 'ທັງໝົດ' },
        { value: 'monk', label: 'ພຣະສົງ' },
        { value: 'novice', label: 'ສາມະເນນ' },
        { value: 'nun', label: 'ແມ່ຂາວ' },
    ];

    return (
        <PublicLayout>
            <SeoHead
                title="ພຣະສົງ ແລະ ສາມະເນນ"
                description="ຂໍ້ມູນພຣະສົງ ແລະ ສາມະເນນ ພາຍໃນວັດປ່າໜອງບົວທອງໃຕ້"
            />

            {/* Hero */}
            <section className="relative overflow-hidden bg-brand-green-dark">
                <span
                    className="absolute -right-6 -top-14 text-[240px] leading-none text-white/[0.04] select-none pointer-events-none"
                    aria-hidden="true"
                >
                    ☸
                </span>

                <div className="relative max-w-6xl mx-auto px-5 sm:px-8 pt-16 pb-10 sm:pt-20 sm:pb-12">
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                        <span aria-hidden="true">☸</span> ທະບຽນວັດ
                    </span>

                    <h1 className="text-white text-3xl sm:text-4xl font-bold leading-tight">ພຣະສົງ ແລະ ສາມະເນນ</h1>
                    <p className="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                        ລາຍຊື່ພຣະສົງ ແລະ ສາມະເນນທັງໝົດພາຍໃນວັດ
                    </p>

                    <div className="flex flex-wrap items-center gap-3 mt-7">
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-white tabular-nums leading-none">{totalMonks}</span>
                            <span className="text-xs text-white/60">ອົງ · ພຣະສົງ</span>
                        </div>
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{totalNovices}</span>
                            <span className="text-xs text-white/60">ອົງ · ສາມະເນນ</span>
                        </div>
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-purple-300 tabular-nums leading-none">{totalNuns}</span>
                            <span className="text-xs text-white/60">ຄົນ · ແມ່ຂາວ</span>
                        </div>
                    </div>
                </div>
            </section>

            <div className="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">
                {/* Filter tabs */}
                <nav
                    aria-label="ປະເພດສະມາຊິກ"
                    className="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5"
                >
                    <div className="relative">
                        <div className="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                            {tabs.map((tab) => (
                                <Link
                                    key={tab.label}
                                    href={route('monks.public.index', tab.value ? { type: tab.value } : {})}
                                    className={`shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green ${
                                        type === tab.value
                                            ? 'bg-brand-green text-white'
                                            : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50'
                                    }`}
                                >
                                    {tab.label}
                                </Link>
                            ))}
                        </div>
                        <div
                            className="sm:hidden pointer-events-none absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-[#faf8f2] to-transparent"
                            aria-hidden="true"
                        ></div>
                    </div>
                </nav>

                {isEmpty ? (
                    /* Empty state */
                    <div className="flex flex-col items-center justify-center text-center py-24">
                        <span className="text-5xl text-brand-green/30 mb-4">☸</span>
                        <p className="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີຂໍ້ມູນ</p>
                        <p className="text-slate-400 text-sm">ກະລຸນາກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
                    </div>
                ) : (
                    SECTIONS.map((section, index) => {
                        const list = groups[section.key];
                        if (list.length === 0) return null;

                        return (
                            <div key={section.key} className={index < SECTIONS.length - 1 ? 'mb-10' : ''}>
                                <div className="flex items-center gap-3 mb-5">
                                    <div className="flex items-center gap-2.5 shrink-0">
                                        <div className={`w-8 h-8 rounded-lg ${section.iconBg} flex items-center justify-center`}>
                                            <span className={`${section.iconColor} text-sm`}>☸</span>
                                        </div>
                                        <div>
                                            <p className="text-sm font-bold text-slate-800 leading-none">{section.title}</p>
                                            <p className="text-[10px] text-gray-400 mt-0.5">{section.subtitle}</p>
                                        </div>
                                    </div>
                                    <div className="flex-1 h-px bg-gray-200"></div>
                                    <span className="text-[11px] text-gray-400 font-medium px-2 py-1 bg-gray-100 rounded-full">
                                        {list.length} {section.unit}
                                    </span>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                    {list.map((monk) => (
                                        <MonkCard key={monk.id} monk={monk} badgeBg={section.badgeBg} badgeColor={section.badgeColor} />
                                    ))}
                                </div>
                            </div>
                        );
                    })
                )}
            </div>
        </PublicLayout>
    );
}
