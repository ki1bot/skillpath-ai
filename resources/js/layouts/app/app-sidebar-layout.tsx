import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import { FlashMessage } from '@/components/flash-message';
import { TooltipProvider } from '@/components/ui/tooltip';
import type { AppLayoutProps } from '@/types';

export default function AppSidebarLayout({
    children,
    breadcrumbs = [],
}: AppLayoutProps) {
    return (
        <TooltipProvider delayDuration={0}>
            <AppShell variant="sidebar">
                <AppSidebar />

                <AppContent
                    variant="sidebar"
                    className="overflow-x-clip bg-background"
                >
                    <AppSidebarHeader breadcrumbs={breadcrumbs} />
                    <FlashMessage />
                    {children}
                </AppContent>
            </AppShell>
        </TooltipProvider>
    );
}
