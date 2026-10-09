import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    CheckCircle2,
    CircleAlert,
    FolderKanban,
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
    project_number: number;
    area_name: string;
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
        evaluation_score?: number | null;
        review_status?: string;
    } | null;
}

const statusLabels: Record<string, string> = {
    planned: 'Belum dimulai',
    in_progress: 'Sedang dikerjakan',
    submitted: 'Sedang diperiksa',
    needs_revision: 'Perlu diperbaiki',
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
            <Head title="Proyek" />

            <div className="neo-page py-7 md:py-9">
                <header className="border-b-2 border-foreground pb-6">
                    <div className="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div className="max-w-3xl">
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Proyek
                            </p>

                            <h1 className="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                                Tiga proyek untuk tiga bidang jurusan
                            </h1>

                            <p className="mt-3 max-w-2xl text-sm leading-7 font-medium text-muted-foreground">
                                Setiap jurusan memiliki tiga proyek.
                                Masing-masing proyek mewakili satu bidang dan
                                menggunakan tiga materi utama dari bidang
                                tersebut. Tugas setiap proyek berbeda sesuai
                                dengan bidangnya.
                            </p>
                        </div>

                        <div className="flex flex-wrap gap-2 text-xs font-black">
                            {programName && (
                                <span className="rounded-full border-2 border-foreground bg-card px-3 py-2">
                                    {programName}
                                </span>
                            )}

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-2">
                                {projects.length} proyek
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-2">
                                {activeProjects} dikerjakan
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-2">
                                {completedProjects} lulus
                            </span>
                        </div>
                    </div>
                </header>

                {projects.length === 0 ? (
                    <section className="neo-empty mt-6">
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
                    <section className="mt-6 grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                        {projects.map((project) => {
                            const catalog = getCatalog(project);

                            const started = Boolean(project.user_project);

                            const completed =
                                project.user_project?.status === 'completed';

                            const needsRevision =
                                project.user_project?.status ===
                                'needs_revision';

                            const recommendation =
                                project.readiness.recommendation;

                            const progress =
                                project.user_project?.progress_percentage ?? 0;

                            const status = project.user_project
                                ? (statusLabels[project.user_project.status] ??
                                  project.user_project.status)
                                : 'Belum dimulai';

                            return (
                                <Card
                                    key={project.id}
                                    className="flex h-full flex-col overflow-hidden"
                                >
                                    <CardHeader className="border-b-2 border-foreground p-5">
                                        <div className="flex items-start justify-between gap-4">
                                            <div>
                                                <p className="text-[10px] font-black tracking-[0.14em] text-muted-foreground uppercase">
                                                    Proyek{' '}
                                                    {project.project_number}
                                                </p>

                                                <p className="mt-1 text-sm font-black">
                                                    {project.area_name}
                                                </p>
                                            </div>

                                            <span
                                                className={`rounded-full border-2 border-[#171717] px-2.5 py-1 text-[10px] font-black text-[#171717] ${
                                                    completed
                                                        ? 'bg-[var(--neo-lime)]'
                                                        : needsRevision
                                                          ? 'bg-[var(--neo-pink)]'
                                                          : started
                                                            ? 'bg-[var(--neo-yellow)]'
                                                            : 'bg-[#fffdf7]'
                                                }`}
                                            >
                                                {status}
                                            </span>
                                        </div>

                                        <CardTitle className="mt-4 text-2xl leading-tight font-black tracking-tight">
                                            {project.title}
                                        </CardTitle>
                                    </CardHeader>

                                    <CardContent className="flex flex-1 flex-col gap-5 pt-5">
                                        <div>
                                            <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                                Yang akan dibuat
                                            </p>

                                            <p className="mt-2 text-sm leading-7 font-medium">
                                                {project.summary}
                                            </p>
                                        </div>

                                        <div>
                                            <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                                Tiga materi bidang ini
                                            </p>

                                            <div className="mt-2 flex flex-wrap gap-2">
                                                {catalog.skills.map((skill) => (
                                                    <span
                                                        key={skill}
                                                        className="rounded-full border-2 border-foreground/15 bg-muted/30 px-3 py-1 text-xs font-bold"
                                                    >
                                                        {skill}
                                                    </span>
                                                ))}
                                            </div>
                                        </div>

                                        {project.user_project
                                            ?.evaluation_score !== null &&
                                            project.user_project
                                                ?.evaluation_score !==
                                                undefined && (
                                                <div className="rounded-[10px] border-2 border-foreground/15 bg-card p-4">
                                                    <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                                        Nilai proyek
                                                    </p>

                                                    <p className="mt-1 text-xl font-black">
                                                        {
                                                            project.user_project
                                                                .evaluation_score
                                                        }
                                                        /100
                                                    </p>
                                                </div>
                                            )}

                                        <div className="rounded-[10px] border-2 border-foreground/15 bg-muted/20 p-4">
                                            <div className="flex items-start gap-2">
                                                {recommendation.level ===
                                                'recommended' ? (
                                                    <CheckCircle2 className="mt-0.5 size-4 shrink-0" />
                                                ) : (
                                                    <CircleAlert className="mt-0.5 size-4 shrink-0" />
                                                )}

                                                <div>
                                                    <p className="text-sm font-black">
                                                        {recommendation.label}
                                                    </p>

                                                    <p className="mt-1 text-xs leading-5 font-medium text-muted-foreground">
                                                        {recommendation.message}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        {started && (
                                            <div>
                                                <div className="mb-2 flex items-center justify-between text-xs font-black">
                                                    <span>Progres</span>

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

                                        <Button
                                            asChild
                                            variant={
                                                completed
                                                    ? 'outline'
                                                    : 'secondary'
                                            }
                                            className="mt-auto w-full"
                                        >
                                            <Link
                                                href={`/projects/${project.slug}`}
                                            >
                                                <FolderKanban className="size-4" />

                                                {completed
                                                    ? 'Lihat hasil proyek'
                                                    : needsRevision
                                                      ? 'Perbaiki proyek'
                                                      : started
                                                        ? 'Lanjutkan proyek'
                                                        : 'Lihat tugas proyek'}

                                                <ArrowRight className="size-4" />
                                            </Link>
                                        </Button>
                                    </CardContent>
                                </Card>
                            );
                        })}
                    </section>
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
