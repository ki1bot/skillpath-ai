import { Link } from '@inertiajs/react';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';
import type { NavItem } from '@/types';

export function NavMain({ items = [] }: { items: NavItem[] }) {
    const { isCurrentUrl } = useCurrentUrl();

    return (
        <SidebarGroup className="px-2 py-1">
            <SidebarGroupLabel className="mb-2 px-2 text-[9px] font-black tracking-[0.2em] text-muted-foreground uppercase">
                Menu utama
            </SidebarGroupLabel>

            <SidebarMenu className="gap-2">
                {items.map((item) => (
                    <SidebarMenuItem key={item.title}>
                        <SidebarMenuButton
                            asChild
                            isActive={isCurrentUrl(item.href)}
                            tooltip={{
                                children: item.title,
                            }}
                            className="h-11 rounded-[11px] border-2 border-transparent px-3.5 font-extrabold text-sidebar-foreground hover:translate-x-[1px] hover:border-sidebar-border hover:bg-muted hover:text-sidebar-foreground data-[active=true]:border-[#171717] data-[active=true]:bg-sidebar-accent data-[active=true]:text-[#171717] data-[active=true]:shadow-[3px_3px_0_var(--neo-shadow-color)] data-[active=true]:hover:text-[#171717]"
                        >
                            <Link
                                href={item.href}
                                prefetch={['hover', 'click']}
                            >
                                {item.icon && (
                                    <item.icon className="size-[18px]" />
                                )}

                                <span>{item.title}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                ))}
            </SidebarMenu>
        </SidebarGroup>
    );
}
