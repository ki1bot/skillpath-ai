import { Link } from '@inertiajs/react';
import AppLogoIcon from '@/components/app-logo-icon';
import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <div className="grid min-h-svh lg:grid-cols-[0.9fr_1.1fr]">
            <div className="hidden border-r-2 border-foreground bg-secondary p-10 text-[#171717] lg:flex lg:flex-col lg:justify-between">
                <Link
                    href="/"
                    className="flex items-center gap-3 text-xl font-black tracking-tight"
                >
                    <span className="flex size-11 items-center justify-center rounded-xl border-2 border-[#171717] bg-[#fffdf7] shadow-[4px_4px_0_#171717]">
                        <AppLogoIcon className="size-7" />
                    </span>
                    SkillPath AI
                </Link>

                <div className="max-w-lg">
                    <span className="mb-4 inline-flex rounded-full border-2 border-[#171717] bg-[#fffdf7] px-3 py-1 text-xs font-black tracking-[0.15em] uppercase">
                        No generic roadmap
                    </span>

                    <h2 className="text-5xl leading-[0.95] font-black tracking-[-0.05em]">
                        Tahu apa yang kurang. Belajar yang memang perlu.
                    </h2>

                    <p className="mt-5 max-w-md text-base leading-relaxed font-semibold">
                        SkillPath membandingkan kemampuan Anda dengan kebutuhan
                        karier, lalu membangun urutan belajar dari data skill
                        dan prasyarat.
                    </p>
                </div>

                <p className="text-sm font-bold">
                    Assess → Compare → Recommend → Learn → Build → Evaluate
                </p>
            </div>

            <div className="flex items-center justify-center p-5 sm:p-8">
                <div className="w-full max-w-md">
                    <Link
                        href="/"
                        className="mb-8 flex items-center gap-3 font-black lg:hidden"
                    >
                        <span className="flex size-10 items-center justify-center rounded-xl border-2 border-foreground bg-secondary shadow-[3px_3px_0_var(--foreground)]">
                            <AppLogoIcon className="size-6 text-[#171717]" />
                        </span>
                        SkillPath AI
                    </Link>

                    <div className="neo-card p-6 sm:p-8">
                        <div className="mb-7">
                            <h1 className="text-3xl font-black tracking-[-0.04em]">
                                {title}
                            </h1>
                            <p className="mt-2 text-sm leading-relaxed font-medium text-muted-foreground">
                                {description}
                            </p>
                        </div>

                        {children}
                    </div>
                </div>
            </div>
        </div>
    );
}
