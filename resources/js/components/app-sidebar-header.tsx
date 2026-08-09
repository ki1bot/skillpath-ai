import { Breadcrumbs } from '@/components/breadcrumbs';
import { SidebarBookToggle } from '@/components/sidebar-book-toggle';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';

export function AppSidebarHeader({
    breadcrumbs = [],
}: {
    breadcrumbs?: BreadcrumbItemType[];
}) {
    return (
        <header className="sticky top-0 z-30 flex min-h-16 shrink-0 items-center gap-3 border-b-2 border-sidebar-border bg-background/95 px-4 backdrop-blur-md sm:px-5 md:px-6">
            <SidebarBookToggle />

            <div className="min-w-0 flex-1">
                {breadcrumbs.length > 0 ? (
                    <Breadcrumbs breadcrumbs={breadcrumbs} />
                ) : (
                    <p className="truncate text-sm font-black">SkillPath AI</p>
                )}
            </div>
        </header>
    );
}
