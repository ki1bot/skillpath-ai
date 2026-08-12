import { Head, Link, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    BookOpen,
    CheckCircle2,
    ExternalLink,
    RotateCcw,
    Save,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

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
    material_type: 'core' | 'reinforcement';
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
    knowledge_score: number;
    evidence_score: number;
    reflection_score: number;
    passed: boolean;
    feedback: string;
    created_at: string;
};

type Item = {
    id: number;
    status: string;
    progress_percentage: number;
    evaluation_score?: number | null;
    evaluation_attempts: number;
    reinforcement_count: number;
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
        practical_evidence_url: '',
        reflection: '',
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

            <div className="neo-page py-8 md:py-10">
                <Link
                    href="/roadmap"
                    className="inline-flex items-center gap-2 text-sm font-black"
                >
                    <ArrowLeft className="size-4" />
                    Kembali ke jalur belajar
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

                                {material.material_type === 'reinforcement' && (
                                    <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-pink)] px-3 py-1 text-xs font-black text-[#171717]">
                                        Materi penguatan
                                    </span>
                                )}
                            </div>

                            <h1 className="neo-heading mt-6 text-4xl sm:text-5xl">
                                {material.title}
                            </h1>

                            <p className="mt-5 max-w-3xl text-base leading-relaxed font-medium text-muted-foreground">
                                {material.summary}
                            </p>

                            <div className="mt-8 border-t-2 border-foreground pt-6">
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Setelah mempelajari materi ini
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
                                Pemeriksaan pemahaman
                            </p>

                            <h2 className="mt-2 text-2xl font-black">
                                Evaluasi berbasis bukti
                            </h2>

                            <div className="mt-4 grid gap-3 sm:grid-cols-3">
                                <div className="neo-card-flat p-4">
                                    <p className="font-mono text-xl font-black">
                                        80
                                    </p>
                                    <p className="mt-1 text-xs font-bold">
                                        Pemahaman konsep
                                    </p>
                                </div>

                                <div className="neo-card-flat p-4">
                                    <p className="font-mono text-xl font-black">
                                        10
                                    </p>
                                    <p className="mt-1 text-xs font-bold">
                                        Bukti praktik
                                    </p>
                                </div>

                                <div className="neo-card-flat p-4">
                                    <p className="font-mono text-xl font-black">
                                        10
                                    </p>
                                    <p className="mt-1 text-xs font-bold">
                                        Refleksi belajar
                                    </p>
                                </div>
                            </div>

                            <p className="mt-6 leading-relaxed font-semibold">
                                {material.quiz_question}
                            </p>

                            <form
                                onSubmit={evaluate}
                                className="mt-5 grid gap-4"
                            >
                                {Object.entries(material.quiz_options).map(
                                    ([key, text]) => (
                                        <label
                                            key={key}
                                            className={`flex cursor-pointer items-start gap-3 rounded-[12px] border-2 border-foreground p-4 text-sm font-semibold ${
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

                                <label className="mt-2">
                                    <span className="mb-2 block text-sm font-black">
                                        Bukti latihan praktik
                                    </span>

                                    <Input
                                        type="url"
                                        value={
                                            evaluationForm.data
                                                .practical_evidence_url
                                        }
                                        onChange={(event) =>
                                            evaluationForm.setData(
                                                'practical_evidence_url',
                                                event.target.value,
                                            )
                                        }
                                        placeholder="https://github.com/... atau tautan bukti lainnya"
                                    />
                                </label>

                                <label>
                                    <span className="mb-2 block text-sm font-black">
                                        Refleksi hasil belajar
                                    </span>

                                    <Textarea
                                        rows={5}
                                        value={evaluationForm.data.reflection}
                                        onChange={(event) =>
                                            evaluationForm.setData(
                                                'reflection',
                                                event.target.value,
                                            )
                                        }
                                        placeholder="Jelaskan apa yang dipahami, kesalahan yang ditemukan, dan bagaimana Anda memperbaikinya. Minimal 80 karakter untuk mendapatkan nilai refleksi penuh."
                                    />
                                </label>

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
                                    className={`mt-5 rounded-[12px] border-2 border-[#171717] p-4 text-sm leading-relaxed font-semibold text-[#171717] ${
                                        latestEvaluation.passed
                                            ? 'bg-secondary'
                                            : 'bg-[var(--neo-pink)]'
                                    }`}
                                >
                                    <p className="font-black">
                                        Hasil terakhir: {latestEvaluation.score}
                                        /100
                                    </p>

                                    <p className="mt-2">
                                        Konsep:{' '}
                                        {latestEvaluation.knowledge_score}/80 ·
                                        Bukti: {latestEvaluation.evidence_score}
                                        /10 · Refleksi:{' '}
                                        {latestEvaluation.reflection_score}/10
                                    </p>

                                    <p className="mt-2">
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
                                        Perkembangan maksimum 95%
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

                                    <Textarea
                                        rows={4}
                                        value={progressForm.data.notes}
                                        onChange={(event) =>
                                            progressForm.setData(
                                                'notes',
                                                event.target.value,
                                            )
                                        }
                                        className="min-h-28 resize-none"
                                        placeholder="Apa yang akhirnya kamu pahami?"
                                    />
                                </label>

                                <label className="block">
                                    <span className="mb-2 block text-xs font-black">
                                        Kendala
                                    </span>

                                    <Textarea
                                        rows={3}
                                        value={progressForm.data.obstacle}
                                        onChange={(event) =>
                                            progressForm.setData(
                                                'obstacle',
                                                event.target.value,
                                            )
                                        }
                                        className="min-h-24 resize-none"
                                        placeholder="Bagian mana yang masih sulit?"
                                    />
                                </label>

                                <label className="block">
                                    <span className="mb-2 block text-xs font-black">
                                        Tautan bukti opsional
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
                                Simpan perkembangan
                            </Button>
                        </form>

                        {material.material_type === 'reinforcement' && (
                            <div className="rounded-[14px] border-2 border-[#171717] bg-[var(--neo-pink)] p-5 text-sm leading-relaxed font-bold text-[#171717]">
                                <RotateCcw className="mb-3 size-5" />
                                Materi ini ditambahkan karena evaluasi
                                sebelumnya belum memenuhi standar. Selesaikan
                                penguatan ini sebelum mencoba materi utama
                                kembali.
                            </div>
                        )}

                        <div className="rounded-[14px] border-2 border-[#171717] bg-[var(--neo-yellow)] p-5 text-sm leading-relaxed font-bold text-[#171717]">
                            Perkembangan manual tetap dibatasi sampai 95%.
                            Materi hanya mencapai 100% setelah evaluasi
                            dinyatakan lulus.
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
            title: 'Jalur Belajar',
            href: '/roadmap',
        },
        {
            title: 'Materi',
            href: '#',
        },
    ],
};
