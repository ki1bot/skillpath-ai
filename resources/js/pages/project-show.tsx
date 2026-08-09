import { Form, Head, Link, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    Check,
    CircleAlert,
    ExternalLink,
    Play,
    Save,
    Sparkles,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

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
    problem_statement: string;
    difficulty: string;
    minimum_features: string[];
    stretch_features: string[] | null;
    completion_criteria: string[];
    estimated_hours: number;
    career: {
        name: string;
    };
}

interface UserProject {
    status: string;
    progress_percentage: number;
    repository_url?: string | null;
    notes?: string | null;
}

export default function ProjectShow({
    project,
    readiness,
    userProject,
}: {
    project: Project;
    readiness: {
        score: number;
        ready: boolean;
        requirements: Requirement[];
    };
    userProject: UserProject | null;
}) {
    const progressForm = useForm({
        progress_percentage: userProject?.progress_percentage ?? 0,
        repository_url: userProject?.repository_url ?? '',
        notes: userProject?.notes ?? '',
    });

    const updateProgress = (event: React.FormEvent) => {
        event.preventDefault();

        progressForm.patch(`/projects/${project.slug}`, {
            preserveScroll: true,
        });
    };

    return (
        <>
            <Head title={project.title} />

            <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-4 md:p-8">
                <Link
                    href="/projects"
                    className="inline-flex w-fit items-center gap-2 text-sm font-black tracking-wide uppercase"
                >
                    <ArrowLeft className="size-4" />
                    Kembali ke proyek
                </Link>

                <section className="grid gap-5 lg:grid-cols-[1.3fr_.7fr]">
                    <div className="neo-card bg-white p-6 md:p-8">
                        <div className="flex flex-wrap gap-2">
                            <span className="neo-label bg-[var(--neo-yellow)]">
                                {project.career.name}
                            </span>
                            <span className="neo-label bg-[var(--neo-cream)]">
                                {project.difficulty}
                            </span>
                            <span className="neo-label bg-[var(--neo-blue)]">
                                ± {project.estimated_hours} jam
                            </span>
                        </div>

                        <h1 className="neo-heading mt-5 text-4xl md:text-5xl">
                            {project.title}
                        </h1>

                        <p className="mt-4 max-w-3xl text-base leading-7 font-medium">
                            {project.summary}
                        </p>

                        <div className="mt-6 border-l-4 border-black pl-4">
                            <p className="text-xs font-black tracking-[0.18em] uppercase">
                                Masalah yang diselesaikan
                            </p>
                            <p className="mt-2 text-sm leading-6">
                                {project.problem_statement}
                            </p>
                        </div>
                    </div>

                    <Card
                        className={
                            readiness.ready
                                ? 'bg-[var(--neo-lime)]'
                                : 'bg-[var(--neo-orange)]'
                        }
                    >
                        <CardHeader>
                            <CardTitle className="text-lg font-black uppercase">
                                Kesiapan proyek
                            </CardTitle>
                        </CardHeader>

                        <CardContent>
                            <p className="text-6xl font-black tracking-tighter">
                                {Math.round(readiness.score)}%
                            </p>

                            <p className="mt-3 text-sm leading-6 font-bold">
                                {readiness.ready
                                    ? 'Semua prasyarat minimum terpenuhi. Fokus Anda sekarang adalah mengeksekusi proyek dengan bukti yang rapi.'
                                    : 'Ada prasyarat yang belum memenuhi level minimum. Anda boleh tetap mulai, tetapi risikonya adalah waktu pengerjaan lebih panjang.'}
                            </p>
                        </CardContent>
                    </Card>
                </section>

                <section className="grid gap-5 lg:grid-cols-2">
                    <Card>
                        <CardHeader className="border-b-2 border-black">
                            <CardTitle className="text-xl font-black">
                                Cek prasyarat skill
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="space-y-4 pt-5">
                            {readiness.requirements.map((item) => (
                                <div key={item.skill_id}>
                                    <div className="mb-2 flex items-center justify-between gap-3 text-sm">
                                        <span className="flex items-center gap-2 font-black">
                                            {item.ready ? (
                                                <Check className="size-4" />
                                            ) : (
                                                <CircleAlert className="size-4" />
                                            )}

                                            {item.name}
                                        </span>

                                        <span className="font-mono text-xs font-black">
                                            {item.current} / {item.required}
                                        </span>
                                    </div>

                                    <div className="neo-progress">
                                        <span
                                            style={{
                                                width: `${Math.min(
                                                    item.percentage,
                                                    100,
                                                )}%`,
                                            }}
                                        />
                                    </div>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="border-b-2 border-black">
                            <CardTitle className="text-xl font-black">
                                Kriteria selesai
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="space-y-3 pt-5">
                            {project.completion_criteria.map((criterion) => (
                                <div
                                    key={criterion}
                                    className="flex gap-3 text-sm leading-6 font-medium"
                                >
                                    <span className="mt-1 flex size-5 shrink-0 items-center justify-center border-2 border-black bg-[var(--neo-lime)] text-[10px] font-black">
                                        ✓
                                    </span>
                                    <span>{criterion}</span>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </section>

                <section className="grid gap-5 lg:grid-cols-2">
                    <Card>
                        <CardHeader className="border-b-2 border-black bg-[var(--neo-blue)]">
                            <CardTitle className="text-xl font-black">
                                Fitur minimum
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="space-y-3 pt-5">
                            {project.minimum_features.map((feature) => (
                                <div
                                    key={feature}
                                    className="flex items-start gap-3 text-sm font-bold"
                                >
                                    <span className="mt-0.5 size-4 shrink-0 border-2 border-black bg-white" />
                                    <span>{feature}</span>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="border-b-2 border-black bg-[var(--neo-yellow)]">
                            <CardTitle className="flex items-center gap-2 text-xl font-black">
                                <Sparkles className="size-5" />
                                Fitur pengembangan
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="space-y-3 pt-5">
                            {(project.stretch_features ?? []).map((feature) => (
                                <div
                                    key={feature}
                                    className="flex items-start gap-3 text-sm font-bold"
                                >
                                    <span className="mt-0.5 size-4 shrink-0 border-2 border-black bg-white" />
                                    <span>{feature}</span>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </section>

                {!userProject ? (
                    <Card className="bg-[var(--neo-lime)]">
                        <CardContent className="flex flex-col justify-between gap-5 pt-6 md:flex-row md:items-center">
                            <div>
                                <p className="text-xl font-black">
                                    Siap mulai pengerjaan?
                                </p>
                                <p className="mt-1 text-sm font-medium">
                                    Memulai proyek tidak mengubah skor skill.
                                    Progres proyek dicatat sebagai bukti
                                    terpisah.
                                </p>
                            </div>

                            <Form
                                action={`/projects/${project.slug}/start`}
                                method="post"
                            >
                                {({ processing }) => (
                                    <Button disabled={processing} size="lg">
                                        <Play className="size-4" />
                                        Mulai proyek
                                    </Button>
                                )}
                            </Form>
                        </CardContent>
                    </Card>
                ) : (
                    <Card>
                        <CardHeader className="border-b-2 border-black bg-[var(--neo-lime)]">
                            <CardTitle className="text-xl font-black">
                                Catat progres proyek
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="pt-6">
                            <form
                                onSubmit={updateProgress}
                                className="grid gap-5"
                            >
                                <label className="grid gap-2 text-sm font-black">
                                    Progres:{' '}
                                    {progressForm.data.progress_percentage}
                                    %
                                    <input
                                        type="range"
                                        min="0"
                                        max="100"
                                        step="5"
                                        value={
                                            progressForm.data
                                                .progress_percentage
                                        }
                                        onChange={(event) =>
                                            progressForm.setData(
                                                'progress_percentage',
                                                Number(event.target.value),
                                            )
                                        }
                                        className="accent-black"
                                    />
                                </label>

                                <label className="grid gap-2 text-sm font-black">
                                    URL repositori / bukti
                                    <div className="relative">
                                        <Input
                                            value={
                                                progressForm.data.repository_url
                                            }
                                            onChange={(event) =>
                                                progressForm.setData(
                                                    'repository_url',
                                                    event.target.value,
                                                )
                                            }
                                            placeholder="https://github.com/..."
                                        />
                                        <ExternalLink className="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2" />
                                    </div>
                                    {progressForm.errors.repository_url && (
                                        <span className="text-xs text-red-700">
                                            {progressForm.errors.repository_url}
                                        </span>
                                    )}
                                </label>

                                <label className="grid gap-2 text-sm font-black">
                                    Catatan pengerjaan
                                    <textarea
                                        value={progressForm.data.notes}
                                        onChange={(event) =>
                                            progressForm.setData(
                                                'notes',
                                                event.target.value,
                                            )
                                        }
                                        rows={5}
                                        className="w-full resize-y border-2 border-black bg-white p-3 text-sm font-medium outline-none focus:shadow-[3px_3px_0_#111]"
                                        placeholder="Apa yang selesai, apa yang masih menghambat?"
                                    />
                                </label>

                                <Button
                                    disabled={progressForm.processing}
                                    className="w-fit"
                                >
                                    <Save className="size-4" />
                                    Simpan progres
                                </Button>
                            </form>
                        </CardContent>
                    </Card>
                )}
            </div>
        </>
    );
}

ProjectShow.layout = {
    breadcrumbs: [
        {
            title: 'Proyek',
            href: '/projects',
        },
        {
            title: 'Detail',
            href: '#',
        },
    ],
};
