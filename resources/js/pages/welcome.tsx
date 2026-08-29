import { Link } from '@inertiajs/react';
import {
    ArrowRight,
    BarChart3,
    BookOpenText,
    BrainCircuit,
    Check,
    GraduationCap,
    Route,
    Target,
    TrendingUp,
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

type ProcessItem = {
    number: string;
    title: string;
    text: string;
    icon: LucideIcon;
    accent: string;
};

type BenefitItem = {
    title: string;
    text: string;
    icon: LucideIcon;
};

const processItems: ProcessItem[] = [
    {
        number: '01',
        title: 'Pilih jurusanmu',
        text: 'Pilih jurusan yang sedang kamu jalani supaya pertanyaan dan kemampuan yang dinilai sesuai dengan bidang yang kamu pelajari.',
        icon: GraduationCap,
        accent: 'bg-[var(--neo-yellow)]',
    },
    {
        number: '02',
        title: 'Kerjakan assessment',
        text: 'Jawab pertanyaan sesuai kemampuanmu saat ini. Tidak perlu merasa harus bisa semuanya, jawab saja sesuai kondisi sebenarnya.',
        icon: BarChart3,
        accent: 'bg-[var(--neo-blue)]',
    },
    {
        number: '03',
        title: 'Lihat hasilnya',
        text: 'Setelah selesai, kamu bisa melihat kemampuan yang sudah cukup baik dan bagian yang masih perlu kamu tingkatkan.',
        icon: Target,
        accent: 'bg-[var(--neo-pink)]',
    },
    {
        number: '04',
        title: 'Mulai belajar',
        text: 'Ikuti roadmap yang sudah disusun, pelajari materinya, kerjakan evaluasi, lalu coba terapkan lewat proyek.',
        icon: Route,
        accent: 'bg-[var(--neo-lime)]',
    },
];

const benefitItems: BenefitItem[] = [
    {
        title: 'Tahu kemampuanmu sekarang',
        text: 'Kamu bisa melihat kemampuan mana yang sudah cukup baik dan mana yang masih perlu ditingkatkan.',
        icon: BarChart3,
    },
    {
        title: 'Tahu harus mulai dari mana',
        text: 'Tidak perlu bingung memilih materi karena SkillPath membantu menunjukkan apa yang sebaiknya dipelajari lebih dulu.',
        icon: Target,
    },
    {
        title: 'Punya urutan belajar yang jelas',
        text: 'Roadmap membantu kamu belajar langkah demi langkah tanpa harus mencoba semuanya sekaligus.',
        icon: Route,
    },
    {
        title: 'Bisa melihat perkembanganmu',
        text: 'Progres belajarmu bisa dipantau sehingga kamu tahu seberapa jauh kemampuanmu berkembang.',
        icon: TrendingUp,
    },
];

const withoutSkillPath = [
    'Bingung mau mulai belajar dari mana',
    'Sering pindah-pindah materi tanpa tahu mana yang lebih penting',
    'Tidak yakin kemampuan apa yang masih kurang',
    'Sulit melihat apakah kemampuanmu benar-benar berkembang',
];

const withSkillPath = [
    'Mulai dengan mengetahui kemampuanmu sekarang',
    'Tahu apa yang sebaiknya dipelajari lebih dulu',
    'Punya roadmap yang membantu mengatur urutan belajar',
    'Bisa melihat perkembanganmu dari waktu ke waktu',
];

export default function Welcome({ careers, stats }: Props) {
    const statItems = [
        {
            value: stats.careers,
            label: 'Jurusan',
        },
        {
            value: stats.skills,
            label: 'Kemampuan',
        },
        {
            value: stats.materials,
            label: 'Materi belajar',
        },
    ];

    return (
        <main className="pb-4">
            <section className="neo-page pt-10 sm:pt-14 lg:pt-16">
                <div className="grid gap-10 lg:grid-cols-[1fr_440px] lg:items-center lg:gap-14">
                    <div>
                        <span className="neo-label">
                            Belajar sesuai kemampuanmu
                        </span>

                        <h1 className="neo-heading mt-6 max-w-4xl text-5xl sm:text-6xl lg:text-7xl">
                            Kenali kemampuanmu.
                            <span className="block text-muted-foreground">
                                Cari tahu apa yang perlu kamu pelajari.
                            </span>
                        </h1>

                        <p className="mt-6 max-w-2xl text-base leading-7 font-semibold text-muted-foreground sm:text-lg sm:leading-8">
                            SkillPath membantu kamu melihat kemampuan yang sudah
                            kamu kuasai dan bagian yang masih perlu
                            ditingkatkan. Dari sana, kamu bisa lebih mudah
                            menentukan apa yang sebaiknya dipelajari
                            selanjutnya.
                        </p>

                        <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                            <Button asChild size="lg">
                                <Link href="/register" prefetch>
                                    Mulai assessment
                                    <ArrowRight />
                                </Link>
                            </Button>

                            <Button asChild size="lg" variant="outline">
                                <Link href="/karier" prefetch>
                                    Lihat jurusan
                                </Link>
                            </Button>
                        </div>

                        <div className="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm font-bold">
                            <div className="flex items-center gap-2">
                                <Check className="size-4" />
                                Assessment sesuai jurusan
                            </div>

                            <div className="flex items-center gap-2">
                                <Check className="size-4" />
                                Roadmap sesuai kebutuhanmu
                            </div>

                            <div className="flex items-center gap-2">
                                <Check className="size-4" />
                                Progres belajar yang bisa dipantau
                            </div>
                        </div>
                    </div>

                    <div className="neo-card p-5 sm:p-6">
                        <div className="flex items-start justify-between gap-4 border-b-2 border-foreground pb-5">
                            <div>
                                <p className="text-xs font-black tracking-[0.12em] text-muted-foreground uppercase">
                                    Cara kerjanya
                                </p>

                                <h2 className="mt-1 text-2xl font-black tracking-tight">
                                    Mulai dari assessment sampai belajar
                                </h2>
                            </div>

                            <div className="flex size-11 shrink-0 items-center justify-center rounded-[10px] border-2 border-foreground bg-secondary text-[#171717] shadow-[2px_2px_0_var(--neo-shadow-color)]">
                                <Route className="size-5" />
                            </div>
                        </div>

                        <div className="mt-5">
                            {processItems.map((item, index) => {
                                const Icon = item.icon;

                                return (
                                    <div
                                        key={item.number}
                                        className="relative flex gap-4"
                                    >
                                        <div className="flex flex-col items-center">
                                            <div
                                                className={`flex size-10 shrink-0 items-center justify-center rounded-[9px] border-2 border-[#171717] text-[#171717] shadow-[2px_2px_0_var(--neo-shadow-color)] ${item.accent}`}
                                            >
                                                <Icon className="size-4" />
                                            </div>

                                            {index <
                                                processItems.length - 1 && (
                                                <div className="h-11 border-l-2 border-dashed border-foreground/30" />
                                            )}
                                        </div>

                                        <div className="pb-6">
                                            <p className="text-xs font-black text-muted-foreground">
                                                {item.number}
                                            </p>

                                            <p className="mt-0.5 font-black">
                                                {item.title}
                                            </p>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        <div className="rounded-[10px] border-2 border-[#171717] bg-[var(--neo-lime)] p-4 text-[#171717]">
                            <p className="text-xs font-black tracking-[0.1em] uppercase">
                                Hasil akhirnya
                            </p>

                            <p className="mt-1 text-sm leading-6 font-semibold">
                                Kamu jadi tahu apa yang perlu dipelajari lebih
                                dulu dan punya langkah yang lebih jelas untuk
                                mengembangkan kemampuanmu.
                            </p>
                        </div>
                    </div>
                </div>

                <div className="mt-12 grid border-y-2 border-foreground sm:grid-cols-3">
                    {statItems.map((item, index) => (
                        <div
                            key={item.label}
                            className={`py-5 sm:px-6 sm:py-6 ${
                                index !== statItems.length - 1
                                    ? 'border-b-2 border-foreground sm:border-r-2 sm:border-b-0'
                                    : ''
                            } ${
                                index === 0
                                    ? 'sm:pl-0'
                                    : index === statItems.length - 1
                                      ? 'sm:pr-0'
                                      : ''
                            }`}
                        >
                            <p className="text-4xl font-black tracking-[-0.04em] sm:text-5xl">
                                {item.value}
                            </p>

                            <p className="mt-2 text-xs font-black tracking-[0.12em] text-muted-foreground uppercase">
                                {item.label}
                            </p>
                        </div>
                    ))}
                </div>
            </section>

            <section className="neo-page mt-16 sm:mt-20">
                <div className="mb-8 max-w-3xl">
                    <span className="neo-label bg-card">
                        Kenapa pakai SkillPath?
                    </span>

                    <h2 className="neo-heading mt-5 text-4xl sm:text-5xl">
                        Belajar jadi lebih mudah kalau kamu tahu harus mulai
                        dari mana.
                    </h2>

                    <p className="mt-5 max-w-2xl leading-7 font-semibold text-muted-foreground">
                        Materi belajar sebenarnya mudah ditemukan. Yang sering
                        bikin bingung justru memilih materi mana yang perlu
                        dipelajari sekarang dan mana yang bisa dipelajari nanti.
                    </p>
                </div>

                <div className="overflow-hidden rounded-[14px] border-2 border-foreground shadow-[4px_4px_0_var(--neo-shadow-color)] md:grid md:grid-cols-2">
                    <div className="bg-card p-5 sm:p-7 lg:p-8">
                        <div className="flex items-center gap-3">
                            <span className="flex size-10 items-center justify-center rounded-[9px] border-2 border-foreground bg-muted">
                                <BookOpenText className="size-5" />
                            </span>

                            <div>
                                <p className="text-xs font-black tracking-[0.1em] text-muted-foreground uppercase">
                                    Kalau belajar tanpa arah
                                </p>

                                <h3 className="text-xl font-black">
                                    Semuanya terasa perlu dipelajari
                                </h3>
                            </div>
                        </div>

                        <div className="mt-7 space-y-4">
                            {withoutSkillPath.map((item) => (
                                <div
                                    key={item}
                                    className="flex items-start gap-3"
                                >
                                    <span className="mt-2 size-2 shrink-0 rounded-full bg-muted-foreground" />

                                    <p className="text-sm leading-6 font-semibold text-muted-foreground">
                                        {item}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="border-t-2 border-[#171717] bg-[var(--neo-lime)] p-5 text-[#171717] sm:p-7 md:border-t-0 md:border-l-2 lg:p-8">
                        <div className="flex items-center gap-3">
                            <span className="flex size-10 items-center justify-center rounded-[9px] border-2 border-[#171717] bg-[#fffdf8] shadow-[2px_2px_0_#171717]">
                                <Target className="size-5" />
                            </span>

                            <div>
                                <p className="text-xs font-black tracking-[0.1em] uppercase">
                                    Kalau pakai SkillPath
                                </p>

                                <h3 className="text-xl font-black">
                                    Kamu punya arah yang lebih jelas
                                </h3>
                            </div>
                        </div>

                        <div className="mt-7 space-y-4">
                            {withSkillPath.map((item) => (
                                <div
                                    key={item}
                                    className="flex items-start gap-3"
                                >
                                    <Check className="mt-0.5 size-5 shrink-0" />

                                    <p className="text-sm leading-6 font-bold">
                                        {item}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            <section className="neo-page mt-16 sm:mt-20">
                <div className="grid gap-8 lg:grid-cols-[320px_1fr] lg:gap-12">
                    <div>
                        <span className="neo-label">Cara menggunakan</span>

                        <h2 className="neo-heading mt-5 text-4xl sm:text-5xl">
                            Cukup mulai dari empat langkah.
                        </h2>

                        <p className="mt-5 leading-7 font-semibold text-muted-foreground">
                            Pilih jurusanmu, kerjakan assessment, lihat
                            hasilnya, lalu mulai belajar dari bagian yang paling
                            perlu kamu tingkatkan.
                        </p>
                    </div>

                    <div className="border-t-2 border-foreground">
                        {processItems.map((item, index) => {
                            const Icon = item.icon;

                            return (
                                <article
                                    key={item.number}
                                    className="grid gap-5 border-b-2 border-foreground py-6 sm:grid-cols-[70px_56px_1fr] sm:items-start sm:py-7"
                                >
                                    <span className="font-mono text-sm font-black text-muted-foreground">
                                        {item.number}
                                    </span>

                                    <span
                                        className={`flex size-11 items-center justify-center rounded-[9px] border-2 border-[#171717] text-[#171717] shadow-[2px_2px_0_var(--neo-shadow-color)] ${item.accent}`}
                                    >
                                        <Icon className="size-5" />
                                    </span>

                                    <div>
                                        <h3 className="text-xl font-black sm:text-2xl">
                                            {item.title}
                                        </h3>

                                        <p className="mt-2 max-w-2xl text-sm leading-6 font-medium text-muted-foreground sm:text-base sm:leading-7">
                                            {item.text}
                                        </p>

                                        {index === processItems.length - 1 && (
                                            <Button
                                                asChild
                                                variant="outline"
                                                className="mt-5"
                                            >
                                                <Link href="/register" prefetch>
                                                    Coba sekarang
                                                    <ArrowRight />
                                                </Link>
                                            </Button>
                                        )}
                                    </div>
                                </article>
                            );
                        })}
                    </div>
                </div>
            </section>

            <section className="mt-16 border-y-2 border-[#171717] bg-[var(--neo-blue)] py-12 text-[#171717] sm:mt-20 sm:py-16">
                <div className="neo-page">
                    <div className="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p className="text-xs font-black tracking-[0.12em] uppercase">
                                Pilihan jurusan
                            </p>

                            <h2 className="mt-3 max-w-3xl text-4xl font-black tracking-[-0.04em] sm:text-5xl">
                                Pilih jurusan yang sedang kamu jalani.
                            </h2>

                            <p className="mt-4 max-w-2xl leading-7 font-semibold text-[#171717]/65">
                                Setiap jurusan punya bidang dan kemampuan yang
                                berbeda. Pilihan jurusanmu akan menentukan
                                kemampuan apa saja yang akan dinilai di
                                assessment.
                            </p>
                        </div>

                        <Button
                            asChild
                            variant="outline"
                            className="border-[#171717] bg-[#fffdf8] text-[#171717]"
                        >
                            <Link href="/karier" prefetch>
                                Lihat semua jurusan
                                <ArrowRight />
                            </Link>
                        </Button>
                    </div>

                    <div className="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {careers.map((career) => {
                            const program = getStudyProgramDefinition(
                                career.name,
                            );

                            return (
                                <Link
                                    key={career.id}
                                    href={`/karier/${career.slug}`}
                                    prefetch
                                    className="group flex min-h-64 flex-col rounded-[12px] border-2 border-[#171717] bg-[#fffdf8] p-5 text-[#171717] shadow-[4px_4px_0_#171717] transition-transform hover:-translate-y-1 sm:p-6"
                                >
                                    <div className="flex items-start justify-between gap-4">
                                        <span
                                            className="inline-flex h-3 w-12 rounded-full border border-[#171717]"
                                            style={{
                                                backgroundColor: career.accent,
                                            }}
                                        />

                                        <ArrowRight className="size-5 transition-transform group-hover:translate-x-1" />
                                    </div>

                                    <h3 className="mt-6 text-2xl font-black tracking-tight">
                                        {career.name}
                                    </h3>

                                    <p className="mt-2 text-sm leading-6 font-semibold text-[#625d55]">
                                        {career.tagline}
                                    </p>

                                    {program && (
                                        <div className="mt-5 flex flex-wrap gap-2">
                                            {program.areas.map((area) => (
                                                <span
                                                    key={area.name}
                                                    className="rounded-full border border-[#171717]/30 px-2.5 py-1 text-[10px] font-black"
                                                >
                                                    {area.name}
                                                </span>
                                            ))}
                                        </div>
                                    )}

                                    <div className="mt-auto pt-6">
                                        <div className="border-t border-[#171717]/30 pt-4 text-xs font-black">
                                            {career.skills_count} kemampuan
                                        </div>
                                    </div>
                                </Link>
                            );
                        })}
                    </div>
                </div>
            </section>

            <section className="neo-page mt-16 sm:mt-20">
                <div className="grid gap-8 lg:grid-cols-[340px_1fr] lg:gap-12">
                    <div>
                        <span className="neo-label bg-card">
                            Setelah assessment
                        </span>

                        <h2 className="neo-heading mt-5 text-4xl sm:text-5xl">
                            Kamu tidak cuma mendapatkan nilai.
                        </h2>

                        <p className="mt-5 leading-7 font-semibold text-muted-foreground">
                            Hasil assessment digunakan untuk membantu kamu
                            memahami kemampuanmu dan menentukan apa yang bisa
                            dilakukan setelahnya.
                        </p>
                    </div>

                    <div className="grid border-2 border-foreground md:grid-cols-2">
                        {benefitItems.map((item, index) => {
                            const Icon = item.icon;

                            return (
                                <article
                                    key={item.title}
                                    className={`p-5 sm:p-6 ${
                                        index === 0
                                            ? 'border-b-2 border-foreground md:border-r-2'
                                            : index === 1
                                              ? 'border-b-2 border-foreground'
                                              : index === 2
                                                ? 'border-b-2 border-foreground md:border-r-2 md:border-b-0'
                                                : ''
                                    }`}
                                >
                                    <div className="flex size-10 items-center justify-center rounded-[9px] border-2 border-foreground bg-card shadow-[2px_2px_0_var(--neo-shadow-color)]">
                                        <Icon className="size-5" />
                                    </div>

                                    <h3 className="mt-5 text-xl font-black">
                                        {item.title}
                                    </h3>

                                    <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                        {item.text}
                                    </p>
                                </article>
                            );
                        })}
                    </div>
                </div>
            </section>

            <section className="neo-page mt-16 sm:mt-20">
                <div className="grid overflow-hidden rounded-[14px] border-2 border-[#171717] bg-[var(--neo-yellow)] text-[#171717] shadow-[5px_5px_0_var(--neo-shadow-color)] lg:grid-cols-[1fr_300px]">
                    <div className="p-6 sm:p-8 lg:p-10">
                        <p className="text-xs font-black tracking-[0.12em] uppercase">
                            Mulai dari sini
                        </p>

                        <h2 className="mt-3 max-w-3xl text-4xl font-black tracking-[-0.04em] sm:text-5xl">
                            Tidak harus langsung bisa semuanya.
                        </h2>

                        <p className="mt-5 max-w-2xl leading-7 font-semibold text-[#171717]/70">
                            Mulai saja dari kemampuanmu sekarang. Pilih jurusan,
                            kerjakan assessment dengan jujur, lalu lihat bagian
                            mana yang bisa kamu tingkatkan sedikit demi sedikit.
                        </p>

                        <div className="mt-7 flex flex-col gap-3 sm:flex-row">
                            <Button
                                asChild
                                size="lg"
                                className="bg-[#171717] text-[#fffdf8] hover:bg-[#171717]/90"
                            >
                                <Link href="/register" prefetch>
                                    Mulai assessment
                                    <ArrowRight />
                                </Link>
                            </Button>

                            <Button
                                asChild
                                size="lg"
                                variant="outline"
                                className="border-[#171717] bg-[#fffdf8] text-[#171717]"
                            >
                                <Link href="/tentang" prefetch>
                                    Kenali SkillPath
                                </Link>
                            </Button>
                        </div>
                    </div>

                    <div className="flex min-h-56 items-center justify-center border-t-2 border-[#171717] bg-[var(--neo-lime)] p-7 lg:border-t-0 lg:border-l-2">
                        <div className="text-center">
                            <p className="mt-5 text-lg font-black">
                                Assessment
                            </p>

                            <ArrowRight className="mx-auto my-2 size-5 rotate-90" />

                            <p className="text-lg font-black">Roadmap</p>

                            <ArrowRight className="mx-auto my-2 size-5 rotate-90" />

                            <p className="text-lg font-black">Progres</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    );
}
