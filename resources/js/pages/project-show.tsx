import { Deferred, Form, Head, Link, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    Check,
    CheckCircle2,
    CircleAlert,
    Clock3,
    ExternalLink,
    FileCheck2,
    Gauge,
    Play,
    RotateCcw,
    Save,
    ShieldAlert,
    Upload,
} from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
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
    progress_percentage?: number;
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

    const catalog = getProjectCatalogEntry(
        project.slug,
        readiness.requirements.map((requirement) => requirement.name),
    );

    const recommendationClass =
        recommendationClasses[readiness.recommendation.level];

    const projectStarted = Boolean(userProject);
    const projectCompleted = userProject?.status === 'completed';

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
                url.hostname.toLowerCase() === 'drive.google.com' &&
                url.pathname !== '/'
            );
        } catch {
            return false;
        }
    })();

    return (
        <>
            <Head title={project.title} />

            <div className="neo-page py-7 md:py-9">
                <Link
                    href="/projects"
                    className="inline-flex items-center gap-2 text-sm font-black"
                >
                    <ArrowLeft className="size-4" />
                    Kembali ke daftar proyek
                </Link>

                <header className="mt-6 border-b-2 border-foreground pb-6">
                    <div className="grid gap-5 lg:grid-cols-[minmax(0,1fr)_280px] lg:items-end">
                        <div>
                            <div className="flex flex-wrap gap-2">
                                <span className="neo-label">
                                    {catalog.program}
                                </span>

                                <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                    {catalog.focus}
                                </span>

                                <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                    {project.difficulty}
                                </span>

                                <span
                                    className={`rounded-full border-2 border-[#171717] px-3 py-1 text-xs font-black text-[#171717] ${
                                        projectCompleted
                                            ? 'bg-[var(--neo-lime)]'
                                            : projectStarted
                                              ? 'bg-[var(--neo-yellow)]'
                                              : 'bg-[#fffdf7]'
                                    }`}
                                >
                                    {projectCompleted
                                        ? 'Selesai'
                                        : projectStarted
                                          ? 'Sedang dikerjakan'
                                          : 'Belum dimulai'}
                                </span>
                            </div>

                            <h1 className="mt-4 max-w-4xl text-3xl font-black tracking-tight sm:text-4xl">
                                {project.title}
                            </h1>

                            <p className="mt-3 max-w-3xl text-sm leading-7 font-medium text-muted-foreground sm:text-base">
                                {project.summary}
                            </p>
                        </div>

                        <div className="grid grid-cols-2 gap-3 lg:grid-cols-1">
                            <div className="rounded-[10px] border-2 border-foreground bg-card p-4">
                                <div className="flex items-center gap-2 text-xs font-black">
                                    <Clock3 className="size-4" />
                                    Estimasi
                                </div>

                                <p className="mt-2 text-xl font-black">
                                    ± {project.estimated_hours} jam
                                </p>
                            </div>

                            <div
                                className={`rounded-[10px] border-2 border-[#171717] p-4 text-[#171717] ${recommendationClass}`}
                            >
                                <div className="flex items-center gap-2 text-xs font-black">
                                    <Gauge className="size-4" />
                                    Kesiapan
                                </div>

                                <div className="mt-2 flex items-end justify-between gap-3">
                                    <p className="text-xl font-black">
                                        {Math.round(readiness.score)}%
                                    </p>

                                    <p className="text-right text-xs font-black">
                                        {readiness.recommendation.label}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <div className="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_300px]">
                    <main className="min-w-0 space-y-5">
                        <Card>
                            <CardHeader className="border-b-2 border-foreground">
                                <div className="flex items-center gap-3">
                                    <span className="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-secondary font-black text-[#171717]">
                                        1
                                    </span>

                                    <div>
                                        <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                            Pahami hasil akhirnya
                                        </p>

                                        <CardTitle className="mt-1 text-xl font-black">
                                            Apa yang harus kamu buat
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent className="grid gap-5 pt-5">
                                <p className="text-sm leading-7 font-medium">
                                    {project.problem_statement}
                                </p>

                                <div>
                                    <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                        Kemampuan yang digunakan
                                    </p>

                                    <div className="mt-3 flex flex-wrap gap-2">
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
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="border-b-2 border-foreground">
                                <div className="flex items-center gap-3">
                                    <span className="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-[var(--neo-yellow)] font-black text-[#171717]">
                                        2
                                    </span>

                                    <div>
                                        <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                            Bagian wajib
                                        </p>

                                        <CardTitle className="mt-1 text-xl font-black">
                                            Kerjakan semua poin ini
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent className="pt-5">
                                <p className="mb-4 text-sm leading-6 font-medium text-muted-foreground">
                                    Fokus pada daftar wajib terlebih dahulu.
                                    Fitur tambahan tidak perlu dikerjakan
                                    sebelum bagian ini selesai.
                                </p>

                                <div className="grid gap-3">
                                    {project.minimum_features.map(
                                        (feature, index) => (
                                            <div
                                                key={feature}
                                                className="flex gap-3 rounded-[10px] border-2 border-foreground/15 bg-muted/20 p-4"
                                            >
                                                <span className="flex size-7 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-background text-xs font-black">
                                                    {index + 1}
                                                </span>

                                                <p className="pt-0.5 text-sm leading-6 font-semibold">
                                                    {feature}
                                                </p>
                                            </div>
                                        ),
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="border-b-2 border-foreground">
                                <div className="flex items-center gap-3">
                                    <span className="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-[var(--neo-lime)] font-black text-[#171717]">
                                        3
                                    </span>

                                    <div>
                                        <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                            Pemeriksaan akhir
                                        </p>

                                        <CardTitle className="mt-1 text-xl font-black">
                                            Cek sebelum dikumpulkan
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent className="grid gap-5 pt-5">
                                <div className="grid gap-3">
                                    {project.completion_criteria.map(
                                        (criterion) => (
                                            <div
                                                key={criterion}
                                                className="flex gap-3 text-sm leading-6 font-medium"
                                            >
                                                <span className="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full border-2 border-[#171717] bg-[var(--neo-lime)] text-[#171717]">
                                                    <Check className="size-3.5" />
                                                </span>

                                                <span>{criterion}</span>
                                            </div>
                                        ),
                                    )}
                                </div>

                                {(project.stretch_features ?? []).length >
                                    0 && (
                                    <details className="rounded-[10px] border-2 border-foreground/15 bg-muted/20 p-4">
                                        <summary className="cursor-pointer text-sm font-black">
                                            Pengembangan tambahan (opsional)
                                        </summary>

                                        <div className="mt-4 grid gap-2 border-t-2 border-foreground/10 pt-4">
                                            {(
                                                project.stretch_features ?? []
                                            ).map((feature) => (
                                                <p
                                                    key={feature}
                                                    className="text-sm leading-6 font-medium"
                                                >
                                                    • {feature}
                                                </p>
                                            ))}
                                        </div>
                                    </details>
                                )}
                            </CardContent>
                        </Card>

                        {!userProject ? (
                            <Card
                                className={`${recommendationClass} text-[#171717]`}
                            >
                                <CardContent className="grid gap-4 pt-6 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-center">
                                    <div>
                                        <p className="text-sm font-black">
                                            Sudah memahami tugasnya?
                                        </p>

                                        <p className="mt-1 text-sm leading-6 font-medium">
                                            Mulai proyek setelah kamu memahami
                                            bagian wajib dan kriteria
                                            pengumpulan.
                                        </p>
                                    </div>

                                    <Form
                                        action={`/projects/${project.slug}/start`}
                                        method="post"
                                    >
                                        {({ processing }) => (
                                            <Button disabled={processing}>
                                                <Play className="size-4" />

                                                {processing
                                                    ? 'Memulai...'
                                                    : readiness.recommendation
                                                            .level ===
                                                        'challenge'
                                                      ? 'Mulai sebagai tantangan'
                                                      : 'Mulai proyek'}
                                            </Button>
                                        )}
                                    </Form>
                                </CardContent>
                            </Card>
                        ) : (
                            <Card>
                                <CardHeader className="border-b-2 border-foreground">
                                    <div className="flex items-center gap-3">
                                        <span className="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-[var(--neo-blue)] font-black text-[#171717]">
                                            4
                                        </span>

                                        <div>
                                            <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                                Pengumpulan
                                            </p>

                                            <CardTitle className="mt-1 text-xl font-black">
                                                {projectCompleted
                                                    ? 'Hasil proyek sudah dikumpulkan'
                                                    : 'Kumpulkan hasil melalui Google Drive'}
                                            </CardTitle>
                                        </div>
                                    </div>
                                </CardHeader>

                                <CardContent className="grid gap-6 pt-5 lg:grid-cols-[minmax(0,1fr)_minmax(280px,.75fr)]">
                                    <div className="space-y-4">
                                        {projectCompleted ? (
                                            <div className="rounded-[10px] border-2 border-[#171717] bg-[var(--neo-lime)] p-4 text-[#171717]">
                                                <div className="flex items-start gap-3">
                                                    <FileCheck2 className="mt-0.5 size-5 shrink-0" />

                                                    <div>
                                                        <p className="font-black">
                                                            Status: selesai
                                                        </p>

                                                        <p className="mt-1 text-sm leading-6 font-medium">
                                                            Link Google Drive
                                                            sudah tersimpan.
                                                            Kamu masih dapat
                                                            memperbaruinya.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        ) : (
                                            <div className="rounded-[10px] border-2 border-foreground/15 bg-muted/20 p-4">
                                                <p className="font-black">
                                                    Sebelum mengirim
                                                </p>

                                                <div className="mt-3 grid gap-2 text-sm leading-6 font-medium">
                                                    <p>
                                                        1. Upload hasil proyek
                                                        atau dokumentasinya ke
                                                        Google Drive.
                                                    </p>

                                                    <p>
                                                        2. Pastikan link dapat
                                                        dibuka oleh pihak yang
                                                        memeriksa.
                                                    </p>

                                                    <p>
                                                        3. Tempel link di
                                                        formulir dan kirim
                                                        setelah semua kriteria
                                                        selesai.
                                                    </p>
                                                </div>
                                            </div>
                                        )}

                                        <div className="rounded-[10px] border-2 border-foreground/15 p-4">
                                            <div className="flex items-center gap-2">
                                                <Upload className="size-4" />

                                                <p className="text-sm font-black">
                                                    Isi Drive yang disarankan
                                                </p>
                                            </div>

                                            <div className="mt-3 grid gap-2 text-sm leading-6 font-medium text-muted-foreground">
                                                <p>• Hasil utama proyek.</p>

                                                <p>
                                                    • Dokumentasi atau petunjuk
                                                    penggunaan.
                                                </p>

                                                <p>
                                                    • Bukti yang mendukung
                                                    checklist tugas.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <form
                                        onSubmit={updateProgress}
                                        className="grid content-start gap-4"
                                    >
                                        <label className="grid gap-2 text-sm font-black">
                                            Link Google Drive
                                            <div className="relative">
                                                <Input
                                                    type="url"
                                                    value={
                                                        progressForm.data
                                                            .repository_url
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
                                        </label>

                                        {!googleDriveUrl && (
                                            <p className="text-xs leading-5 font-medium text-muted-foreground">
                                                Gunakan tautan dari
                                                drive.google.com.
                                            </p>
                                        )}

                                        {googleDriveUrl &&
                                            !googleDriveLinkReady && (
                                                <p className="text-xs leading-5 font-bold text-destructive">
                                                    Tautan belum valid. Gunakan
                                                    link HTTPS dari
                                                    drive.google.com.
                                                </p>
                                            )}

                                        {progressForm.errors.repository_url && (
                                            <p className="text-xs leading-5 font-bold text-destructive">
                                                {
                                                    progressForm.errors
                                                        .repository_url
                                                }
                                            </p>
                                        )}

                                        {googleDriveLinkReady && (
                                            <Button
                                                asChild
                                                type="button"
                                                variant="outline"
                                                className="w-full"
                                            >
                                                <a
                                                    href={googleDriveUrl}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                >
                                                    <ExternalLink className="size-4" />
                                                    Buka link Drive
                                                </a>
                                            </Button>
                                        )}

                                        <Button
                                            disabled={
                                                progressForm.processing ||
                                                !googleDriveLinkReady
                                            }
                                            className="w-full"
                                        >
                                            <Save className="size-4" />

                                            {progressForm.processing
                                                ? 'Menyimpan...'
                                                : projectCompleted
                                                  ? 'Perbarui link Drive'
                                                  : 'Kumpulkan proyek'}
                                        </Button>
                                    </form>
                                </CardContent>
                            </Card>
                        )}

                        <Card>
                            <CardHeader className="border-b-2 border-foreground">
                                <CardTitle className="text-lg font-black">
                                    Bantuan tambahan
                                </CardTitle>
                            </CardHeader>

                            <CardContent className="pt-5">
                                <Deferred
                                    data="aiFeedback"
                                    fallback={
                                        <div className="space-y-2">
                                            <div className="h-3 w-full animate-pulse rounded bg-muted" />
                                            <div className="h-3 w-9/12 animate-pulse rounded bg-muted" />
                                        </div>
                                    }
                                >
                                    {hasAiFeedback && aiFeedback ? (
                                        <details>
                                            <summary className="cursor-pointer text-sm font-black">
                                                Lihat saran pengerjaan
                                            </summary>

                                            <div className="mt-4 border-t-2 border-foreground/15 pt-4">
                                                <p className="text-sm leading-7 font-semibold whitespace-pre-line">
                                                    {aiFeedback.content}
                                                </p>

                                                <p className="mt-3 text-xs leading-5 font-medium text-muted-foreground">
                                                    Saran ini hanya pendamping
                                                    dan tidak menentukan status
                                                    proyek.
                                                </p>
                                            </div>
                                        </details>
                                    ) : (
                                        <div>
                                            <div className="flex items-start gap-3">
                                                <CircleAlert className="mt-0.5 size-5 shrink-0" />

                                                <p className="text-sm leading-6 font-medium text-muted-foreground">
                                                    {aiFeedback?.message ??
                                                        'Saran tambahan sedang tidak tersedia. Tugas utama tetap dapat dikerjakan.'}
                                                </p>
                                            </div>

                                            <Button
                                                type="button"
                                                variant="outline"
                                                size="sm"
                                                className="mt-4"
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
                                        </div>
                                    )}
                                </Deferred>
                            </CardContent>
                        </Card>
                    </main>

                    <aside className="space-y-4 xl:sticky xl:top-6 xl:self-start">
                        <Card>
                            <CardHeader className="border-b-2 border-foreground">
                                <CardTitle className="text-lg font-black">
                                    Ringkasan tugas
                                </CardTitle>
                            </CardHeader>

                            <CardContent className="grid gap-4 pt-5 text-sm">
                                <div>
                                    <p className="text-xs font-black text-muted-foreground uppercase">
                                        Bidang
                                    </p>

                                    <p className="mt-1 font-black">
                                        {catalog.focus}
                                    </p>
                                </div>

                                <div>
                                    <p className="text-xs font-black text-muted-foreground uppercase">
                                        Status
                                    </p>

                                    <p className="mt-1 font-black">
                                        {projectCompleted
                                            ? 'Selesai'
                                            : projectStarted
                                              ? 'Sedang dikerjakan'
                                              : 'Belum dimulai'}
                                    </p>
                                </div>

                                <div>
                                    <p className="text-xs font-black text-muted-foreground uppercase">
                                        Bagian wajib
                                    </p>

                                    <p className="mt-1 font-black">
                                        {project.minimum_features.length} poin
                                    </p>
                                </div>

                                <div>
                                    <p className="text-xs font-black text-muted-foreground uppercase">
                                        Kriteria selesai
                                    </p>

                                    <p className="mt-1 font-black">
                                        {project.completion_criteria.length}{' '}
                                        poin
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <Card
                            className={`${recommendationClass} text-[#171717]`}
                        >
                            <CardHeader className="border-b-2 border-[#171717]">
                                <CardTitle className="flex items-center gap-2 text-lg font-black">
                                    <Gauge className="size-5" />
                                    Kesiapan saat ini
                                </CardTitle>
                            </CardHeader>

                            <CardContent className="pt-5">
                                <div className="flex items-end justify-between gap-4">
                                    <p className="text-3xl font-black">
                                        {Math.round(readiness.score)}%
                                    </p>

                                    <p className="text-right text-xs font-black">
                                        {readiness.recommendation.label}
                                    </p>
                                </div>

                                <p className="mt-3 text-sm leading-6 font-semibold">
                                    {readiness.recommendation.message}
                                </p>

                                <details className="mt-4 rounded-[10px] border-2 border-[#171717] bg-[#fffdf7] p-3 text-[#171717]">
                                    <summary className="cursor-pointer text-xs font-black">
                                        Lihat kemampuan yang dinilai
                                    </summary>

                                    <div className="mt-4 grid gap-4">
                                        {readiness.requirements.map((item) => (
                                            <div key={item.skill_id}>
                                                <div className="mb-2 flex items-center justify-between gap-3 text-xs">
                                                    <span className="flex min-w-0 items-center gap-2 font-black">
                                                        {item.ready ? (
                                                            <CheckCircle2 className="size-4 shrink-0" />
                                                        ) : (
                                                            <CircleAlert className="size-4 shrink-0" />
                                                        )}

                                                        <span className="truncate">
                                                            {item.name}
                                                        </span>
                                                    </span>

                                                    <span className="font-mono font-black">
                                                        {item.current}/
                                                        {item.required}
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
                                    </div>
                                </details>
                            </CardContent>
                        </Card>

                        {readiness.recommendation.level !== 'recommended' &&
                            readiness.top_gaps.length > 0 && (
                                <Card>
                                    <CardHeader className="border-b-2 border-foreground">
                                        <CardTitle className="flex items-center gap-2 text-lg font-black">
                                            {readiness.recommendation.level ===
                                            'challenge' ? (
                                                <ShieldAlert className="size-5" />
                                            ) : (
                                                <CircleAlert className="size-5" />
                                            )}
                                            Perlu diperkuat
                                        </CardTitle>
                                    </CardHeader>

                                    <CardContent className="grid gap-3 pt-5">
                                        {readiness.top_gaps.map((item) => (
                                            <div
                                                key={item.skill_id}
                                                className="rounded-[10px] border-2 border-foreground/15 bg-muted/20 p-3"
                                            >
                                                <p className="text-sm font-black">
                                                    {item.name}
                                                </p>

                                                <p className="mt-1 text-xs font-medium text-muted-foreground">
                                                    Saat ini {item.current},
                                                    target {item.required}.
                                                </p>
                                            </div>
                                        ))}
                                    </CardContent>
                                </Card>
                            )}
                    </aside>
                </div>
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
            title: 'Detail Tugas',
            href: '#',
        },
    ],
};
