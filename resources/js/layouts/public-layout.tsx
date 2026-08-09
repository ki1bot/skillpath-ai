import { Link, usePage } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import { Button } from '@/components/ui/button';

type PublicProps = {
    auth: {
        user?: {
            id: number;
        } | null;
    };
};

export default function PublicLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    const { auth } = usePage().props as PublicProps;

    return (
        <div className="min-h-screen">
            <header className="sticky top-0 z-40 border-b-2 border-foreground bg-background/95 backdrop-blur">
                <div className="mx-auto flex h-18 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
                    <Link
                        href="/"
                        className="flex items-center gap-3 font-black tracking-tight"
                    >
                        <span className="flex size-10 items-center justify-center rounded-xl border-2 border-foreground bg-secondary text-[#171717] shadow-[3px_3px_0_var(--foreground)]">
                            <AppLogoIcon className="size-6" />
                        </span>
                        <span className="text-lg">SkillPath AI</span>
                    </Link>

                    <nav className="hidden items-center gap-6 text-sm font-bold md:flex">
                        <Link
                            href="/karier"
                            className="hover:underline hover:decoration-2 hover:underline-offset-4"
                        >
                            Jalur Karier
                        </Link>
                        <Link
                            href="/tentang"
                            className="hover:underline hover:decoration-2 hover:underline-offset-4"
                        >
                            Tentang
                        </Link>
                    </nav>

                    <div className="flex items-center gap-2">
                        {auth?.user ? (
                            <Button asChild size="sm">
                                <Link href="/dashboard">
                                    Dashboard
                                    <ArrowRight />
                                </Link>
                            </Button>
                        ) : (
                            <>
                                <Button
                                    asChild
                                    variant="ghost"
                                    size="sm"
                                    className="hidden sm:inline-flex"
                                >
                                    <Link href="/login">Masuk</Link>
                                </Button>

                                <Button asChild size="sm">
                                    <Link href="/register">
                                        Mulai gratis
                                        <ArrowRight />
                                    </Link>
                                </Button>
                            </>
                        )}
                    </div>
                </div>
            </header>

            {children}

            <footer className="mt-20 border-t-2 border-foreground bg-foreground text-background">
                <div className="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                    <p className="font-bold">
                        SkillPath AI — belajar terarah, dibuktikan lewat
                        progres.
                    </p>
                    <p className="text-background/70">
                        Indikator kesiapan bukan jaminan diterima bekerja.
                    </p>
                </div>
            </footer>
        </div>
    );
}
