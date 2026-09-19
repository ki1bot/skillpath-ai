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
    const { auth, idleTimeoutMinutes = 0 } = usePage().props as IdlePageProps;

    const isAuthenticated = Boolean(auth?.user);
    const idleTimeoutEnabled = idleTimeoutMinutes > 0;

    const timeoutMs = idleTimeoutEnabled
        ? idleTimeoutMinutes * 60 * 1000
        : null;

    const heartbeatIntervalMs =
        timeoutMs === null
            ? HEARTBEAT_INTERVAL_MS
            : Math.min(
                  HEARTBEAT_INTERVAL_MS,
                  Math.max(15 * 1000, Math.floor(timeoutMs / 2)),
              );

    const lastHeartbeatAt = useRef(0);
    const loggingOut = useRef(false);

    const getLastActivity = useCallback((): number | null => {
        const stored = Number(window.localStorage.getItem(LAST_ACTIVITY_KEY));

        if (!Number.isFinite(stored) || stored <= 0) {
            return null;
        }

        return stored;
    }, []);

    const setLastActivity = useCallback((timestamp: number = Date.now()) => {
        window.localStorage.setItem(LAST_ACTIVITY_KEY, String(timestamp));
    }, []);

    const clearLastActivity = useCallback(() => {
        window.localStorage.removeItem(LAST_ACTIVITY_KEY);
    }, []);

    const logout = useCallback(() => {
        if (loggingOut.current) {
            return;
        }

        loggingOut.current = true;

        clearLastActivity();

        router.post(
            '/logout',
            {},
            {
                preserveScroll: true,
                onFinish: () => {
                    window.location.assign('/login');
                },
            },
        );
    }, [clearLastActivity]);

    const isIdle = useCallback(() => {
        if (timeoutMs === null) {
            return false;
        }

        const lastActivity = getLastActivity();

        if (lastActivity === null) {
            return false;
        }

        return Date.now() - lastActivity >= timeoutMs;
    }, [getLastActivity, timeoutMs]);

    const heartbeat = useCallback(async () => {
        if (
            !isAuthenticated ||
            !idleTimeoutEnabled ||
            loggingOut.current ||
            isIdle()
        ) {
            return;
        }

        try {
            const response = await fetch('/session/heartbeat', {
                method: 'GET',
                credentials: 'same-origin',
                keepalive: true,
                cache: 'no-store',
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
                clearLastActivity();

                window.location.assign('/login');

                return;
            }

            if (response.ok) {
                lastHeartbeatAt.current = Date.now();
            }
        } catch {
            return;
        }
    }, [clearLastActivity, idleTimeoutEnabled, isAuthenticated, isIdle]);

    const checkIdle = useCallback(() => {
        if (!isAuthenticated || !idleTimeoutEnabled || loggingOut.current) {
            return;
        }

        if (isIdle()) {
            logout();
        }
    }, [idleTimeoutEnabled, isAuthenticated, isIdle, logout]);

    const recordActivity = useCallback(() => {
        if (
            !isAuthenticated ||
            !idleTimeoutEnabled ||
            loggingOut.current ||
            document.hidden
        ) {
            return;
        }

        if (isIdle()) {
            logout();

            return;
        }

        const now = Date.now();

        setLastActivity(now);

        if (now - lastHeartbeatAt.current >= heartbeatIntervalMs) {
            void heartbeat();
        }
    }, [
        heartbeat,
        heartbeatIntervalMs,
        idleTimeoutEnabled,
        isAuthenticated,
        isIdle,
        logout,
        setLastActivity,
    ]);

    const resumeVisibleSession = useCallback(() => {
        if (!isAuthenticated || !idleTimeoutEnabled || loggingOut.current) {
            return;
        }

        if (isIdle()) {
            logout();

            return;
        }

        setLastActivity();

        void heartbeat();
    }, [
        heartbeat,
        idleTimeoutEnabled,
        isAuthenticated,
        isIdle,
        logout,
        setLastActivity,
    ]);

    useEffect(() => {
        if (!isAuthenticated || !idleTimeoutEnabled) {
            clearLastActivity();

            loggingOut.current = false;
            lastHeartbeatAt.current = 0;

            return;
        }

        loggingOut.current = false;

        const existingLastActivity = getLastActivity();

        if (
            existingLastActivity !== null &&
            timeoutMs !== null &&
            Date.now() - existingLastActivity >= timeoutMs
        ) {
            logout();

            return;
        }

        if (existingLastActivity === null) {
            setLastActivity();
        }

        if (!document.hidden) {
            setLastActivity();

            void heartbeat();
        }

        const handleVisibilityChange = () => {
            if (document.hidden) {
                if (!isIdle()) {
                    setLastActivity();

                    void heartbeat();
                }

                return;
            }

            resumeVisibleSession();
        };

        const handleFocus = () => {
            if (!document.hidden) {
                resumeVisibleSession();
            }
        };

        const handlePageShow = () => {
            if (!document.hidden) {
                resumeVisibleSession();
            }
        };

        const handlePageHide = () => {
            if (loggingOut.current || isIdle()) {
                return;
            }

            setLastActivity();

            void heartbeat();
        };

        const handlePointerActivity = () => {
            recordActivity();
        };

        const handleKeyboardActivity = () => {
            recordActivity();
        };

        const handleTouchActivity = () => {
            recordActivity();
        };

        const handleScrollActivity = () => {
            recordActivity();
        };

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

        window.addEventListener('focus', handleFocus);
        window.addEventListener('pageshow', handlePageShow);
        window.addEventListener('pagehide', handlePageHide);

        document.addEventListener('visibilitychange', handleVisibilityChange);

        const idleInterval = window.setInterval(
            checkIdle,
            IDLE_CHECK_INTERVAL_MS,
        );

        const heartbeatInterval = window.setInterval(() => {
            if (
                document.hidden ||
                loggingOut.current ||
                !isAuthenticated ||
                !idleTimeoutEnabled
            ) {
                return;
            }

            if (isIdle()) {
                logout();

                return;
            }

            setLastActivity();

            void heartbeat();
        }, heartbeatIntervalMs);

        return () => {
            window.removeEventListener('pointerdown', handlePointerActivity);

            window.removeEventListener('keydown', handleKeyboardActivity);

            window.removeEventListener('touchstart', handleTouchActivity);

            window.removeEventListener('scroll', handleScrollActivity);

            window.removeEventListener('focus', handleFocus);
            window.removeEventListener('pageshow', handlePageShow);
            window.removeEventListener('pagehide', handlePageHide);

            document.removeEventListener(
                'visibilitychange',
                handleVisibilityChange,
            );

            window.clearInterval(idleInterval);
            window.clearInterval(heartbeatInterval);
        };
    }, [
        checkIdle,
        clearLastActivity,
        getLastActivity,
        heartbeat,
        heartbeatIntervalMs,
        idleTimeoutEnabled,
        isAuthenticated,
        isIdle,
        logout,
        recordActivity,
        resumeVisibleSession,
        setLastActivity,
        timeoutMs,
    ]);

    return null;
}
