import { Link } from '@inertiajs/react';

function decodeLabel(label) {
    if (label.includes('Previous') || label === '&laquo; Previous') return '‹';
    if (label.includes('Next')) return '›';
    return label;
}

export default function Pagination({ links }) {
    if (!links || links.length <= 3) return null;

    return (
        <div className="flex items-center justify-center sm:justify-end gap-1 flex-wrap">
            {links.map((link, i) => (
                link.url ? (
                    <Link
                        key={i}
                        href={link.url}
                        preserveScroll
                        preserveState
                        className={`min-w-[32px] h-8 px-2 flex items-center justify-center rounded-lg text-xs font-medium transition-colors
                                    ${link.active ? 'bg-brand-green text-white' : 'text-slate-500 hover:bg-gray-100'}`}
                    >
                        {decodeLabel(link.label)}
                    </Link>
                ) : (
                    <span key={i} className="min-w-[32px] h-8 px-2 flex items-center justify-center rounded-lg text-xs font-medium text-gray-300">
                        {decodeLabel(link.label)}
                    </span>
                )
            ))}
        </div>
    );
}
