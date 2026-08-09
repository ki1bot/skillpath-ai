import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    CheckCircle2,
    CircleDashed,
    FolderKanban,
    LockKeyhole,
} from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Requirement {
    skill_id: number;
    name: string;
    current: number;
    required: number;
    ready: boolean;
    percentage: number;
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
        requirements: Requirement[];
    };
    user_project?: {
        status: string;
        progress_percentage: number;
    } | null;
}

export default function Projects({ projects }: { projects: Project[] }) {
    return (
        <>
            <Head title="Projects" />

            <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-8 p-4 md:p-8">
                <section className="neo-card bg-[var(--neo-blue)] p-6 md:p-8">
                    <div className="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                        <div className="max-w-2xl">
                            <span className="neo-label bg-white">
                                Portfolio lab
                            </span>

                            <h1 className="neo-heading mt-4 text-4xl md:text-5xl">
                                Bangun bukti, bukan cuma checklist.
                            </h1>

                            <p className="mt-4 max-w-xl text-sm leading-6 font-medium md:text-base">
                                Proyek dibuka berdasarkan skill yang sudah Anda
                                kuasai. Anda tetap boleh mengambil proyek yang
                                menantang, tetapi sistem akan menunjukkan bagian
                                yang perlu diperkuat lebih dulu.
                            </p>
                        </div>

                        <div className="neo-card-flat w-full max-w-xs bg-white p-4">
                            <p className="text-xs font-black tracking-[0.18em] uppercase">
                                Cara membaca readiness
                            </p>
                            <p className="mt-2 text-sm leading-6">
                                100% bukan berarti proyek pasti mudah. Artinya
                                seluruh prasyarat minimum proyek sudah
                                terpenuhi.
                            </p>
                        </div>
                    </div>
                </section>

                <section className="grid gap-5 lg:grid-cols-2">
                    {projects.map((project) => {
                        const started = Boolean(project.user_project);

                        return (
                            <Card key={project.id} className="overflow-hidden">
                                <CardHeader className="border-b-2 border-black bg-white">
                                    <div className="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <div className="mb-3 flex flex-wrap gap-2">
                                                <span className="neo-label bg-[var(--neo-yellow)]">
                                                    {project.difficulty}
                                                </span>
                                                <span className="neo-label bg-[var(--neo-cream)]">
                                                    ± {project.estimated_hours}{' '}
                                                    jam
                                                </span>
                                            </div>

                                            <CardTitle className="text-2xl font-black">
                                                {project.title}
                                            </CardTitle>
                                        </div>

                                        <div
                                            className={`flex size-16 items-center justify-center border-2 border-black text-lg font-black ${
                                                project.readiness.ready
                                                    ? 'bg-[var(--neo-lime)]'
                                                    : 'bg-[var(--neo-orange)]'
                                            }`}
                                        >
                                            {Math.round(
                                                project.readiness.score,
                                            )}
                                            %
                                        </div>
                                    </div>
                                </CardHeader>

                                <CardContent className="space-y-5 pt-5">
                                    <p className="text-sm leading-6 font-medium">
                                        {project.summary}
                                    </p>

                                    <div className="space-y-2">
                                        {project.readiness.requirements
                                            .slice(0, 4)
                                            .map((requirement) => (
                                                <div
                                                    key={requirement.skill_id}
                                                    className="flex items-center justify-between gap-3 border-b border-black/15 pb-2 text-sm"
                                                >
                                                    <span className="flex items-center gap-2 font-bold">
                                                        {requirement.ready ? (
                                                            <CheckCircle2 className="size-4" />
                                                        ) : (
                                                            <CircleDashed className="size-4" />
                                                        )}

                                                        {requirement.name}
                                                    </span>

                                                    <span className="font-mono text-xs font-black">
                                                        {requirement.current}/
                                                        {requirement.required}
                                                    </span>
                                                </div>
                                            ))}
                                    </div>

                                    {started && (
                                        <div>
                                            <div className="mb-2 flex items-center justify-between text-xs font-black tracking-wider uppercase">
                                                <span>Progres proyek</span>
                                                <span>
                                                    {project.user_project
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

                                    <Link
                                        href={`/projects/${project.slug}`}
                                        className="inline-flex items-center gap-2 border-b-2 border-black pb-1 text-sm font-black tracking-wide uppercase"
                                    >
                                        {project.readiness.ready ? (
                                            <FolderKanban className="size-4" />
                                        ) : (
                                            <LockKeyhole className="size-4" />
                                        )}
                                        Lihat detail & readiness
                                        <ArrowRight className="size-4" />
                                    </Link>
                                </CardContent>
                            </Card>
                        );
                    })}
                </section>
            </div>
        </>
    );
}

Projects.layout = {
    breadcrumbs: [
        {
            title: 'Projects',
            href: '/projects',
        },
    ],
};
