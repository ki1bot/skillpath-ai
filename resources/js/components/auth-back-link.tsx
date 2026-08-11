import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

const supportedPaths = new Set(['/login', '/register', '/forgot-password']);

export function AuthBackLink() {
    const { url } = usePage();

    const path = url.split('?')[0];

    if (!supportedPaths.has(path)) {
        return null;
    }

    return (
        <Link
            href="/"
            className="mb-5 inline-flex w-fit items-center gap-2 rounded-[9px] border-2 border-transparent px-1 py-1 text-sm font-bold text-muted-foreground transition-colors hover:text-foreground focus-visible:border-foreground"
        >
            <ArrowLeft className="size-4" />
            Kembali ke beranda
        </Link>
    );
}
