import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    BrainCircuit,
    ChartNoAxesCombined,
    CheckCircle2,
    RotateCcw,
    TriangleAlert,
} from 'lucide-react';
import { useCallback, useEffect, useRef, useState } from 'react';
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
    averageMastery: number;
};

type AiState = 'loading' | 'ready' | 'error';

const statusMap = {
    terpenuhi: {
        label: 'Terpenuhi',
        className: 'bg-secondary',
        icon: CheckCircle2,
    },
    kesenjangan_tinggi: {
        label: 'Kesenjangan besar',
        className: 'bg-[var(--neo-pink)]',
        icon: TriangleAlert,
    },
    perlu_ditingkatkan: {
        label: 'Perlu ditingkatkan',
        className: 'bg-[var(--neo-yellow)]',
        icon: ArrowRight,
    },
};

export default function Skills({ career, skills, averageMastery }: Props) {
    const [summary, setSummary] = useState<string | null>(null);
    const [generatedByAi, setGeneratedByAi] = useState(false);
    const [aiState, setAiState] = useState<AiState>('loading');
    const requestStarted = useRef(false);
    const activeController = useRef<AbortController | null>(null);

    const chart = skills.slice(0, 10).map((item) => ({
        skill: item.name.length > 14 ? `${item.name.slice(0, 13)}…` : item.name,
        current: item.current,
        target: item.target,
    }));

    const priorities = skills.filter((item) => item.gap > 0).slice(0, 3);

    const loadAiSummary = useCallback(async () => {
        activeController.current?.abort();

        const controller = new AbortController();

        activeController.current = controller;

        const timeout = window.setTimeout(() => controller.abort(), 25000);

        setSummary(null);
        setGeneratedByAi(false);
        setAiState('loading');

        try {
            const response = await fetch('/skills?ai=1', {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error(
                    `AI request failed with status ${response.status}`,
                );
            }

            const data = (await response.json()) as {
                summary?: unknown;
                generated_by_ai?: unknown;
                model?: unknown;
            };

            if (
                typeof data.summary !== 'string' ||
                data.summary.trim() === ''
            ) {
                throw new Error('AI response did not contain a summary');
            }

            setSummary(data.summary.trim());
            setGeneratedByAi(data.generated_by_ai === true);
            setAiState('ready');
        } catch {
            setAiState('error');
        } finally {
            window.clearTimeout(timeout);

            if (activeController.current === controller) {
                activeController.current = null;
            }
        }
    }, []);

    useEffect(() => {
        if (requestStarted.current) {
            return;
        }

        requestStarted.current = true;

        void loadAiSummary();

        return () => {
            activeController.current?.abort();
        };
    }, [loadAiSummary]);

    return (
        <>
            <Head title="Keahlian & Kesenjangan" />

            <div className="neo-page py-8 md:py-10">
                <div className="flex flex-col justify-between gap-5 lg:flex-row lg:items-end">
                    <div className="max-w-3xl">
                        <span className="neo-label">Peta kemampuan</span>

                        <h1 className="neo-heading mt-5 text-4xl sm:text-5xl">
                            Kesenjangan menuju {career.name}
                        </h1>

                        <p className="mt-4 leading-relaxed font-medium text-muted-foreground">
                            Nilai target bukan nilai sempurna. Angka tersebut
                            digunakan sebagai patokan untuk melihat jarak antara
                            kemampuanmu sekarang dan kebutuhan jalur karier ini.
                        </p>
                    </div>

                    <Button asChild variant="outline">
                        <Link href="/assessment">
                            <RotateCcw />
                            Ulangi Assessment
                        </Link>
                    </Button>
                </div>

                <div className="mt-8 grid gap-5 lg:grid-cols-[0.8fr_1.2fr]">
                    <section className="neo-card p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Penguasaan kemampuan
                                </p>

                                <p className="mt-1 text-4xl font-black">
                                    {averageMastery}%
                                </p>
                            </div>

                            <div className="flex size-14 items-center justify-center rounded-full border-2 border-[#171717] bg-secondary">
                                <ChartNoAxesCombined className="size-6 text-[#171717]" />
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
                            <div className="flex size-10 shrink-0 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-blue)]">
                                <BrainCircuit className="size-5 text-[#171717]" />
                            </div>

                            <div className="min-w-0 flex-1">
                                <div className="flex flex-wrap items-center gap-2">
                                    <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                        Penjelasan rekomendasi
                                    </p>

                                    {aiState === 'ready' && (
                                        <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-lime)] px-2.5 py-1 text-[10px] font-black text-[#171717] uppercase">
                                            {generatedByAi
                                                ? 'AI + data sistem'
                                                : 'Data sistem'}
                                        </span>
                                    )}
                                </div>

                                <div
                                    className="mt-3"
                                    aria-busy={aiState === 'loading'}
                                >
                                    {aiState === 'loading' && (
                                        <div className="space-y-2">
                                            <div className="h-4 w-full animate-pulse rounded bg-muted" />
                                            <div className="h-4 w-11/12 animate-pulse rounded bg-muted" />
                                            <div className="h-4 w-8/12 animate-pulse rounded bg-muted" />
                                        </div>
                                    )}

                                    {aiState === 'ready' && summary && (
                                        <p className="leading-relaxed font-semibold">
                                            {summary}
                                        </p>
                                    )}

                                    {aiState === 'error' && (
                                        <div className="space-y-3">
                                            <p className="text-sm font-semibold text-muted-foreground">
                                                Penjelasan belum dapat dimuat.
                                                Data kemampuan dan urutan
                                                belajar tetap tersedia karena
                                                dihitung langsung oleh sistem.
                                            </p>

                                            <Button
                                                type="button"
                                                variant="outline"
                                                size="sm"
                                                onClick={() => {
                                                    void loadAiSummary();
                                                }}
                                            >
                                                <RotateCcw />
                                                Coba lagi
                                            </Button>
                                        </div>
                                    )}
                                </div>

                                {aiState === 'ready' && generatedByAi && (
                                    <p className="mt-4 text-xs leading-5 font-bold text-muted-foreground">
                                        Penjelasan dibuat model AI dari skor,
                                        kesenjangan, prioritas, alasan, dan
                                        prasyarat yang sudah dihitung SkillPath.
                                        AI tidak mengubah nilai, status, atau
                                        urutan roadmap.
                                    </p>
                                )}

                                {aiState === 'ready' && !generatedByAi && (
                                    <p className="mt-4 text-xs leading-5 font-bold text-muted-foreground">
                                        Layanan AI sedang tidak memberikan
                                        respons yang dapat digunakan. Penjelasan
                                        ini dibuat langsung dari skor,
                                        kesenjangan, prioritas, alasan, dan
                                        prasyarat yang sudah dihitung sistem.
                                    </p>
                                )}
                            </div>
                        </div>

                        <div className="mt-7 grid gap-3 sm:grid-cols-3">
                            {priorities.map((item, index) => (
                                <div
                                    key={item.skill_id}
                                    className="rounded-[12px] border-2 border-foreground bg-muted p-4"
                                >
                                    <span className="font-mono text-xs font-black">
                                        {String(index + 1).padStart(2, '0')}
                                    </span>

                                    <h3 className="mt-5 leading-tight font-black">
                                        {item.name}
                                    </h3>

                                    <p className="mt-2 text-xs font-semibold text-muted-foreground">
                                        Selisih {item.gap} poin
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
                            Alasan di balik rekomendasi
                        </p>

                        <h2 className="mt-1 text-2xl font-black">
                            Mengapa setiap kemampuan berada di urutan ini?
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
                                                className={`inline-flex items-center gap-1.5 rounded-full border-2 border-[#171717] px-2.5 py-1 text-[10px] font-black text-[#171717] uppercase ${state.className}`}
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
                                                className="h-full border-r-2 border-[#171717] bg-[var(--neo-blue)]"
                                                style={{
                                                    width: `${Math.min(
                                                        item.current,
                                                        100,
                                                    )}%`,
                                                }}
                                            />
                                        </div>

                                        <div className="mt-2 text-right font-mono text-xs font-black">
                                            Prioritas {item.priority}
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
            title: 'Keahlian',
            href: '/skills',
        },
    ],
};
