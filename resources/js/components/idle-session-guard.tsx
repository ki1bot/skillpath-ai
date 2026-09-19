import { router, usePage } from '@inertiajs/react';
import { useCallback, useEffect, useRef } from 'react';

const LAST_ACTIVITY_KEY = 'skillpath:last-activity';
const TAB_ID_KEY = 'skillpath:tab-id';
const OPEN_TABS_KEY = 'skillpath:open-tabs';
const LAST_CLOSED_TAB_KEY = 'skillpath:last-closed-tab';

const HEARTBEAT_INTERVAL_MS = 4 * 60 * 1000;
const IDLE_CHECK_INTERVAL_MS = 15 * 1000;
const TAB_HEARTBEAT_INTERVAL_MS = 5 * 1000;
const MINIMUM_TAB_STALE_MS = 2 * 60 * 1000;

type IdlePageProps = {
    auth?: {
        user?: unknown | null;
    };
    idleTimeoutMinutes?: number;
};

type OpenTabs = Record<string, number>;

type ClosedTabState = {
    tabId: string;
    closedAt: number;
};

function createTabId(): string {
    if (
        typeof window.crypto !== 'undefined' &&
        typeof window.crypto.randomUUID === 'function'
    ) {
        return window.crypto.randomUUID();
    }

    return `${Date.now()}-${Math.random().toString(36).slice(2)}`;
}

function getOrCreateTabId(): string {
    const existing = window.sessionStorage.getItem(TAB_ID_KEY);

    if (existing) {
        return existing;
    }

    const tabId = createTabId();

    window.sessionStorage.setItem(TAB_ID_KEY, tabId);

    return tabId;
}

function readOpenTabs(): OpenTabs {
    try {
        const stored = window.localStorage.getItem(OPEN_TABS_KEY);

        if (!stored) {
            return {};
        }

        const parsed: unknown = JSON.parse(stored);

        if (
            parsed === null ||
            typeof parsed !== 'object' ||
            Array.isArray(parsed)
        ) {
            return {};
        }

        const tabs: OpenTabs = {};

        for (const [tabId, timestamp] of Object.entries(parsed)) {
            if (
                typeof timestamp === 'number' &&
                Number.isFinite(timestamp) &&
                timestamp > 0
            ) {
                tabs[tabId] = timestamp;
            }
        }

        return tabs;
    } catch {
        return {};
    }
}

function writeOpenTabs(tabs: OpenTabs): void {
    if (Object.keys(tabs).length === 0) {
        window.localStorage.removeItem(OPEN_TABS_KEY);

        return;
    }

    window.localStorage.setItem(OPEN_TABS_KEY, JSON.stringify(tabs));
}

function pruneOpenTabs(
    tabs: OpenTabs,
    staleAfterMs: number,
    now: number = Date.now(),
): OpenTabs {
    const activeTabs: OpenTabs = {};

    for (const [tabId, timestamp] of Object.entries(tabs)) {
        if (now - timestamp <= staleAfterMs) {
            activeTabs[tabId] = timestamp;
        }
    }

    return activeTabs;
}

function readClosedTabState(): ClosedTabState | null {
    try {
        const stored = window.localStorage.getItem(LAST_CLOSED_TAB_KEY);

        if (!stored) {
            return null;
        }

        const parsed: unknown = JSON.parse(stored);

        if (
            parsed === null ||
            typeof parsed !== 'object' ||
            Array.isArray(parsed)
        ) {
            return null;
        }

        const state = parsed as Partial<ClosedTabState>;

        if (
            typeof state.tabId !== 'string' ||
            typeof state.closedAt !== 'number' ||
            !Number.isFinite(state.closedAt)
        ) {
            return null;
        }

        return {
            tabId: state.tabId,
            closedAt: state.closedAt,
        };
    } catch {
        return null;
    }
}

function writeClosedTabState(tabId: string): void {
    const state: ClosedTabState = {
        tabId,
        closedAt: Date.now(),
    };

    window.localStorage.setItem(LAST_CLOSED_TAB_KEY, JSON.stringify(state));
}

function clearClosedTabState(): void {
    window.localStorage.removeItem(LAST_CLOSED_TAB_KEY);
}

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

    const tabStaleAfterMs = Math.max(
        timeoutMs ?? 10 * 60 * 1000,
        MINIMUM_TAB_STALE_MS,
    );

    const lastHeartbeatAt = useRef(0);
    const loggingOut = useRef(false);

    const clearClientSessionState = useCallback(() => {
        window.localStorage.removeItem(LAST_ACTIVITY_KEY);
        window.localStorage.removeItem(OPEN_TABS_KEY);
        window.localStorage.removeItem(LAST_CLOSED_TAB_KEY);
        window.sessionStorage.removeItem(TAB_ID_KEY);
    }, []);

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

    const registerTab = useCallback(
        (tabId: string) => {
            const now = Date.now();

            const tabs = pruneOpenTabs(readOpenTabs(), tabStaleAfterMs, now);

            tabs[tabId] = now;

            writeOpenTabs(tabs);
        },
        [tabStaleAfterMs],
    );

    const unregisterTab = useCallback(
        (tabId: string): number => {
            const tabs = pruneOpenTabs(readOpenTabs(), tabStaleAfterMs);

            delete tabs[tabId];

            writeOpenTabs(tabs);

            return Object.keys(tabs).length;
        },
        [tabStaleAfterMs],
    );

    const logout = useCallback(() => {
        if (loggingOut.current) {
            return;
        }

        loggingOut.current = true;

        clearClientSessionState();

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
    }, [clearClientSessionState]);

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
                clearClientSessionState();

                window.location.assign('/login');

                return;
            }

            if (response.ok) {
                lastHeartbeatAt.current = Date.now();
            }
        } catch {
            return;
        }
    }, [clearClientSessionState, idleTimeoutEnabled, isAuthenticated, isIdle]);

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
        if (!isAuthenticated || loggingOut.current) {
            return;
        }

        if (idleTimeoutEnabled && isIdle()) {
            logout();

            return;
        }

        if (idleTimeoutEnabled) {
            setLastActivity();

            void heartbeat();
        }
    }, [
        heartbeat,
        idleTimeoutEnabled,
        isAuthenticated,
        isIdle,
        logout,
        setLastActivity,
    ]);

    useEffect(() => {
        if (!isAuthenticated) {
            clearClientSessionState();

            loggingOut.current = false;
            lastHeartbeatAt.current = 0;

            return;
        }

        loggingOut.current = false;

        const tabId = getOrCreateTabId();

        const storedTabs = pruneOpenTabs(readOpenTabs(), tabStaleAfterMs);

        const closedTabState = readClosedTabState();

        const otherOpenTabs = Object.keys(storedTabs).filter(
            (openTabId) => openTabId !== tabId,
        );

        if (
            closedTabState !== null &&
            closedTabState.tabId !== tabId &&
            otherOpenTabs.length === 0
        ) {
            logout();

            return;
        }

        if (closedTabState !== null && closedTabState.tabId === tabId) {
            clearClosedTabState();
        }

        if (otherOpenTabs.length > 0) {
            clearClosedTabState();
        }

        registerTab(tabId);

        if (idleTimeoutEnabled) {
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
        }

        const handleVisibilityChange = () => {
            registerTab(tabId);

            if (document.hidden) {
                if (idleTimeoutEnabled && !isIdle() && !loggingOut.current) {
                    setLastActivity();

                    void heartbeat();
                }

                return;
            }

            resumeVisibleSession();
        };

        const handleFocus = () => {
            registerTab(tabId);

            if (!document.hidden) {
                resumeVisibleSession();
            }
        };

        const handlePageShow = () => {
            const currentTabId = getOrCreateTabId();

            const closedState = readClosedTabState();

            if (closedState !== null && closedState.tabId === currentTabId) {
                clearClosedTabState();
            }

            registerTab(currentTabId);

            if (!document.hidden) {
                resumeVisibleSession();
            }
        };

        const handlePageHide = () => {
            const remainingTabs = unregisterTab(tabId);

            if (remainingTabs === 0) {
                writeClosedTabState(tabId);
            }

            if (idleTimeoutEnabled && !loggingOut.current && !isIdle()) {
                setLastActivity();
            }
        };

        const handlePointerActivity = () => {
            registerTab(tabId);
            recordActivity();
        };

        const handleKeyboardActivity = () => {
            registerTab(tabId);
            recordActivity();
        };

        const handleTouchActivity = () => {
            registerTab(tabId);
            recordActivity();
        };

        const handleScrollActivity = () => {
            registerTab(tabId);
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

        const tabHeartbeatInterval = window.setInterval(() => {
            if (loggingOut.current) {
                return;
            }

            registerTab(tabId);
        }, TAB_HEARTBEAT_INTERVAL_MS);

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
            window.clearInterval(tabHeartbeatInterval);
        };
    }, [
        checkIdle,
        clearClientSessionState,
        getLastActivity,
        heartbeat,
        heartbeatIntervalMs,
        idleTimeoutEnabled,
        isAuthenticated,
        isIdle,
        logout,
        recordActivity,
        registerTab,
        resumeVisibleSession,
        setLastActivity,
        tabStaleAfterMs,
        timeoutMs,
        unregisterTab,
    ]);

    return null;
}
