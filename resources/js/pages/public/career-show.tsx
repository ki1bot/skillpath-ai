import { Head, Link } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Gauge,
    GraduationCap,
    Target,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { getStudyProgramDefinition } from '@/lib/academic-programs';

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
    const program = getStudyProgramDefinition(career.name);

    const skillsByName = new Map(
        career.skills.map((skill) => [skill.name, skill]),
    );

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
                            Kembali ke pilihan jurusan
                        </Link>

                        <div className="mt-10 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                            <div>
                                <p className="text-xs font-black tracking-[0.18em] uppercase">
                                    Jurusan
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
                                                Kesiapan belajar berdasarkan
                                                hasil Assesment
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
                                        <GraduationCap className="size-5" />

                                        <p className="mt-3 text-xs font-black uppercase">
                                            Bidang utama
                                        </p>

                                        <p className="font-bold">
                                            {program?.areas.length ?? 3} bidang
                                        </p>
                                    </div>

                                    <div className="rounded-[11px] border-2 border-[#171717] p-3">
                                        <Target className="size-5" />

                                        <p className="mt-3 text-xs font-black uppercase">
                                            Kemampuan
                                        </p>

                                        <p className="font-bold">
                                            {career.skills.length} kemampuan
                                        </p>
                                    </div>
                                </div>

                                {career.compatibility &&
                                    career.compatibility.top_gaps.length >
                                        0 && (
                                        <div className="mt-5 border-t-2 border-[#171717] pt-4">
                                            <p className="text-xs font-black uppercase">
                                                Yang perlu diperkuat
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
                                                                selisih{' '}
                                                                {gap.gap}
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

                <section className="neo-page py-16 lg:py-20">
                    <div>
                        <p className="text-xs font-black tracking-[0.16em] text-muted-foreground uppercase">
                            Struktur kemampuan
                        </p>

                        <h2 className="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                            3 bidang utama dan 9 kemampuan yang dinilai.
                        </h2>

                        <p className="mt-4 max-w-3xl leading-7 font-medium text-muted-foreground">
                            Setiap bidang memiliki tiga kemampuan. Hasil
                            Assesment digunakan untuk melihat posisi kemampuanmu
                            sekarang dan menentukan bagian yang masih perlu
                            dikembangkan.
                        </p>
                    </div>

                    {program ? (
                        <div className="mt-8 space-y-7">
                            {program.areas.map((area, areaIndex) => (
                                <section
                                    key={area.name}
                                    className="neo-card p-5 sm:p-7"
                                >
                                    <div className="flex items-start gap-3">
                                        <span className="flex size-9 shrink-0 items-center justify-center rounded-[10px] border-2 border-foreground bg-secondary font-mono text-sm font-black text-[#171717]">
                                            {areaIndex + 1}
                                        </span>

                                        <div>
                                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                                Bidang {areaIndex + 1}
                                            </p>

                                            <h3 className="mt-1 text-2xl font-black">
                                                {area.name}
                                            </h3>
                                        </div>
                                    </div>

                                    <div className="mt-6 grid gap-4 lg:grid-cols-3">
                                        {area.skills.map((skillName) => {
                                            const skill =
                                                skillsByName.get(skillName);

                                            return (
                                                <article
                                                    key={skillName}
                                                    className="neo-card-flat p-5"
                                                >
                                                    <div className="flex items-start justify-between gap-3">
                                                        <CheckCircle2 className="size-5 shrink-0" />

                                                        {skill && (
                                                            <span className="font-mono text-xs font-black">
                                                                Target{' '}
                                                                {
                                                                    skill.pivot
                                                                        .target_level
                                                                }
                                                                /100
                                                            </span>
                                                        )}
                                                    </div>

                                                    <h4 className="mt-5 text-lg font-black">
                                                        {skillName}
                                                    </h4>

                                                    {skill?.description && (
                                                        <p className="mt-2 text-sm leading-relaxed font-medium text-muted-foreground">
                                                            {skill.description}
                                                        </p>
                                                    )}
                                                </article>
                                            );
                                        })}
                                    </div>
                                </section>
                            ))}
                        </div>
                    ) : (
                        <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            {career.skills.map((skill) => (
                                <article
                                    key={skill.id}
                                    className="neo-card-flat p-5"
                                >
                                    <h3 className="text-lg font-black">
                                        {skill.name}
                                    </h3>

                                    <p className="mt-2 text-sm leading-relaxed font-medium text-muted-foreground">
                                        {skill.description}
                                    </p>
                                </article>
                            ))}
                        </div>
                    )}
                </section>

                <section className="border-y-2 border-[#171717] bg-[var(--neo-yellow)] py-16 text-[#171717]">
                    <div className="neo-page">
                        <h2 className="text-3xl font-black tracking-tight">
                            Proyek untuk menerapkan kemampuan
                        </h2>

                        <p className="mt-3 max-w-3xl text-sm leading-6 font-semibold">
                            Proyek digunakan untuk menerapkan kemampuan yang
                            sudah dipelajari. Proyek bukan pengganti Assesment,
                            tetapi menjadi tempat untuk mempraktikkan kemampuan
                            dalam bentuk yang lebih nyata.
                        </p>

                        {career.projects.length > 0 ? (
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
                        ) : (
                            <div className="mt-7 rounded-[14px] border-2 border-[#171717] bg-[#fffdf7] p-6 font-semibold shadow-[4px_4px_0_#171717]">
                                Proyek khusus untuk jurusan ini belum tersedia.
                            </div>
                        )}
                    </div>
                </section>

                <section className="mx-auto max-w-4xl px-4 py-20 text-center sm:px-6 lg:px-8">
                    <p className="text-xs font-black tracking-[0.18em] text-muted-foreground uppercase">
                        {career.compatibility
                            ? 'Kemampuanmu bisa terus berkembang'
                            : 'Belum tahu posisi kemampuanmu?'}
                    </p>

                    <h2 className="mt-3 text-4xl font-black tracking-[-0.045em]">
                        {career.compatibility
                            ? 'Ulangi Assesment setelah belajar untuk melihat perkembangan kemampuanmu.'
                            : 'Kerjakan Assesment untuk melihat kemampuan yang sudah kuat dan yang masih perlu dipelajari.'}
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
