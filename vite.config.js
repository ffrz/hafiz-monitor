import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { quasar, transformAssetUrls } from "@quasar/vite-plugin";

export default defineConfig({
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', 'quasar', 'dayjs', 'echarts', 'vue-echarts'],
          components: [
            '/resources/js/pages/admin/auth/Login.vue',
            '/resources/js/pages/admin/auth/Register.vue',
            '/resources/js/pages/admin/user/Index.vue',
            '/resources/js/pages/admin/user/Editor.vue',
            '/resources/js/pages/admin/time-entry/Index.vue',
            '/resources/js/pages/admin/time-entry/Editor.vue',
            '/resources/js/pages/admin/time-tracker/Index.vue',
            '/resources/js/pages/admin/time-tracker/Timer.vue',
            '/resources/js/pages/admin/time-tracker/TimeEntries.vue',
            '/resources/js/pages/admin/project/Index.vue',
            '/resources/js/pages/admin/project/Editor.vue',
            '/resources/js/pages/admin/client/Index.vue',
            '/resources/js/pages/admin/client/Editor.vue',
            '/resources/js/pages/admin/profile/Edit.vue',
            '/resources/js/pages/admin/profile/partials/UpdatePasswordForm.vue',
            '/resources/js/pages/admin/profile/partials/UpdateProfileInformationForm.vue',
          ],
        },
      },
    },
  },
  plugins: [
    vue({
      template: { transformAssetUrls },
    }),
    // @quasar/plugin-vite options list:
    // https://github.com/quasarframework/quasar/blob/dev/vite-plugin/index.d.ts
    quasar({
      sassVariables: "/resources/css/quasar-variables.sass",
    }),
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
  ],
});
