import "primeicons/primeicons.css";
import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
// import { ZiggyVue } from "../../vendor/tightenco/ziggy";
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
import "../css/app.css";
import { Ziggy } from "./ziggy";
// import "primevue/resources/themes/aura/theme.css";
// import "primevue/resources/primevue.min.css";
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
    // resolve: (name) => {
    //     const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
    //     return pages[`./Pages/${name}.vue`];
    // },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Ziggy)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                },
                ripple: true,
            })
            .use(i18n)
            .use(ToastService)
            .use(ConfirmationService)
            .directive("ripple", Ripple)
            .directive("tooltip", Tooltip)
            .component("Message", Message)
            .component("Toast", Toast)
            .component("ConfirmDialog", ConfirmDialog);

        return vueApp.mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
