import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { edit as editProfile } from '@/routes/profile';

type BackLinkConfig = {
    href: string;
    label: string;
};

const backLinks: Record<string, BackLinkConfig> = {
    '/login': {
        href: '/',
        label: 'Kembali ke beranda',
    },
    '/register': {
        href: '/',
        label: 'Kembali ke beranda',
    },
    '/forgot-password': {
        href: '/',
        label: 'Kembali ke beranda',
    },
};

export function AuthBackLink() {
    const { url } = usePage();

    const path = url.split('?')[0];

    const config =
        path === '/user/confirm-password'
            ? {
                  href: editProfile().url,
                  label: 'Kembali ke profil',
              }
            : backLinks[path];

    if (!config) {
        return null;
    }

    return (
        <Link
            href={config.href}
            className="mb-5 inline-flex w-fit items-center gap-2 rounded-[9px] border-2 border-transparent px-1 py-1 text-sm font-bold text-muted-foreground transition-colors hover:text-foreground focus-visible:border-foreground"
        >
            <ArrowLeft className="size-4" />
            {config.label}
        </Link>
    );
}
