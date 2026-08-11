import { Link, usePage } from '@inertiajs/react';
import {
    Activity,
    ClipboardCheck,
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
import type { NavItem } from '@/types';

export function AppSidebar() {
    const { auth } = usePage().props;
    const user = auth.user;

    if (!user) {
        return null;
    }

    const mainItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutDashboard,
        },
        {
            title: 'Profil Belajar',
            href: '/onboarding',
            icon: UserRound,
        },
        {
            title: 'Assessment',
            href: '/assessment',
            icon: ClipboardCheck,
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
    ];

    const adminItems: NavItem[] = [
        {
            title: 'Dashboard Admin',
            href: '/admin/dashboard',
            icon: LayoutDashboard,
        },
        {
            title: 'Kelola Sistem',
            href: '/admin',
            icon: ShieldCheck,
        },
    ];

    const items =
        user.role === 'admin' ? [...mainItems, ...adminItems] : mainItems;

    return (
        <Sidebar collapsible="icon" variant="sidebar">
            <SidebarHeader className="border-b-2 border-sidebar-border bg-sidebar p-3">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            size="lg"
                            asChild
                            className="h-auto min-h-14 rounded-[10px] border-2 border-transparent px-2 group-data-[collapsible=icon]:size-10! group-data-[collapsible=icon]:justify-center group-data-[collapsible=icon]:p-0! hover:border-sidebar-border hover:bg-muted"
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
