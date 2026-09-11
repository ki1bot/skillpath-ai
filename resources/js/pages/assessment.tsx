import { Head, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Clock3,
    History,
    Play,
    RotateCcw,
    ShieldAlert,
} from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
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

type Question = {
    id: number;
    question_type: 'multiple_choice';
    prompt: string;
    options: Record<'A' | 'B' | 'C' | 'D', string>;
    difficulty: string;
};

type Assessment = {
    id: number;
    study_program: string;
    title: string;
    description: string;
    duration_minutes: number;
    question_limit: number;
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
    const [leaveDialogOpen, setLeaveDialogOpen] = useState(false);

    const allowNavigationRef = useRef(false);
    const pendingNavigationRef = useRef<string | null>(null);

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

    useEffect(() => {
        if (!assessment.started) {
            return;
        }

        const handleBeforeUnload = (event: BeforeUnloadEvent) => {
            if (allowNavigationRef.current) {
                return;
            }

            event.preventDefault();
            event.returnValue = '';
        };

        const handleDocumentClick = (event: MouseEvent) => {
            if (
                allowNavigationRef.current ||
                event.defaultPrevented ||
                event.button !== 0 ||
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey
            ) {
                return;
            }

            const target = event.target;

            if (!(target instanceof Element)) {
                return;
            }

            const anchor = target.closest('a[href]');

            if (!(anchor instanceof HTMLAnchorElement)) {
                return;
            }

            if (anchor.target === '_blank' || anchor.hasAttribute('download')) {
                return;
            }

            const destination = new URL(anchor.href, window.location.href);

            const current = new URL(window.location.href);

            if (destination.href === current.href) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            pendingNavigationRef.current = destination.href;

            setLeaveDialogOpen(true);
        };

        window.addEventListener('beforeunload', handleBeforeUnload);

        document.addEventListener('click', handleDocumentClick, true);

        return () => {
            window.removeEventListener('beforeunload', handleBeforeUnload);

            document.removeEventListener('click', handleDocumentClick, true);
        };
    }, [assessment.started]);

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

    const cancelLeaveAssessment = () => {
        pendingNavigationRef.current = null;
        setLeaveDialogOpen(false);
    };

    const confirmLeaveAssessment = () => {
        const destination = pendingNavigationRef.current;

        if (!destination) {
            setLeaveDialogOpen(false);

            return;
        }

        const url = new URL(destination, window.location.href);

        pendingNavigationRef.current = null;
        allowNavigationRef.current = true;
        setLeaveDialogOpen(false);

        if (url.origin === window.location.origin) {
            router.visit(`${url.pathname}${url.search}${url.hash}`, {
                onFinish: () => {
                    allowNavigationRef.current = false;
                },
            });

            return;
        }

        window.location.assign(url.href);
    };

    const submit = () => {
        if (!complete || form.processing) {
            return;
        }

        allowNavigationRef.current = true;

        form.post('/assessment', {
            onError: () => {
                allowNavigationRef.current = false;
            },
            onCancel: () => {
                allowNavigationRef.current = false;
            },
            onFinish: () => {
                allowNavigationRef.current = false;
            },
        });
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
                            Assesment jurusan{' '}
                            <strong>{assessment.study_program}</strong>{' '}
                            menggunakan{' '}
                            <strong>
                                {assessment.question_limit} pertanyaan
                            </strong>{' '}
                            yang seluruh urutannya diacak setiap kali kamu
                            memulai atau mengulangi Assesment.
                        </p>

                        <div className="mt-4 flex flex-wrap gap-2">
                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                Jurusan: {assessment.study_program}
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                {assessment.question_limit} soal acak
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                {assessment.skill_count} kemampuan inti
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

                {!assessment.started ? (
                    <section className="neo-card mt-8 p-6 sm:p-8">
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
                            Sistem akan menggunakan seluruh 30 soal Assesment
                            jurusan dan mengacak urutannya ketika kamu
                            mengonfirmasi. Soal tidak dipilih berdasarkan materi
                            pembelajaran.
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
                                    {assessment.skill_count}
                                </p>

                                <p className="mt-1 text-xs font-bold">
                                    Kemampuan inti
                                </p>
                            </div>

                            <div className="neo-card-flat p-4">
                                <p className="font-mono text-2xl font-black">
                                    {assessment.duration_minutes}
                                </p>

                                <p className="mt-1 text-xs font-bold">
                                    Menit estimasi
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
                    <div className="mt-8 grid gap-6 lg:grid-cols-[1fr_300px]">
                        <section className="neo-card p-6 sm:p-8">
                            <div className="mb-7 flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p className="text-xs font-black tracking-[0.15em] text-muted-foreground uppercase">
                                        Pertanyaan {index + 1} dari{' '}
                                        {assessment.questions.length}
                                    </p>

                                    <h2 className="mt-2 text-xl font-black">
                                        Assesment {assessment.study_program}
                                    </h2>
                                </div>

                                <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] px-3 py-1 text-xs font-black text-[#171717]">
                                    {question.difficulty}
                                </span>
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
                                    Jawab sesuai kemampuanmu saat ini. Hasil
                                    Assesment digunakan untuk memperbarui profil
                                    kemampuan dan roadmap belajar.
                                </p>
                            </section>

                            <section className="rounded-[14px] border-2 border-[#171717] bg-[var(--neo-yellow)] p-5 text-[#171717]">
                                <div className="flex items-start gap-3">
                                    <ShieldAlert className="mt-0.5 size-5 shrink-0" />

                                    <div>
                                        <p className="text-sm font-black">
                                            Jangan tinggalkan Assesment
                                        </p>

                                        <p className="mt-2 text-xs leading-5 font-semibold">
                                            Jika kamu keluar, refresh, atau
                                            menutup halaman sebelum hasil
                                            dikirim, jawaban yang sudah dipilih
                                            pada halaman ini dapat hilang.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        </aside>
                    </div>
                ) : (
                    <section className="neo-card mt-8 p-6 sm:p-8">
                        <h2 className="text-2xl font-black">
                            Sesi Assesment tidak memiliki pertanyaan.
                        </h2>

                        <p className="mt-3 text-sm font-medium text-muted-foreground">
                            Silakan mulai kembali Assesment untuk membuat sesi
                            baru.
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
                                ? `Apakah kamu yakin ingin mengulangi Assesment ${assessment.study_program}? Seluruh 30 soal akan digunakan kembali dengan urutan yang diacak untuk sesi baru. Hasil Assesment sebelumnya tetap tersimpan sebagai riwayat, sedangkan nilai kemampuan dan roadmap akan diperbarui setelah Assesment baru selesai.`
                                : `Apakah kamu yakin ingin memulai Assesment ${assessment.study_program}? Seluruh 30 soal akan digunakan dan urutannya diacak untuk sesi ini.`}
                        </DialogDescription>
                    </DialogHeader>

                    <div className="rounded-[12px] border-2 border-foreground bg-muted p-4 text-sm leading-6 font-semibold">
                        Setelah Assesment dimulai, urutan 30 soal akan tetap
                        sama selama sesi masih aktif. Refresh tidak membuat
                        urutan soal baru, tetapi jawaban yang belum dikirim
                        dapat hilang dari halaman.
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

            <Dialog
                open={assessment.started && leaveDialogOpen}
                onOpenChange={(open) => {
                    setLeaveDialogOpen(open);

                    if (!open) {
                        pendingNavigationRef.current = null;
                    }
                }}
            >
                <DialogContent
                    showCloseButton={false}
                    className="border-2 border-foreground shadow-[5px_5px_0_var(--neo-shadow-color)]"
                >
                    <DialogHeader>
                        <div className="mb-2 flex size-11 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-yellow)] text-[#171717]">
                            <ShieldAlert className="size-5" />
                        </div>

                        <DialogTitle className="text-2xl font-black">
                            Tinggalkan Assesment?
                        </DialogTitle>

                        <DialogDescription className="leading-6 font-medium">
                            Assesment masih sedang dikerjakan. Jawaban yang
                            belum dikirim tidak akan tersimpan jika kamu
                            meninggalkan halaman ini.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="rounded-[12px] border-2 border-foreground bg-muted p-4">
                        <p className="text-sm leading-6 font-semibold">
                            Progres saat ini:{' '}
                            <strong>
                                {completedCount} dari{' '}
                                {assessment.questions.length} pertanyaan
                            </strong>
                            . Kamu dapat tetap di halaman ini untuk melanjutkan
                            Assesment.
                        </p>
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={cancelLeaveAssessment}
                        >
                            Tetap di Assesment
                        </Button>

                        <Button type="button" onClick={confirmLeaveAssessment}>
                            Ya, tinggalkan
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
