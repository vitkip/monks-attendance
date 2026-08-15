import { Link } from '@inertiajs/react';
import { useState } from 'react';
import PublicLayout from '@/Layouts/PublicLayout';
import SeoHead from '@/Components/SeoHead';

export default function ChantShow({ chant, related }) {
    const [copied, setCopied] = useState(false);
    const currentUrl = typeof window !== 'undefined' ? window.location.href : '';

    function copyLink() {
        navigator.clipboard.writeText(currentUrl).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        });
    }

    return (
        <PublicLayout>
            <SeoHead
                title={`ບົດສູດມົນ: ${chant.title}`}
                description={`ບົດສູດມົນ ${chant.title} — ${chant.content_html.replace(/<[^>]*>/g, '').slice(0, 150)}`}
                keywords={`ບົດສູດມົນ, ${chant.title}, ໄຫວ້ພຣະ, ການປະຕິບັດທຳ, ກຳມະຖານ, ວັດປ່າໜອງບົວທອງໃຕ້, ວັດປ່າໜອງບົວທອງ, ພຣະສົງ, ພຸດທະສາສະໜາ, ປະເທດລາວ`}
                type="article"
            />

            <article className="max-w-6xl mx-auto px-5 sm:px-8 py-10 sm:py-14">

                <Link href={route('chants.public.index')}
                    className="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-brand-green transition-colors duration-300 mb-6 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green rounded">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 4l-6 6 6 6" />
                    </svg>
                    ບົດສູດມົນທັງໝົດ
                </Link>

                <div className="flex items-center gap-2 mb-3 flex-wrap">
                    {chant.category && (
                        <span className="px-2.5 py-1 rounded-full text-[10px] font-bold bg-brand-light-green text-brand-green">
                            {chant.category.name}
                        </span>
                    )}
                    <span className="inline-flex items-center gap-1 text-xs text-gray-400">
                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 20 20" strokeWidth="2" aria-hidden="true">
                            <circle cx="10" cy="10" r="7" />
                            <path strokeLinecap="round" d="M10 6v4l3 2" />
                        </svg>
                        {chant.minutes} ນາທີ
                    </span>
                </div>

                <h1 className="text-2xl sm:text-3xl font-bold text-slate-800 leading-snug mb-6">{chant.title}</h1>

                <div className="bg-white rounded-3xl card-shadow border border-black/5 p-6 sm:p-10">
                    <div className="prose prose-slate max-w-none text-base sm:text-lg leading-loose" dangerouslySetInnerHTML={{ __html: chant.content_html }} />
                </div>

                {/* Share */}
                <div className="flex items-center gap-3 mt-8">
                    <span className="text-xs font-bold text-gray-400 uppercase tracking-widest">ແຊຣ໌</span>

                    <a href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`}
                        target="_blank" rel="noopener noreferrer"
                        aria-label="ແຊຣ໌ໄປ Facebook"
                        className="w-9 h-9 rounded-full bg-brand-light-green text-brand-green flex items-center justify-center hover:bg-brand-green hover:text-white transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                        <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.191.312-.271.71-.271 1.206v1.844h3.744l-.494 1.847-.301 1.82h-2.949v7.98H9.101z" />
                        </svg>
                    </a>

                    <button type="button" onClick={copyLink}
                        aria-label="ສຳເນົາລິ້ງບົດສູດມົນ"
                        className="w-9 h-9 rounded-full bg-brand-light-green text-brand-green flex items-center justify-center hover:bg-brand-green hover:text-white transition-colors duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2" aria-hidden="true">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5M10.172 13.828a4 4 0 010-5.656l3-3a4 4 0 015.656 5.656l-1.5 1.5" />
                        </svg>
                    </button>
                    <span role="status" className={`text-xs text-brand-green font-semibold transition-opacity duration-300 ${copied ? 'opacity-100' : 'opacity-0'}`}>ສຳເນົາລິ້ງແລ້ວ</span>
                </div>

                {related.length > 0 && (
                    <div className="mt-16 pt-8 border-t border-black/5">
                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-5">ບົດສູດມົນອື່ນໆ</p>
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            {related.map((item) => (
                                <Link key={item.id} href={route('chants.public.show', item.slug)}
                                    className="group bg-white rounded-2xl card-shadow border border-black/5 overflow-hidden flex flex-col p-4 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-green">
                                    <span className="w-8 h-8 rounded-lg bg-brand-light-green flex items-center justify-center mb-2">
                                        <span className="text-sm text-brand-green">☸</span>
                                    </span>
                                    <h4 className="font-bold text-slate-800 text-sm leading-snug group-hover:text-brand-green transition-colors duration-300">
                                        {item.title}
                                    </h4>
                                </Link>
                            ))}
                        </div>
                    </div>
                )}

            </article>
        </PublicLayout>
    );
}
