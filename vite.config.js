import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import legacy from '@vitejs/plugin-legacy';
import viteCompression from 'vite-plugin-compression';
import { resolve } from 'path';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/scss/public/styles.scss',
        'resources/js/app.js',
      ],
      refresh: true,
    }),
    
    legacy({
      targets: ['defaults', 'not IE 11'],
      additionalLegacyPolyfills: ['regenerator-runtime/runtime'],
      polyfills: [
        'es.promise',
        'es.array.iterator',
        'es.object.assign',
        'es.string.includes',
        'web.dom-collections.iterator',
      ],
      modernPolyfills: [
        'es.promise.finally',
      ],
    }),
    
    viteCompression({
      verbose: true,
      disable: false,
      threshold: 10240,
      algorithm: 'gzip',
      ext: '.gz',
    }),
  ],

  build: {
    outDir: 'public/build',
    manifest: true,
    rollupOptions: {
      output: {
        manualChunks: (id) => {
          if (id.includes('node_modules')) {
            if (id.includes('chart.js')) {
              return 'chart';
            }
            if (id.includes('prismjs')) {
              return 'prism';
            }
            if (id.includes('jquery')) {
              return 'vendor';
            }
            return 'vendor';
          }
        },
        entryFileNames: 'assets/[name]-[hash].js',
        chunkFileNames: 'assets/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          const info = assetInfo.name.split('.');
          const ext = info[info.length - 1];
          if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico|webp)$/i.test(assetInfo.name)) {
            return `images/[name]-[hash].${ext}`;
          }
          if (/\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) {
            return `fonts/[name]-[hash].${ext}`;
          }
          if (ext === 'css') {
            return `assets/[name]-[hash].${ext}`;
          }
          return `assets/[name]-[hash].${ext}`;
        },
      },
    },
    chunkSizeWarningLimit: 1000,
    assetsInlineLimit: 4096,
  },

  css: {
    postcss: './postcss.config.js',
    preprocessorOptions: {
      scss: {
        additionalData: `@import "resources/scss/public/_variables.scss";`,
      },
    },
  },

  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources'),
      '@js': resolve(__dirname, 'resources/js'),
      '@scss': resolve(__dirname, 'resources/scss'),
      '@images': resolve(__dirname, 'resources/images'),
    },
  },

  server: {
    host: 'localhost',
    port: 5173,
    strictPort: false,
    hmr: {
      host: 'localhost',
    },
  },

  performance: {
    maxEntrypointSize: 250000,
    maxAssetSize: 250000,
  },
});
