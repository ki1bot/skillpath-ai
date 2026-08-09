import { Breadcrumbs } from '@/components/breadcrumbs';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';

export function AppSidebarHeader({
    breadcrumbs = [],
}: {
    breadcrumbs?: BreadcrumbItemType[];
}) {
    return (
        <header className="sticky top-0 z-30 flex min-h-16 shrink-0 items-center gap-3 border-b-2 border-sidebar-border bg-background/95 px-4 backdrop-blur-md sm:px-5 md:px-6">
            <SidebarTrigger className="size-9 rounded-[9px] border-2 border-[#171717] bg-secondary text-[#171717] shadow-[2px_2px_0_var(--neo-shadow-color)] hover:bg-secondary" />

            <div className="min-w-0 flex-1">
                <Breadcrumbs breadcrumbs={breadcrumbs} />
            </div>
        </header>
    );
}
