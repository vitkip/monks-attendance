import { useEffect, useState } from 'react';
import { Link } from '@inertiajs/react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';

function RelatedCard({ item }) {
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
                        <span className="text-xl text-brand-green/40">☸</span>
                    </div>
                )}
            </div>
            <div className="p-4 flex-1 flex flex-col">
                <div className="flex items-center gap-2 mb-1.5 flex-wrap">
                    <time dateTime={item.date_iso} className="text-[10px] font-medium text-gray-400 uppercase tracking-widest">
                        {item.date_label}
                    </time>
                </div>
                <h4 className="font-bold text-slate-800 text-sm leading-snug group-hover:text-brand-green transition-colors duration-300 line-clamp-2">
                    {item.title}
                </h4>
                <p className="text-xs text-slate-500 mt-1.5 leading-relaxed flex-1 line-clamp-2">{item.excerpt_or_summary}</p>
                <p className="text-[11px] text-gray-400 mt-2">{item.minutes_label} ນາທີອ່ານ</p>
            </div>
        </Link>
    );
}

export default function Show({ article, recent }) {
    const [progress, setProgress] = useState(0);
    const [copied, setCopied] = useState(false);

    useEffect(() => {
        const onScroll = () => {
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const value = docHeight > 0 ? (window.scrollY / docHeight) * 100 : 0;
            setProgress(Math.min(100, Math.max(0, value)));
        };

        onScroll();
        document.addEventListener('scroll', onScroll, { passive: true });
        return () => document.removeEventListener('scroll', onScroll);
    }, []);

    const currentUrl = typeof window !== 'undefined' ? window.location.href : '';

    const handleCopyLink = () => {
        if (typeof navigator === 'undefined' || !navigator.clipboard) return;
        navigator.clipboard.writeText(currentUrl).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        });
    };

    const articleSchema = {
        '@context': 'https://schema.org',
        '@type': 'NewsArticle',
        headline: article.title,
        description: article.excerpt_or_summary,
        image: article.image_url ? [article.image_url] : [],
        datePublished: article.published_at,
        dateModified: article.updated_at || article.published_at,
        author: {
            '@type': 'Organization',
            name: 'ວັດປ່າໜອງບົວທອງໃຕ້',
        },
        publisher: {
            '@type': 'Organization',
            name: 'ວັດປ່າໜອງບົວທອງໃຕ້',
        },
    };

    return (
        <PublicLayout>
            <SeoHead
                title={article.title}
                description={article.excerpt_or_summary}
                image={article.image_url}
                type="article"
                schemaJson={articleSchema}
                keywords={`${article.title}, ວັດປ່າໜອງບົວທອງໃຕ້, ວັດປ່າໜອງບົວທອງ, ພຣະສົງ, ງານບຸນ, ບຸນ, ພຸດທະສາສະໜາ, ປະເທດລາວ, ການປະຕິບັດທຳ, ກຳມະຖານ, ບວດຂາວ, ໄຫວ້ພຣະ`}
            />

            {/* Reading progress bar */}
            <div
                className="fixed top-0 left-0 h-1 bg-brand-bright-green z-30 transition-[width] duration-150 ease-out"
                style={{ width: `${progress}%` }}
                aria-hidden="true"
            ></div>

            <article className="max-w-3xl mx-auto px-5 sm:px-8 py-10 sm:py-14">
                <nav aria-label="ຍ້ອນກັບ">
                    <Link
                        href={route('news.public.index')}
                        className="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-brand-green transition-colors duration-300 mb-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green rounded"
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 4l-6 6 6 6" />
                        </svg>
                        ຂ່າວທັງໝົດ
                    </Link>
                </nav>

                {article.image_url && (
                    <img
                        src={article.image_url}
                        alt={article.title}
                        className="w-full aspect-[16/9] object-cover rounded-3xl mb-8 shadow-lg shadow-black/5"
                    />
                )}

                <div className="flex items-center gap-2 mb-3 flex-wrap">
                    {article.category && (
                        <span className="px-2.5 py-1 rounded-full text-[10px] font-bold bg-brand-light-green text-brand-green">
                            {article.category.name}
                        </span>
                    )}
                    <span className="inline-flex items-center gap-1 text-xs text-gray-400">
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                            <circle cx="10" cy="10" r="7" />
                            <path strokeLinecap="round" d="M10 6v4l3 2" />
                        </svg>
                        {article.minutes_label} ນາທີອ່ານ
                    </span>
                </div>

                <h1 className="text-2xl sm:text-3xl font-bold text-slate-800 leading-snug mb-6">{article.title}</h1>

                {/* Meta: author + date */}
                <div className="flex items-center gap-3 pb-6 mb-8 border-b border-black/5">
                    <span
                        className="w-10 h-10 rounded-full bg-brand-light-green flex items-center justify-center text-brand-green font-bold text-sm shrink-0"
                        aria-hidden="true"
                    >
                        {(article.author_name || 'ບ').charAt(0)}
                    </span>
                    <div className="leading-tight">
                        <p className="text-sm font-semibold text-slate-700">{article.author_name || 'ບໍ່ລະບຸ'}</p>
                        <time dateTime={article.date_iso} className="text-xs text-gray-400">
                            {article.date_label}
                        </time>
                    </div>
                </div>

                <div
                    className="prose prose-slate prose-lg max-w-none prose-p:leading-loose prose-headings:font-bold prose-a:text-brand-green prose-img:rounded-2xl"
                    dangerouslySetInnerHTML={{ __html: article.content }}
                />

                {/* Share */}
                <div className="flex items-center gap-3 mt-10 pt-8 border-t border-black/5">
                    <span className="text-xs font-bold text-gray-400 uppercase tracking-widest">ແຊຣ໌</span>

                    <a
                        href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="ແຊຣ໌ໄປ Facebook"
                        className="w-9 h-9 rounded-full bg-brand-light-green text-brand-green flex items-center justify-center hover:bg-brand-green hover:text-white transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green"
                    >
                        <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.191.312-.271.71-.271 1.206v1.844h3.744l-.494 1.847-.301 1.82h-2.949v7.98H9.101z" />
                        </svg>
                    </a>

                    <button
                        type="button"
                        onClick={handleCopyLink}
                        aria-label="ສຳເນົາລິ້ງບົດຄວາມ"
                        className="w-9 h-9 rounded-full bg-brand-light-green text-brand-green flex items-center justify-center hover:bg-brand-green hover:text-white transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green"
                    >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2" aria-hidden="true">
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5"
                            />
                        </svg>
                    </button>
                    <span
                        role="status"
                        className={`text-xs text-brand-green font-semibold transition-opacity duration-300 ${copied ? 'opacity-100' : 'opacity-0'}`}
                    >
                        ສຳເນົາລິ້ງແລ້ວ
                    </span>
                </div>

                {recent.length > 0 && (
                    <div className="mt-16 pt-8 border-t border-black/5">
                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-5">ຂ່າວອື່ນໆ</p>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            {recent.map((item) => (
                                <RelatedCard key={item.id} item={item} />
                            ))}
                        </div>
                    </div>
                )}
            </article>
        </PublicLayout>
    );
}
