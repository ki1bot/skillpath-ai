import { Deferred, Head, Link, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    BookOpen,
    BrainCircuit,
    CheckCircle2,
    CircleAlert,
    ExternalLink,
    RotateCcw,
} from 'lucide-react';
import { useState } from 'react';
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
    passed: boolean;
    feedback: string;
    created_at: string;
};

type Item = {
    id: number;
    evaluations: Evaluation[];
};

type AiExercise = {
    content: string | null;
    generatedByAi: boolean;
    model: string | null;
    message: string | null;
};

const isGoogleDriveUrl = (value: string): boolean => {
    const normalized = value.trim();

    if (!normalized) {
        return false;
    }

    try {
        const url = new URL(normalized);

        return (
            url.protocol === 'https:' &&
            url.hostname.toLowerCase() === 'drive.google.com' &&
            url.pathname !== '/'
        );
    } catch {
        return false;
    }
};

export default function MaterialPage({
    item,
    material,
    aiExercise,
}: {
    item: Item;
    material: Material;
    aiExercise?: AiExercise;
}) {
    const [isRetryingAi, setIsRetryingAi] = useState(false);

    const evaluationForm = useForm({
        answer: '',
        practical_evidence_url: '',
    });

    const latestEvaluation = item.evaluations?.[0];

    const hasAiExercise =
        aiExercise?.generatedByAi === true && Boolean(aiExercise.content);

    const evaluationEvidenceValid = isGoogleDriveUrl(
        evaluationForm.data.practical_evidence_url,
    );

    const evaluationReady =
        Boolean(evaluationForm.data.answer) && evaluationEvidenceValid;

    const retryAiExercise = () => {
        setIsRetryingAi(true);

        router.reload({
            only: ['aiExercise'],
            onFinish: () => setIsRetryingAi(false),
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

                <main className="mt-6 space-y-6">
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

                    <Deferred
                        data="aiExercise"
                        fallback={
                            <section className="neo-card overflow-hidden">
                                <div className="flex items-center gap-3 border-b-2 border-[#171717] bg-[var(--neo-blue)] p-5 text-[#171717]">
                                    <BrainCircuit className="size-5" />

                                    <h2 className="text-xl font-black">
                                        Variasi latihan AI
                                    </h2>
                                </div>

                                <div className="space-y-3 p-6">
                                    <div className="h-4 w-full animate-pulse rounded bg-muted" />
                                    <div className="h-4 w-10/12 animate-pulse rounded bg-muted" />
                                    <div className="h-4 w-8/12 animate-pulse rounded bg-muted" />
                                </div>
                            </section>
                        }
                    >
                        {hasAiExercise && aiExercise ? (
                            <section className="neo-card overflow-hidden">
                                <div className="flex flex-wrap items-center justify-between gap-3 border-b-2 border-[#171717] bg-[var(--neo-blue)] p-5 text-[#171717]">
                                    <div className="flex items-center gap-3">
                                        <BrainCircuit className="size-5" />

                                        <h2 className="text-xl font-black">
                                            Variasi latihan AI
                                        </h2>
                                    </div>

                                    <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-lime)] px-2.5 py-1 text-[10px] font-black uppercase">
                                        AI · {aiExercise.model}
                                    </span>
                                </div>

                                <div className="p-6">
                                    <p className="text-sm leading-7 font-semibold whitespace-pre-line">
                                        {aiExercise.content}
                                    </p>

                                    <p className="mt-4 text-xs leading-5 font-bold text-muted-foreground">
                                        Variasi ini tidak mengubah nilai, status
                                        materi, atau roadmap. AI hanya membuat
                                        variasi dari latihan yang sudah tersedia
                                        di database.
                                    </p>
                                </div>
                            </section>
                        ) : (
                            <section className="neo-card overflow-hidden">
                                <div className="flex items-center gap-3 border-b-2 border-[#171717] bg-[var(--neo-blue)] p-5 text-[#171717]">
                                    <BrainCircuit className="size-5" />

                                    <h2 className="text-xl font-black">
                                        Variasi latihan AI
                                    </h2>
                                </div>

                                <div
                                    className="space-y-4 p-6"
                                    aria-live="polite"
                                >
                                    <div className="flex items-start gap-3">
                                        <CircleAlert className="mt-0.5 size-5 shrink-0" />

                                        <p className="text-sm leading-6 font-semibold text-muted-foreground">
                                            {aiExercise?.message ??
                                                'Variasi latihan AI sedang tidak tersedia. Silakan coba lagi.'}
                                        </p>
                                    </div>

                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        disabled={isRetryingAi}
                                        onClick={retryAiExercise}
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
                                </div>
                            </section>
                        )}
                    </Deferred>

                    {material.material_type === 'reinforcement' && (
                        <div className="rounded-[14px] border-2 border-[#171717] bg-[var(--neo-pink)] p-5 text-sm leading-relaxed font-bold text-[#171717]">
                            <RotateCcw className="mb-3 size-5" />
                            Materi ini ditambahkan karena evaluasi sebelumnya
                            belum memenuhi standar. Selesaikan penguatan ini
                            sebelum mencoba materi utama kembali.
                        </div>
                    )}

                    <section className="neo-card p-6">
                        <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                            Pemeriksaan pemahaman
                        </p>

                        <h2 className="mt-2 text-2xl font-black">
                            Evaluasi berbasis bukti
                        </h2>

                        <p className="mt-3 max-w-3xl text-sm leading-6 font-semibold text-muted-foreground">
                            Materi dinyatakan selesai jika jawaban konsep benar
                            dan bukti latihan menggunakan link Google Drive yang
                            valid. Sistem memeriksa format tautannya, bukan
                            membaca isi file Google Drive.
                        </p>

                        <div className="mt-4 grid gap-3 sm:grid-cols-2">
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
                                    20
                                </p>

                                <p className="mt-1 text-xs font-bold">
                                    Bukti Google Drive
                                </p>
                            </div>
                        </div>

                        <p className="mt-6 leading-relaxed font-semibold">
                            {material.quiz_question}
                        </p>

                        <form onSubmit={evaluate} className="mt-5 grid gap-4">
                            {Object.entries(material.quiz_options).map(
                                ([key, text]) => (
                                    <label
                                        key={key}
                                        className={`flex cursor-pointer items-start gap-3 rounded-[12px] border-2 border-foreground p-4 text-sm font-semibold ${
                                            evaluationForm.data.answer === key
                                                ? 'bg-secondary text-[#171717]'
                                                : 'bg-card'
                                        }`}
                                    >
                                        <input
                                            type="radio"
                                            name="answer"
                                            value={key}
                                            checked={
                                                evaluationForm.data.answer ===
                                                key
                                            }
                                            onChange={() =>
                                                evaluationForm.setData(
                                                    'answer',
                                                    key,
                                                )
                                            }
                                            className="mt-1 accent-black"
                                            required
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

                            {evaluationForm.errors.answer && (
                                <p className="text-xs font-bold text-destructive">
                                    {evaluationForm.errors.answer}
                                </p>
                            )}

                            <label className="mt-2">
                                <span className="mb-2 block text-sm font-black">
                                    Bukti latihan praktik Google Drive
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
                                    placeholder="https://drive.google.com/file/d/..."
                                    required
                                />

                                <p className="mt-2 text-xs leading-5 font-semibold text-muted-foreground">
                                    Unggah hasil latihan atau dokumentasi ke
                                    Google Drive, aktifkan akses yang sesuai,
                                    lalu tempel link drive.google.com di sini.
                                </p>

                                {evaluationForm.data.practical_evidence_url.trim()
                                    .length > 0 &&
                                    !evaluationEvidenceValid && (
                                        <p className="mt-2 text-xs font-bold text-destructive">
                                            Gunakan link HTTPS dari
                                            drive.google.com.
                                        </p>
                                    )}

                                {evaluationForm.errors
                                    .practical_evidence_url && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {
                                            evaluationForm.errors
                                                .practical_evidence_url
                                        }
                                    </p>
                                )}
                            </label>

                            <Button
                                className="mt-2 justify-self-start"
                                disabled={
                                    !evaluationReady ||
                                    evaluationForm.processing
                                }
                            >
                                {evaluationForm.processing
                                    ? 'Memeriksa...'
                                    : 'Kirim evaluasi'}
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
                                    Hasil terakhir: {latestEvaluation.score}/100
                                </p>

                                <p className="mt-2">
                                    Konsep: {latestEvaluation.knowledge_score}
                                    /80
                                    {' · '}
                                    Bukti: {latestEvaluation.evidence_score}/20
                                </p>

                                <p className="mt-2">
                                    {latestEvaluation.feedback}
                                </p>
                            </div>
                        )}
                    </section>
                </main>
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
