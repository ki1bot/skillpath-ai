import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    BrainCircuit,
    CheckCircle2,
    RotateCcw,
    TriangleAlert,
} from 'lucide-react';
import {
    PolarAngleAxis,
    PolarGrid,
    Radar,
    RadarChart,
    ResponsiveContainer,
} from 'recharts';
import { Button } from '@/components/ui/button';

type SkillGap = {
    skill_id: number;
    name: string;
    category: string;
    current: number;
    target: number;
    gap: number;
    priority: number;
    status: 'terpenuhi' | 'kesenjangan_tinggi' | 'perlu_ditingkatkan';
    reason: string;
    prerequisites: {
        id: number;
        name: string;
    }[];
};

type Props = {
    career: {
        name: string;
        slug: string;
    };
    skills: SkillGap[];
    summary: string;
    averageMastery: number;
};

const statusMap = {
    terpenuhi: {
        label: 'Terpenuhi',
        className: 'bg-secondary',
        icon: CheckCircle2,
    },
    kesenjangan_tinggi: {
        label: 'Gap tinggi',
        className: 'bg-[#FF8FAB]',
        icon: TriangleAlert,
    },
    perlu_ditingkatkan: {
        label: 'Perlu naik',
        className: 'bg-[#FFCE5C]',
        icon: ArrowRight,
    },
};

export default function Skills({
    career,
    skills,
    summary,
    averageMastery,
}: Props) {
    const chart = skills.slice(0, 10).map((item) => ({
        skill: item.name.length > 14 ? `${item.name.slice(0, 13)}…` : item.name,
        current: item.current,
        target: item.target,
    }));

    const priorities = skills.filter((item) => item.gap > 0).slice(0, 3);

    return (
        <>
            <Head title="Skill & Kesenjangan" />

            <div className="mx-auto w-full max-w-7xl px-4 py-8 md:px-6 md:py-10">
                <div className="flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
                    <div className="max-w-3xl">
                        <span className="neo-label">Peta skill</span>

                        <h1 className="neo-heading mt-5 text-4xl sm:text-5xl">
                            Gap menuju {career.name}
                        </h1>

                        <p className="mt-4 leading-relaxed font-medium text-muted-foreground">
                            Target bukan nilai sempurna. Ia adalah standar
                            internal SkillPath untuk membandingkan posisi
                            sekarang dengan kebutuhan jalur ini.
                        </p>
                    </div>

                    <Button asChild variant="outline">
                        <Link href="/assessment">
                            <RotateCcw />
                            Ulang asesmen
                        </Link>
                    </Button>
                </div>

                <div className="mt-8 grid gap-5 lg:grid-cols-[0.8fr_1.2fr]">
                    <section className="neo-card p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Penguasaan berbobot
                                </p>
                                <p className="mt-1 text-4xl font-black">
                                    {averageMastery}%
                                </p>
                            </div>

                            <div className="flex size-14 items-center justify-center rounded-full border-2 border-foreground bg-secondary">
                                <BrainCircuit className="size-6 text-[#171717]" />
                            </div>
                        </div>

                        <div className="mt-5 h-[320px] w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <RadarChart data={chart} outerRadius="72%">
                                    <PolarGrid
                                        stroke="currentColor"
                                        opacity={0.25}
                                    />
                                    <PolarAngleAxis
                                        dataKey="skill"
                                        tick={{
                                            fill: 'currentColor',
                                            fontSize: 10,
                                            fontWeight: 700,
                                        }}
                                    />
                                    <Radar
                                        dataKey="target"
                                        stroke="currentColor"
                                        fill="currentColor"
                                        fillOpacity={0.08}
                                        strokeWidth={2}
                                    />
                                    <Radar
                                        dataKey="current"
                                        stroke="#5A9E2D"
                                        fill="#C7FF5E"
                                        fillOpacity={0.45}
                                        strokeWidth={2}
                                    />
                                </RadarChart>
                            </ResponsiveContainer>
                        </div>
                    </section>

                    <section className="neo-card p-6">
                        <div className="flex items-start gap-4">
                            <div className="flex size-10 shrink-0 items-center justify-center rounded-xl border-2 border-foreground bg-[#79D7FF]">
                                <BrainCircuit className="size-5 text-[#171717]" />
                            </div>

                            <div>
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Penjelasan hasil
                                </p>
                                <p className="mt-2 leading-relaxed font-semibold">
                                    {summary}
                                </p>
                            </div>
                        </div>

                        <div className="mt-7 grid gap-3 sm:grid-cols-3">
                            {priorities.map((item, index) => (
                                <div
                                    key={item.skill_id}
                                    className="rounded-xl border-2 border-foreground bg-muted p-4"
                                >
                                    <span className="font-mono text-xs font-black">
                                        0{index + 1}
                                    </span>
                                    <h3 className="mt-5 leading-tight font-black">
                                        {item.name}
                                    </h3>
                                    <p className="mt-2 text-xs font-semibold text-muted-foreground">
                                        Gap {item.gap} poin
                                    </p>
                                </div>
                            ))}
                        </div>

                        <Button asChild className="mt-6">
                            <Link href="/roadmap">
                                Lihat urutan belajar
                                <ArrowRight />
                            </Link>
                        </Button>
                    </section>
                </div>

                <section className="mt-8">
                    <div className="mb-5">
                        <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                            Rekomendasi yang dapat dijelaskan
                        </p>
                        <h2 className="mt-1 text-2xl font-black">
                            Kenapa setiap skill berada di posisi ini
                        </h2>
                    </div>

                    <div className="space-y-4">
                        {skills.map((item) => {
                            const state = statusMap[item.status];

                            const Icon = state.icon;

                            return (
                                <article
                                    key={item.skill_id}
                                    className="neo-card-flat grid gap-5 p-5 md:grid-cols-[1fr_250px] md:items-center"
                                >
                                    <div>
                                        <div className="flex flex-wrap items-center gap-2">
                                            <h3 className="text-lg font-black">
                                                {item.name}
                                            </h3>

                                            <span
                                                className={`inline-flex items-center gap-1.5 rounded-full border-2 border-foreground px-2.5 py-1 text-[10px] font-black text-[#171717] uppercase ${state.className}`}
                                            >
                                                <Icon className="size-3" />
                                                {state.label}
                                            </span>

                                            <span className="text-xs font-bold text-muted-foreground">
                                                {item.category}
                                            </span>
                                        </div>

                                        <p className="mt-2 max-w-3xl text-sm leading-relaxed font-medium text-muted-foreground">
                                            {item.reason}
                                        </p>

                                        {item.prerequisites.length > 0 && (
                                            <p className="mt-3 text-xs font-bold">
                                                Prasyarat:{' '}
                                                {item.prerequisites
                                                    .map((pre) => pre.name)
                                                    .join(', ')}
                                            </p>
                                        )}
                                    </div>

                                    <div>
                                        <div className="flex justify-between text-xs font-black">
                                            <span>Sekarang {item.current}</span>
                                            <span>Target {item.target}</span>
                                        </div>

                                        <div className="mt-2 h-4 overflow-hidden rounded-full border-2 border-foreground bg-muted">
                                            <div
                                                className="h-full border-r-2 border-foreground bg-[#79D7FF]"
                                                style={{
                                                    width: `${Math.min(
                                                        item.current,
                                                        100,
                                                    )}%`,
                                                }}
                                            />
                                        </div>

                                        <div className="mt-2 text-right font-mono text-xs font-black">
                                            prioritas {item.priority}
                                        </div>
                                    </div>
                                </article>
                            );
                        })}
                    </div>
                </section>
            </div>
        </>
    );
}

Skills.layout = {
    breadcrumbs: [
        {
            title: 'Skill',
            href: '/skills',
        },
    ],
};
