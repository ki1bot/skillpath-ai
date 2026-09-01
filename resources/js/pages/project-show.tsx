import { Deferred, Form, Head, Link, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    BrainCircuit,
    Check,
    CircleAlert,
    ExternalLink,
    Play,
    RotateCcw,
    Save,
    ShieldAlert,
} from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

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

interface ProjectReadiness {
    score: number;
    ready: boolean;
    missing_count: number;
    requirements: Requirement[];
    top_gaps: Requirement[];
    recommendation: Recommendation;
}

interface UserProject {
    status: string;
    repository_url?: string | null;
}

interface AiFeedback {
    content: string | null;
    generatedByAi: boolean;
    model: string | null;
    message: string | null;
}

const recommendationClasses: Record<RecommendationLevel, string> = {
    recommended: 'bg-[var(--neo-lime)]',
    strengthen: 'bg-[var(--neo-yellow)]',
    challenge: 'bg-[var(--neo-orange)]',
};

export default function ProjectShow({
    project,
    readiness,
    userProject,
    aiFeedback,
}: {
    project: Project;
    readiness: ProjectReadiness;
    userProject: UserProject | null;
    aiFeedback?: AiFeedback;
}) {
    const [isRetryingAi, setIsRetryingAi] = useState(false);

    const progressForm = useForm({
        repository_url: userProject?.repository_url ?? '',
    });

    const updateProgress = (event: React.FormEvent) => {
        event.preventDefault();

        progressForm.patch(`/projects/${project.slug}`, {
            preserveScroll: true,
        });
    };

    const hasAiFeedback =
        aiFeedback?.generatedByAi === true && Boolean(aiFeedback.content);

    const retryAiFeedback = () => {
        setIsRetryingAi(true);

        router.reload({
            only: ['aiFeedback'],
            onFinish: () => setIsRetryingAi(false),
        });
    };

    const googleDriveUrl = progressForm.data.repository_url.trim();

    const googleDriveLinkReady = (() => {
        if (!googleDriveUrl) {
            return false;
        }

        try {
            const url = new URL(googleDriveUrl);

            return (
                url.protocol === 'https:' &&
                url.hostname === 'drive.google.com' &&
                url.pathname !== '/'
            );
        } catch {
            return false;
        }
    })();

    const recommendationClass =
        recommendationClasses[readiness.recommendation.level];

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
                    <div className="neo-card p-6 md:p-8">
                        <div className="flex flex-wrap gap-2">
                            <span
                                className={`neo-label ${recommendationClass}`}
                            >
                                {readiness.recommendation.label}
                            </span>

                            <span className="neo-label bg-[var(--neo-yellow)]">
                                {project.career.name}
                            </span>

                            <span className="neo-label bg-[var(--neo-cream)] dark:border-foreground dark:bg-muted dark:text-foreground">
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

                        <div className="mt-6 border-l-4 border-foreground pl-4">
                            <p className="text-xs font-black tracking-[0.18em] uppercase">
                                Masalah yang diselesaikan
                            </p>

                            <p className="mt-2 text-sm leading-6">
                                {project.problem_statement}
                            </p>
                        </div>
                    </div>

                    <Card className={`${recommendationClass} text-[#171717]`}>
                        <CardHeader>
                            <CardTitle className="text-lg font-black uppercase">
                                Kesiapan proyek
                            </CardTitle>
                        </CardHeader>

                        <CardContent>
                            <p className="text-6xl font-black tracking-tighter">
                                {Math.round(readiness.score)}%
                            </p>

                            <p className="mt-3 text-sm leading-6 font-black">
                                {readiness.recommendation.label}
                            </p>

                            <p className="mt-2 text-sm leading-6 font-bold">
                                {readiness.recommendation.message}
                            </p>

                            <p className="mt-4 text-xs leading-5 font-black uppercase">
                                {readiness.missing_count} skill belum memenuhi
                                minimum
                            </p>
                        </CardContent>
                    </Card>
                </section>

                {readiness.recommendation.level !== 'recommended' &&
                    readiness.top_gaps.length > 0 && (
                        <Card>
                            <CardHeader className="border-b-2 border-foreground">
                                <CardTitle className="flex items-center gap-2 text-xl font-black">
                                    {readiness.recommendation.level ===
                                    'challenge' ? (
                                        <ShieldAlert className="size-5" />
                                    ) : (
                                        <CircleAlert className="size-5" />
                                    )}
                                    Prioritas sebelum proyek
                                </CardTitle>
                            </CardHeader>

                            <CardContent className="grid gap-3 pt-5 md:grid-cols-3">
                                {readiness.top_gaps.map((item) => (
                                    <div
                                        key={item.skill_id}
                                        className="rounded-[12px] border-2 border-foreground bg-muted p-4"
                                    >
                                        <p className="font-black">
                                            {item.name}
                                        </p>

                                        <p className="mt-2 font-mono text-xs font-black">
                                            {item.current} / {item.required}
                                        </p>

                                        <p className="mt-2 text-xs font-semibold text-muted-foreground">
                                            Gap {item.gap} poin · bobot{' '}
                                            {item.weight}
                                        </p>
                                    </div>
                                ))}
                            </CardContent>
                        </Card>
                    )}

                <Deferred
                    data="aiFeedback"
                    fallback={
                        <Card>
                            <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                                <CardTitle className="flex items-center gap-2 text-xl font-black">
                                    <BrainCircuit className="size-5" />
                                    Umpan balik AI proyek
                                </CardTitle>
                            </CardHeader>

                            <CardContent className="space-y-3 pt-6">
                                <div className="h-4 w-full animate-pulse rounded bg-muted" />
                                <div className="h-4 w-10/12 animate-pulse rounded bg-muted" />
                                <div className="h-4 w-8/12 animate-pulse rounded bg-muted" />
                            </CardContent>
                        </Card>
                    }
                >
                    {hasAiFeedback && aiFeedback ? (
                        <Card>
                            <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                                <div className="flex flex-wrap items-center justify-between gap-3">
                                    <CardTitle className="flex items-center gap-2 text-xl font-black">
                                        <BrainCircuit className="size-5" />
                                        Umpan balik AI proyek
                                    </CardTitle>

                                    <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-lime)] px-2.5 py-1 text-[10px] font-black uppercase">
                                        AI · {aiFeedback.model}
                                    </span>
                                </div>
                            </CardHeader>

                            <CardContent className="pt-6">
                                <p className="max-w-4xl text-sm leading-7 font-semibold whitespace-pre-line">
                                    {aiFeedback.content}
                                </p>

                                <p className="mt-4 text-xs leading-5 font-bold text-muted-foreground">
                                    AI hanya membaca deskripsi proyek, kesiapan,
                                    dan status proyek yang tersimpan di
                                    SkillPath AI. Sistem tidak membuka atau
                                    membaca isi file Google Drive pengguna.
                                </p>
                            </CardContent>
                        </Card>
                    ) : (
                        <Card>
                            <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                                <CardTitle className="flex items-center gap-2 text-xl font-black">
                                    <BrainCircuit className="size-5" />
                                    Umpan balik AI proyek
                                </CardTitle>
                            </CardHeader>

                            <CardContent
                                className="space-y-4 pt-6"
                                aria-live="polite"
                            >
                                <div className="flex items-start gap-3">
                                    <CircleAlert className="mt-0.5 size-5 shrink-0" />

                                    <p className="text-sm leading-6 font-semibold text-muted-foreground">
                                        {aiFeedback?.message ??
                                            'Umpan balik AI sedang tidak tersedia. Silakan coba lagi.'}
                                    </p>
                                </div>

                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    disabled={isRetryingAi}
                                    onClick={retryAiFeedback}
                                >
                                    <RotateCcw
                                        className={
                                            isRetryingAi
                                                ? 'animate-spin'
                                                : undefined
                                        }
                                    />

                                    {isRetryingAi
                                        ? 'Memuat ulang...'
                                        : 'Coba lagi'}
                                </Button>
                            </CardContent>
                        </Card>
                    )}
                </Deferred>

                <section className="grid gap-5 lg:grid-cols-2">
                    <Card>
                        <CardHeader className="border-b-2 border-foreground">
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

                                    {!item.ready && (
                                        <p className="mt-1 text-[11px] font-bold text-muted-foreground">
                                            Gap {item.gap} poin · bobot{' '}
                                            {item.weight}
                                        </p>
                                    )}
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="border-b-2 border-foreground">
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
                                    <span className="mt-1 flex size-5 shrink-0 items-center justify-center border-2 border-[#171717] bg-[var(--neo-lime)] text-[10px] font-black text-[#171717]">
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
                        <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
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
                                    <span className="mt-0.5 size-4 shrink-0 border-2 border-foreground bg-background" />

                                    <span>{feature}</span>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-yellow)] text-[#171717]">
                            <CardTitle className="flex items-center gap-2 text-xl font-black">
                                Fitur pengembangan
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="space-y-3 pt-5">
                            {(project.stretch_features ?? []).map((feature) => (
                                <div
                                    key={feature}
                                    className="flex items-start gap-3 text-sm font-bold"
                                >
                                    <span className="mt-0.5 size-4 shrink-0 border-2 border-foreground bg-background" />

                                    <span>{feature}</span>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </section>

                {!userProject ? (
                    <Card className={`${recommendationClass} text-[#171717]`}>
                        <CardContent className="flex flex-col justify-between gap-5 pt-6 md:flex-row md:items-center">
                            <div>
                                <p className="text-xl font-black">
                                    {readiness.recommendation.level ===
                                    'challenge'
                                        ? 'Tetap ingin mengambil challenge ini?'
                                        : 'Siap mulai pengerjaan?'}
                                </p>

                                <p className="mt-1 max-w-3xl text-sm leading-6 font-medium">
                                    {readiness.recommendation.level ===
                                    'recommended'
                                        ? 'Prasyarat minimum sudah terpenuhi. Mulai proyek, kerjakan sesuai kriteria, lalu unggah hasil ke Google Drive sebagai bukti penyelesaian.'
                                        : readiness.recommendation.message}
                                </p>
                            </div>

                            <Form
                                action={`/projects/${project.slug}/start`}
                                method="post"
                            >
                                {({ processing }) => (
                                    <Button disabled={processing} size="lg">
                                        <Play className="size-4" />

                                        {readiness.recommendation.level ===
                                        'challenge'
                                            ? 'Mulai sebagai challenge'
                                            : 'Mulai proyek'}
                                    </Button>
                                )}
                            </Form>
                        </CardContent>
                    </Card>
                ) : (
                    <Card>
                        <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-lime)] text-[#171717]">
                            <CardTitle className="text-xl font-black">
                                Kirim bukti proyek
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="pt-6">
                            <form
                                onSubmit={updateProgress}
                                className="grid gap-5"
                            >
                                <div className="rounded-[12px] border-2 border-foreground bg-muted p-4">
                                    <p className="text-sm leading-6 font-semibold">
                                        Setelah proyek selesai, unggah hasil
                                        atau dokumentasi proyek ke Google Drive.
                                        Tempel link Google Drive di bawah ini
                                        untuk menandai proyek sebagai selesai.
                                    </p>
                                </div>

                                <label className="grid gap-2 text-sm font-black">
                                    Link Google Drive
                                    <div className="relative">
                                        <Input
                                            type="url"
                                            value={
                                                progressForm.data.repository_url
                                            }
                                            onChange={(event) =>
                                                progressForm.setData(
                                                    'repository_url',
                                                    event.target.value,
                                                )
                                            }
                                            placeholder="https://drive.google.com/file/d/..."
                                            required
                                        />

                                        <ExternalLink className="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2" />
                                    </div>
                                    {progressForm.errors.repository_url && (
                                        <span className="text-xs text-destructive">
                                            {progressForm.errors.repository_url}
                                        </span>
                                    )}
                                </label>

                                <Button
                                    disabled={
                                        progressForm.processing ||
                                        !googleDriveLinkReady
                                    }
                                    className="w-fit"
                                >
                                    <Save className="size-4" />

                                    {progressForm.processing
                                        ? 'Menyimpan...'
                                        : 'Selesaikan proyek'}
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
