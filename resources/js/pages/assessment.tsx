import { Head, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Clock3,
    History,
} from 'lucide-react';
import { useMemo, useState } from 'react';
import { Button } from '@/components/ui/button';

type Question = {
    id: number;
    question_type: 'multiple_choice';
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

    const completedCount = assessment.questions.filter((item) =>
        Boolean(form.data.answers[item.id]),
    ).length;

    const progress =
        assessment.questions.length > 0
            ? Math.round((completedCount / assessment.questions.length) * 100)
            : 0;

    const complete =
        assessment.questions.length > 0 &&
        assessment.questions.every((item) =>
            Boolean(form.data.answers[item.id]),
        );

    const categorySummary = useMemo(() => {
        const summary = new Map<string, number>();

        assessment.questions.forEach((item) => {
            summary.set(
                item.skill.category,
                (summary.get(item.skill.category) ?? 0) + 1,
            );
        });

        return Array.from(summary.entries());
    }, [assessment.questions]);

    if (!question) {
        return (
            <>
                <Head title="Assesment Awal" />

                <div className="neo-page py-8 md:py-10">
                    <section className="neo-card p-6 sm:p-8">
                        <h1 className="text-3xl font-black">
                            Assesment belum memiliki pertanyaan.
                        </h1>

                        <p className="mt-3 text-sm font-medium text-muted-foreground">
                            Jalankan seeder assesment akademik agar pertanyaan
                            tersedia.
                        </p>
                    </section>
                </div>
            </>
        );
    }

    const currentRating = form.data.self_ratings[question.id] ?? 50;
    const currentAnswer = form.data.answers[question.id] ?? '';
    const isLastQuestion = index === assessment.questions.length - 1;

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

    const goNext = () => {
        if (!currentAnswer || isLastQuestion) {
            return;
        }

        setIndex((current) =>
            Math.min(current + 1, assessment.questions.length - 1),
        );
    };

    const goPrevious = () => {
        setIndex((current) => Math.max(current - 1, 0));
    };

    const submit = () => {
        if (!complete || form.processing) {
            return;
        }

        form.post('/assessment');
    };

    return (
        <>
            <Head title="Assesment Awal" />

            <div className="neo-page py-8 md:py-10">
                <div className="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                    <div className="max-w-3xl">
                        <span className="neo-label">Tahap 02</span>

                        <h1 className="neo-heading mt-5 text-4xl sm:text-5xl">
                            {assessment.title}
                        </h1>

                        <p className="mt-4 text-sm leading-relaxed font-medium text-muted-foreground">
                            {assessment.description}
                        </p>

                        <div className="mt-4 flex flex-wrap gap-2">
                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                Target karier: {assessment.career.name}
                            </span>

                            {latestAttempt && (
                                <span className="flex items-center gap-1.5 rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] px-3 py-1 text-xs font-black text-[#171717]">
                                    <History className="size-3.5" />
                                    Assesment ulang
                                </span>
                            )}
                        </div>
                    </div>

                    <div className="neo-surface flex items-center gap-3 px-4 py-3 text-sm font-black">
                        <Clock3 className="size-5" />±{' '}
                        {assessment.duration_minutes} menit
                    </div>
                </div>

                <div className="mt-8 grid gap-6 lg:grid-cols-[1fr_300px]">
                    <section className="neo-card p-6 sm:p-8">
                        <div className="mb-7 flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p className="text-xs font-black tracking-[0.15em] text-muted-foreground uppercase">
                                    Pertanyaan {index + 1} dari{' '}
                                    {assessment.questions.length}
                                </p>

                                <h2 className="mt-2 text-xl font-black">
                                    {question.skill.name}
                                </h2>
                            </div>

                            <div className="flex flex-wrap gap-2">
                                <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-blue)] px-3 py-1 text-xs font-black text-[#171717]">
                                    {question.skill.category}
                                </span>

                                <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] px-3 py-1 text-xs font-black text-[#171717]">
                                    {question.difficulty}
                                </span>
                            </div>
                        </div>

                        <h3 className="text-2xl leading-snug font-black tracking-tight">
                            {question.prompt}
                        </h3>

                        <div className="mt-7 grid gap-3">
                            {Object.entries(question.options).map(
                                ([key, value]) => {
                                    const selected = currentAnswer === key;

                                    return (
                                        <button
                                            key={key}
                                            type="button"
                                            onClick={() => selectAnswer(key)}
                                            className={`flex items-start gap-4 rounded-[12px] border-2 border-foreground p-4 text-left text-sm font-semibold transition-[transform,box-shadow,background-color] ${
                                                selected
                                                    ? 'translate-x-[2px] translate-y-[2px] bg-secondary text-[#171717] shadow-none'
                                                    : 'bg-card shadow-[3px_3px_0_var(--neo-shadow-color)] hover:-translate-y-[1px]'
                                            }`}
                                        >
                                            <span className="flex size-7 shrink-0 items-center justify-center rounded-[8px] border-2 border-foreground bg-background font-mono text-xs font-black text-foreground">
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

                        <div className="mt-8 rounded-[14px] border-2 border-foreground bg-muted p-5">
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <p className="text-sm font-black">
                                        Seberapa yakin kamu dengan jawaban dan
                                        kemampuan pada skill ini?
                                    </p>

                                    <p className="mt-1 text-xs font-medium text-muted-foreground">
                                        Jawaban objektif memiliki bobot 80% dan
                                        penilaian diri memiliki bobot 20%.
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
                                <span>Belum yakin</span>
                                <span>Sangat yakin</span>
                            </div>
                        </div>

                        {form.errors.answers && (
                            <p className="mt-5 text-sm font-bold text-destructive">
                                {form.errors.answers}
                            </p>
                        )}

                        <div className="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={goPrevious}
                                disabled={index === 0}
                            >
                                <ArrowLeft />
                                Sebelumnya
                            </Button>

                            {isLastQuestion ? (
                                <Button
                                    type="button"
                                    onClick={submit}
                                    disabled={!complete || form.processing}
                                >
                                    <CheckCircle2 />
                                    {form.processing
                                        ? 'Menyimpan...'
                                        : 'Selesaikan Assesment'}
                                </Button>
                            ) : (
                                <Button
                                    type="button"
                                    onClick={goNext}
                                    disabled={!currentAnswer}
                                >
                                    Selanjutnya
                                    <ArrowRight />
                                </Button>
                            )}
                        </div>
                    </section>

                    <aside className="space-y-5">
                        <section className="neo-card p-5">
                            <div className="flex items-center justify-between gap-3">
                                <div>
                                    <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                        Progress
                                    </p>

                                    <p className="mt-1 text-2xl font-black">
                                        {progress}%
                                    </p>
                                </div>

                                <span className="font-mono text-sm font-black">
                                    {completedCount}/
                                    {assessment.questions.length}
                                </span>
                            </div>

                            <div className="mt-4 h-3 overflow-hidden rounded-full border-2 border-foreground bg-background">
                                <div
                                    className="h-full bg-secondary transition-[width]"
                                    style={{ width: `${progress}%` }}
                                />
                            </div>
                        </section>

                        <section className="neo-card p-5">
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Bidang yang dinilai
                            </p>

                            <div className="mt-4 space-y-3">
                                {categorySummary.map(([category, count]) => (
                                    <div
                                        key={category}
                                        className="rounded-[10px] border-2 border-foreground bg-muted p-3"
                                    >
                                        <p className="text-sm font-black">
                                            {category}
                                        </p>

                                        <p className="mt-1 text-xs font-semibold text-muted-foreground">
                                            {count} skill
                                        </p>
                                    </div>
                                ))}
                            </div>
                        </section>

                        <section className="neo-surface p-5">
                            <p className="text-sm leading-6 font-semibold">
                                Jawab berdasarkan pemahamanmu sekarang. Jangan
                                menaikkan nilai keyakinan jika jawabanmu hanya
                                tebakan karena skor ini akan masuk ke profil
                                skill dan memengaruhi analisis berikutnya.
                            </p>
                        </section>
                    </aside>
                </div>
            </div>
        </>
    );
}

AssessmentPage.layout = {
    breadcrumbs: [
        {
            title: 'Assesment',
            href: '/assessment',
        },
    ],
};
