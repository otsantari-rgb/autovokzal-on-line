import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer'
import { createSSRApp, h } from 'vue'
import AppLayout from './App.vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import Vue3Toastify from 'vue3-toastify'
import flatPickr from 'vue-flatpickr-component'

createServer(
    (page) =>
        createInertiaApp({
            page,
            render: renderToString,
            resolve: (name) => {
                const pages = import.meta.glob('./views/**/*.vue', { eager: true });

                const path = Object.keys(pages).find((key) =>
                    key.endsWith(`/${name}.vue`)
                );

                if (!path) {
                    throw new Error(`[SSR] Page not found: ${name}`);
                }

                const page = pages[path];
                page.default.layout = page.default.layout || AppLayout;
                return page;
            },
            setup({ App, props, plugin }) {
                const app = createSSRApp({ render: () => h(App, props) })
                app.use(plugin)
                app.use(createPinia().use(piniaPluginPersistedstate))
                app.use(Vue3Toastify, { autoClose: 3000 })
                app.component('flat-pickr', flatPickr)

                return app
            },
        }),
)
