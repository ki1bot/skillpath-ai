import { createInertiaApp } from '@inertiajs/react';
import { Toaster } from '@/components/ui/sonner';
import { initializeTheme } from '@/hooks/use-appearance';
import AppLayout from '@/layouts/app-layout';
import AuthLayout from '@/layouts/auth-layout';
import PublicLayout from '@/layouts/public-layout';
import RootLayout from '@/layouts/root-layout';
import SettingsLayout from '@/layouts/settings/layout';

const appName = import.meta.env.VITE_APP_NAME || 'SkillPath AI';

createInertiaApp({
    pages: {
        path: './pages',
        extension: '.tsx',
        lazy: true,
    },

    title: (title) => (title ? `${title} - ${appName}` : appName),

    layout: (name) => {
        switch (true) {
            case name === 'welcome':
            case name.startsWith('public/'):
                return [RootLayout, PublicLayout];

            case name.startsWith('auth/'):
                return [RootLayout, AuthLayout];

            case name.startsWith('settings/'):
                return [RootLayout, AppLayout, SettingsLayout];

            default:
                return [RootLayout, AppLayout];
        }
    },

    strictMode: true,

    defaults: {
        prefetch: {
            cacheFor: '2m',
            hoverDelay: 50,
        },
    },

    withApp(app) {
        return (
            <>
                {app}
                <Toaster />
            </>
        );
    },

    progress: {
        color: '#1f1f1c',
    },
});

initializeTheme();
