import "primeicons/primeicons.css";
import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import PrimeVue from "primevue/config";
import Aura from "@primevue/themes/aura";
import Ripple from "primevue/ripple"; // Import the Ripple directive
import Tooltip from "primevue/tooltip";
import { createI18n } from "vue-i18n";
import Message from "primevue/message";
import Toast from "primevue/toast";
import ToastService from "primevue/toastservice";
import ConfirmationService from "primevue/confirmationservice";
import ConfirmDialog from "primevue/confirmdialog";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

const messages = {
    en: { message: { hello: "hello world" } },
    kh: { message: { hello: "សួស្តី​ពិភពលោក" } },
};

const i18n = createI18n({
    legacy: false, // ✅ Enable Composition API Mode
    locale: "en",
    fallbackLocale: "en",
    messages,
    globalInjection: true, // ✅ Allows `$t()` globally in setup()
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(ZiggyVue) // Use ZiggyVue
            .use(plugin) // Use the plugin
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                },
            }) // Use PrimeVue
            .use(i18n) // Use i18n
            .directive("ripple", Ripple) // Register the Ripple directive
            .directive("tooltip", Tooltip) // Register the Tooltip directive
            .use(ToastService) // Correct usage of ToastService
            .use(ConfirmationService)
            .component("Toast", Toast) // Register the Toast component
            .component("Message", Message) // Register the Message component
            .component('ConfirmDialog', ConfirmDialog)
            .mount(el); // Mount the app finally
    },
    progress: {
        color: "#4B5563",
    },
});
