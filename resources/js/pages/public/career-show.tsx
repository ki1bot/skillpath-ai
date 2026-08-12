import { Head, Link } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    BriefcaseBusiness,
    CheckCircle2,
    Gauge,
    Target,
} from 'lucide-react';
import { Button } from '@/components/ui/button';

type Skill = {
    id: number;
    name: string;
    category: string;
    description: string;
    pivot: {
        target_level: number;
        importance_weight: string;
        is_required: boolean;
    };
};

type Project = {
    id: number;
    title: string;
    slug: string;
    difficulty: string;
    summary: string;
    estimated_hours: number;
};

type Compatibility = {
    score: number;
    label: string;
    assessed_skills: number;
    total_skills: number;
    top_gaps: {
        skill_id: number;
        name: string;
        current: number;
        target: number;
        gap: number;
    }[];
};

type Career = {
    id: number;
    name: string;
    slug: string;
    tagline: string;
    description: string;
    responsibilities: string[];
    difficulty: string;
    accent: string;
    skills: Skill[];
    projects: Project[];
    compatibility?: Compatibility | null;
};

export default function CareerShow({ career }: { career: Career }) {
    return (
        <>
            <Head title={career.name} />

            <main>
                <section
                    className="border-b-2 border-[#171717]"
                    style={{
                        backgroundColor: career.accent,
                    }}
                >
                    <div className="neo-page py-14 text-[#171717] lg:py-20">
                        <Link
                            href="/karier"
                            className="inline-flex items-center gap-2 text-sm font-black"
                        >
                            <ArrowLeft className="size-4" />
                            Kembali ke jalur karier
                        </Link>

                        <div className="mt-10 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                            <div>
                                <p className="text-xs font-black tracking-[0.18em] uppercase">
                                    Jalur karier
                                </p>

                                <h1 className="mt-3 text-5xl font-black tracking-[-0.05em] sm:text-7xl">
                                    {career.name}
                                </h1>

                                <p className="mt-5 max-w-3xl text-xl leading-relaxed font-bold">
                                    {career.tagline}
                                </p>

                                {career.compatibility && (
                                    <div className="mt-7 inline-flex items-center gap-4 rounded-[14px] border-2 border-[#171717] bg-[#fffdf7] px-5 py-4 shadow-[4px_4px_0_#171717]">
                                        <Gauge className="size-7" />

                                        <div>
                                            <p className="text-xs font-black uppercase">
                                                Kecocokan berdasarkan Assesment
                                            </p>

                                            <p className="mt-1 font-mono text-3xl font-black">
                                                {career.compatibility.score}%
                                            </p>

                                            <p className="text-xs font-bold">
                                                {career.compatibility.label}
                                            </p>
                                        </div>
                                    </div>
                                )}
                            </div>

                            <div className="rounded-[16px] border-2 border-[#171717] bg-[#fffdf7] p-6 shadow-[5px_5px_0_#171717]">
                                <p className="text-sm leading-relaxed font-semibold text-[#56524c]">
                                    {career.description}
                                </p>

                                <div className="mt-5 grid grid-cols-2 gap-3">
                                    <div className="rounded-[11px] border-2 border-[#171717] p-3">
                                        <Gauge className="size-5" />

                                        <p className="mt-3 text-xs font-black uppercase">
                                            Kesulitan
                                        </p>

                                        <p className="font-bold">
                                            {career.difficulty}
                                        </p>
                                    </div>

                                    <div className="rounded-[11px] border-2 border-[#171717] p-3">
                                        <Target className="size-5" />

                                        <p className="mt-3 text-xs font-black uppercase">
                                            Kemampuan
                                        </p>

                                        <p className="font-bold">
                                            {career.skills.length} keahlian
                                        </p>
                                    </div>
                                </div>

                                {career.compatibility &&
                                    career.compatibility.top_gaps.length >
                                        0 && (
                                        <div className="mt-5 border-t-2 border-[#171717] pt-4">
                                            <p className="text-xs font-black uppercase">
                                                Prioritas gap
                                            </p>

                                            <div className="mt-3 space-y-2">
                                                {career.compatibility.top_gaps.map(
                                                    (gap) => (
                                                        <div
                                                            key={gap.skill_id}
                                                            className="flex justify-between gap-3 text-sm font-bold"
                                                        >
                                                            <span>
                                                                {gap.name}
                                                            </span>

                                                            <span className="font-mono">
                                                                -{gap.gap}
                                                            </span>
                                                        </div>
                                                    ),
                                                )}
                                            </div>
                                        </div>
                                    )}
                            </div>
                        </div>
                    </div>
                </section>

                <section className="neo-page grid gap-10 py-16 lg:grid-cols-[0.75fr_1.25fr] lg:py-20">
                    <div>
                        <span className="neo-label">
                            <BriefcaseBusiness className="size-3.5" />
                            Yang dikerjakan
                        </span>

                        <div className="mt-6 space-y-3">
                            {career.responsibilities?.map((item) => (
                                <div
                                    key={item}
                                    className="neo-card-flat flex gap-3 p-4 text-sm leading-relaxed font-bold"
                                >
                                    <CheckCircle2 className="mt-0.5 size-5 shrink-0" />
                                    {item}
                                </div>
                            ))}
                        </div>
                    </div>

                    <div>
                        <div className="mb-6">
                            <p className="text-xs font-black tracking-[0.16em] text-muted-foreground uppercase">
                                Kemampuan yang dibutuhkan
                            </p>

                            <h2 className="mt-1 text-3xl font-black tracking-tight">
                                Apa yang perlu dikuasai?
                            </h2>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            {career.skills.map((skill) => (
                                <article
                                    key={skill.id}
                                    className="neo-card-flat p-5"
                                >
                                    <div className="flex items-center justify-between gap-3">
                                        <span className="rounded-full border-2 border-foreground bg-muted px-2.5 py-1 text-[10px] font-black tracking-wide uppercase">
                                            {skill.category}
                                        </span>

                                        <span className="font-mono text-sm font-black">
                                            {skill.pivot.target_level}/100
                                        </span>
                                    </div>

                                    <h3 className="mt-5 text-lg font-black">
                                        {skill.name}
                                    </h3>

                                    <p className="mt-2 text-sm leading-relaxed font-medium text-muted-foreground">
                                        {skill.description}
                                    </p>
                                </article>
                            ))}
                        </div>
                    </div>
                </section>

                <section className="border-y-2 border-[#171717] bg-[var(--neo-yellow)] py-16 text-[#171717]">
                    <div className="neo-page">
                        <h2 className="text-3xl font-black tracking-tight">
                            Contoh proyek portofolio
                        </h2>

                        <div className="mt-7 grid gap-5 md:grid-cols-2">
                            {career.projects.map((project) => (
                                <div
                                    key={project.id}
                                    className="rounded-[16px] border-2 border-[#171717] bg-[#fffdf7] p-6 shadow-[4px_4px_0_#171717]"
                                >
                                    <div className="flex items-center justify-between text-xs font-black uppercase">
                                        <span>{project.difficulty}</span>
                                        <span>
                                            {project.estimated_hours} jam
                                        </span>
                                    </div>

                                    <h3 className="mt-5 text-2xl font-black">
                                        {project.title}
                                    </h3>

                                    <p className="mt-2 text-sm leading-relaxed font-semibold text-[#56524c]">
                                        {project.summary}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                <section className="mx-auto max-w-4xl px-4 py-20 text-center sm:px-6 lg:px-8">
                    <p className="text-xs font-black tracking-[0.18em] text-muted-foreground uppercase">
                        {career.compatibility
                            ? 'Kemampuan dapat berubah'
                            : 'Belum tahu posisi kemampuanmu?'}
                    </p>

                    <h2 className="mt-3 text-4xl font-black tracking-[-0.045em]">
                        {career.compatibility
                            ? 'Ulangi Assesment setelah kemampuan meningkat untuk memperbarui hasil kecocokan.'
                            : 'Cek kemampuan sebelum menentukan apa yang perlu dipelajari.'}
                    </h2>

                    <Button asChild size="lg" className="mt-7">
                        <Link
                            href={
                                career.compatibility
                                    ? '/assessment'
                                    : '/register'
                            }
                        >
                            {career.compatibility
                                ? 'Ulangi Assesment'
                                : 'Mulai Assesment'}
                            <ArrowRight />
                        </Link>
                    </Button>
                </section>
            </main>
        </>
    );
}
