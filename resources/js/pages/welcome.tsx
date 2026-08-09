import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    BarChart3,
    BrainCircuit,
    Check,
    GitBranch,
    Layers3,
    Sparkles,
    Target,
    TimerReset,
} from 'lucide-react';
import { Button } from '@/components/ui/button';

type Career = {
    id: number;
    name: string;
    slug: string;
    tagline: string;
    difficulty: string;
    accent: string;
    skills_count: number;
};

type Props = {
    careers: Career[];
    stats: {
        careers: number;
        skills: number;
        materials: number;
    };
};

export default function Welcome({ careers, stats }: Props) {
    return (
        <>
            <Head title="Jalur belajar yang tahu posisi awalmu" />

            <main>
                <section className="mx-auto grid max-w-7xl gap-10 px-4 pt-14 pb-16 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:px-8 lg:pt-20 lg:pb-24">
                    <div>
                        <span className="neo-label">
                            <Sparkles className="size-3.5" />
                            Roadmap yang punya alasan
                        </span>

                        <h1 className="neo-heading mt-6 max-w-4xl text-5xl sm:text-6xl lg:text-7xl">
                            Belajar lebih sedikit hal yang salah.
                        </h1>

                        <p className="mt-6 max-w-2xl text-lg leading-relaxed font-medium text-muted-foreground sm:text-xl">
                            SkillPath AI memetakan kemampuanmu terhadap target
                            karier, mencari gap yang paling penting, lalu
                            menyusun urutan belajar berdasarkan skill dan
                            prasyarat yang nyata.
                        </p>

                        <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                            <Button asChild size="lg">
                                <Link href="/register">
                                    Mulai asesmen
                                    <ArrowRight />
                                </Link>
                            </Button>

                            <Button asChild size="lg" variant="outline">
                                <Link href="/karier">Lihat jalur karier</Link>
                            </Button>
                        </div>

                        <div className="mt-10 flex flex-wrap gap-x-7 gap-y-3 text-sm font-bold">
                            <span className="flex items-center gap-2">
                                <Check className="size-4" />
                                Tidak mulai dari nol
                            </span>
                            <span className="flex items-center gap-2">
                                <Check className="size-4" />
                                Progress perlu bukti
                            </span>
                            <span className="flex items-center gap-2">
                                <Check className="size-4" />
                                AI bukan sumber kebenaran
                            </span>
                        </div>
                    </div>

                    <div className="relative mx-auto w-full max-w-xl lg:mx-0 lg:ml-auto">
                        <div className="neo-card relative overflow-hidden p-5 sm:p-7">
                            <div className="mb-7 flex items-start justify-between gap-4">
                                <div>
                                    <p className="text-xs font-black tracking-[0.16em] text-muted-foreground uppercase">
                                        Sample diagnosis
                                    </p>
                                    <h2 className="mt-1 text-2xl font-black tracking-tight">
                                        Backend Developer
                                    </h2>
                                </div>

                                <div className="flex size-14 items-center justify-center rounded-full border-2 border-foreground bg-[#FFCE5C] text-lg font-black">
                                    47
                                </div>
                            </div>

                            <div className="space-y-5">
                                {[
                                    ['Dasar Pemrograman', 80, 75, '#C7FF5E'],
                                    ['Database', 30, 75, '#FF8FAB'],
                                    ['REST API', 20, 85, '#FF8FAB'],
                                    ['Git & GitHub', 45, 65, '#79D7FF'],
                                ].map(([name, current, target, color]) => (
                                    <div key={String(name)}>
                                        <div className="mb-2 flex items-end justify-between gap-3 text-sm">
                                            <span className="font-extrabold">
                                                {name}
                                            </span>
                                            <span className="font-mono text-xs font-bold text-muted-foreground">
                                                {current}/{target}
                                            </span>
                                        </div>

                                        <div className="h-4 overflow-hidden rounded-full border-2 border-foreground bg-muted">
                                            <div
                                                className="h-full border-r-2 border-foreground"
                                                style={{
                                                    width: `${current}%`,
                                                    backgroundColor:
                                                        String(color),
                                                }}
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>

                            <div className="mt-7 rounded-xl border-2 border-foreground bg-[#79D7FF] p-4 text-[#171717]">
                                <p className="text-xs font-black tracking-[0.14em] uppercase">
                                    Kenapa database dulu?
                                </p>
                                <p className="mt-1 text-sm leading-relaxed font-semibold">
                                    Gap masih 45 poin dan database menjadi
                                    prasyarat untuk ORM serta sebagian pekerjaan
                                    API. Jadi ia naik ke depan, bukan dipilih
                                    secara acak.
                                </p>
                            </div>
                        </div>

                        <div className="absolute -bottom-5 -left-4 hidden rotate-[-5deg] rounded-xl border-2 border-foreground bg-secondary px-4 py-3 text-sm font-black shadow-[4px_4px_0_var(--foreground)] sm:block">
                            next: Database Fundamentals
                        </div>
                    </div>
                </section>

                <section className="border-y-2 border-foreground bg-foreground text-background">
                    <div className="mx-auto grid max-w-7xl grid-cols-3 divide-x-2 divide-background/20 px-4 sm:px-6 lg:px-8">
                        {[
                            [stats.careers, 'jalur karier MVP'],
                            [stats.skills, 'skill terstruktur'],
                            [stats.materials, 'materi berbasis skill'],
                        ].map(([number, label]) => (
                            <div
                                key={String(label)}
                                className="px-3 py-7 text-center sm:py-9"
                            >
                                <p className="text-3xl font-black sm:text-4xl">
                                    {number}
                                </p>
                                <p className="mt-1 text-[11px] font-bold tracking-[0.12em] text-background/65 uppercase sm:text-xs">
                                    {label}
                                </p>
                            </div>
                        ))}
                    </div>
                </section>

                <section className="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                    <div className="grid gap-10 lg:grid-cols-[0.7fr_1.3fr]">
                        <div>
                            <span className="neo-label">
                                Bukan chatbot roadmap
                            </span>

                            <h2 className="neo-heading mt-5 text-4xl sm:text-5xl">
                                Enam langkah. Satu alur yang bisa diuji.
                            </h2>

                            <p className="mt-5 max-w-md leading-relaxed font-medium text-muted-foreground">
                                Setiap keputusan inti berasal dari data
                                internal: standar skill, hasil asesmen, bobot,
                                prasyarat, evaluasi, dan progres.
                            </p>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            {[
                                [
                                    Target,
                                    '01',
                                    'Assess',
                                    'Ukur skill per area, bukan satu skor abstrak.',
                                ],
                                [
                                    BarChart3,
                                    '02',
                                    'Compare',
                                    'Bandingkan skor dengan standar profesi.',
                                ],
                                [
                                    GitBranch,
                                    '03',
                                    'Recommend',
                                    'Urutkan gap berdasarkan bobot dan dependency.',
                                ],
                                [
                                    Layers3,
                                    '04',
                                    'Learn',
                                    'Pelajari materi yang sudah punya tujuan dan latihan.',
                                ],
                                [
                                    BrainCircuit,
                                    '05',
                                    'Build',
                                    'Buka proyek ketika skill minimum mulai siap.',
                                ],
                                [
                                    TimerReset,
                                    '06',
                                    'Evaluate',
                                    'Nilai ulang skill dan buka langkah berikutnya.',
                                ],
                            ].map(([Icon, no, title, text]) => {
                                const IconComponent = Icon as typeof Target;

                                return (
                                    <article
                                        key={String(no)}
                                        className="neo-card-flat p-5"
                                    >
                                        <div className="flex items-center justify-between">
                                            <IconComponent className="size-6" />
                                            <span className="font-mono text-xs font-black text-muted-foreground">
                                                {no}
                                            </span>
                                        </div>

                                        <h3 className="mt-8 text-xl font-black">
                                            {title}
                                        </h3>

                                        <p className="mt-2 text-sm leading-relaxed font-medium text-muted-foreground">
                                            {text}
                                        </p>
                                    </article>
                                );
                            })}
                        </div>
                    </div>
                </section>

                <section className="border-y-2 border-foreground bg-[#79D7FF] py-20 text-[#171717]">
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="mb-10 flex flex-col justify-between gap-5 md:flex-row md:items-end">
                            <div>
                                <p className="text-xs font-black tracking-[0.18em] uppercase">
                                    Mulai dari tiga jalur
                                </p>

                                <h2 className="mt-2 max-w-2xl text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                                    Cukup sempit untuk selesai. Cukup dalam
                                    untuk berguna.
                                </h2>
                            </div>

                            <Button
                                asChild
                                variant="outline"
                                className="bg-[#fffdf7]"
                            >
                                <Link href="/karier">
                                    Bandingkan semuanya
                                    <ArrowRight />
                                </Link>
                            </Button>
                        </div>

                        <div className="grid gap-5 lg:grid-cols-3">
                            {careers.map((career, index) => (
                                <Link
                                    key={career.id}
                                    href={`/karier/${career.slug}`}
                                    className="group rounded-[18px] border-2 border-[#171717] bg-[#fffdf7] p-6 shadow-[5px_5px_0_#171717] transition-transform hover:-translate-y-1"
                                >
                                    <div className="flex items-center justify-between">
                                        <span className="rounded-full border-2 border-[#171717] px-3 py-1 text-xs font-black">
                                            0{index + 1}
                                        </span>
                                        <ArrowRight className="size-5 transition-transform group-hover:translate-x-1" />
                                    </div>

                                    <h3 className="mt-10 text-2xl font-black tracking-tight">
                                        {career.name}
                                    </h3>

                                    <p className="mt-3 text-sm leading-relaxed font-semibold text-[#56524c]">
                                        {career.tagline}
                                    </p>

                                    <div className="mt-6 flex items-center gap-2 text-xs font-black tracking-wide uppercase">
                                        <span>{career.skills_count} skill</span>
                                        <span>•</span>
                                        <span>{career.difficulty}</span>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    </div>
                </section>

                <section className="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                    <div className="neo-card grid overflow-hidden lg:grid-cols-[1fr_auto]">
                        <div className="p-7 sm:p-10">
                            <p className="text-xs font-black tracking-[0.18em] text-muted-foreground uppercase">
                                Kalau kamu sudah punya target
                            </p>

                            <h2 className="mt-3 max-w-3xl text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                                Jangan mulai dari roadmap orang lain. Mulai dari
                                gap milikmu.
                            </h2>

                            <p className="mt-5 max-w-2xl leading-relaxed font-medium text-muted-foreground">
                                Buat akun, pilih target, jawab asesmen, dan
                                biarkan sistem menunjukkan apa yang perlu
                                dikerjakan lebih dulu.
                            </p>

                            <Button asChild size="lg" className="mt-7">
                                <Link href="/register">
                                    Buat akun
                                    <ArrowRight />
                                </Link>
                            </Button>
                        </div>

                        <div className="flex min-h-52 items-center justify-center border-t-2 border-foreground bg-secondary p-8 text-[#171717] lg:w-80 lg:border-t-0 lg:border-l-2">
                            <div className="text-center">
                                <p className="font-mono text-6xl font-black">
                                    0→1
                                </p>
                                <p className="mt-3 text-sm font-black tracking-[0.14em] uppercase">
                                    mulai dari posisi nyata
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </>
    );
}
