import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import liveReload from 'vite-plugin-live-reload';
import copy from 'rollup-plugin-copy';

import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'
import path from "path";
import fs from "fs";

// https://vitejs.dev/config/

let viteConfig;
const moveManifestPlugin = {
  name: "move-manifest",
  configResolved(resolvedConfig) {
    viteConfig = resolvedConfig;
  },
  writeBundle() {
    const outDir = viteConfig.build.outDir;
    const manifestSrc = path.join(outDir, ".vite", "manifest.json");
    const manifestDest = path.resolve(__dirname, 'assets/manifest.json');
    const viteDir = path.join(outDir, ".vite");

    if (fs.existsSync(manifestSrc)) {
      // Move the manifest file
      fs.renameSync(manifestSrc, manifestDest);

      // Remove empty .vite directory if exists
      if (fs.existsSync(viteDir) && fs.readdirSync(viteDir).length === 0) {
        fs.rmSync(viteDir, {recursive: true});
      }

    }
  },
};
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
    moveManifestPlugin,
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
        'src/Public/Public.js',
        'src/frontend/start.js',
        'src/scss/admin/app.scss'
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
    },
    cors: {
      origin: "*",
      methods: ["GET"],
      allowedHeaders: ["Content-Type", "Authorization"],
    },
  }
})

