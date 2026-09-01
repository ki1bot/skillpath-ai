import { Head, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Clock3,
    GraduationCap,
    History,
} from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { getStudyProgramDefinition } from '@/lib/academic-programs';

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
    study_program: string;
    title: string;
    description: string;
    duration_minutes: number;
    questions: Question[];
};

type FormData = {
    answers: Record<number, string>;
};

export default function AssessmentPage({
    assessment,
    latestAttempt,
}: {
    assessment: Assessment;
    latestAttempt?: string | null;
}) {
    const [index, setIndex] = useState(0);

    const program = getStudyProgramDefinition(assessment.study_program);

    const assessedSkillCount =
        program?.areas.reduce((total, area) => total + area.skills.length, 0) ??
        0;

    const question = assessment.questions[index];

    const form = useForm<FormData>({
        answers: {},
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

    if (!question) {
        return (
            <>
                <Head title="Assesment Awal" />

                <div className="neo-page py-8 md:py-10">
                    <section className="neo-card p-6 sm:p-8">
                        <h1 className="text-3xl font-black">
                            Pertanyaan Assesment belum tersedia.
                        </h1>

                        <p className="mt-3 text-sm font-medium text-muted-foreground">
                            Data Assesment untuk jurusan ini belum tersedia.
                        </p>
                    </section>
                </div>
            </>
        );
    }

    const currentAnswer = form.data.answers[question.id] ?? '';

    const isLastQuestion = index === assessment.questions.length - 1;

    const selectAnswer = (value: string) => {
        form.setData('answers', {
            ...form.data.answers,
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
                            Kenali kemampuan awalmu.
                        </h1>

                        <p className="mt-4 text-sm leading-relaxed font-medium text-muted-foreground">
                            Kamu akan menjawab {assessment.questions.length}{' '}
                            pertanyaan dari tiga bidang utama jurusan{' '}
                            <strong>{assessment.study_program}</strong>.
                        </p>

                        <div className="mt-4 flex flex-wrap gap-2">
                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                Jurusan: {assessment.study_program}
                            </span>

                            {latestAttempt && (
                                <span className="flex items-center gap-1.5 rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] px-3 py-1 text-xs font-black text-[#171717]">
                                    <History className="size-3.5" />
                                    Mengulang Assesment
                                </span>
                            )}
                        </div>
                    </div>

                    <div className="neo-surface flex items-center gap-3 px-4 py-3 text-sm font-black">
                        <Clock3 className="size-5" />±{' '}
                        {assessment.duration_minutes} menit
                    </div>
                </div>

                {program && (
                    <section className="neo-card mt-8 p-5 sm:p-6">
                        <div className="flex items-center gap-3">
                            <span className="flex size-10 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-yellow)] text-[#171717]">
                                <GraduationCap className="size-5" />
                            </span>

                            <div>
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Yang dinilai
                                </p>

                                <h2 className="text-xl font-black">
                                    {program.areas.length} bidang dan{' '}
                                    {assessedSkillCount} kemampuan
                                </h2>
                            </div>
                        </div>

                        <p className="mt-4 max-w-3xl text-sm leading-6 font-medium text-muted-foreground">
                            Tidak masalah kalau ada bagian yang belum kamu
                            kuasai. Tujuan Assesment adalah melihat kemampuanmu
                            sekarang, bukan mencari nilai sempurna.
                        </p>

                        <div className="mt-6 grid gap-4 lg:grid-cols-3">
                            {program.areas.map((area, areaIndex) => (
                                <div
                                    key={area.name}
                                    className="rounded-[14px] border-2 border-foreground bg-muted p-4"
                                >
                                    <div className="flex items-start gap-3">
                                        <span className="flex size-7 shrink-0 items-center justify-center rounded-full border-2 border-foreground bg-background text-xs font-black">
                                            {areaIndex + 1}
                                        </span>

                                        <div>
                                            <p className="text-xs font-black tracking-[0.12em] text-muted-foreground uppercase">
                                                Bidang {areaIndex + 1}
                                            </p>

                                            <h3 className="mt-1 text-base font-black">
                                                {area.name}
                                            </h3>
                                        </div>
                                    </div>

                                    <ul className="mt-4 space-y-2">
                                        {area.skills.map((skill) => (
                                            <li
                                                key={skill}
                                                className="flex gap-2 text-sm leading-5 font-semibold"
                                            >
                                                <CheckCircle2 className="mt-0.5 size-4 shrink-0" />

                                                <span>{skill}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            ))}
                        </div>
                    </section>
                )}

                <div className="mt-6 grid gap-6 lg:grid-cols-[1fr_300px]">
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
                                        ? 'Menyimpan hasil...'
                                        : 'Selesaikan Assesment'}
                                </Button>
                            ) : (
                                <Button
                                    type="button"
                                    onClick={goNext}
                                    disabled={!currentAnswer}
                                >
                                    Pertanyaan berikutnya
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
                                        Progres
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
                                    style={{
                                        width: `${progress}%`,
                                    }}
                                />
                            </div>
                        </section>

                        <section className="neo-surface p-5">
                            <p className="text-sm leading-6 font-semibold">
                                Jawab apa adanya. Hasil yang jujur lebih berguna
                                untuk menentukan bagian mana yang perlu kamu
                                pelajari lebih dulu.
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
            title: 'Assessment',
            href: '/assessment',
        },
    ],
};
