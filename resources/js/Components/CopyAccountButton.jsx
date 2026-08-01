import { useRef, useState } from 'react';

export default function CopyAccountButton({ value, className = 'font-semibold text-slate-600 hover:text-brand-green', iconClassName = 'w-3 h-3' }) {
    const [copied, setCopied] = useState(false);
    const timeoutRef = useRef(null);

    const handleCopy = () => {
        const copyText = (text) => {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                return navigator.clipboard.writeText(text);
            }
            const scratch = document.createElement('textarea');
            scratch.value = text;
            scratch.style.position = 'fixed';
            scratch.style.opacity = '0';
            document.body.appendChild(scratch);
            scratch.select();
            document.execCommand('copy');
            document.body.removeChild(scratch);
            return Promise.resolve();
        };

        copyText(value).then(() => {
            setCopied(true);
            clearTimeout(timeoutRef.current);
            timeoutRef.current = setTimeout(() => setCopied(false), 1500);
        });
    };

    return (
        <button
            type="button"
            onClick={handleCopy}
            className={`relative inline-flex items-center gap-1 align-middle transition-colors ${className}`}
            title="ກົດເພື່ອຄັດລອກເລກບັນຊີ"
            aria-label={`ຄັດລອກເລກບັນຊີ ${value}`}
        >
            <span className="tabular-nums">{value}</span>
            <svg className={`${iconClassName} shrink-0`} fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                {copied ? (
                    <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                ) : (
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M8 7V5a2 2 0 012-2h9a2 2 0 012 2v9a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-2M8 7h8v8H8V7z"
                    />
                )}
            </svg>
            <span
                className={`pointer-events-none absolute -top-7 left-1/2 -translate-x-1/2 whitespace-nowrap px-2 py-1 rounded-md bg-slate-800 text-white text-[10px] font-semibold transition-opacity duration-200 ${
                    copied ? 'opacity-100' : 'opacity-0'
                }`}
            >
                ຄັດລອກແລ້ວ!
            </span>
        </button>
    );
}
