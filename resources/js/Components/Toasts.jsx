import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

function Toast({ tone, message, onDone }) {
    const [show, setShow] = useState(false);

    useEffect(() => {
        const raf = requestAnimationFrame(() => setShow(true));
        const timer = setTimeout(() => setShow(false), tone === 'error' ? 5000 : 4000);
        const cleanup = setTimeout(onDone, (tone === 'error' ? 5000 : 4000) + 250);
        return () => {
            cancelAnimationFrame(raf);
            clearTimeout(timer);
            clearTimeout(cleanup);
        };
    }, []);

    const isError = tone === 'error';

    return (
        <div
            className={`pointer-events-auto flex items-center gap-3 px-4 py-3 min-w-[260px]
                        bg-white border-l-4 rounded-2xl card-shadow shadow-xl text-sm font-medium
                        transition-all duration-300 ease-out
                        ${isError ? 'border-red-500 text-red-600' : 'border-brand-green text-brand-green'}
                        ${show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-2'}`}
        >
            <span className={`font-bold text-base leading-none flex-shrink-0 ${isError ? 'text-red-500' : 'text-brand-green'}`}>
                {isError ? '✕' : '✓'}
            </span>
            <span>{message}</span>
        </div>
    );
}

export default function Toasts() {
    const { flash } = usePage().props;
    const [items, setItems] = useState([]);

    useEffect(() => {
        if (flash?.success) {
            setItems((prev) => [...prev, { id: Date.now() + '-s', tone: 'success', message: flash.success }]);
        }
    }, [flash?.success]);

    useEffect(() => {
        if (flash?.error) {
            setItems((prev) => [...prev, { id: Date.now() + '-e', tone: 'error', message: flash.error }]);
        }
    }, [flash?.error]);

    return (
        <div className="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none">
            {items.map((item) => (
                <Toast
                    key={item.id}
                    tone={item.tone}
                    message={item.message}
                    onDone={() => setItems((prev) => prev.filter((i) => i.id !== item.id))}
                />
            ))}
        </div>
    );
}
