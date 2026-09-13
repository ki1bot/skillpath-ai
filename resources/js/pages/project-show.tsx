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
    ListChecks,
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

            <div className="neo-page flex flex-1 flex-col gap-6 py-6 sm:py-8 lg:gap-8 lg:py-10">
                <Link
                    href="/projects"
                    className="inline-flex w-fit items-center gap-2 text-sm font-black tracking-wide uppercase"
                >
                    <ArrowLeft className="size-4" />
                    Kembali ke daftar proyek
                </Link>

                <section className="overflow-hidden rounded-[18px] border-2 border-[#171717] bg-[#fffdf7] text-[#171717] shadow-[6px_6px_0_var(--neo-shadow-color)]">
                    <div className="grid gap-6 p-5 sm:p-7 lg:grid-cols-[minmax(0,1fr)_320px] lg:p-8">
                        <div>
                            <div className="flex flex-wrap gap-2">
                                <span className="neo-label bg-[var(--neo-blue)]">
                                    {catalog.program}
                                </span>

                                <span className="neo-label bg-[var(--neo-yellow)]">
                                    {catalog.focus}
                                </span>

                                <span className="neo-label bg-[#fffdf7]">
                                    {project.difficulty}
                                </span>

                                {projectCompleted ? (
                                    <span className="neo-label bg-[var(--neo-lime)]">
                                        Selesai
                                    </span>
                                ) : projectStarted ? (
                                    <span className="neo-label bg-[var(--neo-yellow)]">
                                        Sedang dikerjakan
                                    </span>
                                ) : (
                                    <span className="neo-label bg-[#fffdf7]">
                                        Belum dimulai
                                    </span>
                                )}
                            </div>

                            <h1 className="mt-5 max-w-4xl text-4xl leading-[0.95] font-black tracking-[-0.045em] sm:text-5xl">
                                {project.title}
                            </h1>

                            <p className="mt-4 max-w-3xl text-base leading-7 font-semibold text-[#171717]/75">
                                {project.summary}
                            </p>

                            <div className="mt-6">
                                <p className="text-[10px] font-black tracking-[0.14em] uppercase">
                                    Kemampuan utama yang dilatih
                                </p>

                                <div className="mt-3 flex flex-wrap gap-2">
                                    {catalog.skills.map((skill) => (
                                        <span
                                            key={skill}
                                            className="rounded-full border-2 border-[#171717] bg-[var(--neo-cream)] px-3 py-1.5 text-xs font-black"
                                        >
                                            {skill}
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </div>

                        <div className="grid content-start gap-3">
                            <div className="rounded-[12px] border-2 border-[#171717] bg-[var(--neo-blue)] p-4 shadow-[3px_3px_0_#171717]">
                                <div className="flex items-center gap-2 text-[10px] font-black tracking-[0.12em] uppercase">
                                    <Clock3 className="size-4" />
                                    Estimasi pengerjaan
                                </div>

                                <p className="mt-2 text-2xl font-black">
                                    ± {project.estimated_hours} jam
                                </p>
                            </div>

                            <div
                                className={`rounded-[12px] border-2 border-[#171717] p-4 shadow-[3px_3px_0_#171717] ${recommendationClass}`}
                            >
                                <div className="flex items-center gap-2 text-[10px] font-black tracking-[0.12em] uppercase">
                                    <Gauge className="size-4" />
                                    Kesiapan saat ini
                                </div>

                                <div className="mt-2 flex items-end justify-between gap-3">
                                    <p className="text-3xl font-black">
                                        {Math.round(readiness.score)}%
                                    </p>

                                    <p className="text-right text-xs font-black">
                                        {readiness.recommendation.label}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-start">
                    <div className="grid gap-5">
                        <Card>
                            <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                                <div className="flex items-center gap-3">
                                    <span className="flex size-9 shrink-0 items-center justify-center rounded-[9px] border-2 border-[#171717] bg-[#fffdf7] font-black">
                                        1
                                    </span>

                                    <div>
                                        <p className="text-[10px] font-black tracking-[0.12em] uppercase">
                                            Mulai dari sini
                                        </p>

                                        <CardTitle className="mt-1 text-xl font-black">
                                            Pahami tugasnya
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent className="grid gap-5 pt-6">
                                <div>
                                    <p className="text-xs font-black tracking-[0.12em] uppercase">
                                        Tujuan proyek
                                    </p>

                                    <p className="mt-2 text-sm leading-7 font-medium">
                                        {project.summary}
                                    </p>
                                </div>

                                <div className="rounded-[12px] border-2 border-foreground/15 bg-muted/30 p-4">
                                    <p className="text-xs font-black tracking-[0.12em] uppercase">
                                        Skenario tugas
                                    </p>

                                    <p className="mt-2 text-sm leading-7 font-medium">
                                        {project.problem_statement}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-yellow)] text-[#171717]">
                                <div className="flex items-center gap-3">
                                    <span className="flex size-9 shrink-0 items-center justify-center rounded-[9px] border-2 border-[#171717] bg-[#fffdf7] font-black">
                                        2
                                    </span>

                                    <div>
                                        <p className="text-[10px] font-black tracking-[0.12em] uppercase">
                                            Bagian wajib
                                        </p>

                                        <CardTitle className="mt-1 text-xl font-black">
                                            Kerjakan checklist berikut
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent className="pt-6">
                                <p className="mb-5 text-sm leading-6 font-medium text-muted-foreground">
                                    Anggap bagian ini sebagai daftar kerja
                                    utama. Proyek belum perlu dibuat lebih rumit
                                    sebelum semua poin wajib di bawah selesai.
                                </p>

                                <div className="grid gap-3">
                                    {project.minimum_features.map(
                                        (feature, index) => (
                                            <div
                                                key={feature}
                                                className="flex gap-3 rounded-[12px] border-2 border-foreground/15 bg-muted/30 p-4"
                                            >
                                                <span className="flex size-7 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-background text-xs font-black">
                                                    {index + 1}
                                                </span>

                                                <p className="pt-0.5 text-sm leading-6 font-bold">
                                                    {feature}
                                                </p>
                                            </div>
                                        ),
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-lime)] text-[#171717]">
                                <div className="flex items-center gap-3">
                                    <span className="flex size-9 shrink-0 items-center justify-center rounded-[9px] border-2 border-[#171717] bg-[#fffdf7] font-black">
                                        3
                                    </span>

                                    <div>
                                        <p className="text-[10px] font-black tracking-[0.12em] uppercase">
                                            Sebelum dikumpulkan
                                        </p>

                                        <CardTitle className="mt-1 text-xl font-black">
                                            Pastikan proyek sudah memenuhi
                                            kriteria selesai
                                        </CardTitle>
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent className="pt-6">
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
                            </CardContent>
                        </Card>

                        {(project.stretch_features ?? []).length > 0 && (
                            <Card>
                                <CardHeader className="border-b-2 border-foreground">
                                    <CardTitle className="text-xl font-black">
                                        Pengembangan tambahan
                                    </CardTitle>
                                </CardHeader>

                                <CardContent className="pt-6">
                                    <p className="mb-4 text-sm leading-6 font-medium text-muted-foreground">
                                        Bagian ini opsional. Kerjakan setelah
                                        bagian wajib selesai jika masih memiliki
                                        waktu.
                                    </p>

                                    <div className="grid gap-3">
                                        {(project.stretch_features ?? []).map(
                                            (feature) => (
                                                <div
                                                    key={feature}
                                                    className="flex items-start gap-3 text-sm font-bold"
                                                >
                                                    <span className="mt-0.5 size-4 shrink-0 border-2 border-foreground bg-background" />

                                                    <span>{feature}</span>
                                                </div>
                                            ),
                                        )}
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </div>

                    <aside className="grid gap-5 lg:sticky lg:top-6">
                        <Card>
                            <CardHeader className="border-b-2 border-foreground">
                                <CardTitle className="flex items-center gap-2 text-lg font-black">
                                    <ListChecks className="size-5" />
                                    Ringkasan tugas
                                </CardTitle>
                            </CardHeader>

                            <CardContent className="grid gap-4 pt-5">
                                <div>
                                    <p className="text-[10px] font-black tracking-[0.12em] text-muted-foreground uppercase">
                                        Bidang
                                    </p>

                                    <p className="mt-1 text-sm font-black">
                                        {catalog.focus}
                                    </p>
                                </div>

                                <div>
                                    <p className="text-[10px] font-black tracking-[0.12em] text-muted-foreground uppercase">
                                        Tingkat
                                    </p>

                                    <p className="mt-1 text-sm font-black">
                                        {project.difficulty}
                                    </p>
                                </div>

                                <div>
                                    <p className="text-[10px] font-black tracking-[0.12em] text-muted-foreground uppercase">
                                        Estimasi
                                    </p>

                                    <p className="mt-1 text-sm font-black">
                                        ± {project.estimated_hours} jam
                                    </p>
                                </div>

                                <div>
                                    <p className="text-[10px] font-black tracking-[0.12em] text-muted-foreground uppercase">
                                        Status
                                    </p>

                                    <p className="mt-1 text-sm font-black">
                                        {projectCompleted
                                            ? 'Selesai'
                                            : projectStarted
                                              ? 'Sedang dikerjakan'
                                              : 'Belum dimulai'}
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
                                    Kesiapan
                                </CardTitle>
                            </CardHeader>

                            <CardContent className="pt-5">
                                <div className="flex items-end justify-between gap-4">
                                    <p className="text-4xl font-black">
                                        {Math.round(readiness.score)}%
                                    </p>

                                    <p className="text-right text-xs font-black uppercase">
                                        {readiness.recommendation.label}
                                    </p>
                                </div>

                                <p className="mt-3 text-sm leading-6 font-bold">
                                    {readiness.recommendation.message}
                                </p>

                                <details className="mt-4 rounded-[10px] border-2 border-[#171717] bg-[#fffdf7] p-3 text-[#171717]">
                                    <summary className="cursor-pointer text-xs font-black uppercase">
                                        Lihat detail kemampuan
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

                                                    <span className="shrink-0 font-mono font-black">
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
                                            Yang perlu diperkuat
                                        </CardTitle>
                                    </CardHeader>

                                    <CardContent className="grid gap-3 pt-5">
                                        {readiness.top_gaps.map((item) => (
                                            <div
                                                key={item.skill_id}
                                                className="rounded-[10px] border-2 border-foreground/15 bg-muted/30 p-3"
                                            >
                                                <p className="text-sm font-black">
                                                    {item.name}
                                                </p>

                                                <p className="mt-1 text-xs font-medium text-muted-foreground">
                                                    Saat ini {item.current},
                                                    target minimum{' '}
                                                    {item.required}.
                                                </p>
                                            </div>
                                        ))}
                                    </CardContent>
                                </Card>
                            )}
                    </aside>
                </section>

                {!userProject ? (
                    <Card className={`${recommendationClass} text-[#171717]`}>
                        <CardContent className="grid gap-5 pt-6 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
                            <div>
                                <p className="text-xs font-black tracking-[0.12em] uppercase">
                                    Langkah berikutnya
                                </p>

                                <p className="mt-2 text-2xl font-black">
                                    Sudah memahami tugasnya?
                                </p>

                                <p className="mt-2 max-w-3xl text-sm leading-6 font-medium">
                                    Tekan tombol mulai jika kamu sudah memahami
                                    tujuan, checklist wajib, dan kriteria
                                    selesai. Memulai proyek tidak otomatis
                                    berarti seluruh kemampuanmu sudah sempurna.
                                </p>
                            </div>

                            <Form
                                action={`/projects/${project.slug}/start`}
                                method="post"
                            >
                                {({ processing }) => (
                                    <Button disabled={processing} size="lg">
                                        <Play className="size-4" />

                                        {processing
                                            ? 'Memulai...'
                                            : readiness.recommendation.level ===
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
                            <div className="flex items-center gap-3">
                                <span className="flex size-9 shrink-0 items-center justify-center rounded-[9px] border-2 border-[#171717] bg-[#fffdf7] font-black">
                                    4
                                </span>

                                <div>
                                    <p className="text-[10px] font-black tracking-[0.12em] uppercase">
                                        Pengumpulan
                                    </p>

                                    <CardTitle className="mt-1 text-xl font-black">
                                        {projectCompleted
                                            ? 'Proyek sudah dikumpulkan'
                                            : 'Kumpulkan melalui Google Drive'}
                                    </CardTitle>
                                </div>
                            </div>
                        </CardHeader>

                        <CardContent className="grid gap-6 pt-6 lg:grid-cols-[minmax(0,1fr)_minmax(320px,.8fr)]">
                            <div>
                                {projectCompleted ? (
                                    <div className="rounded-[12px] border-2 border-foreground bg-[var(--neo-lime)] p-4 text-[#171717]">
                                        <div className="flex items-start gap-3">
                                            <FileCheck2 className="mt-0.5 size-5 shrink-0" />

                                            <div>
                                                <p className="font-black">
                                                    Status proyek: selesai
                                                </p>

                                                <p className="mt-1 text-sm leading-6 font-medium">
                                                    Tautan Google Drive sudah
                                                    tersimpan. Kamu tetap bisa
                                                    memperbarui tautan jika file
                                                    atau dokumentasi proyek
                                                    berubah.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="rounded-[12px] border-2 border-foreground/15 bg-muted/30 p-4">
                                        <p className="font-black">
                                            Cara mengumpulkan
                                        </p>

                                        <div className="mt-4 grid gap-3">
                                            <div className="flex gap-3 text-sm leading-6 font-medium">
                                                <span className="flex size-6 shrink-0 items-center justify-center rounded-full border-2 border-foreground text-xs font-black">
                                                    1
                                                </span>
                                                Upload hasil proyek atau
                                                dokumentasinya ke Google Drive.
                                            </div>

                                            <div className="flex gap-3 text-sm leading-6 font-medium">
                                                <span className="flex size-6 shrink-0 items-center justify-center rounded-full border-2 border-foreground text-xs font-black">
                                                    2
                                                </span>
                                                Atur akses file agar tautan
                                                dapat dibuka oleh pihak yang
                                                akan memeriksa.
                                            </div>

                                            <div className="flex gap-3 text-sm leading-6 font-medium">
                                                <span className="flex size-6 shrink-0 items-center justify-center rounded-full border-2 border-foreground text-xs font-black">
                                                    3
                                                </span>
                                                Tempel tautan Google Drive di
                                                formulir, lalu tandai proyek
                                                sebagai selesai.
                                            </div>
                                        </div>
                                    </div>
                                )}

                                <div className="mt-5 rounded-[12px] border-2 border-foreground/15 p-4">
                                    <div className="flex items-center gap-2">
                                        <Upload className="size-4" />

                                        <p className="text-sm font-black">
                                            Yang sebaiknya ada di Drive
                                        </p>
                                    </div>

                                    <ul className="mt-3 grid gap-2 text-sm leading-6 font-medium text-muted-foreground">
                                        <li>• Hasil utama proyek.</li>

                                        <li>
                                            • Dokumentasi atau penjelasan cara
                                            menggunakan hasil proyek.
                                        </li>

                                        <li>
                                            • Bukti pendukung yang relevan
                                            dengan checklist tugas.
                                        </li>
                                    </ul>
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
                                </label>

                                {!googleDriveUrl && (
                                    <p className="text-xs leading-5 font-medium text-muted-foreground">
                                        Gunakan tautan dari drive.google.com.
                                    </p>
                                )}

                                {googleDriveUrl && !googleDriveLinkReady && (
                                    <p className="text-xs leading-5 font-bold text-destructive">
                                        Tautan belum valid. Pastikan menggunakan
                                        link HTTPS dari drive.google.com.
                                    </p>
                                )}

                                {progressForm.errors.repository_url && (
                                    <p className="text-xs leading-5 font-bold text-destructive">
                                        {progressForm.errors.repository_url}
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
                                    size="lg"
                                >
                                    <Save className="size-4" />

                                    {progressForm.processing
                                        ? 'Menyimpan...'
                                        : projectCompleted
                                          ? 'Perbarui link Drive'
                                          : 'Kumpulkan dan selesaikan'}
                                </Button>
                            </form>
                        </CardContent>
                    </Card>
                )}

                <Card>
                    <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                        <CardTitle className="flex items-center gap-2 text-xl font-black">
                            Bantuan tambahan dari AI
                        </CardTitle>
                    </CardHeader>

                    <CardContent className="pt-6">
                        <Deferred
                            data="aiFeedback"
                            fallback={
                                <div className="space-y-3">
                                    <div className="h-4 w-full animate-pulse rounded bg-muted" />
                                    <div className="h-4 w-10/12 animate-pulse rounded bg-muted" />
                                    <div className="h-4 w-8/12 animate-pulse rounded bg-muted" />
                                </div>
                            }
                        >
                            {hasAiFeedback && aiFeedback ? (
                                <div>
                                    <div className="flex flex-wrap items-center justify-between gap-3">
                                        <p className="text-sm font-black">
                                            Saran untuk pengerjaan proyek
                                        </p>

                                        <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-lime)] px-2.5 py-1 text-[10px] font-black text-[#171717] uppercase">
                                            AI · {aiFeedback.model}
                                        </span>
                                    </div>

                                    <p className="mt-4 max-w-4xl text-sm leading-7 font-semibold whitespace-pre-line">
                                        {aiFeedback.content}
                                    </p>

                                    <p className="mt-4 text-xs leading-5 font-bold text-muted-foreground">
                                        AI membaca deskripsi proyek, kesiapan,
                                        dan status proyek yang tersimpan di
                                        SkillPath AI. Sistem tidak membuka atau
                                        membaca isi file Google Drive.
                                    </p>
                                </div>
                            ) : (
                                <div className="grid gap-4" aria-live="polite">
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
                                        className="w-fit"
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
            </div>
        </>
    );
}

ProjectShow.layout = {
    breadcrumbs: [
        {
            title: 'Proyek / Tugas Akhir',
            href: '/projects',
        },
        {
            title: 'Detail Tugas',
            href: '#',
        },
    ],
};
