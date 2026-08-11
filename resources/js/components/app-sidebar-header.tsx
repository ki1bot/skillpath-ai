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
            <SidebarTrigger className="size-10 rounded-[10px] border-2 border-foreground bg-card shadow-[2px_2px_0_var(--neo-shadow-color)] transition-[transform,box-shadow,background-color] hover:bg-muted active:translate-x-[1px] active:translate-y-[1px] active:shadow-none" />

            <div className="min-w-0 flex-1">
                {breadcrumbs.length > 0 ? (
                    <Breadcrumbs breadcrumbs={breadcrumbs} />
                ) : (
                    <p className="truncate text-sm font-extrabold tracking-tight">
                        SkillPath AI
                    </p>
                )}
            </div>
        </header>
    );
}
