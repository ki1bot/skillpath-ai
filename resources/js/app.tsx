import { createInertiaApp } from '@inertiajs/react';
import { Toaster } from '@/components/ui/sonner';
import { TooltipProvider } from '@/components/ui/tooltip';
import { initializeTheme } from '@/hooks/use-appearance';
import AppLayout from '@/layouts/app-layout';
import AuthLayout from '@/layouts/auth-layout';
import PublicLayout from '@/layouts/public-layout';
import RootLayout from '@/layouts/root-layout';
import SettingsLayout from '@/layouts/settings/layout';

const appName = import.meta.env.VITE_APP_NAME || 'SkillPath AI';

createInertiaApp({
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

    withApp(app) {
        return (
            <TooltipProvider delayDuration={0}>
                {app}
                <Toaster />
            </TooltipProvider>
        );
    },

    progress: {
        color: '#1f1f1c',
    },
});

initializeTheme();
