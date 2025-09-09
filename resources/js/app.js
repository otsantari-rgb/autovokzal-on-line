import './bootstrap'
import 'bootstrap'

import { createSSRApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import AppLayout from './App.vue'

import Vue3Toastify from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'


import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

import { router } from '@inertiajs/vue3'

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(`./views/${name}.vue`, import.meta.glob('./views/**/*.vue')).then(
            (page) => {
                page.default.layout = page.default.layout || AppLayout
                return page
            }
        ),
    setup({ el, App, props, plugin }) {
        const app = createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia().use(piniaPluginPersistedstate))
            .use(Vue3Toastify, { autoClose: 3000 })

        app.mount(el)

        // Яндекс.Метрика (только в браузере)
        if (typeof window !== 'undefined' && typeof window.ym !== 'undefined') {
            router.on('finish', (event) => {
                if (event.detail.visit) {
                    window.ym(99782890, 'hit', event.detail.visit.url)
                }
            })
        }

        return app
    },
})
