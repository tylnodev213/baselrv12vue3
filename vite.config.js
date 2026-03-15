import { defineConfig, loadEnv } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');
  const appUrl = env.APP_URL || 'http://localhost';
  const host = new URL(appUrl).hostname;

  return {
    plugins: [
      laravel({
        input: ['resources/js/main.js'],
        refresh: true,
      }),
      vue(),
    ],
    resolve: {
      alias: {
        '@': path.resolve(__dirname, './resources/js'),
      },
    },
    server: {
      host: '0.0.0.0',
      port: 5173,
      https: false,
      hmr: {
        host: host,
      },
      proxy: {
        '/api': {
          target: appUrl,
          changeOrigin: true,
          rewrite: (path) => path.replace(/^\/api/, '/api'),
        },
      },
    },
  };
});
