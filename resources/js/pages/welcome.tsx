import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    BarChart3,
    BrainCircuit,
    Check,
    GitBranch,
    GraduationCap,
    Layers3,
    TimerReset,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { getStudyProgramDefinition } from '@/lib/academic-programs';

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

type ProcessStep = {
    icon: LucideIcon;
    number: string;
    title: string;
    text: string;
    accent: string;
};

const processSteps: ProcessStep[] = [
    {
        icon: GraduationCap,
        number: '01',
        title: 'Pilih jurusan',
        text: 'Mulai dengan memilih jurusan yang sedang kamu jalani agar asesmen sesuai dengan bidang yang kamu pelajari.',
        accent: 'bg-[var(--neo-lime)]',
    },
    {
        icon: BarChart3,
        number: '02',
        title: 'Kerjakan asesmen',
        text: 'Jawab 9 pertanyaan yang mewakili 3 bidang utama dan 9 kemampuan dalam jurusanmu.',
        accent: 'bg-[var(--neo-blue)]',
    },
    {
        icon: GitBranch,
        number: '03',
        title: 'Lihat peta kemampuan',
        text: 'Bandingkan kemampuanmu sekarang dengan target penguasaan setiap kemampuan di jurusanmu.',
        accent: 'bg-[var(--neo-yellow)]',
    },
    {
        icon: Layers3,
        number: '04',
        title: 'Ikuti jalur belajar',
        text: 'Pelajari materi yang paling perlu diperkuat lebih dulu dalam urutan yang jelas.',
        accent: 'bg-[var(--neo-orange)]',
    },
    {
        icon: BrainCircuit,
        number: '05',
        title: 'Terapkan lewat proyek',
        text: 'Gunakan kemampuan yang sudah kamu pelajari untuk mengerjakan proyek dan membangun portofolio.',
        accent: 'bg-[var(--neo-pink)]',
    },
    {
        icon: TimerReset,
        number: '06',
        title: 'Evaluasi perkembangan',
        text: 'Ulangi asesmen dan evaluasi setelah belajar untuk melihat perubahan kemampuanmu.',
        accent: 'bg-[var(--neo-lime)]',
    },
];

const diagnosis = [
    {
        name: 'SQL dan Pengolahan Data',
        current: 78,
        target: 75,
        color: '#C7FF5E',
    },
    {
        name: 'Database Management',
        current: 42,
        target: 75,
        color: '#FF8FAB',
    },
    {
        name: 'UI Design',
        current: 60,
        target: 65,
        color: '#79D7FF',
    },
    {
        name: 'User Research',
        current: 35,
        target: 65,
        color: '#FF8FAB',
    },
];

export default function Welcome({ careers, stats }: Props) {
    const statItems = [
        {
            value: stats.careers,
            label: 'Jurusan',
            accent: 'bg-[var(--neo-lime)]',
        },
        {
            value: stats.skills,
            label: 'Kemampuan jurusan',
            accent: 'bg-[var(--neo-blue)]',
        },
        {
            value: stats.materials,
            label: 'Materi belajar',
            accent: 'bg-[var(--neo-yellow)]',
        },
    ];

    return (
        <>
            <main className="pb-12 sm:pb-16">
                <section className="neo-page grid gap-10 pt-10 sm:pt-14 lg:grid-cols-[1.03fr_0.97fr] lg:items-center lg:gap-14 lg:pt-16">
                    <div>
                        <span className="neo-label">
                            Belajar sesuai jurusanmu
                        </span>

                        <h1 className="neo-heading mt-6 max-w-4xl text-[clamp(3.2rem,7vw,6rem)]">
                            Tahu kemampuanmu sekarang, lalu belajar dari bagian
                            yang paling perlu.
                        </h1>

                        <p className="mt-6 max-w-2xl text-lg leading-8 font-semibold text-muted-foreground sm:text-xl">
                            Pilih jurusanmu, kerjakan asesmen dari tiga bidang
                            utama, lalu lihat kemampuan mana yang sudah kuat dan
                            mana yang masih perlu dikembangkan.
                        </p>

                        <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                            <Button asChild size="lg">
                                <Link href="/register" prefetch>
                                    Mulai asesmen
                                    <ArrowRight />
                                </Link>
                            </Button>

                            <Button asChild size="lg" variant="outline">
                                <Link href="/karier" prefetch>
                                    Lihat pilihan jurusan
                                </Link>
                            </Button>
                        </div>

                        <div className="mt-9 grid gap-3 text-sm font-extrabold sm:grid-cols-3">
                            <span className="flex items-center gap-2">
                                <Check className="size-4 shrink-0" />5 jurusan
                                yang berbeda
                            </span>

                            <span className="flex items-center gap-2">
                                <Check className="size-4 shrink-0" />3 bidang
                                utama per jurusan
                            </span>

                            <span className="flex items-center gap-2">
                                <Check className="size-4 shrink-0" />9 kemampuan
                                yang dinilai
                            </span>
                        </div>
                    </div>

                    <div className="relative mx-auto w-full max-w-xl lg:mx-0 lg:ml-auto">
                        <div className="neo-card p-5 sm:p-7">
                            <div className="mb-7 flex items-start justify-between gap-4">
                                <div>
                                    <p className="text-xs font-black tracking-[0.16em] text-muted-foreground uppercase">
                                        Contoh hasil asesmen
                                    </p>

                                    <h2 className="mt-1 text-2xl font-black tracking-tight">
                                        Sistem Informasi
                                    </h2>
                                </div>

                                <div className="flex size-14 shrink-0 items-center justify-center rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] text-lg font-black text-[#171717]">
                                    54
                                </div>
                            </div>

                            <div className="space-y-5">
                                {diagnosis.map((item) => (
                                    <div key={item.name}>
                                        <div className="mb-2 flex items-end justify-between gap-3 text-sm">
                                            <span className="font-extrabold">
                                                {item.name}
                                            </span>

                                            <span className="font-mono text-xs font-bold text-muted-foreground">
                                                {item.current}/{item.target}
                                            </span>
                                        </div>

                                        <div className="h-4 overflow-hidden rounded-full border-2 border-foreground bg-muted">
                                            <div
                                                className="h-full border-r-2 border-[#171717]"
                                                style={{
                                                    width: `${item.current}%`,
                                                    backgroundColor: item.color,
                                                }}
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>

                            <div className="mt-7 rounded-[12px] border-2 border-[#171717] bg-[var(--neo-blue)] p-4 text-[#171717]">
                                <p className="text-xs font-black tracking-[0.14em] uppercase">
                                    Kenapa Database Management diprioritaskan?
                                </p>

                                <p className="mt-1 text-sm leading-relaxed font-semibold">
                                    Nilainya masih cukup jauh dari target.
                                    Memperkuat kemampuan ini lebih dulu akan
                                    membantu saat kamu mempelajari pengembangan
                                    sistem dan pengelolaan data.
                                </p>
                            </div>
                        </div>

                        <div className="absolute -bottom-5 -left-3 hidden rotate-[-4deg] rounded-[11px] border-2 border-[#171717] bg-secondary px-4 py-3 text-sm font-black text-[#171717] shadow-[4px_4px_0_var(--neo-shadow-color)] sm:block">
                            Berikutnya: Database Management
                        </div>
                    </div>
                </section>

                <section className="neo-page mt-14 grid gap-4 sm:grid-cols-3">
                    {statItems.map((item) => (
                        <div
                            key={item.label}
                            className={`neo-interactive rounded-[14px] border-2 border-[#171717] p-5 text-[#171717] shadow-[4px_4px_0_var(--neo-shadow-color)] ${item.accent}`}
                        >
                            <p className="text-4xl font-black">{item.value}</p>

                            <p className="mt-2 text-xs font-black tracking-[0.14em] uppercase">
                                {item.label}
                            </p>
                        </div>
                    ))}
                </section>

                <section className="neo-page mt-16 sm:mt-20">
                    <div className="neo-card p-5 sm:p-8">
                        <div className="grid gap-8 lg:grid-cols-[0.7fr_1.3fr]">
                            <div>
                                <span className="neo-label">
                                    Cara kerja SkillPath
                                </span>

                                <h2 className="neo-heading mt-5 text-4xl sm:text-5xl">
                                    Mulai dari jurusanmu, bukan dari tebakan.
                                </h2>

                                <p className="mt-5 max-w-md leading-7 font-semibold text-muted-foreground">
                                    SkillPath membantu melihat kemampuan yang
                                    sudah kamu punya, bagian yang masih kurang,
                                    dan urutan belajar yang lebih masuk akal.
                                </p>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                                {processSteps.map(
                                    ({
                                        icon: Icon,
                                        number,
                                        title,
                                        text,
                                        accent,
                                    }) => (
                                        <article
                                            key={number}
                                            className="neo-interactive rounded-[14px] border-2 border-foreground bg-background p-5"
                                        >
                                            <div className="flex items-center justify-between gap-3">
                                                <span
                                                    className={`flex size-10 items-center justify-center rounded-[10px] border-2 border-[#171717] text-[#171717] ${accent}`}
                                                >
                                                    <Icon className="size-5" />
                                                </span>

                                                <span className="font-mono text-xs font-black text-muted-foreground">
                                                    {number}
                                                </span>
                                            </div>

                                            <h3 className="mt-7 text-xl font-black">
                                                {title}
                                            </h3>

                                            <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                                {text}
                                            </p>
                                        </article>
                                    ),
                                )}
                            </div>
                        </div>
                    </div>
                </section>

                <section className="neo-page mt-16 sm:mt-20">
                    <div className="neo-card overflow-hidden border-[#171717] bg-[var(--neo-blue)] p-5 text-[#171717] sm:p-8">
                        <div className="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                            <div>
                                <p className="text-xs font-black tracking-[0.18em] uppercase">
                                    Pilihan jurusan
                                </p>

                                <h2 className="mt-2 max-w-3xl text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                                    Pilih jurusan, lalu lihat kemampuan yang
                                    akan dinilai.
                                </h2>

                                <p className="mt-4 max-w-2xl text-sm leading-6 font-semibold text-[#171717]/70">
                                    Setiap jurusan memiliki tiga bidang utama
                                    dan sembilan kemampuan yang berbeda.
                                </p>
                            </div>

                            <Button
                                asChild
                                variant="outline"
                                className="border-[#171717] bg-[#fffdf7] text-[#171717]"
                            >
                                <Link href="/karier" prefetch>
                                    Lihat semua jurusan
                                    <ArrowRight />
                                </Link>
                            </Button>
                        </div>

                        <div className="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                            {careers.map((career, index) => {
                                const program = getStudyProgramDefinition(
                                    career.name,
                                );

                                return (
                                    <Link
                                        key={career.id}
                                        href={`/karier/${career.slug}`}
                                        prefetch
                                        className="group rounded-[14px] border-2 border-[#171717] bg-[#fffdf7] p-5 text-[#171717] shadow-[4px_4px_0_#171717] transition-transform hover:-translate-y-1 sm:p-6"
                                    >
                                        <div className="flex items-center justify-between">
                                            <span
                                                className="rounded-full border-2 border-[#171717] px-3 py-1 text-xs font-black"
                                                style={{
                                                    backgroundColor:
                                                        career.accent,
                                                }}
                                            >
                                                {String(index + 1).padStart(
                                                    2,
                                                    '0',
                                                )}
                                            </span>

                                            <ArrowRight className="size-5 transition-transform group-hover:translate-x-1" />
                                        </div>

                                        <h3 className="mt-8 text-2xl font-black tracking-tight">
                                            {career.name}
                                        </h3>

                                        <p className="mt-3 text-sm leading-6 font-semibold text-[#56524c]">
                                            {career.tagline}
                                        </p>

                                        {program && (
                                            <div className="mt-5 flex flex-wrap gap-2">
                                                {program.areas.map((area) => (
                                                    <span
                                                        key={area.name}
                                                        className="rounded-full border border-[#171717]/25 px-2.5 py-1 text-[10px] font-black"
                                                    >
                                                        {area.name}
                                                    </span>
                                                ))}
                                            </div>
                                        )}

                                        <p className="mt-6 text-xs font-black tracking-wide uppercase">
                                            3 bidang · {career.skills_count}{' '}
                                            kemampuan
                                        </p>
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                </section>

                <section className="neo-page mt-16 sm:mt-20">
                    <div className="neo-card grid overflow-hidden lg:grid-cols-[1fr_300px]">
                        <div className="p-6 sm:p-9">
                            <p className="text-xs font-black tracking-[0.18em] text-muted-foreground uppercase">
                                Siap melihat kemampuanmu?
                            </p>

                            <h2 className="mt-3 max-w-3xl text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                                Kamu tidak harus menguasai semuanya sekarang.
                            </h2>

                            <p className="mt-5 max-w-2xl leading-7 font-semibold text-muted-foreground">
                                Pilih jurusan, jawab asesmen sesuai kemampuanmu,
                                lalu gunakan hasilnya untuk menentukan apa yang
                                sebaiknya dipelajari lebih dulu.
                            </p>

                            <Button asChild size="lg" className="mt-7">
                                <Link href="/register" prefetch>
                                    Buat akun
                                    <ArrowRight />
                                </Link>
                            </Button>
                        </div>

                        <div className="flex min-h-52 items-center justify-center border-t-2 border-[#171717] bg-secondary p-8 text-[#171717] lg:border-t-0 lg:border-l-2">
                            <div className="text-center">
                                <p className="font-mono text-6xl font-black">
                                    0→1
                                </p>

                                <p className="mt-3 text-sm font-black tracking-[0.14em] uppercase">
                                    Mulai dari kemampuanmu sekarang
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </>
    );
}
