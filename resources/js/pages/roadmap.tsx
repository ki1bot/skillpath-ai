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
    evaluation_attempts: number;
    reinforcement_count: number;
    reinforcement_for_roadmap_item_id?: number | null;
    material: {
        id: number;
        title: string;
        slug: string;
        summary: string;
        difficulty: string;
        estimated_minutes: number;
        material_type: 'core' | 'reinforcement';
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
    locked: 'Menunggu materi sebelumnya',
    completed: 'Selesai',
    needs_reinforcement: 'Perlu diulang',
    reinforcement_required: 'Selesaikan penguatan',
};

export default function RoadmapPage({ roadmap }: { roadmap: Roadmap }) {
    const stages = Object.values(
        roadmap.items.reduce<
            Record<
                number,
                {
                    stage: number;
                    title: string;
                    items: RoadmapItem[];
                }
            >
        >((result, item) => {
            result[item.stage] ??= {
                stage: item.stage,
                title: item.stage_title,
                items: [],
            };

            result[item.stage].items.push(item);

            return result;
        }, {}),
    ).sort((a, b) => a.stage - b.stage);

    const coreItems = roadmap.items.filter(
        (item) => item.material.material_type === 'core',
    );

    const completed = coreItems.filter(
        (item) => item.status === 'completed',
    ).length;

    const percentage =
        coreItems.length > 0
            ? Math.round((completed / coreItems.length) * 100)
            : 0;

    return (
        <>
            <Head title="Jalur Belajar" />

            <div className="neo-page py-8 md:py-10">
                <section className="neo-card overflow-hidden">
                    <div className="grid gap-6 p-6 sm:p-8 lg:grid-cols-[1fr_auto] lg:items-end">
                        <div>
                            <span className="neo-label">
                                Jalur belajar v{roadmap.version}
                            </span>

                            <h1 className="neo-heading mt-5 text-4xl sm:text-5xl">
                                {roadmap.career.name}
                            </h1>

                            <p className="mt-4 max-w-3xl text-sm leading-relaxed font-medium text-muted-foreground">
                                Jalur belajar terdiri dari tiga tahap: Amatir,
                                Menengah, dan Ahli. Setiap tahap memiliki
                                sembilan materi dari tiga bidang jurusan. Materi
                                dikerjakan satu per satu. Materi berikutnya baru
                                terbuka setelah tugas sebelumnya diperiksa admin
                                dan mendapat nilai minimal 70. Tahap berikutnya
                                baru dapat dimulai setelah seluruh sembilan
                                materi pada tahap sebelumnya selesai.
                            </p>

                            <p className="mt-3 text-sm font-bold text-muted-foreground">
                                Estimasi penyelesaian sekitar{' '}
                                {roadmap.estimated_weeks} minggu.
                            </p>
                        </div>

                        <div className="min-w-56">
                            <div className="flex justify-between text-xs font-black">
                                <span>Perkembangan</span>

                                <span>
                                    {completed}/{coreItems.length}
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

                <div className="mt-8 space-y-12">
                    {stages.map((stage, stageIndex) => {
                        const stageCoreItems = stage.items.filter(
                            (item) => item.material.material_type === 'core',
                        );

                        const stageCompleted = stageCoreItems.filter(
                            (item) => item.status === 'completed',
                        ).length;

                        const stageFinished =
                            stageCoreItems.length === 9 &&
                            stageCompleted === stageCoreItems.length;

                        return (
                            <section key={`${stage.stage}-${stage.title}`}>
                                <div className="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                                    <div className="flex items-center gap-3">
                                        <span className="flex size-9 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-secondary font-mono text-sm font-black text-[#171717] shadow-[3px_3px_0_var(--neo-shadow-color)]">
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

                                    <div
                                        className={`rounded-full border-2 border-foreground px-3 py-1.5 text-xs font-black ${
                                            stageFinished
                                                ? 'bg-[var(--neo-lime)] text-[#171717]'
                                                : 'bg-card'
                                        }`}
                                    >
                                        {stageCompleted}/9 materi selesai
                                    </div>
                                </div>

                                <div className="relative space-y-4">
                                    <div
                                        aria-hidden="true"
                                        className="absolute inset-y-0 left-0 border-l-2 border-dashed border-foreground/35"
                                    />

                                    {stage.items.map((item) => {
                                        const reinforcementRequired =
                                            item.status ===
                                            'reinforcement_required';

                                        const locked =
                                            item.status === 'locked' ||
                                            reinforcementRequired;

                                        const itemCompleted =
                                            item.status === 'completed';

                                        const reinforcement =
                                            item.material.material_type ===
                                            'reinforcement';

                                        return (
                                            <div
                                                key={item.id}
                                                className="relative pl-5 sm:pl-8"
                                            >
                                                <div className="absolute top-1/2 left-px z-10 flex size-6 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 border-foreground bg-background">
                                                    {itemCompleted ? (
                                                        <CheckCircle2 className="size-4 fill-secondary" />
                                                    ) : reinforcementRequired ? (
                                                        <RotateCcw className="size-3.5" />
                                                    ) : locked ? (
                                                        <LockKeyhole className="size-3.5" />
                                                    ) : (
                                                        <Circle className="size-3.5 fill-[var(--neo-blue)]" />
                                                    )}
                                                </div>

                                                <article
                                                    className={`neo-card-flat grid gap-5 p-5 md:grid-cols-[auto_1fr_auto] md:items-center ${
                                                        locked
                                                            ? 'opacity-65'
                                                            : ''
                                                    } ${
                                                        reinforcement
                                                            ? 'border-[var(--neo-pink)]'
                                                            : ''
                                                    }`}
                                                >
                                                    <div className="hidden size-12 items-center justify-center rounded-[11px] border-2 border-foreground bg-muted font-mono text-sm font-black md:flex">
                                                        {String(
                                                            item.position,
                                                        ).padStart(2, '0')}
                                                    </div>

                                                    <div>
                                                        <div className="flex flex-wrap items-center gap-2">
                                                            <span className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                                                {
                                                                    item
                                                                        .material
                                                                        .skill
                                                                        .name
                                                                }
                                                            </span>

                                                            <span className="rounded-full border-2 border-foreground/15 bg-card px-2 py-0.5 text-[10px] font-black uppercase">
                                                                {
                                                                    item
                                                                        .material
                                                                        .difficulty
                                                                }
                                                            </span>

                                                            {reinforcement && (
                                                                <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-pink)] px-2 py-0.5 text-[10px] font-black text-[#171717] uppercase">
                                                                    Penguatan
                                                                </span>
                                                            )}

                                                            {item.status ===
                                                                'needs_reinforcement' && (
                                                                <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-orange)] px-2 py-0.5 text-[10px] font-black text-[#171717] uppercase">
                                                                    Perlu
                                                                    diperbaiki
                                                                </span>
                                                            )}
                                                        </div>

                                                        <h3 className="mt-1 text-lg font-black">
                                                            {
                                                                item.material
                                                                    .title
                                                            }
                                                        </h3>

                                                        <p className="mt-2 text-sm leading-relaxed font-medium text-muted-foreground">
                                                            {
                                                                item.material
                                                                    .summary
                                                            }
                                                        </p>

                                                        <div className="mt-3 flex flex-wrap gap-3 text-xs font-bold">
                                                            <span>
                                                                {
                                                                    item
                                                                        .material
                                                                        .estimated_minutes
                                                                }{' '}
                                                                menit
                                                            </span>

                                                            <span>
                                                                {statusLabel[
                                                                    item.status
                                                                ] ??
                                                                    item.status}
                                                            </span>

                                                            {item.evaluation_score !==
                                                                null &&
                                                                item.evaluation_score !==
                                                                    undefined && (
                                                                    <span>
                                                                        Nilai:{' '}
                                                                        {
                                                                            item.evaluation_score
                                                                        }
                                                                        /100
                                                                    </span>
                                                                )}

                                                            {item.evaluation_attempts >
                                                                0 && (
                                                                <span>
                                                                    {
                                                                        item.evaluation_attempts
                                                                    }{' '}
                                                                    pengumpulan
                                                                </span>
                                                            )}
                                                        </div>
                                                    </div>

                                                    <div className="md:text-right">
                                                        {reinforcementRequired ? (
                                                            <div className="max-w-48 text-xs font-bold text-muted-foreground">
                                                                <RotateCcw className="mb-2 inline size-4" />
                                                                <br />
                                                                Selesaikan
                                                                materi penguatan
                                                                sebelum kembali
                                                                ke materi ini.
                                                            </div>
                                                        ) : item.status ===
                                                          'locked' ? (
                                                            <div className="max-w-52 text-xs leading-5 font-bold text-muted-foreground">
                                                                <LockKeyhole className="mb-2 inline size-4" />
                                                                <br />
                                                                Selesaikan
                                                                materi
                                                                sebelumnya dan
                                                                dapatkan nilai
                                                                minimal 70 untuk
                                                                membuka materi
                                                                ini.
                                                            </div>
                                                        ) : (
                                                            <Button
                                                                asChild
                                                                variant={
                                                                    itemCompleted
                                                                        ? 'outline'
                                                                        : 'default'
                                                                }
                                                                size="sm"
                                                            >
                                                                <Link
                                                                    href={`/roadmap/materials/${item.material.slug}`}
                                                                >
                                                                    {itemCompleted
                                                                        ? 'Tinjau ulang'
                                                                        : item.status ===
                                                                            'needs_reinforcement'
                                                                          ? 'Perbaiki tugas'
                                                                          : 'Buka materi'}

                                                                    {item.status ===
                                                                        'needs_reinforcement' ||
                                                                    reinforcement ? (
                                                                        <RotateCcw />
                                                                    ) : (
                                                                        <ArrowRight />
                                                                    )}
                                                                </Link>
                                                            </Button>
                                                        )}
                                                    </div>
                                                </article>
                                            </div>
                                        );
                                    })}
                                </div>
                            </section>
                        );
                    })}
                </div>
            </div>
        </>
    );
}

RoadmapPage.layout = {
    breadcrumbs: [
        {
            title: 'Jalur Belajar',
            href: '/roadmap',
        },
    ],
};
