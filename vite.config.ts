import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';

const isDockerDevelopment = process.env.DOCKER_MODE === 'development';

export default defineConfig(({ command }) => {
    const skipWayfinder =
        process.env.SKIP_WAYFINDER === '1' ||
        (isDockerDevelopment && command === 'build');

    return {
        optimizeDeps:
            command === 'serve'
                ? {
                      entries: ['resources/js/app.tsx'],
                      holdUntilCrawlEnd: true,
                      include: [
                          'react',
                          'react/jsx-runtime',
                          'react-dom',
                          'react-dom/client',
                          '@inertiajs/react',
                          '@laravel/passkeys',
                          '@radix-ui/react-avatar',
                          '@radix-ui/react-checkbox',
                          '@radix-ui/react-collapsible',
                          '@radix-ui/react-dialog',
                          '@radix-ui/react-dropdown-menu',
                          '@radix-ui/react-label',
                          '@radix-ui/react-navigation-menu',
                          '@radix-ui/react-select',
                          '@radix-ui/react-separator',
                          '@radix-ui/react-slot',
                          '@radix-ui/react-toggle',
                          '@radix-ui/react-toggle-group',
                          '@radix-ui/react-tooltip',
                          'class-variance-authority',
                          'clsx',
                          'input-otp',
                          'lucide-react',
                          'next-themes',
                          'radix-ui',
                          'recharts',
                          'sonner',
                          'tailwind-merge',
                      ],
                  }
                : undefined,

        server: isDockerDevelopment
            ? {
                  host: '0.0.0.0',
                  port: 5173,
                  strictPort: true,
                  origin: 'http://localhost:5173',

                  cors: {
                      origin: 'http://localhost:8080',
                      credentials: true,
                  },

                  hmr: {
                      protocol: 'ws',
                      host: 'localhost',
                      port: 5173,
                      clientPort: 5173,
                  },

                  watch: {
                      usePolling: true,
                      interval: 1000,
                  },

                  warmup: {
                      clientFiles: [
                          './resources/js/app.tsx',
                          './resources/js/layouts/root-layout.tsx',
                          './resources/js/layouts/public-layout.tsx',
                          './resources/js/pages/welcome.tsx',
                          './resources/css/app.css',
                      ],
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
    };
});
