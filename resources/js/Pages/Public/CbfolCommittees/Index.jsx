import { useMemo, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';

export default function PublicCbfolCommitteesIndex({
    presidents,
    standing,
    committeeMembers,
    advisors,
    counts,
    categories,
    selectedCategory: initialCategory,
    selectedTerm,
    search: initialSearch,
}) {
    const [activeTab, setActiveTab] = useState(initialCategory || 'all');
    const [searchTerm, setSearchTerm] = useState(initialSearch || '');
    const [selectedMember, setSelectedMember] = useState(null);

    // Filter members based on search
    const filterList = (list) => {
        if (!searchTerm) return list;
        const q = searchTerm.toLowerCase();
        return list.filter(
            (m) =>
                m.name.toLowerCase().includes(q) ||
                (m.title && m.title.toLowerCase().includes(q)) ||
                (m.organization && m.organization.toLowerCase().includes(q))
        );
    };

    const filteredPresidents = useMemo(() => filterList(presidents), [presidents, searchTerm]);
    const filteredStanding = useMemo(() => filterList(standing), [standing, searchTerm]);
    const filteredCommittee = useMemo(() => filterList(committeeMembers), [committeeMembers, searchTerm]);
    const filteredAdvisors = useMemo(() => filterList(advisors), [advisors, searchTerm]);

    const presidentMain = filteredPresidents.find((p) => p.sort_order === 1) || filteredPresidents[0];
    const vicePresidents = filteredPresidents.filter((p) => p.id !== presidentMain?.id);

    return (
        <PublicLayout>
            <SeoHead
                title="ຄະນະບໍລິຫານງານສູນກາງອົງການພຸດທະສາສະໜາສັມພັນ ແຫ່ງ ສປປ ລາວ"
                description="ລາຍຊື່ຄະນະບໍລິຫານງານສູນກາງອົງການພຸດທະສາສະໜາສັມພັນ ແຫ່ງ ສປປ ລາວ (ອພສ) ສະໄໝທີ IX ປະທານ, ຮອງປະທານ, ຄະນະປະຈຳ, ຄະນະກຳມະການ ແລະ ທີ່ປຶກສາ"
            />

            <div className="min-h-screen pb-20">
                {/* Grand Ceremonial Banner / Hero */}
                <div className="relative bg-gradient-to-b from-[#2a1708] via-[#3d220a] to-[#1f1105] text-white pt-12 pb-20 px-4 sm:px-6 lg:px-8 overflow-hidden shadow-xl">
                    {/* Golden decorative ambient glows */}
                    <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
                    <div className="absolute bottom-0 right-10 w-96 h-96 bg-orange-600/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div className="max-w-5xl mx-auto text-center relative z-10 space-y-5">
                        {/* Buddhist Emblem */}
                        <div className="inline-flex items-center justify-center w-32 h-32 sm:w-36 sm:h-36 md:w-40 md:h-40 rounded-full bg-gradient-to-br from-amber-400 via-amber-600 to-amber-700 p-1.5 shadow-2xl ring-4 ring-amber-400/30">
                            <div className="w-full h-full rounded-full bg-[#231a13] flex items-center justify-center p-2 overflow-hidden">
                                <img src="/logo.png" alt="Logo" className="w-full h-full object-contain drop-shadow-md" />
                            </div>
                        </div>

                        {/* Titles */}
                        <div className="space-y-2">
                            <span className="inline-block px-4 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-400/30 tracking-wide uppercase">
                                {selectedTerm} · ພສ 2568 (ຄສ 2025)
                            </span>
                            <h1 className="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-amber-100 leading-snug">
                                ຄະນະບໍລິຫານງານສູນກາງອົງການພຸດທະສາສະໜາສັມພັນ ແຫ່ງ ສປປ ລາວ
                            </h1>
                            <p className="text-xs sm:text-sm text-amber-200/80 font-medium tracking-wider">
                                Central Buddhist Fellowship Organization of Lao PDR (CBFOL)
                            </p>
                        </div>

                        {/* Historical resolution note */}
                        <div className="max-w-2xl mx-auto text-xs text-amber-100/70 bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur-sm leading-relaxed">
                            ກອງປະຊຸມໃຫຍ່ຜູ້ແທນພຣະສົງທົ່ວປະເທດ ສະໄໝທີ IX ອົງການພຸດທະສາສະໜາສັມພັນ ແຫ່ງ ສປປ ລາວ
                            ໃນວັນທີ 23 ທັນວາ 2568 (ຄສ 2025) ໄດ້ຮັບຮອງເອົາ ຄະນະກຳມະການບໍລິຫານງານ ສູນກາງ ອພສ 99 ອົງ
                            ແລະ ຄະນະທີ່ປຶກສາ ສູນກາງ ອພສ 15 ອົງ.
                        </div>

                        {/* Fast Stats Badges */}
                        <div className="pt-2 flex flex-wrap items-center justify-center gap-3 text-xs">
                            <div className="px-3.5 py-1.5 rounded-full bg-amber-400/10 border border-amber-400/20 text-amber-200">
                                ປະທານ - ຮອງປະທານ: <strong className="text-white font-bold">{counts.president || 9} ອົງ</strong>
                            </div>
                            <div className="px-3.5 py-1.5 rounded-full bg-orange-400/10 border border-orange-400/20 text-orange-200">
                                ຄະນະປະຈຳການ: <strong className="text-white font-bold">{counts.standing || 26} ອົງ</strong>
                            </div>
                            <div className="px-3.5 py-1.5 rounded-full bg-blue-400/10 border border-blue-400/20 text-blue-200">
                                ຄະນະກຳມະການ: <strong className="text-white font-bold">{counts.member ? '99' : '99'} ອົງ</strong>
                            </div>
                            <div className="px-3.5 py-1.5 rounded-full bg-emerald-400/10 border border-emerald-400/20 text-emerald-200">
                                ຄະນະທີ່ປຶກສາ: <strong className="text-white font-bold">{counts.advisor || 15} ອົງ</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Main Content Area */}
                <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10 space-y-8">
                    {/* Navigation Pills & Search Bar */}
                    <div className="bg-white rounded-3xl p-3 sm:p-4 shadow-xl border border-black/5 flex flex-col md:flex-row items-center justify-between gap-4">
                        {/* Tabs */}
                        <div className="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 no-scrollbar">
                            <button
                                type="button"
                                onClick={() => setActiveTab('all')}
                                className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all whitespace-nowrap ${
                                    activeTab === 'all'
                                        ? 'bg-brand-green text-white shadow-md'
                                        : 'text-slate-600 hover:bg-gray-100'
                                }`}
                            >
                                ໂຄງສ້າງລວມ (Overview)
                            </button>
                            <button
                                type="button"
                                onClick={() => setActiveTab('president')}
                                className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all whitespace-nowrap ${
                                    activeTab === 'president'
                                        ? 'bg-amber-600 text-white shadow-md'
                                        : 'text-slate-600 hover:bg-gray-100'
                                }`}
                            >
                                ປະທານ-ຮອງປະທານ ({filteredPresidents.length})
                            </button>
                            <button
                                type="button"
                                onClick={() => setActiveTab('standing')}
                                className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all whitespace-nowrap ${
                                    activeTab === 'standing'
                                        ? 'bg-orange-600 text-white shadow-md'
                                        : 'text-slate-600 hover:bg-gray-100'
                                }`}
                            >
                                ຄະນະປະຈຳການ ({filteredStanding.length})
                            </button>
                            <button
                                type="button"
                                onClick={() => setActiveTab('member')}
                                className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all whitespace-nowrap ${
                                    activeTab === 'member'
                                        ? 'bg-blue-600 text-white shadow-md'
                                        : 'text-slate-600 hover:bg-gray-100'
                                }`}
                            >
                                ຄະນະກຳມະການ ({filteredCommittee.length})
                            </button>
                            <button
                                type="button"
                                onClick={() => setActiveTab('advisor')}
                                className={`px-4 py-2 rounded-2xl text-xs font-bold transition-all whitespace-nowrap ${
                                    activeTab === 'advisor'
                                        ? 'bg-emerald-600 text-white shadow-md'
                                        : 'text-slate-600 hover:bg-gray-100'
                                }`}
                            >
                                ຄະນະທີ່ປຶກສາ ({filteredAdvisors.length})
                            </button>
                        </div>

                        {/* Search Input */}
                        <div className="relative w-full md:w-72">
                            <input
                                type="text"
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                placeholder="ຄົ້ນຫາຊື່ພຣະສົງ, ຕຳແໜ່ງ..."
                                className="w-full pl-9 pr-4 py-2 text-xs rounded-2xl bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-green/20 focus:border-brand-green"
                            />
                            <svg className="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            {searchTerm && (
                                <button
                                    type="button"
                                    onClick={() => setSearchTerm('')}
                                    className="absolute right-3 top-2.5 text-xs text-gray-400 hover:text-gray-600"
                                >
                                    ✕
                                </button>
                            )}
                        </div>
                    </div>

                    {/* SECTION 1: PRESIDENT & VICE PRESIDENTS */}
                    {(activeTab === 'all' || activeTab === 'president') && (
                        <div className="space-y-6">
                            <div className="flex items-center justify-between border-b border-amber-900/10 pb-3">
                                <div>
                                    <h2 className="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                                        <span className="text-amber-600">👑</span>
                                        <span>ປະທານ ແລະ ຄະນະປະທານ ສູນກາງ ອພສ</span>
                                    </h2>
                                    <p className="text-xs text-gray-500 mt-0.5">
                                        ຈຳນວນ 9 ອົງ (ສະໄໝທີ IX)
                                    </p>
                                </div>
                            </div>

                            {/* President Spotlight Card */}
                            {presidentMain && (
                                <div
                                    onClick={() => setSelectedMember(presidentMain)}
                                    className="cursor-pointer group relative bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-transparent border-2 border-amber-400/40 rounded-3xl p-6 sm:p-8 shadow-lg hover:shadow-2xl transition-all duration-300 max-w-3xl mx-auto flex flex-col sm:flex-row items-center gap-6 sm:gap-8"
                                >
                                    <div className="relative w-36 h-48 sm:w-44 sm:h-56 rounded-2xl overflow-hidden bg-amber-100 flex-shrink-0 shadow-md ring-4 ring-amber-400/40 group-hover:scale-105 transition-transform duration-300">
                                        {presidentMain.photo_url ? (
                                            <img
                                                src={presidentMain.photo_url}
                                                alt={presidentMain.name}
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <div className="w-full h-full flex items-center justify-center p-6">
                                                <img src="/logo.png" alt="Logo" className="w-16 h-16 object-contain opacity-50" />
                                            </div>
                                        )}
                                        <div className="absolute top-2 left-2 bg-amber-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">
                                            ປະທານ
                                        </div>
                                    </div>

                                    <div className="text-center sm:text-left space-y-3 flex-1">
                                        <span className="inline-block px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            ປະທານ ສູນກາງອົງການພຸດທະສາສະໜາສັມພັນ ແຫ່ງ ສປປ ລາວ
                                        </span>
                                        <h3 className="text-xl sm:text-2xl font-extrabold text-slate-800 group-hover:text-amber-800 transition-colors">
                                            {presidentMain.name}
                                        </h3>
                                        {presidentMain.bio && (
                                            <p className="text-xs text-slate-600 leading-relaxed">
                                                {presidentMain.bio}
                                            </p>
                                        )}
                                        <div className="pt-2 flex items-center justify-center sm:justify-start gap-2 text-xs font-semibold text-amber-700">
                                            <span>ກົດເພື່ອເບິ່ງລາຍລະອຽດ</span>
                                            <svg className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Vice Presidents Grid */}
                            {vicePresidents.length > 0 && (
                                <div className="space-y-3">
                                    <h4 className="text-sm font-bold text-slate-700">ຄະນະຮອງປະທານ ({vicePresidents.length} ອົງ)</h4>
                                    <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                        {vicePresidents.map((vp) => (
                                            <div
                                                key={vp.id}
                                                onClick={() => setSelectedMember(vp)}
                                                className="cursor-pointer group bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center space-y-3"
                                            >
                                                <div className="w-24 h-32 sm:w-28 sm:h-36 rounded-xl overflow-hidden bg-amber-50 ring-2 ring-amber-300/40 flex-shrink-0 group-hover:scale-105 transition-transform duration-300 shadow">
                                                    {vp.photo_url ? (
                                                        <img
                                                            src={vp.photo_url}
                                                            alt={vp.name}
                                                            className="w-full h-full object-cover"
                                                        />
                                                    ) : (
                                                        <div className="w-full h-full flex items-center justify-center p-4">
                                                            <img src="/logo.png" alt="Logo" className="w-12 h-12 object-contain opacity-50" />
                                                        </div>
                                                    )}
                                                </div>

                                                <div className="space-y-1 w-full">
                                                    <span className="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                        {vp.title || 'ຮອງປະທານ'}
                                                    </span>
                                                    <h4 className="text-xs sm:text-sm font-bold text-slate-800 line-clamp-2 group-hover:text-amber-700 transition-colors">
                                                        {vp.name}
                                                    </h4>
                                                    {vp.organization && (
                                                        <p className="text-[10px] text-gray-400 line-clamp-1">
                                                            {vp.organization}
                                                        </p>
                                                    )}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    )}

                    {/* SECTION 2: STANDING COMMITTEE (ຄະນະປະຈຳການ) */}
                    {(activeTab === 'all' || activeTab === 'standing') && (
                        <div className="space-y-4 pt-6">
                            <div className="flex items-center justify-between border-b border-orange-900/10 pb-3">
                                <div>
                                    <h2 className="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                                        <span className="text-orange-600">🏛️</span>
                                        <span>ຄະນະບໍລິຫານງານປະຈຳການ ສູນກາງ ອພສ</span>
                                    </h2>
                                    <p className="text-xs text-gray-500 mt-0.5">
                                        ຈຳນວນ 26 ອົງ (ສະໄໝທີ IX)
                                    </p>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                {filteredStanding.map((item, idx) => (
                                    <div
                                        key={item.id}
                                        onClick={() => setSelectedMember(item)}
                                        className="cursor-pointer group bg-white rounded-2xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-orange-200 transition-all flex items-center gap-3"
                                    >
                                        <div className="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 overflow-hidden flex items-center justify-center flex-shrink-0 text-orange-700 font-bold text-xs shadow-sm">
                                            {item.photo_url ? (
                                                <img src={item.photo_url} alt={item.name} className="w-full h-full object-cover" />
                                            ) : (
                                                <span>{item.sort_order || idx + 1}</span>
                                            )}
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <p className="text-xs font-bold text-slate-800 truncate group-hover:text-orange-700 transition-colors">
                                                {item.name}
                                            </p>
                                            <p className="text-[10px] text-gray-400 truncate">
                                                {item.title || 'ຄະນະບໍລິຫານງານປະຈຳການ'}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* SECTION 3: CENTRAL COMMITTEE MEMBERS (ຄະນະກຳມະການ) */}
                    {(activeTab === 'all' || activeTab === 'member') && (
                        <div className="space-y-4 pt-6">
                            <div className="flex items-center justify-between border-b border-blue-900/10 pb-3">
                                <div>
                                    <h2 className="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                                        <span className="text-blue-600">👥</span>
                                        <span>ຄະນະກຳມະການບໍລິຫານງານ ສູນກາງ ອພສ</span>
                                    </h2>
                                    <p className="text-xs text-gray-500 mt-0.5">
                                        ຈຳນວນ 99 ອົງ (ສະໄໝທີ IX)
                                    </p>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                {filteredCommittee.map((item, idx) => (
                                    <div
                                        key={item.id}
                                        onClick={() => setSelectedMember(item)}
                                        className="cursor-pointer group bg-white rounded-2xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-blue-200 transition-all flex items-center gap-3"
                                    >
                                        <div className="w-8 h-8 rounded-lg bg-blue-50 border border-blue-200 overflow-hidden flex items-center justify-center flex-shrink-0 text-blue-700 font-bold text-xs shadow-sm">
                                            {item.photo_url ? (
                                                <img src={item.photo_url} alt={item.name} className="w-full h-full object-cover" />
                                            ) : (
                                                <span>{item.sort_order || idx + 1}</span>
                                            )}
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <p className="text-xs font-semibold text-slate-800 truncate group-hover:text-blue-700 transition-colors">
                                                {item.name}
                                            </p>
                                            <p className="text-[10px] text-gray-400 truncate">
                                                {item.title || 'ຄະນະກຳມະການ'}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* SECTION 4: ADVISORY BOARD (ຄະນະທີ່ປຶກສາ) */}
                    {(activeTab === 'all' || activeTab === 'advisor') && (
                        <div className="space-y-4 pt-6">
                            <div className="flex items-center justify-between border-b border-emerald-900/10 pb-3">
                                <div>
                                    <h2 className="text-lg sm:text-xl font-bold text-slate-800 flex items-center gap-2">
                                        <span className="text-emerald-600">📜</span>
                                        <span>ຄະນະທີ່ປຶກສາ ສູນກາງ ອພສ</span>
                                    </h2>
                                    <p className="text-xs text-gray-500 mt-0.5">
                                        ຈຳນວນ 15 ອົງ (ສະໄໝທີ IX)
                                    </p>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                {filteredAdvisors.map((item, idx) => (
                                    <div
                                        key={item.id}
                                        onClick={() => setSelectedMember(item)}
                                        className="cursor-pointer group bg-white rounded-2xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all flex items-center gap-3"
                                    >
                                        <div className="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 overflow-hidden flex items-center justify-center flex-shrink-0 text-emerald-700 font-bold text-xs shadow-sm">
                                            {item.photo_url ? (
                                                <img src={item.photo_url} alt={item.name} className="w-full h-full object-cover" />
                                            ) : (
                                                <span>{item.sort_order || idx + 1}</span>
                                            )}
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <p className="text-xs font-bold text-slate-800 truncate group-hover:text-emerald-700 transition-colors">
                                                {item.name}
                                            </p>
                                            <p className="text-[10px] text-emerald-600 font-medium truncate">
                                                {item.title || 'ທີ່ປຶກສາສູນກາງ ອພສ'}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                {/* Detail Modal */}
                {selectedMember && (
                    <div
                        onClick={() => setSelectedMember(null)}
                        className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                    >
                        <div
                            onClick={(e) => e.stopPropagation()}
                            className="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative space-y-6 text-center animate-fade-in"
                        >
                            <button
                                type="button"
                                onClick={() => setSelectedMember(null)}
                                className="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-1.5 rounded-full hover:bg-gray-100"
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div className="w-32 h-40 rounded-2xl mx-auto overflow-hidden bg-amber-50 ring-4 ring-amber-400/30 shadow-lg">
                                {selectedMember.photo_url ? (
                                    <img
                                        src={selectedMember.photo_url}
                                        alt={selectedMember.name}
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <div className="w-full h-full flex items-center justify-center p-6">
                                        <img src="/logo.png" alt="Logo" className="w-16 h-16 object-contain opacity-50" />
                                    </div>
                                )}
                            </div>

                            <div className="space-y-2">
                                <span className="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    {selectedMember.title || selectedMember.category_label}
                                </span>
                                <h3 className="text-lg sm:text-xl font-bold text-slate-800">
                                    {selectedMember.name}
                                </h3>
                                {selectedMember.organization && (
                                    <p className="text-xs text-gray-500 font-medium">
                                        {selectedMember.organization}
                                    </p>
                                )}
                            </div>

                            {selectedMember.bio && (
                                <div className="text-xs text-slate-600 bg-gray-50 p-4 rounded-2xl text-left leading-relaxed">
                                    {selectedMember.bio}
                                </div>
                            )}

                            <div className="pt-2 text-[11px] text-gray-400 border-t border-gray-100 flex items-center justify-between">
                                <span>{selectedMember.term}</span>
                                <span>{selectedMember.term_year}</span>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
