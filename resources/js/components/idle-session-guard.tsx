import { router, usePage } from '@inertiajs/react';
import { useCallback, useEffect, useRef } from 'react';

const LAST_ACTIVITY_KEY = 'skillpath:last-activity';
const HEARTBEAT_INTERVAL_MS = 4 * 60 * 1000;
const IDLE_CHECK_INTERVAL_MS = 15 * 1000;

type IdlePageProps = {
    auth?: {
        user?: unknown | null;
    };
    idleTimeoutMinutes?: number;
};

export function IdleSessionGuard() {
    const { auth, idleTimeoutMinutes = 10 } = usePage().props as IdlePageProps;

    const isAuthenticated = Boolean(auth?.user);
    const timeoutMs = Math.max(1, idleTimeoutMinutes) * 60 * 1000;

    const lastHeartbeatAt = useRef(0);
    const loggingOut = useRef(false);

    const getLastActivity = useCallback(() => {
        const stored = Number(window.localStorage.getItem(LAST_ACTIVITY_KEY));

        return Number.isFinite(stored) && stored > 0 ? stored : Date.now();
    }, []);

    const logout = useCallback(() => {
        if (loggingOut.current) {
            return;
        }

        loggingOut.current = true;

        window.localStorage.removeItem(LAST_ACTIVITY_KEY);

        router.post(
            '/logout',
            {},
            {
                preserveScroll: true,
                onFinish: () => window.location.assign('/login'),
            },
        );
    }, []);

    const isIdle = useCallback(
        () => Date.now() - getLastActivity() >= timeoutMs,
        [getLastActivity, timeoutMs],
    );

    const heartbeat = useCallback(async () => {
        if (!isAuthenticated || loggingOut.current || isIdle()) {
            return;
        }

        try {
            const response = await fetch('/session/heartbeat', {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (
                response.redirected ||
                response.status === 401 ||
                response.status === 419
            ) {
                window.localStorage.removeItem(LAST_ACTIVITY_KEY);

                window.location.assign('/login');

                return;
            }

            if (response.ok) {
                lastHeartbeatAt.current = Date.now();
            }
        } catch {
            return;
        }
    }, [isAuthenticated, isIdle]);

    const recordActivity = useCallback(() => {
        if (!isAuthenticated || loggingOut.current) {
            return;
        }

        if (isIdle()) {
            logout();

            return;
        }

        const now = Date.now();

        window.localStorage.setItem(LAST_ACTIVITY_KEY, String(now));

        if (now - lastHeartbeatAt.current >= HEARTBEAT_INTERVAL_MS) {
            void heartbeat();
        }
    }, [heartbeat, isAuthenticated, isIdle, logout]);

    useEffect(() => {
        if (!isAuthenticated) {
            window.localStorage.removeItem(LAST_ACTIVITY_KEY);

            loggingOut.current = false;

            return;
        }

        loggingOut.current = false;
        lastHeartbeatAt.current = Date.now();

        if (!window.localStorage.getItem(LAST_ACTIVITY_KEY)) {
            window.localStorage.setItem(LAST_ACTIVITY_KEY, String(Date.now()));
        }

        const checkIdle = () => {
            if (isIdle()) {
                logout();
            }
        };

        const handleVisibilityChange = () => {
            if (!document.hidden) {
                checkIdle();
            }
        };

        const handlePointerActivity = () => recordActivity();

        const handleKeyboardActivity = () => recordActivity();

        const handleTouchActivity = () => recordActivity();

        const handleScrollActivity = () => recordActivity();

        window.addEventListener('pointerdown', handlePointerActivity, {
            passive: true,
        });

        window.addEventListener('keydown', handleKeyboardActivity);

        window.addEventListener('touchstart', handleTouchActivity, {
            passive: true,
        });

        window.addEventListener('scroll', handleScrollActivity, {
            passive: true,
        });

        document.addEventListener('visibilitychange', handleVisibilityChange);

        const interval = window.setInterval(checkIdle, IDLE_CHECK_INTERVAL_MS);

        return () => {
            window.removeEventListener('pointerdown', handlePointerActivity);

            window.removeEventListener('keydown', handleKeyboardActivity);

            window.removeEventListener('touchstart', handleTouchActivity);

            window.removeEventListener('scroll', handleScrollActivity);

            document.removeEventListener(
                'visibilitychange',
                handleVisibilityChange,
            );

            window.clearInterval(interval);
        };
    }, [isAuthenticated, isIdle, logout, recordActivity]);

    return null;
}
