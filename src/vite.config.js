import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel(["resources/sass/app.scss", "resources/js/app.js"]),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: "0.0.0.0",
        hmr: {
            host: "localhost",
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: ['mixed-decls', 'color-functions', 'global-builtin', 'import']
            },
        }
    },
});
