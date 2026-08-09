import { usePage } from '@inertiajs/react';
import { ChevronsUpDown } from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { UserInfo } from '@/components/user-info';
import { UserMenuContent } from '@/components/user-menu-content';
import { useIsMobile } from '@/hooks/use-mobile';

export function NavUser() {
    const { auth } = usePage().props;
    const isMobile = useIsMobile();

    if (!auth.user) {
        return null;
    }

    return (
        <SidebarMenu>
            <SidebarMenuItem>
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <SidebarMenuButton
                            size="lg"
                            className="h-auto min-h-14 rounded-[12px] border-2 border-transparent px-2.5 text-sidebar-foreground hover:border-sidebar-border hover:bg-muted data-[state=open]:border-[#171717] data-[state=open]:bg-sidebar-accent data-[state=open]:text-[#171717]"
                            data-test="sidebar-menu-button"
                        >
                            <UserInfo user={auth.user} />

                            <ChevronsUpDown className="ml-auto size-4 shrink-0" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>

                    <DropdownMenuContent
                        className="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-[12px] border-2 border-foreground bg-popover shadow-[4px_4px_0_var(--neo-shadow-color)]"
                        align="end"
                        side={isMobile ? 'bottom' : 'right'}
                    >
                        <UserMenuContent user={auth.user} />
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    );
}
