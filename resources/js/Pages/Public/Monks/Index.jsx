import { useMemo, useState } from 'react';
import { Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';

const SECTIONS = [
    {
        key: 'monk',
        title: 'ພຣະສົງ',
        subtitle: 'Monks',
        unit: 'ອົງ',
        ring: 'ring-brand-green/15',
        badgeBg: 'bg-brand-light-green',
        badgeColor: 'text-brand-green',
        dot: 'bg-brand-green',
    },
    {
        key: 'novice',
        title: 'ສາມະເນນ',
        subtitle: 'Novices',
        unit: 'ອົງ',
        ring: 'ring-orange-400/15',
        badgeBg: 'bg-orange-50',
        badgeColor: 'text-orange-600',
        dot: 'bg-orange-500',
    },
    {
        key: 'nun',
        title: 'ແມ່ຂາວ',
        subtitle: 'Nuns',
        unit: 'ຄົນ',
        ring: 'ring-purple-400/15',
        badgeBg: 'bg-purple-50',
        badgeColor: 'text-purple-600',
        dot: 'bg-purple-500',
    },
];

const EAGER_COUNT = 8;

function MonkCard({ monk, ring, badgeBg, badgeColor, dot, priority, delay }) {
    const [loaded, setLoaded] = useState(false);

    return (
        <div
            className="fade-up group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden transition-shadow duration-300 hover:shadow-lg"
            style={{ '--fade-delay': `${delay}ms` }}
        >
            <div className={`relative aspect-[4/5] bg-gray-100 ring-1 ring-inset ${ring} overflow-hidden`}>
                {!loaded && <div className="absolute inset-0 animate-pulse bg-gray-100" aria-hidden="true"></div>}
                <img
                    src={monk.photo_url}
                    alt={monk.full_name}
                    loading={priority ? 'eager' : 'lazy'}
                    fetchpriority={priority ? 'high' : 'auto'}
                    decoding="async"
                    onLoad={() => setLoaded(true)}
                    className={`w-full h-full object-cover transition-opacity duration-500 group-hover:scale-[1.03] ${loaded ? 'opacity-100' : 'opacity-0'}`}
                    style={{ transitionProperty: 'opacity, transform' }}
                />
                <span className={`absolute top-2.5 right-2.5 inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold ${badgeBg} ${badgeColor} shadow-sm`}>
                    <span className={`w-1.5 h-1.5 rounded-full ${dot}`} aria-hidden="true"></span>
                    ພັນສາ {monk.pansa}
                </span>
            </div>

            <div className="p-3.5">
                <p className="font-semibold text-slate-800 text-sm leading-snug truncate" title={monk.full_name}>
                    {monk.full_name}
                </p>
                <p className="text-[11px] text-gray-400 mt-1 truncate">
                    {[monk.temple, monk.age ? `ອາຍຸ ${monk.age} ປີ` : null].filter(Boolean).join(' · ') || ' '}
                </p>
            </div>
        </div>
    );
}

function SearchIcon() {
    return (
        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
            <circle cx="11" cy="11" r="7" strokeLinecap="round" />
            <path d="M21 21l-4.3-4.3" strokeLinecap="round" />
        </svg>
    );
}

export default function Index({ monkGroup, noviceGroup, nunGroup, totalMonks, totalNovices, totalNuns, type }) {
    const groups = { monk: monkGroup, novice: noviceGroup, nun: nunGroup };
    const totalAll = totalMonks + totalNovices + totalNuns;
    const [query, setQuery] = useState('');

    const normalizedQuery = query.trim().toLowerCase();

    const filteredGroups = useMemo(() => {
        if (!normalizedQuery) return groups;
        const filter = (list) =>
            list.filter(
                (monk) =>
                    monk.full_name.toLowerCase().includes(normalizedQuery) ||
                    (monk.temple && monk.temple.toLowerCase().includes(normalizedQuery))
            );
        return { monk: filter(monkGroup), novice: filter(noviceGroup), nun: filter(nunGroup) };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [normalizedQuery, monkGroup, noviceGroup, nunGroup]);

    const resultCount = filteredGroups.monk.length + filteredGroups.novice.length + filteredGroups.nun.length;
    const isEmpty = totalAll === 0;
    const noResults = !isEmpty && normalizedQuery && resultCount === 0;

    let cardsRendered = 0;

    const tabs = [
        { value: null, label: 'ທັງໝົດ' },
        { value: 'monk', label: 'ພຣະສົງ' },
        { value: 'novice', label: 'ສາມະເນນ' },
        { value: 'nun', label: 'ແມ່ຂາວ' },
    ];

    return (
        <PublicLayout>
            <SeoHead
                title="ພຣະສົງ ສາມະເນນ ແລະ ຜູ້ບວດຂາວ"
                description="ລາຍຊື່ ແລະ ຂໍ້ມູນພຣະສົງ, ສາມະເນນ, ແມ່ຂາວ, ຜູ້ບວດຂາວ ປະຕິບັດທຳ ກຳມະຖານ ພາຍໃນວັດປ່າໜອງບົວທອງໃຕ້ ນະຄອນຫຼວງວຽງຈັນ ປະເທດລາວ"
                keywords="ພຣະສົງ, ສາມະເນນ, ບວດຂາວ, ແມ່ຂາວ, ວັດປ່າໜອງບົວທອງໃຕ້, ວັດປ່າໜອງບົວທອງ, ໜອງບົວທອງ, ພຸດທະສາສະໜາ, ປະເທດລາວ, ການປະຕິບັດທຳ, ກຳມະຖານ, ໄຫວ້ພຣະ"
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
                        ລາຍຊື່ພຣະສົງ ແລະ ສາມະເນນທັງໝົດພາຍໃນວັດ ຈຳນວນທັງໝົດ {totalAll} ຮູບ
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
                {/* Filter tabs + search */}
                <nav
                    aria-label="ປະເພດສະມາຊິກ"
                    className="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5"
                >
                    <div className="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div className="relative flex-1 min-w-0">
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

                        {!isEmpty && (
                            <div className="relative shrink-0 sm:w-56">
                                <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <SearchIcon />
                                </span>
                                <input
                                    type="text"
                                    value={query}
                                    onChange={(e) => setQuery(e.target.value)}
                                    placeholder="ຄົ້ນຫາຊື່..."
                                    aria-label="ຄົ້ນຫາຕາມຊື່"
                                    className="w-full bg-white border border-gray-200 rounded-full pl-9 pr-8 py-1.5 text-xs text-slate-700 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-green/30 focus:border-transparent transition-shadow"
                                />
                                {query && (
                                    <button
                                        type="button"
                                        onClick={() => setQuery('')}
                                        aria-label="ລ້າງການຄົ້ນຫາ"
                                        className="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition-colors"
                                    >
                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                            <path strokeLinecap="round" d="M6 6l12 12M18 6L6 18" />
                                        </svg>
                                    </button>
                                )}
                            </div>
                        )}
                    </div>
                </nav>

                {isEmpty ? (
                    /* Empty state — nothing registered at all */
                    <div className="flex flex-col items-center justify-center text-center py-24">
                        <span className="text-5xl text-brand-green/30 mb-4">☸</span>
                        <p className="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີຂໍ້ມູນ</p>
                        <p className="text-slate-400 text-sm">ກະລຸນາກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
                    </div>
                ) : noResults ? (
                    /* Empty state — search matched nothing */
                    <div className="flex flex-col items-center justify-center text-center py-24">
                        <span className="text-5xl text-gray-200 mb-4">
                            <SearchIcon />
                        </span>
                        <p className="font-bold text-slate-700 mb-1">ບໍ່ພົບ "{query}"</p>
                        <p className="text-slate-400 text-sm mb-4">ລອງຄົ້ນຫາດ້ວຍຊື່ອື່ນ ຫຼື ລ້າງການຄົ້ນຫາ</p>
                        <button
                            type="button"
                            onClick={() => setQuery('')}
                            className="px-4 py-2 rounded-full text-xs font-bold bg-white border border-gray-200 text-slate-600 hover:bg-gray-50 transition-colors"
                        >
                            ລ້າງການຄົ້ນຫາ
                        </button>
                    </div>
                ) : (
                    SECTIONS.map((section, index) => {
                        const list = filteredGroups[section.key];
                        if (list.length === 0) return null;

                        return (
                            <div key={section.key} className={index < SECTIONS.length - 1 ? 'mb-10' : ''}>
                                <div className="flex items-center gap-3 mb-5">
                                    <div className="flex items-center gap-2.5 shrink-0">
                                        <span className={`w-2 h-2 rounded-full ${section.dot}`} aria-hidden="true"></span>
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

                                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                    {list.map((monk) => {
                                        const isPriority = cardsRendered < EAGER_COUNT;
                                        const delay = Math.min(cardsRendered, 10) * 30;
                                        cardsRendered += 1;
                                        return (
                                            <MonkCard
                                                key={monk.id}
                                                monk={monk}
                                                ring={section.ring}
                                                badgeBg={section.badgeBg}
                                                badgeColor={section.badgeColor}
                                                dot={section.dot}
                                                priority={isPriority}
                                                delay={delay}
                                            />
                                        );
                                    })}
                                </div>
                            </div>
                        );
                    })
                )}
            </div>
        </PublicLayout>
    );
}
