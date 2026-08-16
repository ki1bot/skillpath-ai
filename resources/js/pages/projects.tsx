import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    CheckCircle2,
    CircleAlert,
    CircleDashed,
    Clock3,
    FolderKanban,
    Gauge,
    ShieldAlert,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type RecommendationLevel = 'recommended' | 'strengthen' | 'challenge';

interface Requirement {
    skill_id: number;
    name: string;
    current: number;
    required: number;
    gap: number;
    weight: number;
    ready: boolean;
    percentage: number;
}

interface Recommendation {
    level: RecommendationLevel;
    rank: number;
    label: string;
    message: string;
}

interface Project {
    id: number;
    title: string;
    slug: string;
    summary: string;
    difficulty: string;
    estimated_hours: number;
    readiness: {
        score: number;
        ready: boolean;
        missing_count: number;
        requirements: Requirement[];
        top_gaps: Requirement[];
        recommendation: Recommendation;
    };
    user_project?: {
        status: string;
        progress_percentage: number;
    } | null;
}

const sections: {
    level: RecommendationLevel;
    title: string;
    description: string;
}[] = [
    {
        level: 'recommended',
        title: 'Direkomendasikan sekarang',
        description:
            'Seluruh prasyarat minimum sudah terpenuhi. Ini adalah pilihan paling rasional untuk kondisi kemampuanmu saat ini.',
    },
    {
        level: 'strengthen',
        title: 'Perlu penguatan',
        description:
            'Kesiapan sudah cukup dekat, tetapi masih ada beberapa skill yang sebaiknya diperkuat sebelum proyek menjadi rekomendasi utama.',
    },
    {
        level: 'challenge',
        title: 'Challenge',
        description:
            'Gap masih cukup besar. Proyek tetap boleh dimulai, tetapi risiko hambatan dan waktu pengerjaannya lebih tinggi.',
    },
];

const recommendationClasses: Record<RecommendationLevel, string> = {
    recommended: 'bg-[var(--neo-lime)]',
    strengthen: 'bg-[var(--neo-yellow)]',
    challenge: 'bg-[var(--neo-orange)]',
};

export default function Projects({ projects }: { projects: Project[] }) {
    return (
        <>
            <Head title="Proyek" />

            <div className="neo-page flex flex-1 flex-col gap-6 py-6 sm:py-8 lg:gap-8 lg:py-10">
                <section className="overflow-hidden rounded-[18px] border-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717] shadow-[6px_6px_0_var(--neo-shadow-color)]">
                    <div className="grid gap-7 p-5 sm:p-7 lg:grid-cols-[minmax(0,1fr)_320px] lg:items-end lg:p-8">
                        <div className="max-w-3xl">
                            <span className="inline-flex items-center rounded-full border-2 border-[#171717] bg-[#fffdf7] px-3 py-1.5 text-[10px] font-black tracking-[0.12em] text-[#171717] uppercase">
                                Ruang portofolio
                            </span>

                            <h1 className="mt-5 max-w-3xl text-4xl leading-[0.95] font-black tracking-[-0.045em] sm:text-5xl">
                                Bangun bukti, bukan sekadar mencentang daftar.
                            </h1>

                            <p className="mt-4 max-w-2xl text-sm leading-7 font-semibold text-[#171717]/75 sm:text-base">
                                Proyek sekarang dipisahkan berdasarkan kesiapan
                                aktual. SkillPath membedakan proyek yang sudah
                                layak dikerjakan, proyek yang masih membutuhkan
                                penguatan, dan proyek yang sengaja dipilih
                                sebagai challenge.
                            </p>
                        </div>

                        <div className="rounded-[14px] border-2 border-[#171717] bg-[#fffdf7] p-5 text-[#171717] shadow-[4px_4px_0_#171717]">
                            <div className="flex items-center gap-3">
                                <span className="flex size-10 shrink-0 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-yellow)]">
                                    <Gauge className="size-5" />
                                </span>

                                <div>
                                    <p className="text-[10px] font-black tracking-[0.14em] uppercase">
                                        Cara membaca kesiapan
                                    </p>

                                    <p className="mt-1 text-sm font-black">
                                        Berbobot 0–100
                                    </p>
                                </div>
                            </div>

                            <p className="mt-4 text-sm leading-6 font-semibold text-[#171717]/70">
                                Setiap skill proyek memiliki level minimum dan
                                bobot. Nilai 100 berarti seluruh prasyarat
                                minimum sudah terpenuhi, bukan berarti proyek
                                akan mudah.
                            </p>
                        </div>
                    </div>
                </section>

                {projects.length === 0 ? (
                    <section className="neo-empty">
                        <FolderKanban className="size-9" />

                        <h2 className="mt-4 text-xl font-black">
                            Belum ada proyek yang tersedia
                        </h2>

                        <p className="mt-2 max-w-lg text-sm leading-6 font-medium text-muted-foreground">
                            Rekomendasi proyek akan muncul setelah data proyek
                            dan kebutuhan kemampuannya tersedia.
                        </p>
                    </section>
                ) : (
                    <div className="grid gap-8">
                        {sections.map((section) => {
                            const sectionProjects = projects.filter(
                                (project) =>
                                    project.readiness.recommendation.level ===
                                    section.level,
                            );

                            if (sectionProjects.length === 0) {
                                return null;
                            }

                            return (
                                <section key={section.level}>
                                    <div className="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                                        <div>
                                            <div className="flex items-center gap-2">
                                                {section.level ===
                                                'recommended' ? (
                                                    <CheckCircle2 className="size-5" />
                                                ) : section.level ===
                                                  'challenge' ? (
                                                    <ShieldAlert className="size-5" />
                                                ) : (
                                                    <CircleAlert className="size-5" />
                                                )}

                                                <h2 className="text-2xl font-black tracking-tight">
                                                    {section.title}
                                                </h2>
                                            </div>

                                            <p className="mt-2 max-w-3xl text-sm leading-6 font-medium text-muted-foreground">
                                                {section.description}
                                            </p>
                                        </div>

                                        <span className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                            {sectionProjects.length} proyek
                                        </span>
                                    </div>

                                    <div className="grid items-stretch gap-5 xl:grid-cols-2">
                                        {sectionProjects.map((project) => {
                                            const started = Boolean(
                                                project.user_project,
                                            );
                                            const visibleRequirements =
                                                project.readiness.requirements.slice(
                                                    0,
                                                    4,
                                                );
                                            const hiddenRequirements =
                                                project.readiness.requirements
                                                    .length -
                                                visibleRequirements.length;
                                            const recommendation =
                                                project.readiness
                                                    .recommendation;

                                            return (
                                                <Card
                                                    key={project.id}
                                                    className="group h-full overflow-hidden"
                                                >
                                                    <CardHeader className="border-b-2 border-[#171717] bg-[#fffdf7] p-5 text-[#171717] sm:p-6">
                                                        <div className="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                                                            <div className="min-w-0 flex-1">
                                                                <div className="flex flex-wrap items-center gap-2">
                                                                    <span
                                                                        className={`inline-flex items-center rounded-full border-2 border-[#171717] px-3 py-1 text-[10px] font-black tracking-wide text-[#171717] uppercase ${recommendationClasses[recommendation.level]}`}
                                                                    >
                                                                        {
                                                                            recommendation.label
                                                                        }
                                                                    </span>

                                                                    <span className="inline-flex items-center rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] px-3 py-1 text-[10px] font-black tracking-wide text-[#171717] uppercase">
                                                                        {
                                                                            project.difficulty
                                                                        }
                                                                    </span>

                                                                    <span className="inline-flex items-center gap-1.5 rounded-full border-2 border-[#171717] bg-[#fffdf7] px-3 py-1 text-[10px] font-black tracking-wide text-[#171717] uppercase">
                                                                        <Clock3 className="size-3" />
                                                                        ±{' '}
                                                                        {
                                                                            project.estimated_hours
                                                                        }{' '}
                                                                        jam
                                                                    </span>

                                                                    {started && (
                                                                        <span className="inline-flex items-center rounded-full border-2 border-[#171717] bg-[var(--neo-lime)] px-3 py-1 text-[10px] font-black tracking-wide text-[#171717] uppercase">
                                                                            Sudah
                                                                            dimulai
                                                                        </span>
                                                                    )}
                                                                </div>

                                                                <CardTitle className="mt-4 max-w-xl text-2xl leading-tight font-black tracking-[-0.035em] text-[#171717] sm:text-[1.7rem]">
                                                                    {
                                                                        project.title
                                                                    }
                                                                </CardTitle>
                                                            </div>

                                                            <div
                                                                className={`flex min-w-24 shrink-0 items-center gap-3 rounded-[12px] border-2 border-[#171717] px-3 py-3 text-[#171717] shadow-[3px_3px_0_#171717] ${recommendationClasses[recommendation.level]}`}
                                                            >
                                                                <Gauge className="size-5 shrink-0" />

                                                                <div>
                                                                    <p className="text-[9px] font-black tracking-[0.1em] uppercase">
                                                                        Kesiapan
                                                                    </p>

                                                                    <p className="text-xl leading-none font-black">
                                                                        {Math.round(
                                                                            project
                                                                                .readiness
                                                                                .score,
                                                                        )}
                                                                        %
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </CardHeader>

                                                    <CardContent className="flex flex-1 flex-col gap-5 pt-5 sm:pt-6">
                                                        <p className="text-sm leading-7 font-medium text-card-foreground/85">
                                                            {project.summary}
                                                        </p>

                                                        <div
                                                            className={`rounded-[12px] border-2 border-foreground p-4 text-sm leading-6 font-semibold ${recommendationClasses[recommendation.level]} text-[#171717]`}
                                                        >
                                                            {
                                                                recommendation.message
                                                            }
                                                        </div>

                                                        <div>
                                                            <div className="mb-3 flex flex-wrap items-center justify-between gap-2">
                                                                <p className="text-xs font-black tracking-[0.12em] uppercase">
                                                                    Kemampuan
                                                                    yang
                                                                    dibutuhkan
                                                                </p>

                                                                <span className="text-[10px] font-black tracking-wide text-muted-foreground uppercase">
                                                                    {
                                                                        project
                                                                            .readiness
                                                                            .missing_count
                                                                    }{' '}
                                                                    gap
                                                                </span>
                                                            </div>

                                                            <div className="grid gap-2">
                                                                {visibleRequirements.map(
                                                                    (
                                                                        requirement,
                                                                    ) => (
                                                                        <div
                                                                            key={
                                                                                requirement.skill_id
                                                                            }
                                                                            className="flex min-w-0 items-center justify-between gap-3 rounded-[10px] border-2 border-foreground/15 bg-muted/40 px-3 py-2.5"
                                                                        >
                                                                            <div className="flex min-w-0 items-center gap-2.5">
                                                                                {requirement.ready ? (
                                                                                    <CheckCircle2 className="size-4 shrink-0 text-foreground" />
                                                                                ) : (
                                                                                    <CircleDashed className="size-4 shrink-0 text-muted-foreground" />
                                                                                )}

                                                                                <span className="min-w-0 truncate text-sm font-bold">
                                                                                    {
                                                                                        requirement.name
                                                                                    }
                                                                                </span>
                                                                            </div>

                                                                            <span className="shrink-0 rounded-md border border-foreground/20 bg-background px-2 py-1 font-mono text-[10px] font-black">
                                                                                {
                                                                                    requirement.current
                                                                                }

                                                                                /
                                                                                {
                                                                                    requirement.required
                                                                                }
                                                                            </span>
                                                                        </div>
                                                                    ),
                                                                )}

                                                                {hiddenRequirements >
                                                                    0 && (
                                                                    <p className="pt-1 text-xs font-bold text-muted-foreground">
                                                                        +
                                                                        {
                                                                            hiddenRequirements
                                                                        }{' '}
                                                                        kemampuan
                                                                        lainnya
                                                                    </p>
                                                                )}
                                                            </div>
                                                        </div>

                                                        {started && (
                                                            <div className="rounded-[12px] border-2 border-foreground/15 bg-muted/30 p-4">
                                                                <div className="mb-2 flex items-center justify-between gap-3 text-xs font-black tracking-wide uppercase">
                                                                    <span>
                                                                        Perkembangan
                                                                        proyek
                                                                    </span>

                                                                    <span>
                                                                        {project
                                                                            .user_project
                                                                            ?.progress_percentage ??
                                                                            0}
                                                                        %
                                                                    </span>
                                                                </div>

                                                                <div className="neo-progress">
                                                                    <span
                                                                        style={{
                                                                            width: `${project.user_project?.progress_percentage ?? 0}%`,
                                                                        }}
                                                                    />
                                                                </div>
                                                            </div>
                                                        )}

                                                        <div className="mt-auto pt-1">
                                                            <Button
                                                                asChild
                                                                variant={
                                                                    recommendation.level ===
                                                                    'recommended'
                                                                        ? 'secondary'
                                                                        : 'outline'
                                                                }
                                                                className="w-full sm:w-auto"
                                                            >
                                                                <Link
                                                                    href={`/projects/${project.slug}`}
                                                                >
                                                                    {recommendation.level ===
                                                                    'recommended' ? (
                                                                        <FolderKanban className="size-4" />
                                                                    ) : (
                                                                        <CircleAlert className="size-4" />
                                                                    )}
                                                                    Lihat detail
                                                                    proyek
                                                                    <ArrowRight className="size-4" />
                                                                </Link>
                                                            </Button>
                                                        </div>
                                                    </CardContent>
                                                </Card>
                                            );
                                        })}
                                    </div>
                                </section>
                            );
                        })}
                    </div>
                )}
            </div>
        </>
    );
}

Projects.layout = {
    breadcrumbs: [
        {
            title: 'Proyek',
            href: '/projects',
        },
    ],
};
