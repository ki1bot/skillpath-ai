import { Deferred, Head, Link, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    BookOpen,
    CheckCircle2,
    CircleAlert,
    ExternalLink,
    FileCheck2,
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
    status: string;
    progress_percentage: number;
    evaluation_score?: number | null;
    evaluation_attempts: number;
    reinforcement_count: number;
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
    const itemCompleted = item.status === 'completed';

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

        if (itemCompleted) {
            return;
        }

        evaluationForm.post(`/roadmap/items/${item.id}/evaluate`, {
            preserveScroll: true,
        });
    };

    return (
        <>
            <Head title={material.title} />

            <div className="neo-page py-7 md:py-9">
                <Link
                    href="/roadmap"
                    className="inline-flex items-center gap-2 text-sm font-black"
                >
                    <ArrowLeft className="size-4" />
                    Kembali ke jalur belajar
                </Link>

                <header className="mt-6 border-b-2 border-foreground pb-6">
                    <div className="flex flex-wrap gap-2">
                        <span className="neo-label">{material.skill.name}</span>

                        <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                            {material.difficulty}
                        </span>

                        <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                            ± {material.estimated_minutes} menit
                        </span>

                        {material.material_type === 'reinforcement' && (
                            <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-pink)] px-3 py-1 text-xs font-black text-[#171717]">
                                Materi penguatan
                            </span>
                        )}

                        {itemCompleted && (
                            <span className="inline-flex items-center gap-1.5 rounded-full border-2 border-[#171717] bg-[var(--neo-lime)] px-3 py-1 text-xs font-black text-[#171717]">
                                <CheckCircle2 className="size-3.5" />
                                Selesai
                            </span>
                        )}
                    </div>

                    <h1 className="mt-4 max-w-4xl text-3xl font-black tracking-tight sm:text-4xl">
                        {material.title}
                    </h1>

                    <p className="mt-3 max-w-3xl text-sm leading-7 font-medium text-muted-foreground sm:text-base">
                        {material.summary}
                    </p>
                </header>

                {material.material_type === 'reinforcement' && (
                    <div className="mt-5 rounded-[12px] border-2 border-[#171717] bg-[var(--neo-pink)] p-4 text-sm leading-6 font-semibold text-[#171717]">
                        Materi ini muncul karena evaluasi sebelumnya belum
                        memenuhi standar. Selesaikan penguatan ini sebelum
                        kembali ke materi utama.
                    </div>
                )}

                <div className="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_300px]">
                    <main className="min-w-0 space-y-5">
                        <section className="neo-card p-5 sm:p-6">
                            <div className="flex items-start gap-3">
                                <span className="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-secondary font-black text-[#171717]">
                                    1
                                </span>

                                <div>
                                    <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                        Pahami materinya
                                    </p>

                                    <h2 className="mt-1 text-xl font-black">
                                        Apa yang perlu kamu kuasai
                                    </h2>
                                </div>
                            </div>

                            <div className="mt-5 grid gap-3 sm:grid-cols-2">
                                {material.learning_objectives.map(
                                    (objective) => (
                                        <div
                                            key={objective}
                                            className="flex gap-3 rounded-[10px] border-2 border-foreground/15 bg-muted/30 p-4 text-sm leading-6 font-semibold"
                                        >
                                            <CheckCircle2 className="mt-0.5 size-4 shrink-0" />
                                            <span>{objective}</span>
                                        </div>
                                    ),
                                )}
                            </div>

                            {material.skill.prerequisites.length > 0 && (
                                <div className="mt-5 border-t-2 border-foreground/15 pt-4">
                                    <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                        Dasar yang membantu
                                    </p>

                                    <div className="mt-2 flex flex-wrap gap-2">
                                        {material.skill.prerequisites.map(
                                            (prerequisite) => (
                                                <span
                                                    key={prerequisite.id}
                                                    className="rounded-full border-2 border-foreground/20 bg-card px-3 py-1 text-xs font-bold"
                                                >
                                                    {prerequisite.name}
                                                </span>
                                            ),
                                        )}
                                    </div>
                                </div>
                            )}
                        </section>

                        <section className="neo-card p-5 sm:p-6">
                            <div className="flex items-start gap-3">
                                <span className="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-[var(--neo-yellow)] font-black text-[#171717]">
                                    2
                                </span>

                                <div>
                                    <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                        Praktik
                                    </p>

                                    <h2 className="mt-1 text-xl font-black">
                                        Kerjakan latihan berikut
                                    </h2>
                                </div>
                            </div>

                            <div className="mt-5 rounded-[12px] border-2 border-foreground bg-card p-5">
                                <p className="text-sm leading-7 font-semibold">
                                    {material.practice_task}
                                </p>
                            </div>

                            {material.resource_url && (
                                <Button
                                    asChild
                                    variant="outline"
                                    size="sm"
                                    className="mt-4"
                                >
                                    <a
                                        href={material.resource_url}
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        <BookOpen className="size-4" />

                                        {material.resource_title ??
                                            'Buka referensi'}

                                        <ExternalLink className="size-4" />
                                    </a>
                                </Button>
                            )}
                        </section>

                        <Deferred
                            data="aiExercise"
                            fallback={
                                <section className="neo-card p-5 sm:p-6">
                                    <p className="text-sm font-black">
                                        Memuat latihan tambahan...
                                    </p>

                                    <div className="mt-4 space-y-2">
                                        <div className="h-3 w-full animate-pulse rounded bg-muted" />
                                        <div className="h-3 w-10/12 animate-pulse rounded bg-muted" />
                                    </div>
                                </section>
                            }
                        >
                            {hasAiExercise && aiExercise ? (
                                <section className="neo-card p-5 sm:p-6">
                                    <details>
                                        <summary className="cursor-pointer text-sm font-black">
                                            Lihat latihan tambahan
                                        </summary>

                                        <div className="mt-4 border-t-2 border-foreground/15 pt-4">
                                            <p className="text-sm leading-7 font-semibold whitespace-pre-line">
                                                {aiExercise.content}
                                            </p>

                                            <p className="mt-4 text-xs leading-5 font-medium text-muted-foreground">
                                                Latihan tambahan ini tidak
                                                mengubah nilai atau status
                                                materi.
                                            </p>
                                        </div>
                                    </details>
                                </section>
                            ) : (
                                <section className="neo-card p-5 sm:p-6">
                                    <div className="flex items-start gap-3">
                                        <CircleAlert className="mt-0.5 size-5 shrink-0" />

                                        <div>
                                            <p className="text-sm font-black">
                                                Latihan tambahan belum tersedia
                                            </p>

                                            <p className="mt-1 text-sm leading-6 font-medium text-muted-foreground">
                                                {aiExercise?.message ??
                                                    'Kamu tetap dapat menyelesaikan materi dan evaluasi tanpa latihan tambahan.'}
                                            </p>
                                        </div>
                                    </div>

                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        className="mt-4"
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
                                </section>
                            )}
                        </Deferred>

                        <section className="neo-card p-5 sm:p-6">
                            <div className="flex items-start gap-3">
                                <span className="flex size-8 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-[var(--neo-lime)] font-black text-[#171717]">
                                    3
                                </span>

                                <div>
                                    <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                        Evaluasi
                                    </p>

                                    <h2 className="mt-1 text-xl font-black">
                                        Jawab soal dan kirim bukti praktik
                                    </h2>
                                </div>
                            </div>

                            {itemCompleted ? (
                                <div className="mt-5 rounded-[12px] border-2 border-[#171717] bg-[var(--neo-lime)] p-5 text-[#171717]">
                                    <div className="flex items-start gap-3">
                                        <CheckCircle2 className="mt-0.5 size-6 shrink-0" />

                                        <div>
                                            <p className="font-black">
                                                Materi sudah selesai
                                            </p>

                                            <p className="mt-2 text-sm leading-6 font-semibold">
                                                Evaluasi sudah lulus dan status
                                                penyelesaian telah disimpan.
                                            </p>

                                            {item.evaluation_score !== null &&
                                                item.evaluation_score !==
                                                    undefined && (
                                                    <p className="mt-3 font-mono text-sm font-black">
                                                        Skor evaluasi:{' '}
                                                        {item.evaluation_score}
                                                        /100
                                                    </p>
                                                )}
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <>
                                    <div className="mt-5 grid gap-3 sm:grid-cols-2">
                                        <div className="rounded-[10px] border-2 border-foreground/15 bg-muted/30 p-4">
                                            <p className="font-black">
                                                Jawaban konsep
                                            </p>

                                            <p className="mt-1 text-sm font-medium text-muted-foreground">
                                                Bernilai maksimal 80 poin.
                                            </p>
                                        </div>

                                        <div className="rounded-[10px] border-2 border-foreground/15 bg-muted/30 p-4">
                                            <p className="font-black">
                                                Bukti Google Drive
                                            </p>

                                            <p className="mt-1 text-sm font-medium text-muted-foreground">
                                                Bernilai maksimal 20 poin.
                                            </p>
                                        </div>
                                    </div>

                                    <form
                                        onSubmit={evaluate}
                                        className="mt-6 grid gap-5"
                                    >
                                        <fieldset>
                                            <legend className="text-base leading-7 font-black">
                                                {material.quiz_question}
                                            </legend>

                                            <div className="mt-4 grid gap-2.5">
                                                {Object.entries(
                                                    material.quiz_options,
                                                ).map(([key, text]) => (
                                                    <label
                                                        key={key}
                                                        className={`flex cursor-pointer items-start gap-3 rounded-[10px] border-2 border-foreground p-4 text-sm ${
                                                            evaluationForm.data
                                                                .answer === key
                                                                ? 'bg-secondary text-[#171717]'
                                                                : 'bg-card'
                                                        }`}
                                                    >
                                                        <input
                                                            type="radio"
                                                            name="answer"
                                                            value={key}
                                                            checked={
                                                                evaluationForm
                                                                    .data
                                                                    .answer ===
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

                                                        <span className="leading-6 font-semibold">
                                                            <strong className="mr-2 font-mono">
                                                                {key}.
                                                            </strong>

                                                            {text}
                                                        </span>
                                                    </label>
                                                ))}
                                            </div>

                                            {evaluationForm.errors.answer && (
                                                <p className="mt-2 text-xs font-bold text-destructive">
                                                    {
                                                        evaluationForm.errors
                                                            .answer
                                                    }
                                                </p>
                                            )}
                                        </fieldset>

                                        <label>
                                            <span className="mb-2 block text-sm font-black">
                                                Link bukti praktik di Google
                                                Drive
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

                                            <p className="mt-2 text-xs leading-5 font-medium text-muted-foreground">
                                                Upload hasil praktik atau
                                                dokumentasinya ke Google Drive,
                                                atur aksesnya, lalu tempel link
                                                drive.google.com di sini.
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
                                            className="justify-self-start"
                                            disabled={
                                                !evaluationReady ||
                                                evaluationForm.processing
                                            }
                                        >
                                            <FileCheck2 className="size-4" />

                                            {evaluationForm.processing
                                                ? 'Memeriksa...'
                                                : 'Kirim evaluasi'}
                                        </Button>
                                    </form>
                                </>
                            )}

                            {latestEvaluation && (
                                <div
                                    className={`mt-5 rounded-[12px] border-2 border-[#171717] p-4 text-sm leading-6 font-semibold text-[#171717] ${
                                        latestEvaluation.passed
                                            ? 'bg-[var(--neo-lime)]'
                                            : 'bg-[var(--neo-pink)]'
                                    }`}
                                >
                                    <p className="font-black">
                                        Hasil terakhir: {latestEvaluation.score}
                                        /100
                                    </p>

                                    <p className="mt-1">
                                        Konsep:{' '}
                                        {latestEvaluation.knowledge_score}/80 ·
                                        Bukti: {latestEvaluation.evidence_score}
                                        /20
                                    </p>

                                    <p className="mt-2">
                                        {latestEvaluation.feedback}
                                    </p>
                                </div>
                            )}
                        </section>
                    </main>

                    <aside className="space-y-4 xl:sticky xl:top-6 xl:self-start">
                        <section className="neo-card p-5">
                            <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                Urutan pengerjaan
                            </p>

                            <div className="mt-4 grid gap-4 text-sm">
                                <div className="flex gap-3">
                                    <span className="font-mono font-black">
                                        01
                                    </span>

                                    <div>
                                        <p className="font-black">
                                            Pahami materi
                                        </p>

                                        <p className="mt-1 leading-5 font-medium text-muted-foreground">
                                            Baca ringkasan dan lihat apa saja
                                            yang perlu kamu kuasai.
                                        </p>
                                    </div>
                                </div>

                                <div className="flex gap-3">
                                    <span className="font-mono font-black">
                                        02
                                    </span>

                                    <div>
                                        <p className="font-black">
                                            Kerjakan latihan
                                        </p>

                                        <p className="mt-1 leading-5 font-medium text-muted-foreground">
                                            Selesaikan tugas praktik, lalu
                                            simpan hasilnya sebagai bukti
                                            pekerjaan.
                                        </p>
                                    </div>
                                </div>

                                <div className="flex gap-3">
                                    <span className="font-mono font-black">
                                        03
                                    </span>

                                    <div>
                                        <p className="font-black">
                                            Selesaikan evaluasi
                                        </p>

                                        <p className="mt-1 leading-5 font-medium text-muted-foreground">
                                            Jawab soal evaluasi dan sertakan
                                            link Google Drive berisi hasil
                                            praktikmu.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section className="neo-card p-5">
                            <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                Status materi
                            </p>

                            <p className="mt-2 text-xl font-black">
                                {itemCompleted ? 'Selesai' : 'Belum selesai'}
                            </p>

                            <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                Percobaan evaluasi: {item.evaluation_attempts}
                                {item.reinforcement_count > 0 && (
                                    <>
                                        {' '}
                                        · Penguatan: {item.reinforcement_count}
                                    </>
                                )}
                            </p>
                        </section>
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
