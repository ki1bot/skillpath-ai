import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';

type PublicBackLinkProps = {
    href?: string;
    label?: string;
};

export default function PublicBackLink({
    href = '/',
    label = 'Kembali ke beranda',
}: PublicBackLinkProps) {
    return (
        <Link
            href={href}
            className="inline-flex items-center gap-2 rounded-[9px] border-2 border-foreground bg-card px-3.5 py-2 text-sm font-black shadow-[2px_2px_0_var(--neo-shadow-color)] transition-transform hover:-translate-y-0.5 active:translate-x-[1px] active:translate-y-[1px] active:shadow-none"
        >
            <ArrowLeft className="size-4" />
            {label}
        </Link>
    );
}
