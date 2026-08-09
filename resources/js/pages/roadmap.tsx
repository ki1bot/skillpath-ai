import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    CheckCircle2,
    Circle,
    LockKeyhole,
    RotateCcw,
} from 'lucide-react';
import { Button } from '@/components/ui/button';

type RoadmapItem = {
    id: number;
    stage: number;
    stage_title: string;
    position: number;
    status: string;
    progress_percentage: number;
    evaluation_score?: number | null;
    material: {
        id: number;
        title: string;
        slug: string;
        summary: string;
        difficulty: string;
        estimated_minutes: number;
        skill: {
            id: number;
            name: string;
            prerequisites: {
                id: number;
                name: string;
            }[];
        };
    };
};

type Roadmap = {
    id: number;
    version: number;
    reason: string;
    estimated_weeks: number;
    career: {
        name: string;
    };
    items: RoadmapItem[];
};

const statusLabel: Record<string, string> = {
    available: 'Siap dipelajari',
    locked: 'Menunggu prasyarat',
    completed: 'Dikuasai',
    needs_reinforcement: 'Perlu penguatan',
};

export default function RoadmapPage({ roadmap }: { roadmap: Roadmap }) {
    const stages = Object.values(
        roadmap.items.reduce<
            Record<
                number,
                {
                    title: string;
                    items: RoadmapItem[];
                }
            >
        >((result, item) => {
            result[item.stage] ??= {
                title: item.stage_title,
                items: [],
            };

            result[item.stage].items.push(item);

            return result;
        }, {}),
    );

    const completed = roadmap.items.filter(
        (item) => item.status === 'completed',
    ).length;

    const percentage =
        roadmap.items.length > 0
            ? Math.round((completed / roadmap.items.length) * 100)
            : 0;

    return (
        <>
            <Head title="Roadmap" />

            <div className="mx-auto w-full max-w-6xl px-4 py-8 md:px-6 md:py-10">
                <section className="neo-card overflow-hidden">
                    <div className="grid gap-6 p-6 sm:p-8 lg:grid-cols-[1fr_auto] lg:items-end">
                        <div>
                            <span className="neo-label">
                                Roadmap v{roadmap.version}
                            </span>

                            <h1 className="neo-heading mt-5 text-4xl sm:text-5xl">
                                {roadmap.career.name}
                            </h1>

                            <p className="mt-4 max-w-2xl text-sm leading-relaxed font-medium text-muted-foreground">
                                Dibuat dari {roadmap.reason}. Estimasi sekitar{' '}
                                {roadmap.estimated_weeks} minggu berdasarkan
                                waktu belajar yang tersedia.
                            </p>
                        </div>

                        <div className="min-w-56">
                            <div className="flex justify-between text-xs font-black">
                                <span>Progress</span>
                                <span>
                                    {completed}/{roadmap.items.length}
                                </span>
                            </div>

                            <div className="neo-progress mt-2 h-4">
                                <span
                                    style={{
                                        width: `${percentage}%`,
                                    }}
                                />
                            </div>

                            <p className="mt-2 text-right font-mono text-sm font-black">
                                {percentage}%
                            </p>
                        </div>
                    </div>
                </section>

                <div className="mt-8 space-y-10">
                    {stages.map((stage, stageIndex) => (
                        <section key={stage.title}>
                            <div className="mb-5 flex items-center gap-3">
                                <span className="flex size-9 items-center justify-center rounded-xl border-2 border-foreground bg-secondary font-mono text-sm font-black text-[#171717] shadow-[3px_3px_0_var(--foreground)]">
                                    {stageIndex + 1}
                                </span>

                                <div>
                                    <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                        Tahap {stageIndex + 1}
                                    </p>
                                    <h2 className="text-2xl font-black tracking-tight">
                                        {stage.title}
                                    </h2>
                                </div>
                            </div>

                            <div className="space-y-4 border-l-2 border-dashed border-foreground/35 pl-5 sm:pl-8">
                                {stage.items.map((item) => {
                                    const locked = item.status === 'locked';

                                    const completed =
                                        item.status === 'completed';

                                    return (
                                        <article
                                            key={item.id}
                                            className={`neo-card-flat relative grid gap-5 p-5 md:grid-cols-[auto_1fr_auto] md:items-center ${
                                                locked ? 'opacity-60' : ''
                                            }`}
                                        >
                                            <div className="absolute top-8 -left-[31px] flex size-6 items-center justify-center rounded-full border-2 border-foreground bg-background sm:-left-[43px]">
                                                {completed ? (
                                                    <CheckCircle2 className="size-4 fill-secondary" />
                                                ) : locked ? (
                                                    <LockKeyhole className="size-3.5" />
                                                ) : (
                                                    <Circle className="size-3.5 fill-[#79D7FF]" />
                                                )}
                                            </div>

                                            <div className="hidden size-12 items-center justify-center rounded-xl border-2 border-foreground bg-muted font-mono text-sm font-black md:flex">
                                                {String(item.position).padStart(
                                                    2,
                                                    '0',
                                                )}
                                            </div>

                                            <div>
                                                <div className="flex flex-wrap items-center gap-2">
                                                    <span className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                                        {
                                                            item.material.skill
                                                                .name
                                                        }
                                                    </span>

                                                    {item.status ===
                                                        'needs_reinforcement' && (
                                                        <span className="rounded-full border-2 border-foreground bg-[#FF8FAB] px-2 py-0.5 text-[10px] font-black text-[#171717] uppercase">
                                                            ulang
                                                        </span>
                                                    )}
                                                </div>

                                                <h3 className="mt-1 text-lg font-black">
                                                    {item.material.title}
                                                </h3>

                                                <p className="mt-2 text-sm leading-relaxed font-medium text-muted-foreground">
                                                    {item.material.summary}
                                                </p>

                                                <div className="mt-3 flex flex-wrap gap-3 text-xs font-bold">
                                                    <span>
                                                        {
                                                            item.material
                                                                .estimated_minutes
                                                        }{' '}
                                                        menit
                                                    </span>
                                                    <span>
                                                        {
                                                            item.material
                                                                .difficulty
                                                        }
                                                    </span>
                                                    <span>
                                                        {
                                                            statusLabel[
                                                                item.status
                                                            ]
                                                        }
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="md:text-right">
                                                {locked ? (
                                                    <div className="text-xs font-bold text-muted-foreground">
                                                        <LockKeyhole className="mb-2 inline size-4" />
                                                        <br />
                                                        {item.material.skill
                                                            .prerequisites
                                                            .length > 0
                                                            ? item.material.skill.prerequisites
                                                                  .map(
                                                                      (
                                                                          prerequisite,
                                                                      ) =>
                                                                          prerequisite.name,
                                                                  )
                                                                  .join(', ')
                                                            : 'Belum tersedia'}
                                                    </div>
                                                ) : (
                                                    <Button
                                                        asChild
                                                        variant={
                                                            completed
                                                                ? 'outline'
                                                                : 'default'
                                                        }
                                                        size="sm"
                                                    >
                                                        <Link
                                                            href={`/roadmap/materials/${item.material.slug}`}
                                                        >
                                                            {completed
                                                                ? 'Review'
                                                                : 'Buka'}
                                                            {item.status ===
                                                            'needs_reinforcement' ? (
                                                                <RotateCcw />
                                                            ) : (
                                                                <ArrowRight />
                                                            )}
                                                        </Link>
                                                    </Button>
                                                )}
                                            </div>
                                        </article>
                                    );
                                })}
                            </div>
                        </section>
                    ))}
                </div>
            </div>
        </>
    );
}

RoadmapPage.layout = {
    breadcrumbs: [
        {
            title: 'Roadmap',
            href: '/roadmap',
        },
    ],
};
