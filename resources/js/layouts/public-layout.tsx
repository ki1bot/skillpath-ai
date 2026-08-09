import { Link, usePage } from '@inertiajs/react';
import { ArrowRight, Menu, X } from 'lucide-react';
import { useState } from 'react';
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
    const [mobileOpen, setMobileOpen] = useState(false);

    return (
        <div className="min-h-screen overflow-x-hidden">
            <header className="sticky top-0 z-40 border-b-2 border-foreground bg-background/95 backdrop-blur">
                <div className="mx-auto flex min-h-18 max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <Link
                        href="/"
                        className="flex min-w-0 items-center gap-3 font-black tracking-tight"
                        onClick={() => setMobileOpen(false)}
                    >
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-xl border-2 border-foreground bg-secondary text-[#171717] shadow-[3px_3px_0_var(--foreground)]">
                            <AppLogoIcon className="size-6" />
                        </span>

                        <span className="truncate text-base sm:text-lg">
                            SkillPath AI
                        </span>
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

                    <div className="hidden items-center gap-2 sm:flex">
                        {auth?.user ? (
                            <Button asChild size="sm">
                                <Link href="/dashboard">
                                    Dasbor
                                    <ArrowRight />
                                </Link>
                            </Button>
                        ) : (
                            <>
                                <Button asChild variant="ghost" size="sm">
                                    <Link href="/login">Masuk</Link>
                                </Button>

                                <Button asChild size="sm">
                                    <Link href="/register">
                                        Daftar
                                        <ArrowRight />
                                    </Link>
                                </Button>
                            </>
                        )}
                    </div>

                    <Button
                        type="button"
                        variant="outline"
                        size="icon-sm"
                        className="sm:hidden"
                        onClick={() => setMobileOpen((value) => !value)}
                        aria-label={mobileOpen ? 'Tutup menu' : 'Buka menu'}
                    >
                        {mobileOpen ? <X /> : <Menu />}
                    </Button>
                </div>

                {mobileOpen && (
                    <div className="border-t-2 border-foreground bg-background px-4 py-4 sm:hidden">
                        <nav className="grid gap-2">
                            <Link
                                href="/karier"
                                onClick={() => setMobileOpen(false)}
                                className="rounded-xl border-2 border-foreground bg-card px-4 py-3 text-sm font-black shadow-[3px_3px_0_var(--foreground)]"
                            >
                                Jalur Karier
                            </Link>

                            <Link
                                href="/tentang"
                                onClick={() => setMobileOpen(false)}
                                className="rounded-xl border-2 border-foreground bg-card px-4 py-3 text-sm font-black shadow-[3px_3px_0_var(--foreground)]"
                            >
                                Tentang
                            </Link>

                            {auth?.user ? (
                                <Button asChild className="mt-2 w-full">
                                    <Link
                                        href="/dashboard"
                                        onClick={() => setMobileOpen(false)}
                                    >
                                        Dasbor
                                        <ArrowRight />
                                    </Link>
                                </Button>
                            ) : (
                                <div className="mt-2 grid grid-cols-2 gap-3">
                                    <Button asChild variant="outline">
                                        <Link
                                            href="/login"
                                            onClick={() => setMobileOpen(false)}
                                        >
                                            Masuk
                                        </Link>
                                    </Button>

                                    <Button asChild>
                                        <Link
                                            href="/register"
                                            onClick={() => setMobileOpen(false)}
                                        >
                                            Daftar
                                        </Link>
                                    </Button>
                                </div>
                            )}
                        </nav>
                    </div>
                )}
            </header>

            {children}

            <footer className="mt-16 border-t-2 border-foreground bg-foreground text-background sm:mt-20">
                <div className="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                    <p className="font-bold">
                        SkillPath AI — belajar terarah dan dibuktikan melalui
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
