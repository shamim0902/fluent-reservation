import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import liveReload from 'vite-plugin-live-reload';
import copy from 'rollup-plugin-copy';

import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: 
  [
    vue(),
    liveReload(`${__dirname}/**/*\.php`),
    // ...
    AutoImport({
      resolvers: [ElementPlusResolver()],
    }),
    Components({
      resolvers: [ElementPlusResolver()],
    }),
    copy({
      targets: [
        { src: 'src/assets/*', dest: 'assets/' },
      ]
    })
  ],

  build: {
    manifest: true,
    outDir: 'assets',
    assetsDir: 'assetsDIR',
    // publicDir: 'public',
    emptyOutDir: true, // delete the contents of the output directory before each build

 // https://rollupjs.org/guide/en/#big-list-of-options
    rollupOptions: {
      input: [
        'src/admin/start.js',
        'src/style.css',
        // 'src/assets'
      ],
      output: {
        chunkFileNames: 'js/[name].js',
        entryFileNames: 'js/[name].js',
        
        assetFileNames: ({name}) => {
          // if (/\.(gif|jpe?g|png|svg)$/.test(name ?? '')){
          //     return 'images/[name][extname]';
          // }
          
          if (/\.css$/.test(name ?? '')) {
              return 'css/[name][extname]';   
          }
 
          // default value
          // ref: https://rollupjs.org/guide/en/#outputassetfilenames
          return '[name][extname]';
        },
      },
    },
  },

  resolve: {
    alias: {
      'vue': 'vue/dist/vue.esm-bundler.js',
    },
  },

  server: {
    port: 2222,
    strictPort: true,
    hmr: {
      port: 2222,
      host: 'localhost',
      protocol: 'ws',
    }
  }
})

