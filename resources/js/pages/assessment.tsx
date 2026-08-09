import { Head, useForm } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, CheckCircle2, Clock3 } from 'lucide-react';
import { useMemo, useState } from 'react';
import { Button } from '@/components/ui/button';

type Question = {
    id: number;
    prompt: string;
    options: Record<'A' | 'B' | 'C' | 'D', string>;
    difficulty: string;
    skill: {
        id: number;
        name: string;
        category: string;
    };
};

type Assessment = {
    id: number;
    title: string;
    description: string;
    duration_minutes: number;
    career: {
        name: string;
    };
    questions: Question[];
};

type FormData = {
    answers: Record<number, string>;
    self_ratings: Record<number, number>;
};

export default function AssessmentPage({
    assessment,
    latestAttempt,
}: {
    assessment: Assessment;
    latestAttempt?: string | null;
}) {
    const [index, setIndex] = useState(0);
    const question = assessment.questions[index];

    const initialRatings = useMemo(
        () =>
            Object.fromEntries(
                assessment.questions.map((item) => [item.id, 50]),
            ),
        [assessment.questions],
    );

    const form = useForm<FormData>({
        answers: {},
        self_ratings: initialRatings,
    });

    const answered = Object.keys(form.data.answers).length;

    const progress =
        assessment.questions.length > 0
            ? Math.round((answered / assessment.questions.length) * 100)
            : 0;

    const currentRating = form.data.self_ratings[question?.id] ?? 50;

    const canMove = Boolean(question && form.data.answers[question.id]);

    const complete = assessment.questions.every((item) =>
        Boolean(form.data.answers[item.id]),
    );

    const statusText = useMemo(
        () =>
            latestAttempt
                ? 'Asesmen ulang akan mengganti skor aktif dengan hasil terbaru.'
                : 'Ini asesmen pertamamu. Jawab tanpa membuka referensi.',
        [latestAttempt],
    );

    const selectAnswer = (value: string) => {
        form.setData('answers', {
            ...form.data.answers,
            [question.id]: value,
        });
    };

    const setRating = (value: number) => {
        form.setData('self_ratings', {
            ...form.data.self_ratings,
            [question.id]: value,
        });
    };

    const submit = () => {
        form.post('/assessment');
    };

    return (
        <>
            <Head title="Asesmen Awal" />

            <div className="mx-auto w-full max-w-5xl px-4 py-8 md:px-6 md:py-10">
                <div className="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                    <div className="max-w-2xl">
                        <span className="neo-label">Tahap 02</span>

                        <h1 className="neo-heading mt-5 text-4xl sm:text-5xl">
                            {assessment.title}
                        </h1>

                        <p className="mt-4 text-sm leading-relaxed font-medium text-muted-foreground">
                            {assessment.description}
                        </p>
                    </div>

                    <div className="neo-card-flat flex items-center gap-3 px-4 py-3 text-sm font-black">
                        <Clock3 className="size-5" />±{' '}
                        {assessment.duration_minutes} menit
                    </div>
                </div>

                <div className="mt-8 grid gap-6 lg:grid-cols-[1fr_260px]">
                    <section className="neo-card p-6 sm:p-8">
                        <div className="mb-7 flex items-center justify-between gap-4">
                            <div>
                                <p className="text-xs font-black tracking-[0.15em] text-muted-foreground uppercase">
                                    Pertanyaan {index + 1} dari{' '}
                                    {assessment.questions.length}
                                </p>
                                <p className="mt-1 text-sm font-bold">
                                    {question.skill.name}
                                </p>
                            </div>

                            <span className="rounded-full border-2 border-foreground bg-[#79D7FF] px-3 py-1 text-xs font-black text-[#171717]">
                                {question.skill.category}
                            </span>
                        </div>

                        <h2 className="text-2xl leading-snug font-black tracking-tight">
                            {question.prompt}
                        </h2>

                        <div className="mt-7 grid gap-3">
                            {Object.entries(question.options).map(
                                ([key, value]) => {
                                    const selected =
                                        form.data.answers[question.id] === key;

                                    return (
                                        <button
                                            key={key}
                                            type="button"
                                            onClick={() => selectAnswer(key)}
                                            className={`flex items-start gap-4 rounded-xl border-2 border-foreground p-4 text-left text-sm font-semibold transition-transform ${
                                                selected
                                                    ? 'translate-x-1 translate-y-1 bg-secondary text-[#171717]'
                                                    : 'bg-card shadow-[3px_3px_0_var(--neo-shadow-color)] hover:-translate-y-0.5'
                                            }`}
                                        >
                                            <span className="flex size-7 shrink-0 items-center justify-center rounded-lg border-2 border-foreground bg-background font-mono text-xs font-black text-foreground">
                                                {key}
                                            </span>
                                            <span className="pt-1 leading-relaxed">
                                                {value}
                                            </span>
                                        </button>
                                    );
                                },
                            )}
                        </div>

                        <div className="mt-8 rounded-2xl border-2 border-foreground bg-muted p-5">
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <p className="text-sm font-black">
                                        Seberapa yakin kamu dengan skill ini?
                                    </p>
                                    <p className="mt-1 text-xs font-medium text-muted-foreground">
                                        Penilaian diri hanya 30% dari skor.
                                        Jawaban objektif tetap lebih berat.
                                    </p>
                                </div>

                                <span className="font-mono text-xl font-black">
                                    {currentRating}
                                </span>
                            </div>

                            <input
                                type="range"
                                min={0}
                                max={100}
                                step={5}
                                value={currentRating}
                                onChange={(event) =>
                                    setRating(Number(event.target.value))
                                }
                                className="mt-5 w-full accent-black"
                            />

                            <div className="mt-1 flex justify-between text-[10px] font-black tracking-wide text-muted-foreground uppercase">
                                <span>Belum paham</span>
                                <span>Sangat yakin</span>
                            </div>
                        </div>

                        <div className="mt-8 flex items-center justify-between gap-3">
                            <Button
                                type="button"
                                variant="outline"
                                disabled={index === 0}
                                onClick={() => setIndex((value) => value - 1)}
                            >
                                <ArrowLeft />
                                Sebelumnya
                            </Button>

                            {index < assessment.questions.length - 1 ? (
                                <Button
                                    type="button"
                                    disabled={!canMove}
                                    onClick={() =>
                                        setIndex((value) => value + 1)
                                    }
                                >
                                    Berikutnya
                                    <ArrowRight />
                                </Button>
                            ) : (
                                <Button
                                    type="button"
                                    disabled={!complete || form.processing}
                                    onClick={submit}
                                >
                                    <CheckCircle2 />
                                    Selesaikan asesmen
                                </Button>
                            )}
                        </div>
                    </section>

                    <aside className="space-y-4">
                        <div className="neo-card-flat p-5">
                            <div className="flex items-end justify-between">
                                <span className="text-sm font-black">
                                    Terjawab
                                </span>
                                <span className="font-mono text-xl font-black">
                                    {progress}%
                                </span>
                            </div>

                            <div className="neo-progress mt-3 h-4">
                                <span
                                    style={{
                                        width: `${progress}%`,
                                    }}
                                />
                            </div>

                            <div className="mt-5 grid grid-cols-5 gap-2">
                                {assessment.questions.map((item, itemIndex) => (
                                    <button
                                        key={item.id}
                                        type="button"
                                        onClick={() => setIndex(itemIndex)}
                                        className={`flex aspect-square items-center justify-center rounded-lg border-2 border-foreground text-xs font-black ${
                                            itemIndex === index
                                                ? 'bg-[#79D7FF] text-[#171717]'
                                                : form.data.answers[item.id]
                                                  ? 'bg-secondary text-[#171717]'
                                                  : 'bg-card'
                                        }`}
                                    >
                                        {itemIndex + 1}
                                    </button>
                                ))}
                            </div>
                        </div>

                        <div className="rounded-2xl border-2 border-foreground bg-[#FFCE5C] p-5 text-sm leading-relaxed font-bold text-[#171717]">
                            {statusText}
                        </div>
                    </aside>
                </div>
            </div>
        </>
    );
}

AssessmentPage.layout = {
    breadcrumbs: [
        {
            title: 'Asesmen',
            href: '/assessment',
        },
    ],
};
