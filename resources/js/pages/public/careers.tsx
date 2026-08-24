import { Head, Link } from '@inertiajs/react';
import { ArrowRight, CheckCircle2, Gauge, GraduationCap } from 'lucide-react';
import { getStudyProgramDefinition } from '@/lib/academic-programs';

type Skill = {
    id: number;
    name: string;
    category: string;
    pivot: {
        target_level: number;
        importance_weight: string;
        is_required: boolean;
    };
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
        priority: number;
    }[];
};

type Career = {
    id: number;
    name: string;
    slug: string;
    tagline: string;
    description: string;
    difficulty: string;
    accent: string;
    skills: Skill[];
    compatibility?: Compatibility | null;
};

export default function Careers({ careers }: { careers: Career[] }) {
    const areaCount = careers.reduce((total, career) => {
        const program = getStudyProgramDefinition(career.name);

        return total + (program?.areas.length ?? 0);
    }, 0);

    const skillCount = careers.reduce(
        (total, career) => total + career.skills.length,
        0,
    );

    return (
        <>
            <Head title="Pilihan Jurusan" />

            <main className="neo-page py-14 lg:py-20">
                <div className="max-w-4xl">
                    <span className="neo-label">
                        {careers.length} jurusan · {areaCount} bidang ·{' '}
                        {skillCount} kemampuan
                    </span>

                    <h1 className="neo-heading mt-6 text-5xl sm:text-6xl">
                        Kenali bidang dan kemampuan yang dipelajari di setiap
                        jurusan.
                    </h1>

                    <p className="mt-5 text-lg leading-relaxed font-medium text-muted-foreground">
                        Setiap jurusan memiliki tiga bidang utama. Di dalam
                        setiap bidang ada tiga kemampuan yang digunakan
                        SkillPath sebagai dasar assesment dan penyusunan jalur
                        belajar.
                    </p>
                </div>

                <div className="mt-12 space-y-7">
                    {careers.map((career, index) => {
                        const program = getStudyProgramDefinition(career.name);

                        return (
                            <article
                                key={career.id}
                                className="neo-card overflow-hidden"
                            >
                                <div className="grid lg:grid-cols-[210px_1fr]">
                                    <div
                                        className="flex min-h-44 flex-col justify-between border-b-2 border-[#171717] p-6 text-[#171717] lg:border-r-2 lg:border-b-0"
                                        style={{
                                            backgroundColor: career.accent,
                                        }}
                                    >
                                        <div>
                                            <span className="font-mono text-sm font-black">
                                                {String(index + 1).padStart(
                                                    2,
                                                    '0',
                                                )}
                                            </span>

                                            <GraduationCap className="mt-6 size-8" />
                                        </div>

                                        {career.compatibility ? (
                                            <div>
                                                <div className="flex items-center gap-2">
                                                    <Gauge className="size-5" />

                                                    <p className="text-xs font-black tracking-[0.14em] uppercase">
                                                        Kesiapan belajar
                                                    </p>
                                                </div>

                                                <p className="mt-1 font-mono text-3xl font-black">
                                                    {career.compatibility.score}
                                                    %
                                                </p>

                                                <p className="mt-1 text-xs font-bold">
                                                    {career.compatibility.label}
                                                </p>
                                            </div>
                                        ) : (
                                            <div>
                                                <p className="text-xs font-black tracking-[0.14em] uppercase">
                                                    Struktur
                                                </p>

                                                <p className="mt-1 text-xl font-black">
                                                    3 bidang
                                                </p>

                                                <p className="mt-1 text-xs font-bold">
                                                    9 kemampuan
                                                </p>
                                            </div>
                                        )}
                                    </div>

                                    <div className="p-6 sm:p-8">
                                        <div className="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                                            <div className="max-w-3xl">
                                                <h2 className="text-3xl font-black tracking-[-0.035em]">
                                                    {career.name}
                                                </h2>

                                                <p className="mt-2 leading-relaxed font-bold">
                                                    {career.tagline}
                                                </p>

                                                <p className="mt-4 text-sm leading-relaxed font-medium text-muted-foreground">
                                                    {career.description}
                                                </p>
                                            </div>

                                            <Link
                                                href={`/karier/${career.slug}`}
                                                className="inline-flex shrink-0 items-center gap-2 border-b-2 border-foreground pb-1 text-sm font-black"
                                            >
                                                Lihat detail
                                                <ArrowRight className="size-4" />
                                            </Link>
                                        </div>

                                        {program && (
                                            <div className="mt-7 grid gap-4 xl:grid-cols-3">
                                                {program.areas.map(
                                                    (area, areaIndex) => (
                                                        <section
                                                            key={area.name}
                                                            className="rounded-[12px] border-2 border-foreground bg-muted p-4"
                                                        >
                                                            <div className="flex items-start gap-3">
                                                                <span className="flex size-7 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-background text-xs font-black">
                                                                    {areaIndex +
                                                                        1}
                                                                </span>

                                                                <h3 className="leading-5 font-black">
                                                                    {area.name}
                                                                </h3>
                                                            </div>

                                                            <ul className="mt-4 space-y-2">
                                                                {area.skills.map(
                                                                    (skill) => (
                                                                        <li
                                                                            key={
                                                                                skill
                                                                            }
                                                                            className="flex gap-2 text-xs leading-5 font-semibold"
                                                                        >
                                                                            <CheckCircle2 className="mt-0.5 size-3.5 shrink-0" />
                                                                            <span>
                                                                                {
                                                                                    skill
                                                                                }
                                                                            </span>
                                                                        </li>
                                                                    ),
                                                                )}
                                                            </ul>
                                                        </section>
                                                    ),
                                                )}
                                            </div>
                                        )}

                                        {career.compatibility &&
                                            career.compatibility.top_gaps
                                                .length > 0 && (
                                                <div className="mt-5 rounded-[12px] border-2 border-foreground bg-[var(--neo-yellow)] p-4 text-[#171717]">
                                                    <p className="text-xs font-black tracking-wide uppercase">
                                                        Yang paling perlu
                                                        diperkuat
                                                    </p>

                                                    <p className="mt-2 text-sm font-semibold">
                                                        {career.compatibility.top_gaps
                                                            .map(
                                                                (gap) =>
                                                                    gap.name,
                                                            )
                                                            .join(' · ')}
                                                    </p>
                                                </div>
                                            )}
                                    </div>
                                </div>
                            </article>
                        );
                    })}
                </div>
            </main>
        </>
    );
}
