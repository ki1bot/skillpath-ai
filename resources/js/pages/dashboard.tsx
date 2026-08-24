import { Head, Link } from '@inertiajs/react';
import {
    Activity,
    ArrowRight,
    BookOpen,
    BookOpenCheck,
    BriefcaseBusiness,
    CalendarDays,
    CheckCircle2,
    Clock3,
    FolderKanban,
    Gauge,
    Map,
    Target,
    Trophy,
} from 'lucide-react';
import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    CartesianGrid,
    Legend,
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

type SkillChartItem = {
    skill: string;
    current: number;
    target: number;
};

type Props = {
    career: {
        name: string;
        slug: string;
        tagline: string;
    };
    readiness: Readiness;
    priorities: Priority[];
    skillChart: SkillChartItem[];
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

function clampPercent(value: number) {
    return Math.min(Math.max(value, 0), 100);
}

function ReadinessIndicator({
    label,
    value,
    weight,
}: {
    label: string;
    value: number;
    weight: string;
}) {
    const percentage = clampPercent(value);

    return (
        <div>
            <div className="mb-2 flex items-center justify-between gap-4">
                <div className="min-w-0">
                    <span className="text-sm font-extrabold">{label}</span>
                    <span className="ml-2 text-xs font-bold text-muted-foreground">
                        {weight}
                    </span>
                </div>

                <span className="shrink-0 font-mono text-xs font-black">
                    {percentage}%
                </span>
            </div>

            <div className="h-3 overflow-hidden rounded-full border-2 border-foreground bg-muted">
                <div
                    className="h-full border-r-2 border-foreground bg-secondary transition-[width] duration-300"
                    style={{
                        width: `${percentage}%`,
                    }}
                />
            </div>
        </div>
    );
}

function DashboardStat({
    icon: Icon,
    label,
    value,
    description,
    accent,
}: {
    icon: typeof Activity;
    label: string;
    value: string;
    description: string;
    accent: 'lime' | 'blue' | 'yellow' | 'orange';
}) {
    const accentClass = {
        lime: 'neo-accent-lime',
        blue: 'neo-accent-blue',
        yellow: 'neo-accent-yellow',
        orange: 'neo-accent-orange',
    }[accent];

    return (
        <div className="neo-card group relative p-5 sm:p-6">
            <div className="flex items-start justify-between gap-4">
                <div
                    className={`flex size-11 shrink-0 items-center justify-center rounded-[10px] border-2 border-foreground ${accentClass}`}
                >
                    <Icon className="size-5" />
                </div>

                <span className="font-mono text-[10px] font-black tracking-[0.14em] text-muted-foreground uppercase">
                    Ringkasan
                </span>
            </div>

            <p className="mt-6 text-xs font-black tracking-[0.12em] text-muted-foreground uppercase">
                {label}
            </p>

            <p className="mt-1 text-3xl font-black tracking-[-0.04em] sm:text-4xl">
                {value}
            </p>

            <p className="mt-2 text-xs leading-relaxed font-semibold text-muted-foreground">
                {description}
            </p>
        </div>
    );
}

export default function Dashboard({
    career,
    readiness,
    priorities,
    skillChart,
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

    const studyHours = Math.floor(totalStudyMinutes / 60);
    const studyMinutes = totalStudyMinutes % 60;

    const totalActivityMinutes = activity.reduce(
        (total, item) => total + item.minutes,
        0,
    );

    const readinessIndicators = [
        {
            label: 'Penguasaan kemampuan',
            value: readiness.skill_mastery,
            weight: '45%',
        },
        {
            label: 'Jalur belajar',
            value: readiness.roadmap_completion,
            weight: '20%',
        },
        {
            label: 'Proyek',
            value: readiness.project_score,
            weight: '20%',
        },
        {
            label: 'Konsistensi',
            value: readiness.consistency,
            weight: '10%',
        },
        {
            label: 'Evaluasi',
            value: readiness.evaluation_score,
            weight: '5%',
        },
    ];

    return (
        <>
            <Head title="Dashboard" />

            <main className="w-full pb-14">
                <div className="mx-auto w-full max-w-[1500px] px-4 py-5 sm:px-6 sm:py-7 lg:px-8">
                    <section className="mb-6 flex flex-col gap-5 border-b-2 border-foreground/15 pb-6 lg:flex-row lg:items-end lg:justify-between">
                        <div className="min-w-0">
                            <span className="neo-label">Dashboard</span>

                            <h1 className="neo-heading mt-4 max-w-4xl text-3xl sm:text-4xl lg:text-5xl">
                                Perkembangan belajarmu di{' '}
                                <span className="underline decoration-secondary decoration-[5px] underline-offset-[6px]">
                                    {career.name}
                                </span>
                            </h1>

                            <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold text-muted-foreground sm:text-base">
                                {career.tagline}
                            </p>
                        </div>

                        <div className="flex shrink-0 flex-wrap gap-2">
                            <Button asChild variant="outline">
                                <Link href="/skills">
                                    <Target />
                                    Peta kemampuan
                                </Link>
                            </Button>

                            <Button asChild>
                                <Link href="/roadmap">
                                    <Map />
                                    Buka jalur belajar
                                </Link>
                            </Button>
                        </div>
                    </section>

                    <section className="grid gap-5 xl:grid-cols-[1.55fr_0.75fr]">
                        <div className="neo-card overflow-hidden">
                            <div className="grid min-h-full lg:grid-cols-[1.05fr_0.95fr]">
                                <div className="flex flex-col justify-between bg-foreground p-6 text-background sm:p-8">
                                    <div>
                                        <div className="flex items-center gap-2">
                                            <Gauge className="size-5" />

                                            <p className="text-xs font-black tracking-[0.14em] uppercase">
                                                Kesiapan belajar
                                            </p>
                                        </div>

                                        <div className="mt-8 flex items-end gap-2">
                                            <span className="text-7xl font-black tracking-[-0.08em] sm:text-8xl">
                                                {readiness.score}
                                            </span>

                                            <span className="mb-3 text-xl font-black text-background/50">
                                                /100
                                            </span>
                                        </div>

                                        <p className="mt-4 max-w-md text-sm leading-6 font-semibold text-background/65">
                                            Skor ini merangkum penguasaan
                                            kemampuan, jalur belajar, proyek,
                                            konsistensi, dan hasil evaluasimu.
                                        </p>
                                    </div>

                                    <div className="mt-8 flex flex-wrap gap-2">
                                        <span className="rounded-full border-2 border-background/30 px-3 py-1 text-xs font-black">
                                            {readiness.active_days_28} hari
                                            aktif
                                        </span>

                                        <span className="rounded-full border-2 border-background/30 px-3 py-1 text-xs font-black">
                                            {roadmapPercent}% jalur belajar
                                        </span>
                                    </div>
                                </div>

                                <div className="bg-secondary p-6 text-[#171717] sm:p-8">
                                    <div className="flex items-start justify-between gap-5">
                                        <div>
                                            <p className="text-xs font-black tracking-[0.13em] uppercase">
                                                Fokus sekarang
                                            </p>

                                            <h2 className="mt-2 text-2xl font-black tracking-[-0.035em]">
                                                Tidak perlu memperbaiki semuanya
                                                sekaligus.
                                            </h2>
                                        </div>

                                        <Trophy className="size-7 shrink-0" />
                                    </div>

                                    {priorities.length > 0 ? (
                                        <div className="mt-7 space-y-3">
                                            {priorities
                                                .slice(0, 3)
                                                .map((item, index) => (
                                                    <div
                                                        key={item.skill_id}
                                                        className="flex items-center gap-3 rounded-[10px] border-2 border-[#171717] bg-[#fffdf8] p-3 shadow-[2px_2px_0_#171717]"
                                                    >
                                                        <span className="flex size-8 shrink-0 items-center justify-center rounded-[8px] border-2 border-[#171717] bg-[#171717] font-mono text-xs font-black text-white">
                                                            {String(
                                                                index + 1,
                                                            ).padStart(2, '0')}
                                                        </span>

                                                        <div className="min-w-0 flex-1">
                                                            <div className="flex items-center justify-between gap-3">
                                                                <p className="truncate text-sm font-black">
                                                                    {item.name}
                                                                </p>

                                                                <span className="shrink-0 font-mono text-[11px] font-black">
                                                                    selisih{' '}
                                                                    {item.gap}
                                                                </span>
                                                            </div>

                                                            <div className="mt-2 h-2 overflow-hidden rounded-full border border-[#171717] bg-[#e8e2d7]">
                                                                <div
                                                                    className="h-full bg-[#aac8f5]"
                                                                    style={{
                                                                        width: `${clampPercent(
                                                                            item.current,
                                                                        )}%`,
                                                                    }}
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                ))}
                                        </div>
                                    ) : (
                                        <div className="mt-7 rounded-[10px] border-2 border-[#171717] bg-[#fffdf8] p-5">
                                            <CheckCircle2 className="size-7" />

                                            <p className="mt-3 font-black">
                                                Tidak ada kemampuan prioritas.
                                            </p>

                                            <p className="mt-1 text-xs font-semibold opacity-70">
                                                Pertahankan hasilmu dan
                                                lanjutkan jalur belajar.
                                            </p>
                                        </div>
                                    )}

                                    <Button
                                        asChild
                                        variant="outline"
                                        className="mt-6 border-[#171717] bg-[#fffdf8] text-[#171717]"
                                    >
                                        <Link href="/skills">
                                            Lihat peta kemampuan
                                            <ArrowRight />
                                        </Link>
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <div className="neo-card flex flex-col justify-between p-6">
                            <div>
                                <div className="flex items-start justify-between">
                                    <div>
                                        <p className="text-xs font-black tracking-[0.13em] text-muted-foreground uppercase">
                                            Jalur belajar
                                        </p>

                                        <h2 className="mt-1 text-2xl font-black">
                                            Progres belajarmu
                                        </h2>
                                    </div>

                                    <div className="flex size-11 items-center justify-center rounded-[10px] border-2 border-foreground bg-accent text-[#171717]">
                                        <Map className="size-5" />
                                    </div>
                                </div>

                                <div className="mt-8 flex items-end justify-between gap-4">
                                    <div>
                                        <p className="text-5xl font-black tracking-[-0.05em]">
                                            {roadmapPercent}%
                                        </p>

                                        <p className="mt-1 text-xs font-bold text-muted-foreground">
                                            progres keseluruhan
                                        </p>
                                    </div>

                                    {roadmap && (
                                        <p className="text-right text-xs leading-5 font-bold text-muted-foreground">
                                            {roadmap.completed} dari{' '}
                                            {roadmap.total} materi
                                        </p>
                                    )}
                                </div>

                                <div className="neo-progress mt-6 h-5">
                                    <span
                                        style={{
                                            width: `${roadmapPercent}%`,
                                        }}
                                    />
                                </div>

                                <div className="mt-6 grid grid-cols-2 gap-3">
                                    <div className="rounded-[10px] border-2 border-foreground bg-muted/60 p-3">
                                        <CalendarDays className="size-4" />

                                        <p className="mt-3 text-xl font-black">
                                            {roadmap
                                                ? `±${roadmap.estimated_weeks}`
                                                : '—'}
                                        </p>

                                        <p className="mt-1 text-[11px] font-bold text-muted-foreground">
                                            estimasi minggu
                                        </p>
                                    </div>

                                    <div className="rounded-[10px] border-2 border-foreground bg-muted/60 p-3">
                                        <BookOpenCheck className="size-4" />

                                        <p className="mt-3 text-xl font-black">
                                            {roadmap ? roadmap.total : '—'}
                                        </p>

                                        <p className="mt-1 text-[11px] font-bold text-muted-foreground">
                                            total materi
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <Button asChild className="mt-6 w-full">
                                <Link href="/roadmap">
                                    Lanjutkan belajar
                                    <ArrowRight />
                                </Link>
                            </Button>
                        </div>
                    </section>

                    <section className="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <DashboardStat
                            icon={Clock3}
                            label="Total waktu belajar"
                            value={`${studyHours}j ${studyMinutes}m`}
                            description="Akumulasi seluruh waktu belajar yang sudah kamu catat."
                            accent="blue"
                        />

                        <DashboardStat
                            icon={Activity}
                            label="Aktif 28 hari"
                            value={`${readiness.active_days_28} hari`}
                            description="Jumlah hari saat kamu memiliki aktivitas belajar selama empat minggu terakhir."
                            accent="lime"
                        />

                        <DashboardStat
                            icon={BookOpen}
                            label="Aktivitas 14 hari"
                            value={`${totalActivityMinutes} mnt`}
                            description="Total waktu belajar yang tercatat selama dua minggu terakhir."
                            accent="yellow"
                        />

                        <DashboardStat
                            icon={FolderKanban}
                            label="Proyek aktif"
                            value={
                                activeProject
                                    ? `${activeProject.progress_percentage}%`
                                    : 'Belum ada'
                            }
                            description={
                                activeProject
                                    ? 'Progres proyek portofolio yang sedang kamu kerjakan.'
                                    : 'Mulai proyek ketika kamu siap menerapkan kemampuan yang sudah dipelajari.'
                            }
                            accent="orange"
                        />
                    </section>

                    <section className="mt-5 grid gap-5 xl:grid-cols-[1.35fr_0.65fr]">
                        <div className="neo-card p-5 sm:p-6">
                            <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p className="text-xs font-black tracking-[0.13em] text-muted-foreground uppercase">
                                        Aktivitas belajar
                                    </p>

                                    <h2 className="mt-1 text-2xl font-black tracking-[-0.035em]">
                                        Ritme 14 hari terakhir
                                    </h2>

                                    <p className="mt-2 max-w-xl text-sm font-medium text-muted-foreground">
                                        Grafik ini menampilkan waktu belajar
                                        yang benar-benar tercatat di sistem.
                                    </p>
                                </div>

                                <span className="w-fit rounded-full border-2 border-foreground bg-muted px-3 py-1 text-xs font-black">
                                    menit / hari
                                </span>
                            </div>

                            <div className="mt-6 h-[280px] w-full sm:h-[330px]">
                                <ResponsiveContainer width="100%" height="100%">
                                    <AreaChart
                                        data={activity}
                                        margin={{
                                            top: 10,
                                            right: 8,
                                            left: -20,
                                            bottom: 0,
                                        }}
                                    >
                                        <defs>
                                            <linearGradient
                                                id="activityGradient"
                                                x1="0"
                                                y1="0"
                                                x2="0"
                                                y2="1"
                                            >
                                                <stop
                                                    offset="5%"
                                                    stopColor="#aac8f5"
                                                    stopOpacity={0.85}
                                                />

                                                <stop
                                                    offset="95%"
                                                    stopColor="#aac8f5"
                                                    stopOpacity={0.08}
                                                />
                                            </linearGradient>
                                        </defs>

                                        <CartesianGrid
                                            strokeDasharray="4 4"
                                            vertical={false}
                                            opacity={0.18}
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
                                            allowDecimals={false}
                                        />

                                        <Tooltip
                                            cursor={{
                                                stroke: '#1f1f1c',
                                                strokeDasharray: '4 4',
                                            }}
                                            contentStyle={{
                                                border: '2px solid #1f1f1c',
                                                borderRadius: 10,
                                                boxShadow: '3px 3px 0 #1f1f1c',
                                                background: '#fffdf8',
                                                color: '#1f1f1c',
                                                fontWeight: 800,
                                            }}
                                            formatter={(value) => [
                                                `${value} menit`,
                                                'Belajar',
                                            ]}
                                        />

                                        <Area
                                            type="monotone"
                                            dataKey="minutes"
                                            stroke="#1f1f1c"
                                            strokeWidth={3}
                                            fill="url(#activityGradient)"
                                            activeDot={{
                                                r: 5,
                                                stroke: '#1f1f1c',
                                                strokeWidth: 2,
                                                fill: '#d7e6b2',
                                            }}
                                        />
                                    </AreaChart>
                                </ResponsiveContainer>
                            </div>
                        </div>

                        <div className="neo-card overflow-hidden">
                            <div className="border-b-2 border-foreground bg-accent p-5 text-[#171717] sm:p-6">
                                <div className="flex items-center justify-between gap-3">
                                    <div>
                                        <p className="text-xs font-black tracking-[0.13em] uppercase">
                                            Berikutnya
                                        </p>

                                        <h2 className="mt-1 text-2xl font-black">
                                            Prioritas belajar
                                        </h2>
                                    </div>

                                    <BookOpenCheck className="size-7" />
                                </div>
                            </div>

                            <div className="p-5 sm:p-6">
                                {nextItem ? (
                                    <>
                                        <span className="inline-flex rounded-full border-2 border-foreground bg-secondary px-3 py-1 text-xs font-black text-[#171717]">
                                            {nextItem.skill}
                                        </span>

                                        <h3 className="mt-5 text-2xl font-black tracking-[-0.035em]">
                                            {nextItem.title}
                                        </h3>

                                        <p className="mt-3 text-sm leading-6 font-medium text-muted-foreground">
                                            {nextItem.status ===
                                            'needs_reinforcement'
                                                ? 'Materi ini perlu dipelajari kembali karena evaluasi sebelumnya belum memenuhi batas kelulusan.'
                                                : 'Materi ini menjadi salah satu langkah berikutnya dalam jalur belajarmu.'}
                                        </p>

                                        <div className="mt-6">
                                            <div className="mb-2 flex items-center justify-between text-xs font-black">
                                                <span>Progres</span>

                                                <span>
                                                    {nextItem.progress}%
                                                </span>
                                            </div>

                                            <div className="neo-progress h-4">
                                                <span
                                                    style={{
                                                        width: `${clampPercent(
                                                            nextItem.progress,
                                                        )}%`,
                                                    }}
                                                />
                                            </div>
                                        </div>

                                        <Button asChild className="mt-6 w-full">
                                            <Link
                                                href={`/roadmap/materials/${nextItem.slug}`}
                                            >
                                                Buka materi
                                                <ArrowRight />
                                            </Link>
                                        </Button>
                                    </>
                                ) : (
                                    <div className="neo-empty min-h-[250px]">
                                        <BookOpenCheck className="size-9" />

                                        <h3 className="mt-4 text-lg font-black">
                                            Belum ada materi aktif
                                        </h3>

                                        <p className="mt-2 max-w-sm text-sm font-medium text-muted-foreground">
                                            Periksa jalur belajar atau
                                            selesaikan Assesment untuk
                                            mendapatkan rekomendasi berikutnya.
                                        </p>

                                        <Button
                                            asChild
                                            variant="outline"
                                            size="sm"
                                            className="mt-5"
                                        >
                                            <Link href="/roadmap">
                                                Buka jalur belajar
                                            </Link>
                                        </Button>
                                    </div>
                                )}
                            </div>
                        </div>
                    </section>

                    <section className="mt-5 grid gap-5 xl:grid-cols-[1.15fr_0.85fr]">
                        <div className="neo-card p-5 sm:p-6">
                            <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p className="text-xs font-black tracking-[0.13em] text-muted-foreground uppercase">
                                        Peta kemampuan
                                    </p>

                                    <h2 className="mt-1 text-2xl font-black tracking-[-0.035em]">
                                        Posisi kemampuanmu sekarang
                                    </h2>

                                    <p className="mt-2 text-sm font-medium text-muted-foreground">
                                        Bandingkan hasilmu sekarang dengan
                                        target penguasaan untuk jurusan{' '}
                                        {career.name}.
                                    </p>
                                </div>

                                <Button asChild variant="ghost" size="sm">
                                    <Link href="/skills">
                                        Lihat detail
                                        <ArrowRight />
                                    </Link>
                                </Button>
                            </div>

                            {skillChart.length > 0 ? (
                                <div className="mt-6 h-[320px] w-full">
                                    <ResponsiveContainer
                                        width="100%"
                                        height="100%"
                                    >
                                        <BarChart
                                            data={skillChart}
                                            layout="vertical"
                                            margin={{
                                                top: 0,
                                                right: 15,
                                                left: 10,
                                                bottom: 0,
                                            }}
                                            barGap={4}
                                        >
                                            <CartesianGrid
                                                horizontal={false}
                                                strokeDasharray="4 4"
                                                opacity={0.15}
                                            />

                                            <XAxis
                                                type="number"
                                                domain={[0, 100]}
                                                tick={{
                                                    fontSize: 10,
                                                    fontWeight: 700,
                                                }}
                                                tickLine={false}
                                                axisLine={false}
                                            />

                                            <YAxis
                                                type="category"
                                                dataKey="skill"
                                                width={95}
                                                tick={{
                                                    fontSize: 10,
                                                    fontWeight: 800,
                                                }}
                                                tickLine={false}
                                                axisLine={false}
                                            />

                                            <Tooltip
                                                contentStyle={{
                                                    border: '2px solid #1f1f1c',
                                                    borderRadius: 10,
                                                    boxShadow:
                                                        '3px 3px 0 #1f1f1c',
                                                    background: '#fffdf8',
                                                    color: '#1f1f1c',
                                                    fontWeight: 800,
                                                }}
                                            />

                                            <Legend
                                                wrapperStyle={{
                                                    fontSize: 11,
                                                    fontWeight: 800,
                                                }}
                                            />

                                            <Bar
                                                dataKey="current"
                                                name="Saat ini"
                                                fill="#aac8f5"
                                                stroke="#1f1f1c"
                                                strokeWidth={2}
                                                radius={[0, 5, 5, 0]}
                                                maxBarSize={18}
                                            />

                                            <Bar
                                                dataKey="target"
                                                name="Target"
                                                fill="#d7e6b2"
                                                stroke="#1f1f1c"
                                                strokeWidth={2}
                                                radius={[0, 5, 5, 0]}
                                                maxBarSize={18}
                                            />
                                        </BarChart>
                                    </ResponsiveContainer>
                                </div>
                            ) : (
                                <div className="neo-empty mt-6">
                                    <Target className="size-8" />

                                    <p className="mt-3 font-black">
                                        Data kemampuan belum tersedia.
                                    </p>
                                </div>
                            )}
                        </div>

                        <div className="neo-card p-5 sm:p-6">
                            <div className="flex items-start justify-between gap-4">
                                <div>
                                    <p className="text-xs font-black tracking-[0.13em] text-muted-foreground uppercase">
                                        Komposisi kesiapan
                                    </p>

                                    <h2 className="mt-1 text-2xl font-black tracking-[-0.035em]">
                                        Dari mana skor ini berasal?
                                    </h2>
                                </div>

                                <Gauge className="size-6 shrink-0" />
                            </div>

                            <div className="mt-7 space-y-5">
                                {readinessIndicators.map((item) => (
                                    <ReadinessIndicator
                                        key={item.label}
                                        label={item.label}
                                        value={item.value}
                                        weight={item.weight}
                                    />
                                ))}
                            </div>

                            <div className="mt-7 rounded-[10px] border-2 border-foreground bg-muted/50 p-4">
                                <p className="text-xs leading-5 font-semibold text-muted-foreground">
                                    Skor ini adalah indikator internal SkillPath
                                    untuk membantu membaca perkembangan dan
                                    menentukan prioritas belajar. Skor ini bukan
                                    nilai akademik resmi dan bukan jaminan hasil
                                    di dunia kerja.
                                </p>
                            </div>
                        </div>
                    </section>

                    <section className="mt-5 grid gap-5 lg:grid-cols-2">
                        <div className="neo-card p-5 sm:p-6">
                            <div className="flex items-start justify-between gap-4">
                                <div>
                                    <p className="text-xs font-black tracking-[0.13em] text-muted-foreground uppercase">
                                        Prioritas kemampuan
                                    </p>

                                    <h2 className="mt-1 text-2xl font-black tracking-[-0.035em]">
                                        Yang masih perlu diperkuat
                                    </h2>
                                </div>

                                <Target className="size-6 shrink-0" />
                            </div>

                            <div className="mt-6 space-y-3">
                                {priorities.length > 0 ? (
                                    priorities.map((item, index) => (
                                        <div
                                            key={item.skill_id}
                                            className="rounded-[11px] border-2 border-foreground bg-muted/45 p-4"
                                        >
                                            <div className="flex gap-3">
                                                <span className="flex size-9 shrink-0 items-center justify-center rounded-[9px] border-2 border-foreground bg-card font-mono text-xs font-black">
                                                    {String(index + 1).padStart(
                                                        2,
                                                        '0',
                                                    )}
                                                </span>

                                                <div className="min-w-0 flex-1">
                                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                                        <h3 className="font-black">
                                                            {item.name}
                                                        </h3>

                                                        <span className="rounded-full border-2 border-foreground bg-accent px-2.5 py-0.5 font-mono text-[10px] font-black text-[#171717]">
                                                            SELISIH {item.gap}
                                                        </span>
                                                    </div>

                                                    <p className="mt-2 text-xs leading-5 font-medium text-muted-foreground">
                                                        {item.reason}
                                                    </p>

                                                    <div className="mt-4 grid grid-cols-2 gap-2">
                                                        <div className="rounded-lg border border-foreground/20 bg-card px-3 py-2">
                                                            <p className="text-[10px] font-black tracking-wide text-muted-foreground uppercase">
                                                                Sekarang
                                                            </p>

                                                            <p className="mt-1 font-mono text-sm font-black">
                                                                {item.current}
                                                            </p>
                                                        </div>

                                                        <div className="rounded-lg border border-foreground/20 bg-card px-3 py-2">
                                                            <p className="text-[10px] font-black tracking-wide text-muted-foreground uppercase">
                                                                Target
                                                            </p>

                                                            <p className="mt-1 font-mono text-sm font-black">
                                                                {item.target}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))
                                ) : (
                                    <div className="neo-empty">
                                        <CheckCircle2 className="size-8" />

                                        <h3 className="mt-3 font-black">
                                            Tidak ada kemampuan utama yang
                                            tertinggal
                                        </h3>

                                        <p className="mt-1 text-sm font-medium text-muted-foreground">
                                            Kemampuan utamamu sudah memenuhi
                                            target yang digunakan saat ini.
                                        </p>
                                    </div>
                                )}
                            </div>

                            <Button
                                asChild
                                variant="outline"
                                className="mt-5 w-full"
                            >
                                <Link href="/skills">
                                    Buka peta kemampuan
                                    <ArrowRight />
                                </Link>
                            </Button>
                        </div>

                        <div className="neo-card overflow-hidden">
                            <div className="border-b-2 border-foreground bg-[#e8a16e] p-5 text-[#171717] sm:p-6">
                                <div className="flex items-center justify-between gap-3">
                                    <div>
                                        <p className="text-xs font-black tracking-[0.13em] uppercase">
                                            Portofolio
                                        </p>

                                        <h2 className="mt-1 text-2xl font-black">
                                            Proyek aktif
                                        </h2>
                                    </div>

                                    <BriefcaseBusiness className="size-7" />
                                </div>
                            </div>

                            <div className="p-5 sm:p-6">
                                {activeProject ? (
                                    <>
                                        <div className="flex size-12 items-center justify-center rounded-[10px] border-2 border-foreground bg-muted">
                                            <FolderKanban className="size-6" />
                                        </div>

                                        <h3 className="mt-5 text-2xl font-black tracking-[-0.035em]">
                                            {activeProject.project.title}
                                        </h3>

                                        <span className="mt-3 inline-flex rounded-full border-2 border-foreground bg-muted px-3 py-1 text-xs font-black capitalize">
                                            {activeProject.status.replace(
                                                /_/g,
                                                ' ',
                                            )}
                                        </span>

                                        <div className="mt-7">
                                            <div className="mb-2 flex items-center justify-between text-xs font-black">
                                                <span>Progres proyek</span>

                                                <span>
                                                    {
                                                        activeProject.progress_percentage
                                                    }
                                                    %
                                                </span>
                                            </div>

                                            <div className="neo-progress h-5">
                                                <span
                                                    style={{
                                                        width: `${clampPercent(
                                                            activeProject.progress_percentage,
                                                        )}%`,
                                                    }}
                                                />
                                            </div>
                                        </div>

                                        <p className="mt-5 text-sm leading-6 font-medium text-muted-foreground">
                                            Gunakan proyek ini untuk
                                            mempraktikkan kemampuan yang sudah
                                            dipelajari dan membangun bukti yang
                                            dapat dimasukkan ke portofolio.
                                        </p>

                                        <Button asChild className="mt-6 w-full">
                                            <Link
                                                href={`/projects/${activeProject.project.slug}`}
                                            >
                                                Lanjutkan proyek
                                                <ArrowRight />
                                            </Link>
                                        </Button>
                                    </>
                                ) : (
                                    <div className="neo-empty min-h-[300px]">
                                        <FolderKanban className="size-9" />

                                        <h3 className="mt-4 text-xl font-black">
                                            Belum ada proyek aktif
                                        </h3>

                                        <p className="mt-2 max-w-sm text-sm leading-6 font-medium text-muted-foreground">
                                            Pilih proyek yang sesuai dengan
                                            kemampuan yang sudah kamu miliki dan
                                            bagian yang ingin kamu latih.
                                        </p>

                                        <Button asChild className="mt-5">
                                            <Link href="/projects">
                                                Cari proyek
                                                <ArrowRight />
                                            </Link>
                                        </Button>
                                    </div>
                                )}
                            </div>
                        </div>
                    </section>
                </div>
            </main>
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
