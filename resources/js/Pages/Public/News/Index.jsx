import { useEffect, useRef, useState } from 'react';
import { Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';
import Pagination from '@/Components/Pagination';

function HeroSlider({ slides }) {
    const total = slides.length;
    const [current, setCurrent] = useState(0);
    const timerRef = useRef(null);
    const touchStartX = useRef(null);

    const prefersReducedMotion = () =>
        typeof window !== 'undefined' && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const stopAutoplay = () => {
        if (timerRef.current) clearInterval(timerRef.current);
        timerRef.current = null;
    };

    const startAutoplay = () => {
        stopAutoplay();
        if (total <= 1 || prefersReducedMotion()) return;
        timerRef.current = setInterval(() => {
            setCurrent((c) => (c + 1) % total);
        }, 6000);
    };

    useEffect(() => {
        startAutoplay();
        return stopAutoplay;
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [total]);

    const goTo = (index) => {
        setCurrent(((index % total) + total) % total);
        startAutoplay();
    };

    const next = () => goTo(current + 1);
    const prev = () => goTo(current - 1);

    const onTouchStart = (e) => {
        touchStartX.current = e.changedTouches[0].clientX;
        stopAutoplay();
    };

    const onTouchEnd = (e) => {
        if (touchStartX.current === null) return;
        const deltaX = e.changedTouches[0].clientX - touchStartX.current;
        if (Math.abs(deltaX) > 40) {
            deltaX < 0 ? next() : prev();
        } else {
            startAutoplay();
        }
        touchStartX.current = null;
    };

    return (
        <section
            className="relative overflow-hidden bg-brand-green-dark min-h-[420px] sm:min-h-[500px]"
            onMouseEnter={stopAutoplay}
            onMouseLeave={startAutoplay}
            onTouchStart={onTouchStart}
            onTouchEnd={onTouchEnd}
        >
            {slides.map((slide, index) => {
                const active = index === current;

                return (
                    <div
                        key={slide.id}
                        className={`hero-slide absolute inset-0 transition-opacity duration-700 ease-in-out ${
                            active ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'
                        }`}
                        aria-hidden={active ? 'false' : 'true'}
                    >
                        <img
                            src={slide.image_url}
                            alt=""
                            aria-hidden="true"
                            className="absolute inset-0 w-full h-full object-cover opacity-90"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-brand-green-dark via-brand-green-dark/50 to-brand-green-dark/10"></div>

                        <div className="relative h-full max-w-6xl mx-auto px-5 sm:px-8 pt-24 pb-10 sm:pb-14 w-full flex flex-col justify-end">
                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4 w-fit">
                                <span aria-hidden="true">☸</span> ຂ່າວສານຊຸມຊົນ
                            </span>

                            {slide.link_url ? (
                                <a
                                    href={slide.link_url}
                                    target="_blank"
                                    rel="noopener"
                                    className="group block focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-bright-green rounded"
                                >
                                    <h1 className="text-white text-2xl sm:text-4xl font-bold leading-tight max-w-2xl group-hover:text-brand-bright-green transition-colors duration-300 line-clamp-2">
                                        {slide.title}
                                    </h1>
                                </a>
                            ) : (
                                <h1 className="text-white text-2xl sm:text-4xl font-bold leading-tight max-w-2xl line-clamp-2">
                                    {slide.title}
                                </h1>
                            )}

                            {slide.subtitle && (
                                <p className="text-white/70 text-sm sm:text-base mt-3 max-w-xl leading-relaxed hidden sm:block line-clamp-2">
                                    {slide.subtitle}
                                </p>
                            )}

                            {slide.link_url && (
                                <a
                                    href={slide.link_url}
                                    target="_blank"
                                    rel="noopener"
                                    className="inline-flex items-center gap-2 mt-6 w-fit bg-brand-bright-green text-brand-green-dark px-6 py-3 rounded-xl text-sm font-bold transition-all hover:shadow-lg hover:shadow-black/20 active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                                >
                                    {slide.button_text || 'ອ່ານຕໍ່'}
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M8 4l6 6-6 6" />
                                    </svg>
                                </a>
                            )}
                        </div>
                    </div>
                );
            })}

            {total > 1 && (
                <>
                    <button
                        type="button"
                        onClick={prev}
                        className="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center backdrop-blur-sm transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-white"
                        aria-label="ສະໄລ້ກ່ອນໜ້າ"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4l-6 6 6 6" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        onClick={next}
                        className="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center backdrop-blur-sm transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-white"
                        aria-label="ສະໄລ້ຕໍ່ໄປ"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M8 4l6 6-6 6" />
                        </svg>
                    </button>

                    <div className="absolute bottom-4 sm:bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
                        {slides.map((slide, index) => (
                            <button
                                key={slide.id}
                                type="button"
                                onClick={() => goTo(index)}
                                className={`h-1.5 rounded-full transition-all duration-300 ${
                                    index === current ? 'w-6 bg-white' : 'w-1.5 bg-white/40 hover:bg-white/60'
                                }`}
                                aria-label={`ໄປສະໄລ້ທີ ${index + 1}`}
                            ></button>
                        ))}
                    </div>
                </>
            )}
        </section>
    );
}

function NewsCard({ item }) {
    return (
        <Link
            href={route('news.public.show', item.slug)}
            className="group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green"
        >
            <div className="aspect-[16/9] bg-brand-light-green overflow-hidden">
                {item.image_url ? (
                    <img
                        src={item.image_url}
                        alt={item.title}
                        loading="lazy"
                        className="w-full h-full object-cover opacity-0 group-hover:scale-105 transition-[opacity,transform] duration-500"
                        onLoad={(e) => e.currentTarget.classList.remove('opacity-0')}
                    />
                ) : (
                    <div className="w-full h-full flex items-center justify-center">
                        <span className="text-2xl text-brand-green/40">☸</span>
                    </div>
                )}
            </div>
            <div className="p-5 flex-1 flex flex-col">
                {item.category && (
                    <span className="text-[10px] font-bold text-brand-green uppercase tracking-widest">{item.category.name}</span>
                )}
                <h3 className="font-bold text-slate-800 text-base leading-snug mt-2 mb-2 group-hover:text-brand-green transition-colors duration-300 line-clamp-2">
                    {item.title}
                </h3>
                <p className="text-sm text-slate-500 leading-relaxed flex-1 line-clamp-2">{item.excerpt_or_summary}</p>
                <div className="flex items-center justify-between mt-4 pt-3 border-t border-black/5">
                    <span className="inline-flex items-center gap-1 text-[11px] text-gray-400">
                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                            <rect x="3" y="4" width="14" height="13" rx="2" />
                            <path strokeLinecap="round" d="M3 8h14M7 2v4M13 2v4" />
                        </svg>
                        {item.date_label}
                    </span>
                    <span className="text-[11px] text-gray-400">{item.minutes_label} ນາທີອ່ານ</span>
                </div>
            </div>
        </Link>
    );
}

export default function Index({ heroSlides, featured, news, categories, categorySlug, latestNews }) {
    const activeCategory = categorySlug ? categories.find((c) => c.slug === categorySlug) : null;

    return (
        <PublicLayout>
            <SeoHead
                title="ຂ່າວສານ ແລະ ປະກາດ"
                description="ຕິດຕາມຂ່າວສານ, ປະກາດ, ແລະ ຄວາມເຄື່ອນໄຫວຫລ້າສຸດຂອງວັດປ່າໜອງບົວທອງໃຕ້"
            />

            {/* Hero */}
            {heroSlides.length > 0 ? (
                <HeroSlider slides={heroSlides} />
            ) : (
                <section className="relative overflow-hidden bg-brand-green-dark min-h-[420px] sm:min-h-[500px] flex items-end">
                    <div className="absolute inset-0">
                        {featured && featured.image_url ? (
                            <img src={featured.image_url} alt="" aria-hidden="true" className="w-full h-full object-cover opacity-60" />
                        ) : (
                            <div className="w-full h-full bg-brand-tracker opacity-80"></div>
                        )}
                    </div>
                    <div className="absolute inset-0 bg-gradient-to-t from-brand-green-dark via-brand-green-dark/75 to-brand-green-dark/30"></div>

                    <div className="relative max-w-6xl mx-auto px-5 sm:px-8 pt-24 pb-10 sm:pb-14 w-full">
                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/10 text-brand-bright-green mb-4">
                            <span aria-hidden="true">☸</span> ຂ່າວສານຊຸມຊົນ
                        </span>

                        {featured ? (
                            <>
                                <div className="flex flex-wrap items-center gap-1.5 mb-3">
                                    <span className="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-brand-bright-green/20 text-brand-bright-green">
                                        ຂ່າວລ່າສຸດ
                                    </span>
                                    {featured.category && (
                                        <span className="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-white/15 text-white">
                                            {featured.category.name}
                                        </span>
                                    )}
                                </div>

                                <Link
                                    href={route('news.public.show', featured.slug)}
                                    className="group block focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-bright-green rounded"
                                >
                                    <h1 className="text-white text-2xl sm:text-4xl font-bold leading-tight max-w-2xl group-hover:text-brand-bright-green transition-colors duration-300 line-clamp-2">
                                        {featured.title}
                                    </h1>
                                </Link>
                                <p className="text-white/70 text-sm sm:text-base mt-3 max-w-xl leading-relaxed hidden sm:block line-clamp-2">
                                    {featured.excerpt_or_summary}
                                </p>
                                <p className="flex items-center flex-wrap gap-x-1.5 text-white/50 text-xs mt-4">
                                    <time dateTime={featured.date_iso}>{featured.date_label}</time>
                                    <span aria-hidden="true">·</span>
                                    <span>{featured.author_name || 'ບໍ່ລະບຸ'}</span>
                                    <span aria-hidden="true">·</span>
                                    <span className="inline-flex items-center gap-1">
                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                                            <circle cx="10" cy="10" r="7" />
                                            <path strokeLinecap="round" d="M10 6v4l3 2" />
                                        </svg>
                                        {featured.minutes_label} ນາທີອ່ານ
                                    </span>
                                </p>

                                <Link
                                    href={route('news.public.show', featured.slug)}
                                    className="inline-flex items-center gap-2 mt-6 bg-brand-bright-green text-brand-green-dark px-6 py-3 rounded-xl text-sm font-bold transition-all hover:shadow-lg hover:shadow-black/20 active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                                >
                                    ອ່ານຕໍ່
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M8 4l6 6-6 6" />
                                    </svg>
                                </Link>
                            </>
                        ) : (
                            <>
                                <h1 className="text-white text-3xl sm:text-4xl font-bold leading-tight">ຂ່າວສານ ແລະ ປະກາດ</h1>
                                <p className="text-white/60 text-sm sm:text-base mt-3 max-w-xl leading-relaxed">
                                    ຕິດຕາມຂ່າວສານ, ກິດຈະກຳ ແລະ ປະກາດຫຼ້າສຸດຈາກທາງວັດ ເພື່ອບໍ່ພາດທຸກຄວາມເຄື່ອນໄຫວ
                                </p>
                            </>
                        )}
                    </div>
                </section>
            )}

            <div className="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">
                {categories.length > 0 && (
                    <nav
                        aria-label="ໝວດໝູ່ຂ່າວ"
                        className="sticky top-16 z-10 -mx-5 px-5 sm:mx-0 sm:px-0 py-3 mb-10 bg-[#faf8f2]/90 backdrop-blur-sm border-b border-black/5"
                    >
                        <div className="relative">
                            <div className="flex items-center gap-2 overflow-x-auto no-scrollbar sm:flex-wrap">
                                <Link
                                    href={route('news.public.index')}
                                    className={`shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green ${
                                        !categorySlug ? 'bg-brand-green text-white' : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50'
                                    }`}
                                >
                                    ທັງໝົດ
                                </Link>
                                {categories.map((category) => (
                                    <Link
                                        key={category.id}
                                        href={route('news.public.index', { category: category.slug })}
                                        className={`shrink-0 px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green ${
                                            categorySlug === category.slug
                                                ? 'bg-brand-green text-white'
                                                : 'bg-white border border-gray-200 text-slate-600 hover:bg-gray-50'
                                        }`}
                                    >
                                        {category.name}
                                    </Link>
                                ))}
                            </div>
                            <div
                                className="sm:hidden pointer-events-none absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-[#faf8f2] to-transparent"
                                aria-hidden="true"
                            ></div>
                        </div>
                    </nav>
                )}

                {!featured && news.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center text-center py-24">
                        <span className="text-5xl text-brand-green/30 mb-4">☸</span>
                        <p className="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີຂ່າວປະກາດ</p>
                        <p className="text-slate-400 text-sm">ກະລຸນາກັບມາເບິ່ງໃໝ່ພາຍຫຼັງ</p>
                    </div>
                ) : categorySlug && news.data.length === 0 ? (
                    <div className="flex flex-col items-center justify-center text-center py-24">
                        <span className="text-5xl text-brand-green/30 mb-4">☸</span>
                        <p className="font-bold text-slate-700 mb-1">ຍັງບໍ່ມີຂ່າວໃນໝວດໝູ່ນີ້</p>
                        <p className="text-slate-400 text-sm">ລອງເລືອກໝວດໝູ່ອື່ນເບິ່ງ</p>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        {/* News grid (2/3 width) */}
                        <div className="lg:col-span-2">
                            <div className="flex items-end justify-between mb-6">
                                <div>
                                    <h2 className="text-lg font-bold text-slate-800">
                                        {categorySlug ? activeCategory?.name : 'ຂ່າວອື່ນໆ'}
                                    </h2>
                                    <p className="text-slate-400 text-xs mt-0.5">ຕິດຕາມຄວາມເຄື່ອນໄຫວພາຍໃນວັດ</p>
                                </div>
                            </div>

                            {news.data.length === 0 ? (
                                <p className="text-sm text-slate-400 py-6">ຍັງບໍ່ມີຂ່າວອື່ນເພີ່ມເຕີມ</p>
                            ) : (
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    {news.data.map((item) => (
                                        <NewsCard key={item.id} item={item} />
                                    ))}
                                </div>
                            )}

                            {news.links && news.links.length > 3 && (
                                <div className="mt-10">
                                    <Pagination links={news.links} />
                                </div>
                            )}
                        </div>

                        {/* Sidebar (1/3 width) */}
                        <aside className="lg:col-span-1">
                            <div className="bg-white rounded-2xl border border-black/5 card-shadow p-6 sm:p-7 lg:sticky lg:top-32">
                                <h2 className="text-base font-bold text-slate-800 mb-6 pb-4 border-b border-black/5">ຂ່າວຫຼ້າສຸດ</h2>

                                {latestNews.length === 0 ? (
                                    <p className="text-sm text-slate-400">ຍັງບໍ່ມີຂ່າວ</p>
                                ) : (
                                    <div className="space-y-5">
                                        {latestNews.map((item) => (
                                            <Link
                                                key={item.id}
                                                href={route('news.public.show', item.slug)}
                                                className="flex gap-4 group focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green rounded-lg"
                                            >
                                                <div className="shrink-0 w-12 h-12 rounded-lg flex flex-col items-center justify-center font-bold bg-brand-light-green text-brand-green">
                                                    <span className="text-[10px] uppercase leading-none">{item.month_label}</span>
                                                    <span className="text-base leading-none mt-0.5">{item.day_label}</span>
                                                </div>
                                                <div className="min-w-0">
                                                    <h4 className="font-bold text-sm text-slate-800 leading-snug group-hover:text-brand-green transition-colors duration-300 line-clamp-2">
                                                        {item.title}
                                                    </h4>
                                                    {item.category && (
                                                        <p className="text-[11px] text-gray-400 mt-1">{item.category.name}</p>
                                                    )}
                                                </div>
                                            </Link>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </aside>
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
