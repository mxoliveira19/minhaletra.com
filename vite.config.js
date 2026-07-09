import { defineConfig } from 'vite'

export default defineConfig({
  build: {
    manifest: true,
    outDir: 'public/build',
    emptyOutDir: true,
    rollupOptions: {
      input: 'src/main.js'
    }
  },
  server: {
    host: '0.0.0.0',
    port: 5173
  }
})