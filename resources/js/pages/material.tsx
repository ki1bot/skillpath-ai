import { Head, Link, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    BookOpen,
    CheckCircle2,
    ExternalLink,
    Save,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

type Material = {
    id: number;
    title: string;
    slug: string;
    summary: string;
    learning_objectives: string[];
    difficulty: string;
    estimated_minutes: number;
    resource_title?: string | null;
    resource_url?: string | null;
    practice_task: string;
    quiz_question: string;
    quiz_options: Record<string, string>;
    skill: {
        name: string;
        prerequisites: {
            id: number;
            name: string;
        }[];
    };
};

type Evaluation = {
    id: number;
    score: number;
    passed: boolean;
    feedback: string;
};

type Item = {
    id: number;
    status: string;
    progress_percentage: number;
    evaluation_score?: number | null;
    evaluations: Evaluation[];
};

export default function MaterialPage({
    item,
    material,
}: {
    item: Item;
    material: Material;
}) {
    const progressForm = useForm({
        progress_percentage: Math.min(item.progress_percentage, 95),
        minutes_spent: 30,
        notes: '',
        obstacle: '',
        evidence_url: '',
    });

    const evaluationForm = useForm({
        answer: '',
    });

    const latestEvaluation = item.evaluations?.[0];

    const saveProgress = (event: React.FormEvent) => {
        event.preventDefault();

        progressForm.patch(`/roadmap/items/${item.id}/progress`, {
            preserveScroll: true,
        });
    };

    const evaluate = (event: React.FormEvent) => {
        event.preventDefault();

        evaluationForm.post(`/roadmap/items/${item.id}/evaluate`, {
            preserveScroll: true,
        });
    };

    return (
        <>
            <Head title={material.title} />

            <div className="mx-auto w-full max-w-6xl px-4 py-8 md:px-6 md:py-10">
                <Link
                    href="/roadmap"
                    className="inline-flex items-center gap-2 text-sm font-black"
                >
                    <ArrowLeft className="size-4" />
                    Kembali ke roadmap
                </Link>

                <div className="mt-6 grid gap-6 lg:grid-cols-[1fr_320px]">
                    <main className="space-y-6">
                        <section className="neo-card p-6 sm:p-8">
                            <div className="flex flex-wrap gap-2">
                                <span className="neo-label">
                                    {material.skill.name}
                                </span>
                                <span className="rounded-full border-2 border-foreground bg-muted px-3 py-1 text-xs font-black">
                                    {material.difficulty}
                                </span>
                                <span className="rounded-full border-2 border-foreground bg-muted px-3 py-1 text-xs font-black">
                                    ± {material.estimated_minutes} menit
                                </span>
                            </div>

                            <h1 className="neo-heading mt-6 text-4xl sm:text-5xl">
                                {material.title}
                            </h1>

                            <p className="mt-5 max-w-3xl text-base leading-relaxed font-medium text-muted-foreground">
                                {material.summary}
                            </p>

                            <div className="mt-8 border-t-2 border-foreground pt-6">
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Setelah materi ini
                                </p>

                                <div className="mt-4 grid gap-3 sm:grid-cols-2">
                                    {material.learning_objectives.map(
                                        (objective) => (
                                            <div
                                                key={objective}
                                                className="neo-card-flat flex gap-3 p-4 text-sm font-bold"
                                            >
                                                <CheckCircle2 className="mt-0.5 size-4 shrink-0" />
                                                {objective}
                                            </div>
                                        ),
                                    )}
                                </div>
                            </div>
                        </section>

                        <section className="neo-card p-6">
                            <div className="flex items-center gap-3">
                                <BookOpen className="size-6" />
                                <h2 className="text-xl font-black">
                                    Latihan praktik
                                </h2>
                            </div>

                            <p className="mt-4 text-sm leading-relaxed font-medium">
                                {material.practice_task}
                            </p>

                            {material.resource_url && (
                                <Button
                                    asChild
                                    variant="outline"
                                    size="sm"
                                    className="mt-5"
                                >
                                    <a
                                        href={material.resource_url}
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        {material.resource_title ??
                                            'Buka referensi'}
                                        <ExternalLink />
                                    </a>
                                </Button>
                            )}
                        </section>

                        <section className="neo-card p-6">
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Pemeriksaan bukti
                            </p>

                            <h2 className="mt-2 text-2xl font-black">
                                Evaluasi singkat
                            </h2>

                            <p className="mt-3 leading-relaxed font-semibold">
                                {material.quiz_question}
                            </p>

                            <form
                                onSubmit={evaluate}
                                className="mt-5 grid gap-3"
                            >
                                {Object.entries(material.quiz_options).map(
                                    ([key, text]) => (
                                        <label
                                            key={key}
                                            className={`flex cursor-pointer items-start gap-3 rounded-xl border-2 border-foreground p-4 text-sm font-semibold ${
                                                evaluationForm.data.answer ===
                                                key
                                                    ? 'bg-secondary text-[#171717]'
                                                    : 'bg-card'
                                            }`}
                                        >
                                            <input
                                                type="radio"
                                                name="answer"
                                                value={key}
                                                checked={
                                                    evaluationForm.data
                                                        .answer === key
                                                }
                                                onChange={() =>
                                                    evaluationForm.setData(
                                                        'answer',
                                                        key,
                                                    )
                                                }
                                                className="mt-1 accent-black"
                                            />

                                            <span>
                                                <strong className="mr-2 font-mono">
                                                    {key}.
                                                </strong>
                                                {text}
                                            </span>
                                        </label>
                                    ),
                                )}

                                <Button
                                    className="mt-2 justify-self-start"
                                    disabled={
                                        !evaluationForm.data.answer ||
                                        evaluationForm.processing
                                    }
                                >
                                    Kirim evaluasi
                                </Button>
                            </form>

                            {latestEvaluation && (
                                <div
                                    className={`mt-5 rounded-xl border-2 border-foreground p-4 text-sm leading-relaxed font-semibold ${
                                        latestEvaluation.passed
                                            ? 'bg-secondary text-[#171717]'
                                            : 'bg-[#FF8FAB] text-[#171717]'
                                    }`}
                                >
                                    <p className="font-black">
                                        Hasil terakhir: {latestEvaluation.score}
                                        /100
                                    </p>
                                    <p className="mt-1">
                                        {latestEvaluation.feedback}
                                    </p>
                                </div>
                            )}
                        </section>
                    </main>

                    <aside className="space-y-5 lg:sticky lg:top-24 lg:self-start">
                        <form onSubmit={saveProgress} className="neo-card p-5">
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Catatan belajar
                            </p>

                            <h2 className="mt-1 text-xl font-black">
                                Catat sesi ini
                            </h2>

                            <div className="mt-5 space-y-4">
                                <label className="block">
                                    <span className="mb-2 block text-xs font-black">
                                        Progres maksimum 95%
                                    </span>
                                    <Input
                                        type="number"
                                        min={0}
                                        max={95}
                                        value={
                                            progressForm.data
                                                .progress_percentage
                                        }
                                        onChange={(event) =>
                                            progressForm.setData(
                                                'progress_percentage',
                                                Number(event.target.value),
                                            )
                                        }
                                    />
                                </label>

                                <label className="block">
                                    <span className="mb-2 block text-xs font-black">
                                        Menit belajar
                                    </span>
                                    <Input
                                        type="number"
                                        min={0}
                                        max={1440}
                                        value={progressForm.data.minutes_spent}
                                        onChange={(event) =>
                                            progressForm.setData(
                                                'minutes_spent',
                                                Number(event.target.value),
                                            )
                                        }
                                    />
                                </label>

                                <label className="block">
                                    <span className="mb-2 block text-xs font-black">
                                        Catatan
                                    </span>

                                    <textarea
                                        rows={4}
                                        value={progressForm.data.notes}
                                        onChange={(event) =>
                                            progressForm.setData(
                                                'notes',
                                                event.target.value,
                                            )
                                        }
                                        className="w-full resize-none rounded-xl border-2 border-foreground bg-card p-3 text-sm font-medium"
                                        placeholder="Apa yang akhirnya kamu pahami?"
                                    />
                                </label>

                                <label className="block">
                                    <span className="mb-2 block text-xs font-black">
                                        Kendala
                                    </span>

                                    <textarea
                                        rows={3}
                                        value={progressForm.data.obstacle}
                                        onChange={(event) =>
                                            progressForm.setData(
                                                'obstacle',
                                                event.target.value,
                                            )
                                        }
                                        className="w-full resize-none rounded-xl border-2 border-foreground bg-card p-3 text-sm font-medium"
                                        placeholder="Bagian mana yang masih menghambat?"
                                    />
                                </label>

                                <label className="block">
                                    <span className="mb-2 block text-xs font-black">
                                        Link bukti opsional
                                    </span>
                                    <Input
                                        type="url"
                                        value={progressForm.data.evidence_url}
                                        onChange={(event) =>
                                            progressForm.setData(
                                                'evidence_url',
                                                event.target.value,
                                            )
                                        }
                                        placeholder="https://..."
                                    />
                                </label>
                            </div>

                            <Button
                                type="submit"
                                variant="outline"
                                className="mt-5 w-full"
                                disabled={progressForm.processing}
                            >
                                <Save />
                                Simpan progres
                            </Button>
                        </form>

                        <div className="rounded-2xl border-2 border-foreground bg-[#FFCE5C] p-5 text-sm leading-relaxed font-bold text-[#171717]">
                            Progres manual dibatasi sampai 95%. Status 100%
                            hanya diberikan ketika evaluasi materi lulus.
                        </div>
                    </aside>
                </div>
            </div>
        </>
    );
}

MaterialPage.layout = {
    breadcrumbs: [
        {
            title: 'Roadmap',
            href: '/roadmap',
        },
        {
            title: 'Materi',
            href: '#',
        },
    ],
};
