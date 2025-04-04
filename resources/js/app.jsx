import './bootstrap';
import '../css/app.css';

import {createRoot} from 'react-dom/client';
import {createInertiaApp} from '@inertiajs/react';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import DashboardLayout from './Layouts/DashboardLayout';


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

export const routeTenant = (name, params = {}) => {

    return route(name, {...params, tenant: route().params.tenant});
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx'))
        .then((page) => {
            if (page.default.layout === undefined && !name.startsWith('Auth/')) {
                page.default.layout = page => <DashboardLayout>{page}</DashboardLayout>;
            }
            return page;
        }),
    setup({el, App, props}) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});
