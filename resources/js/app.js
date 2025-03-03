import 'primeicons/primeicons.css';
import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import ToastService from 'primevue/toastservice';
import Ripple from 'primevue/ripple'; // Import the Ripple directive
import Tooltip from 'primevue/tooltip';
import { createI18n } from 'vue-i18n';
import kh from './locale/kh.json';
import en from './locale/en.json';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';


createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura
                }
            })
            .use(i18n)
            .directive('ripple', Ripple) // Register the Ripple directive
            .directive('tooltip', Tooltip)
            .use(ToastService)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

const i18n = createI18n({
    fallbackLocale: 'en',
    locale: document.cookie.split('=')[1],
    messages:{
        en: en,
        kh: kh,
    }
});
