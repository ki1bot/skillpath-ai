import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';

const HEARTBEAT_INTERVAL_MS = 4 * 60 * 1000;

type SessionPageProps = {
    auth?: {
        user?: unknown | null;
    };
};

export function SessionKeepAlive() {
    const { auth } = usePage().props as SessionPageProps;
    const isAuthenticated = Boolean(auth?.user);

    useEffect(() => {
        if (!isAuthenticated) {
            return;
        }

        let active = true;
        let requestInProgress = false;
        let currentController: AbortController | null = null;

        const heartbeat = async () => {
            if (!active || requestInProgress) {
                return;
            }

            requestInProgress = true;

            const controller = new AbortController();

            currentController = controller;

            try {
                await fetch('/session/heartbeat', {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    signal: controller.signal,
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
            } catch {
                return;
            } finally {
                requestInProgress = false;

                if (currentController === controller) {
                    currentController = null;
                }
            }
        };

        const refreshSession = () => {
            void heartbeat();
        };

        refreshSession();

        const interval = window.setInterval(
            refreshSession,
            HEARTBEAT_INTERVAL_MS,
        );

        window.addEventListener('focus', refreshSession);
        window.addEventListener('pageshow', refreshSession);

        document.addEventListener('visibilitychange', refreshSession);

        return () => {
            active = false;

            currentController?.abort();

            window.clearInterval(interval);

            window.removeEventListener('focus', refreshSession);
            window.removeEventListener('pageshow', refreshSession);

            document.removeEventListener('visibilitychange', refreshSession);
        };
    }, [isAuthenticated]);

    return null;
}
