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

    const closeMenu = () => setMobileOpen(false);

    return (
        <div className="min-h-screen overflow-x-hidden">
            <header className="sticky top-0 z-40 border-b-2 border-foreground bg-background/95 backdrop-blur-md">
                <div className="neo-page flex min-h-18 items-center justify-between gap-3">
                    <Link
                        href="/"
                        className="flex min-w-0 items-center gap-3 font-black tracking-tight"
                        onClick={closeMenu}
                    >
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-[11px] border-2 border-foreground bg-secondary text-[#171717] shadow-[3px_3px_0_var(--neo-shadow-color)]">
                            <AppLogoIcon className="size-6" />
                        </span>

                        <span className="truncate text-base sm:text-lg">
                            SkillPath AI
                        </span>
                    </Link>

                    <nav className="hidden items-center gap-2 text-sm font-black md:flex">
                        <Link
                            href="/karier"
                            className="rounded-[9px] border-2 border-transparent px-3 py-2 transition-colors hover:border-foreground hover:bg-card"
                        >
                            Jalur Karier
                        </Link>

                        <Link
                            href="/tentang"
                            className="rounded-[9px] border-2 border-transparent px-3 py-2 transition-colors hover:border-foreground hover:bg-card"
                        >
                            Tentang
                        </Link>
                    </nav>

                    <div className="hidden items-center gap-3 md:flex">
                        {auth?.user ? (
                            <Button asChild size="sm">
                                <Link href="/dashboard">
                                    Dashboard
                                    <ArrowRight />
                                </Link>
                            </Button>
                        ) : (
                            <>
                                <Button asChild variant="outline" size="sm">
                                    <Link href="/login">Masuk</Link>
                                </Button>

                                <Button asChild size="sm">
                                    <Link href="/register">
                                        Buat akun
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
                        className="md:hidden"
                        onClick={() => setMobileOpen((value) => !value)}
                        aria-expanded={mobileOpen}
                        aria-controls="public-mobile-menu"
                        aria-label={
                            mobileOpen
                                ? 'Tutup menu navigasi'
                                : 'Buka menu navigasi'
                        }
                    >
                        {mobileOpen ? <X /> : <Menu />}
                    </Button>
                </div>

                {mobileOpen && (
                    <div
                        id="public-mobile-menu"
                        className="animate-in border-t-2 border-foreground bg-background px-4 py-4 duration-200 fade-in slide-in-from-top-2 md:hidden"
                    >
                        <nav className="mx-auto grid max-w-7xl gap-3">
                            <Link
                                href="/karier"
                                onClick={closeMenu}
                                className="rounded-[11px] border-2 border-foreground bg-card px-4 py-3 text-sm font-black shadow-[3px_3px_0_var(--neo-shadow-color)] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none"
                            >
                                Jalur Karier
                            </Link>

                            <Link
                                href="/tentang"
                                onClick={closeMenu}
                                className="rounded-[11px] border-2 border-foreground bg-card px-4 py-3 text-sm font-black shadow-[3px_3px_0_var(--neo-shadow-color)] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none"
                            >
                                Tentang
                            </Link>

                            {auth?.user ? (
                                <Button asChild className="mt-1 w-full">
                                    <Link href="/dashboard" onClick={closeMenu}>
                                        Dashboard
                                        <ArrowRight />
                                    </Link>
                                </Button>
                            ) : (
                                <div className="mt-1 grid grid-cols-2 gap-3">
                                    <Button asChild variant="outline">
                                        <Link href="/login" onClick={closeMenu}>
                                            Masuk
                                        </Link>
                                    </Button>

                                    <Button asChild>
                                        <Link
                                            href="/register"
                                            onClick={closeMenu}
                                        >
                                            Buat akun
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
                <div className="neo-page flex flex-col gap-4 py-8 text-sm md:flex-row md:items-center md:justify-between">
                    <p className="font-bold">
                        SkillPath AI — belajar lebih terarah, satu langkah pada
                        satu waktu.
                    </p>

                    <p className="max-w-xl text-background/70 md:text-right">
                        Skor kesiapan membantu melihat perkembangan, bukan
                        menjamin hasil rekrutmen.
                    </p>
                </div>
            </footer>
        </div>
    );
}
