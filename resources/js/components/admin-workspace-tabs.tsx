import { Link, usePage } from '@inertiajs/react';
import { ClipboardList, FolderKanban } from 'lucide-react';

export function AdminWorkspaceTabs() {
    const { url } = usePage();

    const [pathname, queryString = ''] = url.split('?');
    const normalizedPath = pathname.replace(/\/+$/, '');
    const searchParams = new URLSearchParams(queryString);

    const isSubmissions = normalizedPath === '/admin/submissions';

    if (!isSubmissions) {
        return null;
    }

    const isProject = searchParams.get('type') === 'project';

    const tabs = [
        {
            title: 'Tugas Pembelajaran',
            href: '/admin/submissions',
            active: !isProject,
            icon: ClipboardList,
        },
        {
            title: 'Penilaian Proyek',
            href: '/admin/submissions?type=project',
            active: isProject,
            icon: FolderKanban,
        },
    ];

    return (
        <nav
            aria-label="Navigasi Pengumpulan dan Penilaian"
            className="neo-page pt-6"
        >
            <div className="flex flex-wrap gap-3 border-b-2 border-foreground pb-4">
                {tabs.map((tab) => {
                    const Icon = tab.icon;

                    return (
                        <Link
                            key={tab.href}
                            href={tab.href}
                            preserveScroll={false}
                            aria-current={tab.active ? 'page' : undefined}
                            className={`inline-flex min-h-11 items-center gap-2 rounded-[10px] border-2 border-foreground px-4 py-2 text-sm font-black transition-colors ${
                                tab.active
                                    ? 'bg-foreground text-background'
                                    : 'bg-card text-foreground hover:bg-muted'
                            }`}
                        >
                            <Icon className="size-4 shrink-0" />

                            <span>{tab.title}</span>
                        </Link>
                    );
                })}
            </div>
        </nav>
    );
}
