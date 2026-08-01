import { Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';
import Pagination from '@/Components/Pagination';

export default function ChantsIndex({ chants, categories, categorySlug }) {
    return (
        <PublicLayout>
            <SeoHead title="ບົດສູດມົນ" description="ລວມບົດສູດມົນຕ່າງໆ ຂອງວັດປ່າໜອງບົວທອງໃຕ້" />

            {/* Hero */}
            <section className="relative overflow-hidden bg-brand-green-dark">
                <span className="absolute -right-6 -top-14 text-[240px] leading-none text-white/[0.04] select-none pointer-events-none" aria-hidden="true">☸</span>

                <div className="relative max-w-6xl mx-auto px-5 sm:px-8 pt-16 pb-10 sm:pt-20 sm:pb-12">
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                        <span aria-hidden="true">☸</span> ຄຳສອນ
                    </span>

                    <h1 className="text-white text-3xl sm:text-4xl font-bold leading-tight">ບົດສູດມົນ</h1>
                    <p className="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                        ລວມບົດສູດມົນສຳລັບການປະຕິບັດທຳ
                    </p>

                    <div className="flex flex-wrap items-center gap-3 mt-7">
                        <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                            <span className="text-lg font-bold text-white tabular-nums leading-none">{chants.total}</span>
                            <span className="text-xs text-white/60">ບົດສູດມົນ</span>
                        </div>
                        {categories.length > 0 && (
                            <div className="flex items-baseline gap-1.5 bg-white/10 rounded-2xl px-4 py-2.5">
                                <span className="text-lg font-bold text-brand-bright-green tabular-nums leading-none">{categories.length}</span>
                                <span className="text-xs text-white/60">ໝວດໝູ່</span>
                            </div>
                        )}
                    </div>
                </div>
            </section>

            <div className="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

                {categories.length > 0 && (
                    <nav aria-label="ໝວດໝູ່ບົດສູດມົນ"
                        className="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5">
                        <div className="relative">
                            <div className="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                                <Link href={route('chants.public.index')}
                                    className={`shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                                        ${!categorySlug ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50'}`}>
                                    ທັງໝົດ
                                </Link>
                                {categories.map((category) => (
                                    <Link key={category.slug} href={route('chants.public.index', { category: category.slug })}
                                        className={`shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green
                                            ${categorySlug === category.slug ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50'}`}>
                                        {'— '.repeat(category.depth)}{category.name}
                                    </Link>
                                ))}
                            </div>
                            <div className="sm:hidden pointer-events-none absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-[#faf8f2] to-transparent" aria-hidden="true"></div>
                        </div>
                    </nav>
                )}

                {chants.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center text-center py-24">
                        <span className="text-5xl text-brand-green/30 mb-4">☸</span>
                        <p className="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີບົດສູດມົນ</p>
                        <p className="text-slate-400 text-sm">ກະລຸນາກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
                    </div>
                ) : (
                    <>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                            {chants.data.map((item) => (
                                <Link key={item.id} href={route('chants.public.show', item.slug)}
                                    className="group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden flex flex-col p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                                    <div className="flex items-center gap-2 mb-3">
                                        <span className="w-9 h-9 rounded-xl bg-brand-light-green flex items-center justify-center flex-shrink-0">
                                            <span className="text-brand-green">☸</span>
                                        </span>
                                        {item.category && (
                                            <span className="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-light-green text-brand-green">
                                                {item.category.name}
                                            </span>
                                        )}
                                    </div>
                                    <h3 className="font-bold text-slate-800 text-base leading-snug group-hover:text-brand-green transition-colors duration-300">
                                        {item.title}
                                    </h3>
                                    <p className="text-sm text-slate-500 mt-2 leading-relaxed flex-1 line-clamp-3">
                                        {item.excerpt}
                                    </p>
                                    <div className="flex items-center justify-between mt-4 pt-3 border-t border-black/5">
                                        <span className="inline-flex items-center gap-1 text-[11px] text-gray-400">
                                            <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                                                <circle cx="10" cy="10" r="7" />
                                                <path strokeLinecap="round" d="M10 6v4l3 2" />
                                            </svg>
                                            {item.minutes} ນາທີ
                                        </span>
                                        <span className="text-[11px] font-semibold text-brand-green group-hover:translate-x-0.5 transition-transform duration-300">ອ່ານ →</span>
                                    </div>
                                </Link>
                            ))}
                        </div>

                        {chants.links.length > 3 && (
                            <div className="mt-10">
                                <Pagination links={chants.links} />
                            </div>
                        )}
                    </>
                )}

            </div>
        </PublicLayout>
    );
}
