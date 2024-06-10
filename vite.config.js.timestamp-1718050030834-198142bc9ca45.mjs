// vite.config.js
import { defineConfig } from "file:///C:/Users/stefv/Documents/lessen/Eindwerk_comics_platform/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/Users/stefv/Documents/lessen/Eindwerk_comics_platform/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/Users/stefv/Documents/lessen/Eindwerk_comics_platform/node_modules/@vitejs/plugin-vue/dist/index.mjs";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/sass/app.scss",
        "resources/css/app.css",
        "resources/css/home.css",
        "resources/css/comics.css",
        "resources/css/header.css",
        "resources/css/footer.css",
        "resources/js/app.js",
        "resources/js/comics.js",
        "resources/js/comic.js",
        "resources/js/characters.js",
        "resources/js/character.js",
        "resources/js/wishlist.js"
      ],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    })
  ],
  resolve: {
    alias: {
      vue: "vue/dist/vue.esm-bundler.js"
    }
  },
  build: {
    outDir: "public/build",
    // Zorg ervoor dat dit correct is ingesteld
    emptyOutDir: true
    // Leeg de output directory voor elke build
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxVc2Vyc1xcXFxzdGVmdlxcXFxEb2N1bWVudHNcXFxcbGVzc2VuXFxcXEVpbmR3ZXJrX2NvbWljc19wbGF0Zm9ybVwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiQzpcXFxcVXNlcnNcXFxcc3RlZnZcXFxcRG9jdW1lbnRzXFxcXGxlc3NlblxcXFxFaW5kd2Vya19jb21pY3NfcGxhdGZvcm1cXFxcdml0ZS5jb25maWcuanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL0M6L1VzZXJzL3N0ZWZ2L0RvY3VtZW50cy9sZXNzZW4vRWluZHdlcmtfY29taWNzX3BsYXRmb3JtL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XHJcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nO1xyXG5pbXBvcnQgdnVlIGZyb20gJ0B2aXRlanMvcGx1Z2luLXZ1ZSc7XHJcblxyXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xyXG4gICAgcGx1Z2luczogW1xyXG4gICAgICAgIGxhcmF2ZWwoe1xyXG4gICAgICAgICAgICBpbnB1dDogW1xyXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9zYXNzL2FwcC5zY3NzJyxcclxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvY3NzL2FwcC5jc3MnLFxyXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9jc3MvaG9tZS5jc3MnLFxyXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9jc3MvY29taWNzLmNzcycsXHJcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2Nzcy9oZWFkZXIuY3NzJyxcclxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvY3NzL2Zvb3Rlci5jc3MnLFxyXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9hcHAuanMnLFxyXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9jb21pY3MuanMnLFxyXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9jb21pYy5qcycsXHJcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL2NoYXJhY3RlcnMuanMnLFxyXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9jaGFyYWN0ZXIuanMnLFxyXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy93aXNobGlzdC5qcycsXHJcbiAgICAgICAgICAgIF0sXHJcbiAgICAgICAgICAgIHJlZnJlc2g6IHRydWUsXHJcbiAgICAgICAgfSksXHJcbiAgICAgICAgdnVlKHtcclxuICAgICAgICAgICAgdGVtcGxhdGU6IHtcclxuICAgICAgICAgICAgICAgIHRyYW5zZm9ybUFzc2V0VXJsczoge1xyXG4gICAgICAgICAgICAgICAgICAgIGJhc2U6IG51bGwsXHJcbiAgICAgICAgICAgICAgICAgICAgaW5jbHVkZUFic29sdXRlOiBmYWxzZSxcclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSksXHJcbiAgICBdLFxyXG4gICAgcmVzb2x2ZToge1xyXG4gICAgICAgIGFsaWFzOiB7XHJcbiAgICAgICAgICAgIHZ1ZTogJ3Z1ZS9kaXN0L3Z1ZS5lc20tYnVuZGxlci5qcycsXHJcbiAgICAgICAgfSxcclxuICAgIH0sXHJcbiAgICBidWlsZDoge1xyXG4gICAgICAgIG91dERpcjogJ3B1YmxpYy9idWlsZCcsIC8vIFpvcmcgZXJ2b29yIGRhdCBkaXQgY29ycmVjdCBpcyBpbmdlc3RlbGRcclxuICAgICAgICBlbXB0eU91dERpcjogdHJ1ZSwgLy8gTGVlZyBkZSBvdXRwdXQgZGlyZWN0b3J5IHZvb3IgZWxrZSBidWlsZFxyXG4gICAgfSxcclxufSk7Il0sCiAgIm1hcHBpbmdzIjogIjtBQUFzVyxTQUFTLG9CQUFvQjtBQUNuWSxPQUFPLGFBQWE7QUFDcEIsT0FBTyxTQUFTO0FBRWhCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQ3hCLFNBQVM7QUFBQSxJQUNMLFFBQVE7QUFBQSxNQUNKLE9BQU87QUFBQSxRQUNIO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxNQUNKO0FBQUEsTUFDQSxTQUFTO0FBQUEsSUFDYixDQUFDO0FBQUEsSUFDRCxJQUFJO0FBQUEsTUFDQSxVQUFVO0FBQUEsUUFDTixvQkFBb0I7QUFBQSxVQUNoQixNQUFNO0FBQUEsVUFDTixpQkFBaUI7QUFBQSxRQUNyQjtBQUFBLE1BQ0o7QUFBQSxJQUNKLENBQUM7QUFBQSxFQUNMO0FBQUEsRUFDQSxTQUFTO0FBQUEsSUFDTCxPQUFPO0FBQUEsTUFDSCxLQUFLO0FBQUEsSUFDVDtBQUFBLEVBQ0o7QUFBQSxFQUNBLE9BQU87QUFBQSxJQUNILFFBQVE7QUFBQTtBQUFBLElBQ1IsYUFBYTtBQUFBO0FBQUEsRUFDakI7QUFDSixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
