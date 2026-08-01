import '../css/app.css';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { route } from 'ziggy-js';

const appName = document.querySelector('meta[name="app-name"]')?.content || 'ລະບົບກວດສອບການຂາດລາ';

createInertiaApp({
    title: (title) => (title ? `${title} — ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        return pages[`./Pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        // Ziggy attaches window.Ziggy via the @routes Blade directive; expose route()
        // globally so it works the same as the Blade route() helper everywhere.
        window.route = (name, params, absolute, config) => route(name, params, absolute, config ?? window.Ziggy);

        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#8f4e00',
    },
});
