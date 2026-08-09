import { Head, Link } from '@inertiajs/react';
import {
    Activity,
    ArrowUpRight,
    BookCheck,
    Clock3,
    History,
    Route,
    Trophy,
} from 'lucide-react';
import {
    Area,
    AreaChart,
    CartesianGrid,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Readiness {
    score: number;
    skill_mastery: number;
    roadmap_completion: number;
    project_score: number;
    consistency: number;
    evaluation_score: number;
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

function formatDate(value: string) {
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
}

export default function Progress({
    readiness,
    readinessHistory,
    assessmentHistory,
    logs,
    evaluations,
    projects,
    roadmaps,
}: {
    readiness: Readiness;
    readinessHistory: ReadinessSnapshot[];
    assessmentHistory: Attempt[];
    logs: Log[];
    evaluations: Evaluation[];
    projects: UserProject[];
    roadmaps: RoadmapHistory[];
}) {
    const chartData = readinessHistory.map((snapshot) => ({
        name: new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
        }).format(new Date(snapshot.created_at)),
        score: snapshot.score,
        trigger: snapshot.trigger.replaceAll('_', ' '),
    }));

    return (
        <>
            <Head title="Progress" />

            <div className="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-4 md:p-8">
                <section className="neo-card bg-[var(--neo-yellow)] p-6 md:p-8">
                    <div className="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                        <div>
                            <span className="neo-label bg-white">
                                Career readiness history
                            </span>

                            <h1 className="neo-heading mt-4 text-4xl md:text-5xl">
                                Lihat perubahan, bukan perasaan.
                            </h1>

                            <p className="mt-3 max-w-2xl text-sm leading-6 font-medium">
                                Riwayat ini menyimpan asesmen, evaluasi,
                                roadmap, aktivitas belajar, dan proyek agar
                                perkembangan Anda dapat ditelusuri kembali.
                            </p>
                        </div>

                        <div className="border-2 border-black bg-white px-5 py-4 text-center shadow-[4px_4px_0_#111]">
                            <p className="text-xs font-black tracking-[0.18em] uppercase">
                                Readiness sekarang
                            </p>
                            <p className="mt-1 text-5xl font-black">
                                {Math.round(readiness.score)}
                            </p>
                        </div>
                    </div>
                </section>

                <section className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    {[
                        ['Skill', readiness.skill_mastery, Activity],
                        ['Roadmap', readiness.roadmap_completion, Route],
                        ['Project', readiness.project_score, Trophy],
                        ['Consistency', readiness.consistency, Clock3],
                        ['Evaluation', readiness.evaluation_score, BookCheck],
                    ].map(([label, value, Icon]) => {
                        const IconComponent = Icon as typeof Activity;

                        return (
                            <Card key={String(label)}>
                                <CardContent className="pt-6">
                                    <IconComponent className="size-5" />

                                    <p className="mt-4 text-3xl font-black">
                                        {Math.round(Number(value))}%
                                    </p>

                                    <p className="mt-1 text-xs font-black tracking-wider uppercase">
                                        {String(label)}
                                    </p>
                                </CardContent>
                            </Card>
                        );
                    })}
                </section>

                <Card>
                    <CardHeader className="border-b-2 border-black">
                        <CardTitle className="text-xl font-black">
                            Tren career readiness
                        </CardTitle>
                    </CardHeader>

                    <CardContent className="h-72 pt-6">
                        {chartData.length > 0 ? (
                            <ResponsiveContainer width="100%" height="100%">
                                <AreaChart data={chartData}>
                                    <CartesianGrid strokeDasharray="4 4" />
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
                                    <Tooltip />
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
                                Belum ada snapshot career readiness.
                            </div>
                        )}
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="border-b-2 border-black">
                        <CardTitle className="text-xl font-black">
                            Riwayat asesmen
                        </CardTitle>
                    </CardHeader>

                    <CardContent className="grid gap-3 pt-6 md:grid-cols-2">
                        {assessmentHistory.length === 0 && (
                            <p className="text-sm font-bold">
                                Belum ada riwayat asesmen.
                            </p>
                        )}

                        {assessmentHistory.map((attempt, index) => (
                            <div
                                key={attempt.attempt_uuid}
                                className="rounded-xl border-2 border-black bg-[var(--neo-cream)] p-4"
                            >
                                <div className="flex items-center justify-between gap-3">
                                    <p className="text-sm font-black">
                                        Asesmen #
                                        {assessmentHistory.length - index}
                                    </p>

                                    <span className="neo-label bg-white">
                                        {attempt.average}
                                    </span>
                                </div>

                                <p className="mt-2 text-xs font-bold text-black/55">
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
                        <CardHeader className="border-b-2 border-black bg-[var(--neo-blue)]">
                            <CardTitle className="flex items-center gap-2 text-xl font-black">
                                <History className="size-5" />
                                Aktivitas terbaru
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="divide-y-2 divide-black/15 pt-2">
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
                                                {log.activity_type.replaceAll(
                                                    '_',
                                                    ' ',
                                                )}
                                            </p>

                                            <p className="mt-1 text-sm font-medium">
                                                {log.roadmap_item?.material
                                                    ?.title ??
                                                    log.notes ??
                                                    'Aktivitas SkillPath AI'}
                                            </p>
                                        </div>

                                        {log.minutes_spent > 0 && (
                                            <span className="neo-label bg-[var(--neo-cream)]">
                                                {log.minutes_spent}m
                                            </span>
                                        )}
                                    </div>

                                    <p className="mt-2 text-xs font-bold text-black/55">
                                        {formatDate(log.logged_at)}
                                    </p>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="border-b-2 border-black bg-[var(--neo-lime)]">
                            <CardTitle className="text-xl font-black">
                                Evaluasi terakhir
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="divide-y-2 divide-black/15 pt-2">
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
                                        <p className="mt-1 text-xs font-bold text-black/55">
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
                                        {evaluation.passed ? 'Lulus' : 'Ulang'}
                                    </span>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </section>

                <section className="grid gap-5 lg:grid-cols-2">
                    <Card>
                        <CardHeader className="border-b-2 border-black">
                            <CardTitle className="text-xl font-black">
                                Riwayat proyek
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="divide-y-2 divide-black/15 pt-2">
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

                                        <p className="mt-1 text-xs font-bold text-black/55 uppercase">
                                            {item.status.replaceAll('_', ' ')}
                                        </p>
                                    </div>

                                    <Link
                                        href={`/projects/${item.project?.slug}`}
                                        className="flex items-center gap-1 text-sm font-black"
                                    >
                                        {item.progress_percentage}
                                        %
                                        <ArrowUpRight className="size-4" />
                                    </Link>
                                </div>
                            ))}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="border-b-2 border-black">
                            <CardTitle className="text-xl font-black">
                                Versi roadmap
                            </CardTitle>
                        </CardHeader>

                        <CardContent className="divide-y-2 divide-black/15 pt-2">
                            {roadmaps.map((roadmap) => (
                                <div key={roadmap.id} className="py-4">
                                    <div className="flex flex-wrap items-center justify-between gap-3">
                                        <p className="text-sm font-black">
                                            {roadmap.career?.name} · v
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

                                    <p className="mt-2 text-xs font-bold text-black/55">
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
            title: 'Progress',
            href: '/progress',
        },
    ],
};
