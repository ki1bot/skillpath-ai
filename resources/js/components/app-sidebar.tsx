import { Link, usePage } from '@inertiajs/react';
import {
    Activity,
    FolderKanban,
    LayoutDashboard,
    Map,
    ShieldCheck,
    Target,
    UserRound,
} from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { Auth, NavItem } from '@/types';

export function AppSidebar() {
    const { auth } = usePage().props as {
        auth: Auth;
    };

    const items: NavItem[] = [
        {
            title: 'Dasbor',
            href: '/dashboard',
            icon: LayoutDashboard,
        },
        {
            title: 'Jalur Belajar',
            href: '/roadmap',
            icon: Map,
        },
        {
            title: 'Keahlian',
            href: '/skills',
            icon: Target,
        },
        {
            title: 'Proyek',
            href: '/projects',
            icon: FolderKanban,
        },
        {
            title: 'Perkembangan',
            href: '/progress',
            icon: Activity,
        },
        {
            title: 'Profil Belajar',
            href: '/onboarding',
            icon: UserRound,
        },
    ];

    if (auth.user.role === 'admin') {
        items.push({
            title: 'Kelola Sistem',
            href: '/admin',
            icon: ShieldCheck,
        });
    }

    return (
        <Sidebar
            collapsible="offcanvas"
            variant="floating"
            className="duration-300 ease-out [&_[data-sidebar=sidebar]]:overflow-hidden [&_[data-sidebar=sidebar]]:rounded-[18px] [&_[data-sidebar=sidebar]]:border-2 [&_[data-sidebar=sidebar]]:border-sidebar-border [&_[data-sidebar=sidebar]]:bg-sidebar [&_[data-sidebar=sidebar]]:shadow-[5px_5px_0_var(--neo-shadow-color)]"
        >
            <SidebarHeader className="border-b-2 border-sidebar-border bg-sidebar p-3">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            size="lg"
                            asChild
                            className="h-auto min-h-14 rounded-[12px] border-2 border-transparent px-2 hover:border-sidebar-border hover:bg-muted"
                        >
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent className="bg-sidebar px-1 py-3">
                <NavMain items={items} />
            </SidebarContent>

            <SidebarFooter className="border-t-2 border-sidebar-border bg-sidebar p-3">
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
