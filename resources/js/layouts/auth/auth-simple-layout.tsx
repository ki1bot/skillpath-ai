import { Link } from '@inertiajs/react';
import { Check } from 'lucide-react';
import AppLogoIcon from '@/components/app-logo-icon';
import { AuthBackLink } from '@/components/auth-back-link';
import type { AuthLayoutProps } from '@/types';

const benefits = [
    'Pilih jurusan yang sedang kamu jalani',
    'Lihat kemampuan yang sudah kuat dan yang masih perlu dikembangkan',
    'Ikuti jalur belajar berdasarkan hasil Assesmentmu',
];

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <main className="min-h-svh bg-background lg:grid lg:grid-cols-[minmax(360px,0.82fr)_minmax(0,1.18fr)]">
            <section className="relative hidden min-h-svh overflow-hidden border-r-2 border-foreground bg-foreground text-background lg:flex lg:flex-col">
                <div className="flex min-h-svh flex-col justify-between p-9 xl:p-12">
                    <Link
                        href="/"
                        className="flex w-fit items-center gap-3 text-lg font-extrabold tracking-tight"
                    >
                        <span className="flex size-11 items-center justify-center rounded-[10px] border-2 border-background/80 bg-secondary text-[#171717] shadow-[3px_3px_0_#000]">
                            <AppLogoIcon className="size-6" />
                        </span>

                        <span>SkillPath AI</span>
                    </Link>

                    <div className="max-w-xl py-12">
                        <p className="text-xs font-extrabold tracking-[0.16em] text-background/60 uppercase">
                            Belajar lebih terarah
                        </p>

                        <h2 className="mt-4 max-w-lg text-4xl leading-[1.02] font-black tracking-[-0.04em] xl:text-5xl">
                            Kenali kemampuanmu dari jurusan yang sedang kamu
                            jalani.
                        </h2>

                        <p className="mt-5 max-w-lg text-base leading-7 font-medium text-background/70">
                            SkillPath membantu kamu melihat bagian yang sudah
                            kuat, bagian yang masih kurang, dan langkah belajar
                            yang lebih masuk akal.
                        </p>

                        <div className="mt-8 grid gap-3">
                            {benefits.map((benefit) => (
                                <div
                                    key={benefit}
                                    className="flex items-start gap-3 text-sm font-semibold text-background/85"
                                >
                                    <span className="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-secondary text-[#171717]">
                                        <Check className="size-3.5 stroke-[3]" />
                                    </span>

                                    <span>{benefit}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    <p className="max-w-md text-xs leading-5 font-medium text-background/50">
                        Rekomendasi dibuat dari struktur kemampuan jurusan,
                        hasil Assesment, evaluasi, dan progres yang tercatat di
                        sistem.
                    </p>
                </div>
            </section>

            <section className="flex min-h-svh items-center justify-center px-4 py-7 sm:px-6 sm:py-10 lg:px-10 xl:px-16">
                <div className="w-full max-w-md">
                    <Link
                        href="/"
                        className="mb-7 flex w-fit items-center gap-3 font-extrabold tracking-tight lg:hidden"
                    >
                        <span className="flex size-10 items-center justify-center rounded-[9px] border-2 border-foreground bg-secondary text-[#171717] shadow-[2px_2px_0_var(--neo-shadow-color)]">
                            <AppLogoIcon className="size-5" />
                        </span>

                        <span>SkillPath AI</span>
                    </Link>

                    <AuthBackLink />

                    <div className="neo-card p-5 sm:p-7">
                        <header className="mb-7">
                            <p className="text-xs font-extrabold tracking-[0.14em] text-muted-foreground uppercase">
                                Akun SkillPath
                            </p>

                            <h1 className="mt-2 text-3xl font-black tracking-[-0.035em] sm:text-4xl">
                                {title}
                            </h1>

                            <p className="mt-3 max-w-md text-sm leading-6 font-medium text-muted-foreground">
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
