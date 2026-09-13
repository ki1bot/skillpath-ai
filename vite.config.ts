import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';

const skipWayfinder = process.env.SKIP_WAYFINDER === '1';
const isDockerDevelopment = process.env.DOCKER_MODE === 'development';

export default defineConfig({
    server: isDockerDevelopment
        ? {
              host: '0.0.0.0',
              port: 5173,
              strictPort: true,
              origin: 'http://localhost:5173',
              hmr: {
                  host: 'localhost',
                  clientPort: 5173,
              },
              watch: {
                  usePolling: true,
                  interval: 300,
              },
          }
        : undefined,
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        inertia({
            ssr: false,
        }),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        ...(skipWayfinder
            ? []
            : [
                  wayfinder({
                      formVariants: true,
                  }),
              ]),
    ],
});
