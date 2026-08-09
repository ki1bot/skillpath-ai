import { Breadcrumbs } from '@/components/breadcrumbs';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';

export function AppSidebarHeader({
    breadcrumbs = [],
}: {
    breadcrumbs?: BreadcrumbItemType[];
}) {
    return (
        <header className="flex h-16 shrink-0 items-center gap-3 border-b-2 border-sidebar-border bg-card/95 px-4 backdrop-blur md:px-6">
            <SidebarTrigger className="rounded-lg border-2 border-foreground bg-secondary text-[#171717]" />
            <Breadcrumbs breadcrumbs={breadcrumbs} />
        </header>
    );
}
