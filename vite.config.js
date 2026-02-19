import { defineConfig } from 'vite';
import path from 'path';
import liveReload from 'vite-plugin-live-reload';

const themeName = 'themesName';

export default defineConfig({
  server: {
    port: 5173,
    strictPort: true,
    proxy: {
      '^(?!/(src|node_modules|@vite|@id|@fs)).*': {
        target: 'http://localhost:8888',
        changeOrigin: true,
        secure: false,
      },
    },
    hmr: {
      host: 'localhost',
    },
  },

  build: {
    outDir: path.resolve(__dirname, `themes/${themeName}/dist`),
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        common: path.resolve(__dirname, 'src/js/common.js'),
        style: path.resolve(__dirname, 'src/scss/style.scss'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          const extType = assetInfo.name.split('.').pop();
          if (/css/i.test(extType)) {
            return 'css/[name][extname]';
          }
          if (/png|jpe?g|gif|svg|webp|ico/i.test(extType)) {
            return 'assets/img/[name][extname]';
          }
          if (/woff2?|eot|ttf|otf/i.test(extType)) {
            return 'assets/fonts/[name][extname]';
          }
          return 'assets/[name][extname]';
        },
      },
    },
    sourcemap: true,
    cssCodeSplit: false,
  },

  css: {
    devSourcemap: true,
    preprocessorOptions: {
      scss: {
        loadPaths: [
          path.resolve(__dirname, 'src/scss'),
        ],
        api: 'modern-compiler',
      },
    },
  },

  plugins: [
    liveReload([
      `themes/${themeName}/**/*.php`,
    ]),
  ],

  publicDir: 'public',

  base: process.env.NODE_ENV === 'production'
    ? `/wp-content/themes/${themeName}/dist/`
    : '/',

  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src'),
      '@scss': path.resolve(__dirname, 'src/scss'),
      '@js': path.resolve(__dirname, 'src/js'),
      '@assets': path.resolve(__dirname, 'src/assets'),
    },
  },
});
