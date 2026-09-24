import { Link, usePage } from '@inertiajs/react';
import { Menu, X } from 'lucide-react';
import { useState } from 'react';
import AppLogoIcon from '@/components/app-logo-icon';
import PublicProjectChat from '@/components/public-project-chat';
import { Button } from '@/components/ui/button';
import type { Auth } from '@/types';

export default function PublicLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    const [mobileOpen, setMobileOpen] = useState(false);

    const page = usePage();

    const { auth } = page.props as {
        auth: Auth;
    };

    const closeMenu = () => setMobileOpen(false);

    const currentPath = page.url.split('?')[0];

    const showPublicChat = currentPath === '/' && auth.user === null;

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

            <footer className="mt-16 border-t-2 border-foreground/20 bg-card sm:mt-20">
                <div className="neo-page py-10 lg:py-12">
                    <div className="grid gap-10 md:grid-cols-[1.4fr_0.8fr_1fr]">
                        <div className="max-w-md">
                            <Link
                                href="/"
                                className="inline-flex items-center gap-3"
                            >
                                <span className="flex size-10 items-center justify-center rounded-[9px] border-2 border-foreground bg-secondary text-[#171717] shadow-[2px_2px_0_var(--neo-shadow-color)]">
                                    <AppLogoIcon className="size-5" />
                                </span>

                                <div>
                                    <p className="text-lg font-black tracking-tight">
                                        SkillPath AI
                                    </p>

                                    <p className="text-xs font-bold text-muted-foreground">
                                        Belajar lebih terarah.
                                    </p>
                                </div>
                            </Link>

                            <p className="mt-5 text-sm leading-6 font-medium text-muted-foreground">
                                SkillPath membantu mahasiswa memahami kemampuan,
                                melihat bagian yang masih perlu dikembangkan,
                                dan menentukan langkah belajar berikutnya.
                            </p>
                        </div>

                        <div>
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Navigasi
                            </p>

                            <nav className="mt-4 flex flex-col items-start gap-3 text-sm font-bold">
                                <Link
                                    href="/"
                                    className="underline-offset-4 transition-opacity hover:underline"
                                >
                                    Beranda
                                </Link>

                                <Link
                                    href="/karier"
                                    className="underline-offset-4 transition-opacity hover:underline"
                                >
                                    Jurusan
                                </Link>

                                <Link
                                    href="/tentang"
                                    className="underline-offset-4 transition-opacity hover:underline"
                                >
                                    Tentang
                                </Link>
                            </nav>
                        </div>

                        <div>
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Informasi
                            </p>

                            <nav className="mt-4 flex flex-col items-start gap-3 text-sm font-bold">
                                <Link
                                    href="/privacy-policy"
                                    className="underline-offset-4 transition-opacity hover:underline"
                                >
                                    Kebijakan Privasi
                                </Link>

                                <Link
                                    href="/terms"
                                    className="underline-offset-4 transition-opacity hover:underline"
                                >
                                    Ketentuan Layanan
                                </Link>

                                <Link
                                    href="/data-deletion"
                                    className="underline-offset-4 transition-opacity hover:underline"
                                >
                                    Penghapusan Data
                                </Link>
                            </nav>
                        </div>
                    </div>

                    <div className="mt-10 flex flex-col gap-2 border-t-2 border-foreground/10 pt-5 text-xs font-semibold text-muted-foreground sm:flex-row sm:items-center sm:justify-between">
                        <p>
                            © {new Date().getFullYear()} SkillPath AI. Semua hak
                            dilindungi.
                        </p>

                        <p>
                            SkillPath AI merupakan alat bantu pembelajaran,
                            bukan penilaian akademik resmi.
                        </p>
                    </div>
                </div>
            </footer>

            {showPublicChat && <PublicProjectChat />}
        </div>
    );
}
