import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    CheckCircle2,
    CircleAlert,
    Clock3,
    FolderKanban,
    Gauge,
    ListChecks,
    Upload,
    Workflow,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { getProjectCatalogEntry } from '@/lib/project-catalog';

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

const recommendationClasses: Record<RecommendationLevel, string> = {
    recommended: 'bg-[var(--neo-lime)]',
    strengthen: 'bg-[var(--neo-yellow)]',
    challenge: 'bg-[var(--neo-orange)]',
};

const statusLabels: Record<string, string> = {
    in_progress: 'Sedang dikerjakan',
    completed: 'Selesai',
};

function getCatalog(project: Project) {
    return getProjectCatalogEntry(
        project.slug,
        project.readiness.requirements.map((requirement) => requirement.name),
    );
}

export default function Projects({ projects }: { projects: Project[] }) {
    const programName =
        projects.length > 0 ? getCatalog(projects[0]).program : null;

    const completedProjects = projects.filter(
        (project) => project.user_project?.status === 'completed',
    ).length;

    const activeProjects = projects.filter(
        (project) =>
            project.user_project && project.user_project.status !== 'completed',
    ).length;

    return (
        <>
            <Head title="Proyek / Tugas Akhir" />

            <div className="neo-page flex flex-1 flex-col gap-6 py-6 sm:py-8 lg:gap-8 lg:py-10">
                <section className="overflow-hidden rounded-[18px] border-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717] shadow-[6px_6px_0_var(--neo-shadow-color)]">
                    <div className="grid gap-7 p-5 sm:p-7 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-end lg:p-8">
                        <div className="max-w-3xl">
                            <span className="inline-flex items-center rounded-full border-2 border-[#171717] bg-[#fffdf7] px-3 py-1.5 text-[10px] font-black tracking-[0.12em] uppercase">
                                Proyek / Tugas Akhir
                            </span>

                            <h1 className="mt-5 max-w-3xl text-4xl leading-[0.95] font-black tracking-[-0.045em] sm:text-5xl">
                                Pilih proyek yang paling sesuai dengan bidang
                                yang ingin kamu kuasai.
                            </h1>

                            <p className="mt-4 max-w-2xl text-sm leading-7 font-semibold text-[#171717]/75 sm:text-base">
                                Setiap proyek mewakili satu bidang utama di
                                jurusanmu. Baca tujuan proyek, pahami kemampuan
                                yang dilatih, kerjakan bagian wajib, lalu
                                kumpulkan hasil akhirnya melalui Google Drive.
                            </p>

                            {programName && (
                                <div className="mt-5 inline-flex items-center gap-2 rounded-[10px] border-2 border-[#171717] bg-[#fffdf7] px-3 py-2 text-sm font-black">
                                    <FolderKanban className="size-4" />
                                    Jurusan: {programName}
                                </div>
                            )}
                        </div>

                        <div className="rounded-[14px] border-2 border-[#171717] bg-[#fffdf7] p-5 text-[#171717] shadow-[4px_4px_0_#171717]">
                            <p className="text-[10px] font-black tracking-[0.14em] uppercase">
                                Ringkasan progres
                            </p>

                            <div className="mt-4 grid grid-cols-3 gap-3">
                                <div>
                                    <p className="text-2xl font-black">
                                        {projects.length}
                                    </p>

                                    <p className="mt-1 text-[10px] font-black tracking-wide uppercase">
                                        Pilihan
                                    </p>
                                </div>

                                <div>
                                    <p className="text-2xl font-black">
                                        {activeProjects}
                                    </p>

                                    <p className="mt-1 text-[10px] font-black tracking-wide uppercase">
                                        Dikerjakan
                                    </p>
                                </div>

                                <div>
                                    <p className="text-2xl font-black">
                                        {completedProjects}
                                    </p>

                                    <p className="mt-1 text-[10px] font-black tracking-wide uppercase">
                                        Selesai
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="grid gap-4 md:grid-cols-3">
                    <div className="neo-card p-5">
                        <div className="flex items-start gap-3">
                            <span className="flex size-10 shrink-0 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-yellow)] text-[#171717]">
                                <span className="font-black">1</span>
                            </span>

                            <div>
                                <div className="flex items-center gap-2">
                                    <FolderKanban className="size-4" />

                                    <h2 className="font-black">
                                        Pilih bidang proyek
                                    </h2>
                                </div>

                                <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                    Pilih berdasarkan minat dan kemampuan yang
                                    ingin kamu kembangkan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="neo-card p-5">
                        <div className="flex items-start gap-3">
                            <span className="flex size-10 shrink-0 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-lime)] text-[#171717]">
                                <span className="font-black">2</span>
                            </span>

                            <div>
                                <div className="flex items-center gap-2">
                                    <ListChecks className="size-4" />

                                    <h2 className="font-black">
                                        Kerjakan checklist
                                    </h2>
                                </div>

                                <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                    Halaman detail menjelaskan tugas wajib,
                                    kriteria selesai, dan kemampuan yang
                                    digunakan.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="neo-card p-5">
                        <div className="flex items-start gap-3">
                            <span className="flex size-10 shrink-0 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-orange)] text-[#171717]">
                                <span className="font-black">3</span>
                            </span>

                            <div>
                                <div className="flex items-center gap-2">
                                    <Upload className="size-4" />

                                    <h2 className="font-black">
                                        Kumpulkan lewat Drive
                                    </h2>
                                </div>

                                <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                    Upload hasil atau dokumentasi ke Google
                                    Drive, lalu tempel tautannya di halaman
                                    proyek.
                                </p>
                            </div>
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
                            Proyek akan muncul setelah jurusan dan katalog
                            proyek untuk akunmu tersedia.
                        </p>
                    </section>
                ) : (
                    <section>
                        <div className="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <div className="flex items-center gap-2">
                                    <Workflow className="size-5" />

                                    <h2 className="text-2xl font-black tracking-tight">
                                        Pilihan bidang proyek
                                    </h2>
                                </div>

                                <p className="mt-2 max-w-3xl text-sm leading-6 font-medium text-muted-foreground">
                                    Kamu tidak harus mengerjakan semuanya
                                    sekaligus. Buka satu proyek, baca tugasnya
                                    sampai jelas, lalu tentukan apakah proyek
                                    tersebut ingin kamu mulai.
                                </p>
                            </div>

                            <span className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                {projects.length} pilihan
                            </span>
                        </div>

                        <div className="grid items-stretch gap-5 xl:grid-cols-3">
                            {projects.map((project, index) => {
                                const catalog = getCatalog(project);

                                const started = Boolean(project.user_project);

                                const completed =
                                    project.user_project?.status ===
                                    'completed';

                                const recommendation =
                                    project.readiness.recommendation;

                                const progress =
                                    project.user_project?.progress_percentage ??
                                    0;

                                return (
                                    <Card
                                        key={project.id}
                                        className="group flex h-full flex-col overflow-hidden"
                                    >
                                        <CardHeader className="border-b-2 border-[#171717] bg-[#fffdf7] p-5 text-[#171717]">
                                            <div className="flex items-start justify-between gap-4">
                                                <div className="min-w-0">
                                                    <span className="text-[10px] font-black tracking-[0.16em] uppercase">
                                                        Pilihan{' '}
                                                        {String(
                                                            index + 1,
                                                        ).padStart(2, '0')}
                                                    </span>

                                                    <p className="mt-2 text-sm font-black text-[#171717]/70">
                                                        {catalog.focus}
                                                    </p>
                                                </div>

                                                <span
                                                    className={`shrink-0 rounded-full border-2 border-[#171717] px-2.5 py-1 text-[9px] font-black tracking-wide uppercase ${
                                                        completed
                                                            ? 'bg-[var(--neo-lime)]'
                                                            : started
                                                              ? 'bg-[var(--neo-yellow)]'
                                                              : 'bg-[#fffdf7]'
                                                    }`}
                                                >
                                                    {completed
                                                        ? 'Selesai'
                                                        : started
                                                          ? (statusLabels[
                                                                project
                                                                    .user_project
                                                                    ?.status ??
                                                                    ''
                                                            ] ??
                                                            'Sedang dikerjakan')
                                                          : 'Belum dimulai'}
                                                </span>
                                            </div>

                                            <CardTitle className="mt-4 text-2xl leading-tight font-black tracking-[-0.035em] text-[#171717]">
                                                {project.title}
                                            </CardTitle>
                                        </CardHeader>

                                        <CardContent className="flex flex-1 flex-col gap-5 pt-5">
                                            <div>
                                                <p className="text-[10px] font-black tracking-[0.12em] uppercase">
                                                    Apa yang akan kamu buat
                                                </p>

                                                <p className="mt-2 text-sm leading-7 font-medium text-card-foreground/85">
                                                    {project.summary}
                                                </p>
                                            </div>

                                            <div>
                                                <p className="text-[10px] font-black tracking-[0.12em] uppercase">
                                                    Kemampuan utama
                                                </p>

                                                <div className="mt-3 flex flex-wrap gap-2">
                                                    {catalog.skills.map(
                                                        (skill) => (
                                                            <span
                                                                key={skill}
                                                                className="rounded-full border-2 border-foreground/15 bg-muted/40 px-3 py-1.5 text-[11px] font-bold"
                                                            >
                                                                {skill}
                                                            </span>
                                                        ),
                                                    )}
                                                </div>
                                            </div>

                                            <div className="grid grid-cols-2 gap-3">
                                                <div className="rounded-[10px] border-2 border-foreground/15 bg-muted/30 p-3">
                                                    <div className="flex items-center gap-2 text-[10px] font-black tracking-wide uppercase">
                                                        <Clock3 className="size-3.5" />
                                                        Estimasi
                                                    </div>

                                                    <p className="mt-2 text-sm font-black">
                                                        ±{' '}
                                                        {
                                                            project.estimated_hours
                                                        }{' '}
                                                        jam
                                                    </p>
                                                </div>

                                                <div
                                                    className={`rounded-[10px] border-2 border-foreground p-3 text-[#171717] ${
                                                        recommendationClasses[
                                                            recommendation.level
                                                        ]
                                                    }`}
                                                >
                                                    <div className="flex items-center gap-2 text-[10px] font-black tracking-wide uppercase">
                                                        <Gauge className="size-3.5" />
                                                        Kesiapan
                                                    </div>

                                                    <p className="mt-2 text-sm font-black">
                                                        {Math.round(
                                                            project.readiness
                                                                .score,
                                                        )}
                                                        %
                                                    </p>
                                                </div>
                                            </div>

                                            <div className="rounded-[10px] border-2 border-foreground/15 bg-muted/30 p-4">
                                                <div className="flex items-start gap-2">
                                                    {recommendation.level ===
                                                    'recommended' ? (
                                                        <CheckCircle2 className="mt-0.5 size-4 shrink-0" />
                                                    ) : (
                                                        <CircleAlert className="mt-0.5 size-4 shrink-0" />
                                                    )}

                                                    <div>
                                                        <p className="text-xs font-black">
                                                            Saran saat ini:{' '}
                                                            {
                                                                recommendation.label
                                                            }
                                                        </p>

                                                        <p className="mt-1 text-xs leading-5 font-medium text-muted-foreground">
                                                            {
                                                                recommendation.message
                                                            }
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            {started && (
                                                <div>
                                                    <div className="mb-2 flex items-center justify-between gap-3 text-xs font-black tracking-wide uppercase">
                                                        <span>
                                                            Progres proyek
                                                        </span>

                                                        <span>{progress}%</span>
                                                    </div>

                                                    <div className="neo-progress">
                                                        <span
                                                            style={{
                                                                width: `${progress}%`,
                                                            }}
                                                        />
                                                    </div>
                                                </div>
                                            )}

                                            <div className="mt-auto pt-1">
                                                <Button
                                                    asChild
                                                    variant={
                                                        completed
                                                            ? 'outline'
                                                            : 'secondary'
                                                    }
                                                    className="w-full"
                                                >
                                                    <Link
                                                        href={`/projects/${project.slug}`}
                                                    >
                                                        <FolderKanban className="size-4" />

                                                        {completed
                                                            ? 'Lihat tugas dan hasil'
                                                            : started
                                                              ? 'Lanjutkan tugas'
                                                              : 'Buka tugas'}

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
                )}
            </div>
        </>
    );
}

Projects.layout = {
    breadcrumbs: [
        {
            title: 'Proyek / Tugas Akhir',
            href: '/projects',
        },
    ],
};
