import { Link } from '@inertiajs/react';
import AppLogoIcon from '@/components/app-logo-icon';
import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <main className="min-h-svh lg:grid lg:grid-cols-[minmax(380px,0.9fr)_minmax(0,1.1fr)]">
            <section className="relative hidden min-h-svh overflow-hidden border-r-2 border-foreground bg-secondary text-secondary-foreground lg:flex lg:flex-col lg:justify-between">
                <div className="absolute top-24 -right-16 size-56 rotate-12 border-2 border-[#171717] bg-[var(--neo-blue)] shadow-[10px_10px_0_#171717]" />

                <div className="absolute bottom-24 -left-12 size-36 -rotate-12 border-2 border-[#171717] bg-[var(--neo-yellow)] shadow-[8px_8px_0_#171717]" />

                <div className="relative z-10 flex min-h-svh flex-col justify-between p-8 xl:p-12">
                    <Link
                        href="/"
                        className="flex w-fit items-center gap-3 text-xl font-black tracking-tight"
                    >
                        <span className="flex size-12 items-center justify-center rounded-xl border-2 border-[#171717] bg-[#fffdf7] shadow-[4px_4px_0_#171717]">
                            <AppLogoIcon className="size-7 text-[#171717]" />
                        </span>

                        <span>SkillPath AI</span>
                    </Link>

                    <div className="max-w-xl">
                        <span className="mb-5 inline-flex rounded-full border-2 border-[#171717] bg-[#fffdf7] px-4 py-2 text-xs font-black tracking-[0.15em] text-[#171717] uppercase shadow-[3px_3px_0_#171717]">
                            Roadmap bukan generik
                        </span>

                        <h2 className="max-w-xl text-[clamp(2.8rem,5vw,5.5rem)] leading-[0.9] font-black tracking-[-0.065em]">
                            Tahu apa yang kurang. Belajar yang memang perlu.
                        </h2>

                        <p className="mt-6 max-w-lg text-base leading-7 font-semibold xl:text-lg">
                            SkillPath membandingkan kemampuan Anda dengan
                            kebutuhan karier, kemudian membangun urutan belajar
                            berdasarkan data skill dan prasyarat.
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-2 text-xs font-black uppercase">
                        <span className="border-2 border-[#171717] bg-[#fffdf7] px-3 py-2 text-[#171717]">
                            Asesmen
                        </span>
                        <span className="border-2 border-[#171717] bg-[#fffdf7] px-3 py-2 text-[#171717]">
                            Bandingkan
                        </span>
                        <span className="border-2 border-[#171717] bg-[#fffdf7] px-3 py-2 text-[#171717]">
                            Rekomendasi
                        </span>
                        <span className="border-2 border-[#171717] bg-[#fffdf7] px-3 py-2 text-[#171717]">
                            Belajar
                        </span>
                        <span className="border-2 border-[#171717] bg-[#fffdf7] px-3 py-2 text-[#171717]">
                            Bangun
                        </span>
                        <span className="border-2 border-[#171717] bg-[#fffdf7] px-3 py-2 text-[#171717]">
                            Evaluasi
                        </span>
                    </div>
                </div>
            </section>

            <section className="flex min-h-svh items-center justify-center px-4 py-8 sm:px-6 sm:py-12 lg:px-10 xl:px-16">
                <div className="w-full max-w-lg">
                    <Link
                        href="/"
                        className="mb-8 flex w-fit items-center gap-3 font-black lg:hidden"
                    >
                        <span className="flex size-11 items-center justify-center rounded-xl border-2 border-foreground bg-secondary shadow-[3px_3px_0_var(--foreground)]">
                            <AppLogoIcon className="size-6 text-[#171717]" />
                        </span>

                        <span className="text-lg">SkillPath AI</span>
                    </Link>

                    <div className="neo-card p-5 sm:p-8">
                        <header className="mb-7">
                            <span className="neo-label mb-4">SkillPath AI</span>

                            <h1 className="neo-heading text-3xl sm:text-4xl">
                                {title}
                            </h1>

                            <p className="mt-3 max-w-md text-sm leading-6 font-medium text-muted-foreground sm:text-base">
                                {description}
                            </p>
                        </header>

                        {children}
                    </div>
                </div>
            </section>
        </main>
    );
}
