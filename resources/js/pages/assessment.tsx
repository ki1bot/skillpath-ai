import { Head, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Clock3,
    GraduationCap,
    History,
    Play,
    RotateCcw,
    ShieldCheck,
} from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
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
    question_limit: number;
    reserve_question_count: number;
    skill_count: number;
    started: boolean;
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
    const [startDialogOpen, setStartDialogOpen] = useState(!assessment.started);

    const program = getStudyProgramDefinition(assessment.study_program);

    const academicSkillCount =
        program?.areas.reduce((total, area) => total + area.skills.length, 0) ??
        0;

    const isRepeat = Boolean(latestAttempt);

    const startForm = useForm({});

    const form = useForm<FormData>({
        answers: {},
    });

    const question = assessment.questions[index];

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

    const currentAnswer = question
        ? (form.data.answers[question.id] ?? '')
        : '';

    const isLastQuestion =
        Boolean(question) && index === assessment.questions.length - 1;

    const beginAssessment = () => {
        if (startForm.processing) {
            return;
        }

        setStartDialogOpen(false);

        startForm.post('/assessment/start', {
            preserveScroll: true,
        });
    };

    const selectAnswer = (value: string) => {
        if (!question) {
            return;
        }

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
                            Assesment menggunakan{' '}
                            <strong>
                                {assessment.question_limit} pertanyaan
                            </strong>{' '}
                            yang dipilih secara acak dari bank soal jurusan{' '}
                            <strong>{assessment.study_program}</strong>.
                        </p>

                        <div className="mt-4 flex flex-wrap gap-2">
                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                Jurusan: {assessment.study_program}
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                {assessment.skill_count} kemampuan inti
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                {assessment.reserve_question_count} soal
                                cadangan
                            </span>

                            {latestAttempt && (
                                <span className="flex items-center gap-1.5 rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] px-3 py-1 text-xs font-black text-[#171717]">
                                    <History className="size-3.5" />

                                    {assessment.started
                                        ? 'Mengulang Assesment'
                                        : 'Pernah mengikuti Assesment'}
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
                                    Cakupan jurusan
                                </p>

                                <h2 className="text-xl font-black">
                                    {program.areas.length} bidang dan{' '}
                                    {academicSkillCount} kemampuan akademik
                                </h2>
                            </div>
                        </div>

                        <p className="mt-4 max-w-3xl text-sm leading-6 font-medium text-muted-foreground">
                            Dari cakupan jurusan ini, Assesment mengukur{' '}
                            {assessment.skill_count} kemampuan inti melalui{' '}
                            {assessment.question_limit} pertanyaan yang dipilih
                            untuk sesi yang sedang dikerjakan.
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

                {!assessment.started ? (
                    <section className="neo-card mt-6 p-6 sm:p-8">
                        <div className="flex size-12 items-center justify-center rounded-[12px] border-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                            {isRepeat ? (
                                <RotateCcw className="size-6" />
                            ) : (
                                <Play className="size-6" />
                            )}
                        </div>

                        <h2 className="mt-5 text-2xl font-black">
                            {isRepeat
                                ? 'Siap mengulangi Assesment?'
                                : 'Siap memulai Assesment?'}
                        </h2>

                        <p className="mt-3 max-w-3xl text-sm leading-6 font-semibold text-muted-foreground">
                            Sistem baru akan mengacak soal setelah kamu
                            mengonfirmasi. Selama sesi masih aktif, refresh
                            halaman tidak akan membuat set soal baru.
                        </p>

                        <div className="mt-5 grid gap-3 sm:grid-cols-3">
                            <div className="neo-card-flat p-4">
                                <p className="font-mono text-2xl font-black">
                                    {assessment.question_limit}
                                </p>

                                <p className="mt-1 text-xs font-bold">
                                    Soal yang dikerjakan
                                </p>
                            </div>

                            <div className="neo-card-flat p-4">
                                <p className="font-mono text-2xl font-black">
                                    {assessment.reserve_question_count}
                                </p>

                                <p className="mt-1 text-xs font-bold">
                                    Soal cadangan
                                </p>
                            </div>

                            <div className="neo-card-flat p-4">
                                <p className="font-mono text-2xl font-black">
                                    {assessment.skill_count}
                                </p>

                                <p className="mt-1 text-xs font-bold">
                                    Kemampuan inti
                                </p>
                            </div>
                        </div>

                        <Button
                            type="button"
                            className="mt-6"
                            onClick={() => setStartDialogOpen(true)}
                        >
                            {isRepeat ? <RotateCcw /> : <Play />}

                            {isRepeat ? 'Ulangi Assesment' : 'Mulai Assesment'}
                        </Button>
                    </section>
                ) : question ? (
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
                                                onClick={() =>
                                                    selectAnswer(key)
                                                }
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
                                    Jawab apa adanya. Hasil yang jujur lebih
                                    berguna untuk menentukan bagian mana yang
                                    perlu kamu pelajari lebih dulu.
                                </p>
                            </section>

                            <section className="neo-surface p-5">
                                <div className="flex items-start gap-3">
                                    <ShieldCheck className="mt-0.5 size-5 shrink-0" />

                                    <p className="text-sm leading-6 font-semibold">
                                        Lima soal cadangan disimpan di server
                                        dan tidak dihitung sebagai jawaban
                                        Assesment utama.
                                    </p>
                                </div>
                            </section>
                        </aside>
                    </div>
                ) : (
                    <section className="neo-card mt-6 p-6 sm:p-8">
                        <h2 className="text-2xl font-black">
                            Sesi Assesment tidak memiliki pertanyaan.
                        </h2>

                        <p className="mt-3 text-sm font-medium text-muted-foreground">
                            Silakan kembali dan mulai Assesment baru.
                        </p>
                    </section>
                )}
            </div>

            <Dialog
                open={!assessment.started && startDialogOpen}
                onOpenChange={setStartDialogOpen}
            >
                <DialogContent
                    showCloseButton={false}
                    className="border-2 border-foreground shadow-[5px_5px_0_var(--neo-shadow-color)]"
                >
                    <DialogHeader>
                        <DialogTitle className="text-2xl font-black">
                            {isRepeat
                                ? 'Ulangi Assesment?'
                                : 'Mulai Assesment?'}
                        </DialogTitle>

                        <DialogDescription className="leading-6 font-medium">
                            {isRepeat
                                ? `Apakah kamu yakin ingin mengulangi Assesment ${assessment.study_program}? Sistem akan mengacak 25 soal utama dan 5 soal cadangan untuk sesi baru. Hasil Assesment sebelumnya tetap tersimpan sebagai riwayat, tetapi nilai kemampuan terbaru dan roadmap akan diperbarui setelah Assesment baru selesai.`
                                : `Apakah kamu yakin ingin memulai Assesment ${assessment.study_program}? Sistem akan mengacak 25 soal utama dan menyiapkan 5 soal cadangan untuk sesi ini.`}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="rounded-[12px] border-2 border-foreground bg-muted p-4 text-sm leading-6 font-semibold">
                        Setelah sesi dibuat, set soal tetap sama selama sesi
                        tersebut masih aktif. Soal baru hanya diacak ketika kamu
                        secara eksplisit memulai atau mengulangi Assesment.
                    </div>

                    <DialogFooter>
                        <DialogClose asChild>
                            <Button type="button" variant="outline">
                                Batal
                            </Button>
                        </DialogClose>

                        <Button
                            type="button"
                            onClick={beginAssessment}
                            disabled={startForm.processing}
                        >
                            {isRepeat ? <RotateCcw /> : <Play />}

                            {startForm.processing
                                ? 'Menyiapkan soal...'
                                : isRepeat
                                  ? 'Ya, ulangi Assesment'
                                  : 'Ya, mulai Assesment'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
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
