import '@inertiajs/core';
import type { Auth } from '@/types/auth';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            publicChat: {
                model: string;
            };
            flash: {
                success?: string | null;
                error?: string | null;
            };
            [key: string]: unknown;
        };
    }
}
