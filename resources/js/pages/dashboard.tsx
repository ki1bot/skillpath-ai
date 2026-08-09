import { Head, Link } from '@inertiajs/react';
import {
    Activity,
    ArrowRight,
    BookOpenCheck,
    Clock3,
    FolderKanban,
    Map,
    Target,
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
import { Button } from '@/components/ui/button';

type Readiness = {
    score: number;
    skill_mastery: number;
    roadmap_completion: number;
    project_score: number;
    consistency: number;
    evaluation_score: number;
    active_days_28: number;
};

type Priority = {
    skill_id: number;
    name: string;
    gap: number;
    current: number;
    target: number;
    reason: string;
};

type Props = {
    career: {
        name: string;
        slug: string;
        tagline: string;
    };
    readiness: Readiness;
    priorities: Priority[];
    skillChart: {
        skill: string;
        current: number;
        target: number;
    }[];
    roadmap: {
        version: number;
        estimated_weeks: number;
        total: number;
        completed: number;
    } | null;
    nextItem: {
        id: number;
        title: string;
        slug: string;
        skill: string;
        status: string;
        progress: number;
    } | null;
    totalStudyMinutes: number;
    activity: {
        date: string;
        minutes: number;
    }[];
    activeProject?: {
        progress_percentage: number;
        status: string;
        project: {
            title: string;
            slug: string;
        };
    } | null;
};

export default function Dashboard({
    career,
    readiness,
    priorities,
    roadmap,
    nextItem,
    totalStudyMinutes,
    activity,
    activeProject,
}: Props) {
    const roadmapPercent =
        roadmap && roadmap.total > 0
            ? Math.round((roadmap.completed / roadmap.total) * 100)
            : 0;

    return (
        <>
            <Head title="Dashboard" />

            <div className="mx-auto w-full max-w-7xl px-4 py-8 md:px-6 md:py-10">
                <div className="flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
                    <div>
                        <p className="text-xs font-black tracking-[0.16em] text-muted-foreground uppercase">
                            Current target
                        </p>

                        <h1 className="neo-heading mt-2 text-4xl sm:text-5xl">
                            {career.name}
                        </h1>

                        <p className="mt-3 max-w-2xl leading-relaxed font-medium text-muted-foreground">
                            {career.tagline}
                        </p>
                    </div>

                    <Button asChild variant="outline">
                        <Link href="/skills">
                            <Target />
                            Lihat skill gap
                        </Link>
                    </Button>
                </div>

                <section className="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    <div className="neo-card bg-secondary p-5 text-[#171717]">
                        <div className="flex items-start justify-between">
                            <p className="text-xs font-black tracking-[0.14em] uppercase">
                                Career readiness
                            </p>
                            <Trophy className="size-5" />
                        </div>

                        <p className="mt-7 text-5xl font-black tracking-tight">
                            {readiness.score}
                            <span className="text-xl">/100</span>
                        </p>

                        <p className="mt-2 text-xs font-bold">
                            Indikator internal, bukan prediksi diterima kerja.
                        </p>
                    </div>

                    <div className="neo-card p-5">
                        <div className="flex items-start justify-between">
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Roadmap
                            </p>
                            <Map className="size-5" />
                        </div>

                        <p className="mt-7 text-4xl font-black">
                            {roadmapPercent}%
                        </p>

                        <p className="mt-2 text-sm font-semibold text-muted-foreground">
                            {roadmap
                                ? `${roadmap.completed}/${roadmap.total} langkah · ±${roadmap.estimated_weeks} minggu`
                                : 'Belum dibuat'}
                        </p>
                    </div>

                    <div className="neo-card p-5">
                        <div className="flex items-start justify-between">
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Waktu belajar
                            </p>
                            <Clock3 className="size-5" />
                        </div>

                        <p className="mt-7 text-4xl font-black">
                            {Math.floor(totalStudyMinutes / 60)}j{' '}
                            {totalStudyMinutes % 60}m
                        </p>

                        <p className="mt-2 text-sm font-semibold text-muted-foreground">
                            Tercatat dari aktivitas belajar.
                        </p>
                    </div>

                    <div className="neo-card p-5">
                        <div className="flex items-start justify-between">
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Konsistensi
                            </p>
                            <Activity className="size-5" />
                        </div>

                        <p className="mt-7 text-4xl font-black">
                            {readiness.active_days_28}
                            <span className="text-lg"> hari</span>
                        </p>

                        <p className="mt-2 text-sm font-semibold text-muted-foreground">
                            Aktif dalam 28 hari terakhir.
                        </p>
                    </div>
                </section>

                <section className="mt-6 grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
                    <div className="neo-card p-6">
                        <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                            <div>
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Aktivitas 14 hari
                                </p>
                                <h2 className="mt-1 text-2xl font-black">
                                    Belajar yang benar-benar tercatat
                                </h2>
                            </div>

                            <span className="rounded-full border-2 border-foreground bg-muted px-3 py-1 text-xs font-black">
                                menit / hari
                            </span>
                        </div>

                        <div className="mt-6 h-[280px]">
                            <ResponsiveContainer width="100%" height="100%">
                                <AreaChart data={activity}>
                                    <CartesianGrid
                                        strokeDasharray="3 3"
                                        opacity={0.2}
                                    />
                                    <XAxis
                                        dataKey="date"
                                        tick={{
                                            fontSize: 10,
                                            fontWeight: 700,
                                        }}
                                        tickLine={false}
                                        axisLine={false}
                                    />
                                    <YAxis
                                        tick={{
                                            fontSize: 10,
                                            fontWeight: 700,
                                        }}
                                        tickLine={false}
                                        axisLine={false}
                                        width={30}
                                    />
                                    <Tooltip
                                        contentStyle={{
                                            border: '2px solid #171717',
                                            borderRadius: 12,
                                            fontWeight: 700,
                                        }}
                                    />
                                    <Area
                                        type="monotone"
                                        dataKey="minutes"
                                        stroke="#171717"
                                        fill="#79D7FF"
                                        strokeWidth={3}
                                    />
                                </AreaChart>
                            </ResponsiveContainer>
                        </div>
                    </div>

                    <div className="space-y-6">
                        <div className="neo-card overflow-hidden">
                            <div className="border-b-2 border-foreground bg-[#79D7FF] p-5 text-[#171717]">
                                <p className="text-xs font-black tracking-[0.14em] uppercase">
                                    Langkah berikutnya
                                </p>
                            </div>

                            <div className="p-6">
                                {nextItem ? (
                                    <>
                                        <span className="rounded-full border-2 border-foreground bg-muted px-3 py-1 text-xs font-black">
                                            {nextItem.skill}
                                        </span>

                                        <h2 className="mt-5 text-2xl font-black tracking-tight">
                                            {nextItem.title}
                                        </h2>

                                        <p className="mt-2 text-sm font-medium text-muted-foreground">
                                            {nextItem.status ===
                                            'needs_reinforcement'
                                                ? 'Evaluasi sebelumnya belum lulus. Kembali ke materi penguatan sebelum lanjut.'
                                                : `Progres saat ini ${nextItem.progress}%.`}
                                        </p>

                                        <Button asChild className="mt-6">
                                            <Link
                                                href={`/roadmap/materials/${nextItem.slug}`}
                                            >
                                                Buka materi
                                                <ArrowRight />
                                            </Link>
                                        </Button>
                                    </>
                                ) : (
                                    <>
                                        <BookOpenCheck className="size-8" />
                                        <h2 className="mt-5 text-xl font-black">
                                            Tidak ada materi aktif.
                                        </h2>
                                        <p className="mt-2 text-sm font-medium text-muted-foreground">
                                            Selesaikan asesmen atau cek roadmap
                                            terbaru.
                                        </p>
                                    </>
                                )}
                            </div>
                        </div>

                        <div className="neo-card p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                        Project pulse
                                    </p>
                                    <h2 className="mt-1 text-xl font-black">
                                        {activeProject?.project.title ??
                                            'Belum ada proyek aktif'}
                                    </h2>
                                </div>
                                <FolderKanban className="size-6" />
                            </div>

                            {activeProject ? (
                                <>
                                    <div className="neo-progress mt-5 h-4">
                                        <span
                                            style={{
                                                width: `${activeProject.progress_percentage}%`,
                                            }}
                                        />
                                    </div>

                                    <div className="mt-2 flex justify-between text-xs font-black">
                                        <span>
                                            {activeProject.status.replace(
                                                '_',
                                                ' ',
                                            )}
                                        </span>
                                        <span>
                                            {activeProject.progress_percentage}%
                                        </span>
                                    </div>

                                    <Button
                                        asChild
                                        variant="outline"
                                        size="sm"
                                        className="mt-5"
                                    >
                                        <Link
                                            href={`/projects/${activeProject.project.slug}`}
                                        >
                                            Lanjut proyek
                                        </Link>
                                    </Button>
                                </>
                            ) : (
                                <Button
                                    asChild
                                    variant="outline"
                                    size="sm"
                                    className="mt-5"
                                >
                                    <Link href="/projects">
                                        Lihat rekomendasi
                                    </Link>
                                </Button>
                            )}
                        </div>
                    </div>
                </section>

                <section className="mt-6 grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                    <div className="neo-card p-6">
                        <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                            Readiness breakdown
                        </p>
                        <h2 className="mt-1 text-2xl font-black">
                            Skor ini datang dari mana?
                        </h2>

                        <div className="mt-6 space-y-4">
                            {[
                                [
                                    'Penguasaan skill',
                                    readiness.skill_mastery,
                                    '45%',
                                ],
                                [
                                    'Penyelesaian roadmap',
                                    readiness.roadmap_completion,
                                    '20%',
                                ],
                                [
                                    'Proyek portofolio',
                                    readiness.project_score,
                                    '20%',
                                ],
                                ['Konsistensi', readiness.consistency, '10%'],
                                ['Evaluasi', readiness.evaluation_score, '5%'],
                            ].map(([label, value, weight]) => (
                                <div key={String(label)}>
                                    <div className="mb-2 flex justify-between text-xs font-black">
                                        <span>
                                            {label}{' '}
                                            <span className="text-muted-foreground">
                                                ({weight})
                                            </span>
                                        </span>
                                        <span>{value}%</span>
                                    </div>

                                    <div className="h-3 overflow-hidden rounded-full border-2 border-foreground bg-muted">
                                        <div
                                            className="h-full border-r-2 border-foreground bg-secondary"
                                            style={{
                                                width: `${Math.min(
                                                    Number(value),
                                                    100,
                                                )}%`,
                                            }}
                                        />
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="neo-card p-6">
                        <div className="flex items-end justify-between">
                            <div>
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Gap tertinggi
                                </p>
                                <h2 className="mt-1 text-2xl font-black">
                                    Jangan dikerjakan semuanya sekaligus.
                                </h2>
                            </div>

                            <Button asChild variant="ghost" size="sm">
                                <Link href="/skills">
                                    Detail
                                    <ArrowRight />
                                </Link>
                            </Button>
                        </div>

                        <div className="mt-6 space-y-3">
                            {priorities.map((item, index) => (
                                <div
                                    key={item.skill_id}
                                    className="rounded-xl border-2 border-foreground bg-muted p-4"
                                >
                                    <div className="flex items-center gap-3">
                                        <span className="flex size-8 shrink-0 items-center justify-center rounded-lg border-2 border-foreground bg-card font-mono text-xs font-black">
                                            0{index + 1}
                                        </span>

                                        <div className="min-w-0 flex-1">
                                            <div className="flex justify-between gap-3">
                                                <p className="font-black">
                                                    {item.name}
                                                </p>
                                                <span className="font-mono text-xs font-black">
                                                    gap {item.gap}
                                                </span>
                                            </div>

                                            <p className="mt-1 line-clamp-2 text-xs leading-relaxed font-medium text-muted-foreground">
                                                {item.reason}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
    ],
};
