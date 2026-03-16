import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

/**
 * Load dashmix.app.min.js exactly once, after React has rendered #page-container.
 */
let dashmixLoaded = false;
function loadDashmix() {
    if (dashmixLoaded) {
        // Already loaded: just re-init the theme
        requestAnimationFrame(() => {
            if (window.Dashmix && typeof window.Dashmix.init === 'function') {
                try { window.Dashmix.init(); } catch(e) { /* ignore */ }
            }
        });
        return;
    }
    dashmixLoaded = true;
    const script = document.createElement('script');
    script.src = '/js/dashmix.app.min.js';
    script.async = false;
    document.body.appendChild(script);
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
        // Load dashmix after first React paint (#page-container is now in DOM)
        requestAnimationFrame(loadDashmix);
    },
    progress: {
        color: '#4B5563',
    },
});

// Re-init on every subsequent Inertia navigation
router.on('finish', () => requestAnimationFrame(loadDashmix));
