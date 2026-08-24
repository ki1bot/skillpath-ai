import { Deferred, Head, Link, router } from '@inertiajs/react';
import {
    Activity,
    ArrowUpRight,
    BookCheck,
    BrainCircuit,
    CircleAlert,
    Clock3,
    History,
    RotateCcw,
    Route,
    Trophy,
} from 'lucide-react';
import { useState } from 'react';
import {
    Area,
    AreaChart,
    CartesianGrid,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Readiness {
    score: number;
    skill_mastery: number;
    roadmap_completion: number;
    project_score: number;
    consistency: number;
    evaluation_score: number;
}

interface AiInsights {
    progress: string | null;
    schedule: string | null;
    obstacles: string | null;
    generatedByAi: boolean;
    model: string | null;
    message: string | null;
}

interface ReadinessSnapshot {
    id: number;
    score: number;
    trigger: string;
    created_at: string;
    career?: {
        name: string;
        slug: string;
    } | null;
}

interface Attempt {
    attempt_uuid: string;
    date: string;
    average: number;
    skills: Array<{
        name: string;
        score: number;
    }>;
}

interface Log {
    id: number;
    activity_type: string;
    minutes_spent: number;
    progress_percentage: number | null;
    notes?: string | null;
    logged_at: string;
    roadmap_item?: {
        material?: {
            title: string;
            slug: string;
        } | null;
    } | null;
}

interface Evaluation {
    id: number;
    score: number;
    passed: boolean;
    created_at: string;
    roadmap_item?: {
        material?: {
            title: string;
            slug: string;
        } | null;
    } | null;
}

interface UserProject {
    id: number;
    status: string;
    progress_percentage: number;
    updated_at: string;
    project?: {
        title: string;
        slug: string;
    } | null;
}

interface RoadmapHistory {
    id: number;
    version: number;
    reason: string;
    estimated_weeks: number;
    is_active: boolean;
    created_at: string;
    career?: {
        name: string;
        slug: string;
    } | null;
}

const triggerLabels: Record<string, string> = {
    assessment_completed: 'Assesment selesai',
    assessment_updated: 'Assesment diperbarui',
    roadmap_created: 'Jalur belajar dibuat',
    roadmap_regenerated: 'Jalur belajar diperbarui',
    material_completed: 'Materi selesai',
    material_evaluated: 'Materi dievaluasi',
    project_started: 'Proyek dimulai',
    project_updated: 'Proyek diperbarui',
    project_completed: 'Proyek selesai',
    progress_updated: 'Perkembangan diperbarui',
    evaluation_passed: 'Evaluasi lulus',
    evaluation_failed: 'Evaluasi perlu diulang',
};

const activityLabels: Record<string, string> = {
    study: 'Belajar',
    learning: 'Belajar',
    material_study: 'Mempelajari materi',
    practice: 'Latihan',
    evaluation: 'Evaluasi',
    evaluation_passed: 'Evaluasi lulus',
    evaluation_failed: 'Evaluasi perlu diulang',
    project: 'Mengerjakan proyek',
    project_progress: 'Progres proyek',
    project_completed: 'Proyek selesai',
    review: 'Mengulang materi',
    roadmap_reinforcement_added: 'Materi penguatan ditambahkan',
    roadmap_reinforcement_reopened: 'Materi penguatan dibuka kembali',
    roadmap_reinforcement_retry: 'Penguatan perlu diulang',
    roadmap_reinforcement_completed: 'Penguatan selesai',
    roadmap_inactivity_adjusted: 'Jalur belajar disesuaikan',
};

const projectStatusLabels: Record<string, string> = {
    not_started: 'Belum dimulai',
    in_progress: 'Sedang dikerjakan',
    paused: 'Dijeda',
    completed: 'Selesai',
};

function formatDate(value: string) {
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

function formatTrigger(value: string) {
    return triggerLabels[value] ?? 'Perkembangan diperbarui';
}

function formatActivity(value: string) {
    return activityLabels[value] ?? 'Aktivitas belajar';
}

function formatProjectStatus(value: string) {
    return projectStatusLabels[value] ?? 'Sedang dikerjakan';
}

export default function Progress({
    readiness,
    aiInsights,
    readinessHistory,
    assessmentHistory,
    logs,
    evaluations,
    projects,
    roadmaps,
}: {
    readiness: Readiness;
    aiInsights?: AiInsights;
    readinessHistory: ReadinessSnapshot[];
    assessmentHistory: Attempt[];
    logs: Log[];
    evaluations: Evaluation[];
    projects: UserProject[];
    roadmaps: RoadmapHistory[];
}) {
    const [isRetryingAi, setIsRetryingAi] = useState(false);

    const chartData = readinessHistory.map((snapshot) => ({
        name: new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
        }).format(new Date(snapshot.created_at)),
        score: snapshot.score,
        trigger: formatTrigger(snapshot.trigger),
    }));

    const readinessItems = [
        ['Kemampuan', readiness.skill_mastery, Activity],
        ['Jalur belajar', readiness.roadmap_completion, Route],
        ['Proyek', readiness.project_score, Trophy],
        ['Konsistensi', readiness.consistency, Clock3],
        ['Evaluasi', readiness.evaluation_score, BookCheck],
    ] as const;

    const hasAiInsights =
        aiInsights?.generatedByAi === true &&
        Boolean(aiInsights.progress) &&
        Boolean(aiInsights.schedule) &&
        Boolean(aiInsights.obstacles);

    const retryAiInsights = () => {
        setIsRetryingAi(true);

        router.reload({
            only: ['aiInsights'],
            onFinish: () => setIsRetryingAi(false),
        });
    };

    return (
        <>
            <Head title="Perkembangan" />

            <div className="neo-page flex flex-1 flex-col gap-6 py-6 md:py-8">
                <section className="neo-hero neo-accent-yellow border-[#171717]">
                    <div className="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                        <div>
                            <span className="neo-label bg-[#fffdf7]">
                                Riwayat perkembangan belajar
                            </span>

                            <h1 className="mt-5 text-4xl font-black tracking-[-0.045em] md:text-5xl">
                                Lihat perubahan kemampuanmu dari waktu ke waktu.
                            </h1>

                            <p className="mt-3 max-w-2xl text-sm leading-6 font-semibold">
                                Assesment, evaluasi, jalur belajar, waktu
                                belajar, dan proyek disimpan agar kamu dapat
                                melihat perkembangan berdasarkan aktivitas yang
                                benar- benar tercatat.
                            </p>
                        </div>

                        <div className="rounded-[12px] border-2 border-[#171717] bg-[#fffdf7] px-5 py-4 text-center text-[#171717] shadow-[4px_4px_0_#171717]">
                            <p className="text-xs font-black tracking-[0.18em] uppercase">
                                Kesiapan belajar saat ini
                            </p>

                            <p className="mt-1 text-5xl font-black">
                                {Math.round(readiness.score)}
                            </p>
                        </div>
                    </div>
                </section>

                <section className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    {readinessItems.map(([label, value, Icon]) => (
                        <Card key={label}>
                            <CardContent className="pt-6">
                                <Icon className="size-5" />

                                <p className="mt-4 text-3xl font-black">
                                    {Math.round(Number(value))}%
                                </p>

                                <p className="mt-1 text-xs font-black tracking-wider uppercase">
                                    {label}
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </section>

                <Deferred
                    data="aiInsights"
                    fallback={
                        <Card>
                            <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                                <CardTitle className="flex items-center gap-2 text-xl font-black">
                                    <BrainCircuit className="size-5" />
                                    AI Learning Coach
                                </CardTitle>
                            </CardHeader>

                            <CardContent className="grid gap-4 pt-6 lg:grid-cols-3">
                                {[1, 2, 3].map((item) => (
                                    <div
                                        key={item}
                                        className="neo-card-flat space-y-3 p-5"
                                    >
                                        <div className="h-4 w-2/3 animate-pulse rounded bg-muted" />
                                        <div className="h-3 w-full animate-pulse rounded bg-muted" />
                                        <div className="h-3 w-10/12 animate-pulse rounded bg-muted" />
                                        <div className="h-3 w-8/12 animate-pulse rounded bg-muted" />
                                    </div>
                                ))}
                            </CardContent>
                        </Card>
                    }
                >
                    {hasAiInsights && aiInsights ? (
                        <Card>
                            <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                                <div className="flex flex-wrap items-center justify-between gap-3">
                                    <CardTitle className="flex items-center gap-2 text-xl font-black">
                                        <BrainCircuit className="size-5" />
                                        AI Learning Coach
                                    </CardTitle>

                                    <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-lime)] px-2.5 py-1 text-[10px] font-black uppercase">
                                        AI · {aiInsights.model}
                                    </span>
                                </div>
                            </CardHeader>

                            <CardContent className="grid gap-4 pt-6 lg:grid-cols-3">
                                <div className="neo-card-flat p-5">
                                    <div className="flex items-center gap-2">
                                        <BrainCircuit className="size-5" />

                                        <h3 className="font-black">
                                            Ringkasan perkembangan
                                        </h3>
                                    </div>

                                    <p className="mt-3 text-sm leading-6 font-medium whitespace-pre-line">
                                        {aiInsights.progress}
                                    </p>
                                </div>

                                <div className="neo-card-flat p-5">
                                    <div className="flex items-center gap-2">
                                        <Clock3 className="size-5" />

                                        <h3 className="font-black">
                                            Saran jadwal belajar
                                        </h3>
                                    </div>

                                    <p className="mt-3 text-sm leading-6 font-medium whitespace-pre-line">
                                        {aiInsights.schedule}
                                    </p>
                                </div>

                                <div className="neo-card-flat p-5">
                                    <div className="flex items-center gap-2">
                                        <CircleAlert className="size-5" />

                                        <h3 className="font-black">
                                            Pola kendala belajar
                                        </h3>
                                    </div>

                                    <p className="mt-3 text-sm leading-6 font-medium whitespace-pre-line">
                                        {aiInsights.obstacles}
                                    </p>
                                </div>
                            </CardContent>
                        </Card>
                    ) : (
                        <Card>
                            <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                                <CardTitle className="flex items-center gap-2 text-xl font-black">
                                    <BrainCircuit className="size-5" />
                                    AI Learning Coach
                                </CardTitle>
                            </CardHeader>

                            <CardContent
                                className="space-y-4 pt-6"
                                aria-live="polite"
                            >
                                <div className="flex items-start gap-3">
                                    <CircleAlert className="mt-0.5 size-5 shrink-0" />

                                    <p className="text-sm leading-6 font-semibold text-muted-foreground">
                                        {aiInsights?.message ??
                                            'AI Learning Coach sedang tidak tersedia. Silakan coba lagi.'}
                                    </p>
                                </div>

                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    disabled={isRetryingAi}
                                    onClick={retryAiInsights}
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

                <Card>
                    <CardHeader className="border-b-2 border-foreground">
                        <CardTitle className="text-xl font-black">
                            Tren kesiapan belajar
                        </CardTitle>
                    </CardHeader>

                    <CardContent className="h-72 pt-6">
                        {chartData.length > 0 ? (
                            <ResponsiveContainer width="100%" height="100%">
                                <AreaChart data={chartData}>
                                    <CartesianGrid
                                        strokeDasharray="4 4"
                                        opacity={0.25}
                                    />

                                    <XAxis
                                        dataKey="name"
                                        tick={{
                                            fontSize: 12,
                                            fontWeight: 700,
                                        }}
                                    />

                                    <YAxis
                                        domain={[0, 100]}
                                        tick={{
                                            fontSize: 12,
                                            fontWeight: 700,
                                        }}
                                    />

                                    <Tooltip
                                        contentStyle={{
                                            border: '2px solid var(--border)',
                                            borderRadius: 12,
                                            background: 'var(--card)',
                                            color: 'var(--card-foreground)',
                                            fontWeight: 700,
                                        }}
                                    />

                                    <Area
                                        type="monotone"
                                        dataKey="score"
                                        stroke="currentColor"
                                        fill="currentColor"
                                        fillOpacity={0.12}
                                        strokeWidth={3}
                                    />
                                </AreaChart>
                            </ResponsiveContainer>
                        ) : (
                            <div className="flex h-full items-center justify-center text-center text-sm font-bold">
                                Belum ada riwayat skor perkembangan.
                            </div>
                        )}
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="border-b-2 border-foreground">
                        <CardTitle className="text-xl font-black">
                            Riwayat Assesment
                        </CardTitle>
                    </CardHeader>

                    <CardContent className="grid gap-3 pt-6 md:grid-cols-2">
                        {assessmentHistory.length === 0 && (
                            <p className="text-sm font-bold">
                                Belum ada riwayat Assesment.
                            </p>
                        )}

                        {assessmentHistory.map((attempt, index) => (
                            <div
                                key={attempt.attempt_uuid}
                                className="rounded-[12px] border-2 border-foreground bg-muted p-4"
                            >
                                <div className="flex items-center justify-between gap-3">
                                    <p className="text-sm font-black">
                                        Assesment #
                                        {assessmentHistory.length - index}
                                    </p>

                                    <span className="neo-label bg-card">
                                        {attempt.average}
                                    </span>
                                </div>

                                <p className="mt-2 text-xs font-bold text-muted-foreground">
                                    {attempt.date}
                                </p>

                                <p className="mt-3 text-xs leading-5 font-medium">
                                    {attempt.skills
                                        .slice(0, 4)
                                        .map(
                                            (skill) =>
                                                `${skill.name}: ${skill.score}`,
                                        )
                                        .join(' · ')}
                                </p>
                            </div>
                        ))}
                    </CardContent>
                </Card>

                <section className="grid gap-5 lg:grid-cols-2">
                    <Card>
                        <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                            <CardTitle className="flex items-center gap-2 text-xl font-black">
                                <History className="size-5" />
                                Aktivitas terbaru
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="divide-y-2 divide-foreground/15 pt-2">
                            {logs.length === 0 && (
                                <p className="py-6 text-sm font-bold">
                                    Belum ada aktivitas belajar.
                                </p>
                            )}

                            {logs.map((log) => (
                                <div key={log.id} className="py-4">
                                    <div className="flex items-start justify-between gap-4">
                                        <div>
                                            <p className="text-sm font-black uppercase">
                                                {formatActivity(
                                                    log.activity_type,
                                                )}
                                            </p>

                                            <p className="mt-1 text-sm font-medium">
                                                {log.roadmap_item?.material
                                                    ?.title ??
                                                    log.notes ??
                                                    'Aktivitas belajar'}
                                            </p>
                                        </div>

                                        {log.minutes_spent > 0 && (
                                            <span className="neo-label bg-muted">
                                                {log.minutes_spent} menit
                                            </span>
                                        )}
                                    </div>

                                    <p className="mt-2 text-xs font-bold text-muted-foreground">
                                        {formatDate(log.logged_at)}
                                    </p>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="border-b-2 border-[#171717] bg-[var(--neo-lime)] text-[#171717]">
                            <CardTitle className="text-xl font-black">
                                Evaluasi terbaru
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="divide-y-2 divide-foreground/15 pt-2">
                            {evaluations.length === 0 && (
                                <p className="py-6 text-sm font-bold">
                                    Belum ada evaluasi.
                                </p>
                            )}

                            {evaluations.map((evaluation) => (
                                <div
                                    key={evaluation.id}
                                    className="flex items-center justify-between gap-4 py-4"
                                >
                                    <div>
                                        <p className="text-sm font-black">
                                            {evaluation.roadmap_item?.material
                                                ?.title ?? 'Evaluasi materi'}
                                        </p>

                                        <p className="mt-1 text-xs font-bold text-muted-foreground">
                                            {formatDate(evaluation.created_at)}
                                        </p>
                                    </div>

                                    <span
                                        className={`neo-label ${
                                            evaluation.passed
                                                ? 'bg-[var(--neo-lime)]'
                                                : 'bg-[var(--neo-orange)]'
                                        }`}
                                    >
                                        {evaluation.score} ·{' '}
                                        {evaluation.passed ? 'Lulus' : 'Ulangi'}
                                    </span>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </section>

                <section className="grid gap-5 lg:grid-cols-2">
                    <Card>
                        <CardHeader className="border-b-2 border-foreground">
                            <CardTitle className="text-xl font-black">
                                Riwayat proyek
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="divide-y-2 divide-foreground/15 pt-2">
                            {projects.length === 0 && (
                                <p className="py-6 text-sm font-bold">
                                    Belum ada proyek yang dimulai.
                                </p>
                            )}

                            {projects.map((item) => (
                                <div
                                    key={item.id}
                                    className="flex items-center justify-between gap-4 py-4"
                                >
                                    <div>
                                        <p className="text-sm font-black">
                                            {item.project?.title}
                                        </p>

                                        <p className="mt-1 text-xs font-bold text-muted-foreground uppercase">
                                            {formatProjectStatus(item.status)}
                                        </p>
                                    </div>

                                    <Link
                                        href={`/projects/${item.project?.slug}`}
                                        className="flex items-center gap-1 text-sm font-black"
                                    >
                                        {item.progress_percentage}%
                                        <ArrowUpRight className="size-4" />
                                    </Link>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="border-b-2 border-foreground">
                            <CardTitle className="text-xl font-black">
                                Riwayat jalur belajar
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="divide-y-2 divide-foreground/15 pt-2">
                            {roadmaps.length === 0 && (
                                <p className="py-6 text-sm font-bold">
                                    Belum ada riwayat jalur belajar.
                                </p>
                            )}

                            {roadmaps.map((roadmap) => (
                                <div key={roadmap.id} className="py-4">
                                    <div className="flex flex-wrap items-center justify-between gap-3">
                                        <p className="text-sm font-black">
                                            {roadmap.career?.name} · versi{' '}
                                            {roadmap.version}
                                        </p>

                                        {roadmap.is_active && (
                                            <span className="neo-label bg-[var(--neo-lime)]">
                                                Aktif
                                            </span>
                                        )}
                                    </div>

                                    <p className="mt-1 text-sm font-medium">
                                        {roadmap.reason}
                                    </p>

                                    <p className="mt-2 text-xs font-bold text-muted-foreground">
                                        {formatDate(roadmap.created_at)} ·
                                        estimasi {roadmap.estimated_weeks}{' '}
                                        minggu
                                    </p>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </section>
            </div>
        </>
    );
}

Progress.layout = {
    breadcrumbs: [
        {
            title: 'Perkembangan',
            href: '/progress',
        },
    ],
};
