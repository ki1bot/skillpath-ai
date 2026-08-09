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
    const { auth } = usePage().props as { auth: Auth };

    const items: NavItem[] = [
        {
            title: 'Dasbor',
            href: '/dashboard',
            icon: LayoutDashboard,
        },
        {
            title: 'Roadmap',
            href: '/roadmap',
            icon: Map,
        },
        {
            title: 'Skill',
            href: '/skills',
            icon: Target,
        },
        {
            title: 'Proyek',
            href: '/projects',
            icon: FolderKanban,
        },
        {
            title: 'Progres',
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
            title: 'Administrator',
            href: '/admin',
            icon: ShieldCheck,
        });
    }

    return (
        <Sidebar
            collapsible="icon"
            variant="inset"
            className="border-r-2 border-sidebar-border"
        >
            <SidebarHeader className="border-b-2 border-sidebar-border p-3">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent className="py-3">
                <NavMain items={items} />
            </SidebarContent>

            <SidebarFooter className="border-t-2 border-sidebar-border p-3">
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
