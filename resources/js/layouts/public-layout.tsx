import { Link } from '@inertiajs/react';
import { Menu, X } from 'lucide-react';
import { useState } from 'react';
import AppLogoIcon from '@/components/app-logo-icon';
import { Button } from '@/components/ui/button';

export default function PublicLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    const [mobileOpen, setMobileOpen] = useState(false);

    const closeMenu = () => setMobileOpen(false);

    return (
        <div className="min-h-screen overflow-x-clip">
            <header className="sticky top-0 z-40 border-b-2 border-foreground bg-background/95 backdrop-blur-md">
                <div className="neo-page flex min-h-17 items-center justify-between gap-3">
                    <Link
                        href="/"
                        className="flex min-w-0 items-center gap-3 font-extrabold tracking-tight"
                        onClick={closeMenu}
                    >
                        <span className="flex size-9 shrink-0 items-center justify-center rounded-[9px] border-2 border-foreground bg-secondary text-[#171717] shadow-[2px_2px_0_var(--neo-shadow-color)]">
                            <AppLogoIcon className="size-5" />
                        </span>

                        <span className="truncate text-base sm:text-lg">
                            SkillPath AI
                        </span>
                    </Link>

                    <nav className="hidden items-center gap-1 text-sm font-bold md:flex">
                        <Link
                            href="/karier"
                            className="rounded-[8px] border-2 border-transparent px-3 py-2 transition-colors hover:border-foreground/30 hover:bg-card"
                        >
                            Jurusan
                        </Link>

                        <Link
                            href="/tentang"
                            className="rounded-[8px] border-2 border-transparent px-3 py-2 transition-colors hover:border-foreground/30 hover:bg-card"
                        >
                            Tentang
                        </Link>
                    </nav>

                    <div className="hidden items-center gap-2 md:flex">
                        <Button asChild variant="ghost" size="sm">
                            <Link href="/login">Masuk</Link>
                        </Button>

                        <Button asChild size="sm">
                            <Link href="/register">Buat akun</Link>
                        </Button>
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
                        className="animate-in border-t-2 border-foreground bg-background px-4 py-4 duration-150 fade-in slide-in-from-top-1 md:hidden"
                    >
                        <nav className="mx-auto grid max-w-7xl gap-2.5">
                            <Link
                                href="/karier"
                                onClick={closeMenu}
                                className="rounded-[9px] border-2 border-foreground bg-card px-4 py-3 text-sm font-bold shadow-[2px_2px_0_var(--neo-shadow-color)] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none"
                            >
                                Jurusan
                            </Link>

                            <Link
                                href="/tentang"
                                onClick={closeMenu}
                                className="rounded-[9px] border-2 border-foreground bg-card px-4 py-3 text-sm font-bold shadow-[2px_2px_0_var(--neo-shadow-color)] active:translate-x-[1px] active:translate-y-[1px] active:shadow-none"
                            >
                                Tentang
                            </Link>

                            <div className="mt-1 grid grid-cols-2 gap-2.5">
                                <Button asChild variant="outline">
                                    <Link href="/login" onClick={closeMenu}>
                                        Masuk
                                    </Link>
                                </Button>

                                <Button asChild>
                                    <Link href="/register" onClick={closeMenu}>
                                        Buat akun
                                    </Link>
                                </Button>
                            </div>
                        </nav>
                    </div>
                )}
            </header>

            {children}

            <footer className="mt-16 border-t-2 border-foreground bg-foreground text-background sm:mt-20">
                <div className="neo-page flex flex-col gap-3 py-8 text-sm md:flex-row md:items-center md:justify-between">
                    <div className="font-bold">
                        <p>© {new Date().getFullYear()} SkillPath AI.</p>

                        <p>
                            Membantu kamu menentukan langkah belajar berikutnya
                            dengan lebih jelas.
                        </p>
                    </div>

                    <p className="max-w-xl text-background/65 md:text-right">
                        Gunakan hasil assesment dan perkembangan belajarmu
                        sebagai panduan untuk menentukan kemampuan yang perlu
                        dikembangkan.
                    </p>
                </div>
            </footer>
        </div>
    );
}
