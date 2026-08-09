import { Link } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { cn, toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import type { NavItem } from '@/types';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profil',
        href: edit(),
        icon: null,
    },
    {
        title: 'Keamanan',
        href: editSecurity(),
        icon: null,
    },
    {
        title: 'Tampilan',
        href: editAppearance(),
        icon: null,
    },
];

export default function SettingsLayout({ children }: PropsWithChildren) {
    const { isCurrentOrParentUrl } = useCurrentUrl();

    return (
        <div className="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <Heading
                title="Pengaturan"
                description="Kelola profil, keamanan akun, dan tampilan SkillPath AI."
            />

            <div className="mt-7 grid gap-6 lg:grid-cols-[220px_minmax(0,1fr)] lg:gap-8">
                <aside className="min-w-0">
                    <nav
                        className="grid grid-cols-3 gap-2 lg:grid-cols-1"
                        aria-label="Pengaturan akun"
                    >
                        {sidebarNavItems.map((item, index) => (
                            <Button
                                key={`${toUrl(item.href)}-${index}`}
                                size="sm"
                                variant={
                                    isCurrentOrParentUrl(item.href)
                                        ? 'secondary'
                                        : 'outline'
                                }
                                asChild
                                className={cn(
                                    'min-w-0 justify-center lg:justify-start',
                                )}
                            >
                                <Link href={item.href}>
                                    <span className="truncate">
                                        {item.title}
                                    </span>
                                </Link>
                            </Button>
                        ))}
                    </nav>
                </aside>

                <section className="neo-card min-w-0 p-5 sm:p-7 lg:p-8">
                    <div className="max-w-2xl space-y-10">{children}</div>
                </section>
            </div>
        </div>
    );
}
