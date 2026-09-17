import { Head, router } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Clock3,
    History,
    Play,
    RotateCcw,
    X,
} from 'lucide-react';
import { useEffect, useMemo, useRef, useState } from 'react';
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
    user_id: number;
    study_program: string;
    title: string;
    description: string;
    duration_minutes: number;
    question_limit: number;
    skill_count: number;
    started: boolean;
    questions: Question[];
};

type StoredDraft = {
    question_ids: number[];
    answers: Record<string, string>;
    current_section: number;
};

type DraftState = {
    answers: Record<number, string>;
    currentSection: number;
};

const SECTION_SIZE = 10;
const VALID_ANSWERS = ['A', 'B', 'C', 'D'];

const emptyDraftState = (): DraftState => ({
    answers: {},
    currentSection: 0,
});

const restoreDraftState = (
    assessment: Assessment,
    questionIds: number[],
    sections: Question[][],
    storageKey: string,
): DraftState => {
    if (!assessment.started || typeof window === 'undefined') {
        return emptyDraftState();
    }

    const restoredAnswers: Record<number, string> = {};
    let restoredSection = 0;

    try {
        const stored = window.localStorage.getItem(storageKey);

        if (!stored) {
            return emptyDraftState();
        }

        const parsed = JSON.parse(stored) as StoredDraft;

        const sameQuestionOrder =
            Array.isArray(parsed.question_ids) &&
            parsed.question_ids.length === questionIds.length &&
            parsed.question_ids.every(
                (id, index) => Number(id) === Number(questionIds[index]),
            );

        if (!sameQuestionOrder) {
            return emptyDraftState();
        }

        assessment.questions.forEach((question) => {
            const value = parsed.answers?.[String(question.id)];

            if (typeof value === 'string' && VALID_ANSWERS.includes(value)) {
                restoredAnswers[question.id] = value;
            }
        });

        const maximumSection = Math.max(sections.length - 1, 0);

        restoredSection = Math.min(
            Math.max(Number(parsed.current_section) || 0, 0),
            maximumSection,
        );

        for (
            let sectionIndex = 0;
            sectionIndex < restoredSection;
            sectionIndex += 1
        ) {
            const section = sections[sectionIndex];

            const sectionComplete = section.every((question) =>
                Boolean(restoredAnswers[question.id]),
            );

            if (!sectionComplete) {
                restoredSection = sectionIndex;
                break;
            }
        }
    } catch {
        return emptyDraftState();
    }

    return {
        answers: restoredAnswers,
        currentSection: restoredSection,
    };
};

export default function AssessmentPage({
    assessment,
    latestAttempt,
}: {
    assessment: Assessment;
    latestAttempt?: string | null;
}) {
    const storageKey = useMemo(
        () =>
            `skillpath.assessment.${assessment.user_id}.${assessment.id}.draft`,
        [assessment.id, assessment.user_id],
    );

    const questionIds = useMemo(
        () => assessment.questions.map((question) => question.id),
        [assessment.questions],
    );

    const sections = useMemo(() => {
        const result: Question[][] = [];

        for (
            let index = 0;
            index < assessment.questions.length;
            index += SECTION_SIZE
        ) {
            result.push(
                assessment.questions.slice(index, index + SECTION_SIZE),
            );
        }

        return result;
    }, [assessment.questions]);

    const [draftState, setDraftState] = useState<DraftState>(() =>
        restoreDraftState(assessment, questionIds, sections, storageKey),
    );

    const [isOnline, setIsOnline] = useState(() =>
        typeof window === 'undefined' ? true : window.navigator.onLine,
    );

    const [startProcessing, setStartProcessing] = useState(false);
    const [submitProcessing, setSubmitProcessing] = useState(false);
    const [abandonProcessing, setAbandonProcessing] = useState(false);
    const [answerError, setAnswerError] = useState<string | null>(null);
    const [startDialogOpen, setStartDialogOpen] = useState(false);
    const [leaveDialogOpen, setLeaveDialogOpen] = useState(false);

    const allowNavigationRef = useRef(false);
    const pendingNavigationRef = useRef<string | null>(null);

    const answers = draftState.answers;
    const currentSection = draftState.currentSection;
    const isRepeat = Boolean(latestAttempt);
    const currentQuestions = sections[currentSection] ?? [];

    const completedCount = assessment.questions.filter((question) =>
        Boolean(answers[question.id]),
    ).length;

    const progress =
        assessment.questions.length > 0
            ? Math.round((completedCount / assessment.questions.length) * 100)
            : 0;

    const complete =
        assessment.questions.length > 0 &&
        assessment.questions.every((question) => Boolean(answers[question.id]));

    const currentSectionComplete =
        currentQuestions.length > 0 &&
        currentQuestions.every((question) => Boolean(answers[question.id]));

    const isLastSection =
        sections.length > 0 && currentSection === sections.length - 1;

    const removeDraft = () => {
        try {
            window.localStorage.removeItem(storageKey);
        } catch {
            return;
        }
    };

    useEffect(() => {
        const handleOnline = () => setIsOnline(true);
        const handleOffline = () => setIsOnline(false);

        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);

        return () => {
            window.removeEventListener('online', handleOnline);
            window.removeEventListener('offline', handleOffline);
        };
    }, []);

    useEffect(() => {
        if (!assessment.started) {
            return;
        }

        const storedAnswers = Object.fromEntries(
            Object.entries(answers).map(([questionId, answer]) => [
                String(questionId),
                answer,
            ]),
        );

        const draft: StoredDraft = {
            question_ids: questionIds,
            answers: storedAnswers,
            current_section: currentSection,
        };

        try {
            window.localStorage.setItem(storageKey, JSON.stringify(draft));
        } catch {
            return;
        }
    }, [answers, assessment.started, currentSection, questionIds, storageKey]);

    useEffect(() => {
        if (!assessment.started) {
            return;
        }

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

        document.addEventListener('click', handleDocumentClick, true);

        return () => {
            document.removeEventListener('click', handleDocumentClick, true);
        };
    }, [assessment.started]);

    const beginAssessment = () => {
        if (startProcessing) {
            return;
        }

        removeDraft();
        setDraftState(emptyDraftState());
        setAnswerError(null);
        setStartDialogOpen(false);
        setStartProcessing(true);

        router.post(
            '/assessment/start',
            {},
            {
                preserveScroll: true,
                onFinish: () => setStartProcessing(false),
            },
        );
    };

    const selectAnswer = (questionId: number, value: string) => {
        setAnswerError(null);

        setDraftState((current) => ({
            ...current,
            answers: {
                ...current.answers,
                [questionId]: value,
            },
        }));
    };

    const clearAnswer = (questionId: number) => {
        setAnswerError(null);

        setDraftState((current) => {
            const nextAnswers = { ...current.answers };

            delete nextAnswers[questionId];

            return {
                ...current,
                answers: nextAnswers,
            };
        });
    };

    const goNextSection = () => {
        if (!currentSectionComplete || isLastSection) {
            return;
        }

        setDraftState((current) => ({
            ...current,
            currentSection: Math.min(
                current.currentSection + 1,
                sections.length - 1,
            ),
        }));

        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    };

    const goPreviousSection = () => {
        setDraftState((current) => ({
            ...current,
            currentSection: Math.max(current.currentSection - 1, 0),
        }));

        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    };

    const requestLeaveAssessment = (destination = '/dashboard') => {
        pendingNavigationRef.current = new URL(
            destination,
            window.location.href,
        ).href;

        setLeaveDialogOpen(true);
    };

    const cancelLeaveAssessment = () => {
        pendingNavigationRef.current = null;
        setLeaveDialogOpen(false);
    };

    const confirmLeaveAssessment = () => {
        const destination = pendingNavigationRef.current;

        if (!destination || abandonProcessing) {
            return;
        }

        if (!isOnline) {
            setAnswerError(
                'Hubungkan kembali internet sebelum membatalkan assessment agar sesi di server dapat dihapus dengan benar.',
            );

            setLeaveDialogOpen(false);

            return;
        }

        allowNavigationRef.current = true;
        setAbandonProcessing(true);

        router.post(
            '/assessment/abandon',
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    removeDraft();
                    setDraftState(emptyDraftState());

                    pendingNavigationRef.current = null;

                    setLeaveDialogOpen(false);

                    window.location.assign(destination);
                },
                onError: () => {
                    allowNavigationRef.current = false;
                },
                onFinish: () => setAbandonProcessing(false),
            },
        );
    };

    const submit = () => {
        if (!complete || submitProcessing || !isOnline) {
            return;
        }

        setAnswerError(null);
        setSubmitProcessing(true);
        allowNavigationRef.current = true;

        router.post(
            '/assessment',
            {
                answers,
            },
            {
                onSuccess: () => {
                    removeDraft();
                    setDraftState(emptyDraftState());
                },
                onError: (errors) => {
                    allowNavigationRef.current = false;

                    const error = errors.answers;

                    setAnswerError(
                        typeof error === 'string'
                            ? error
                            : 'Jawaban assessment belum dapat disimpan. Periksa kembali seluruh jawaban.',
                    );
                },
                onCancel: () => {
                    allowNavigationRef.current = false;
                },
                onFinish: () => setSubmitProcessing(false),
            },
        );
    };

    return (
        <>
            <Head title="Assessment Awal" />

            <div className="neo-page py-7 md:py-9">
                <header className="border-b-2 border-foreground pb-6">
                    <div className="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div className="max-w-3xl">
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Assessment awal · {assessment.study_program}
                            </p>

                            <h1 className="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                                Jawab sesuai kemampuanmu saat ini
                            </h1>

                            <p className="mt-3 max-w-2xl text-sm leading-6 font-medium text-muted-foreground">
                                Ada {assessment.question_limit} soal yang dibagi
                                menjadi {sections.length} bagian. Selesaikan
                                satu bagian sebelum lanjut ke bagian berikutnya.
                            </p>
                        </div>

                        <div className="flex flex-wrap gap-2 text-xs font-black">
                            <span className="inline-flex items-center gap-2 rounded-full border-2 border-foreground bg-card px-3 py-2">
                                <Clock3 className="size-4" />±{' '}
                                {assessment.duration_minutes} menit
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-2">
                                {assessment.skill_count} kemampuan
                            </span>

                            {latestAttempt && (
                                <span className="inline-flex items-center gap-2 rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] px-3 py-2 text-[#171717]">
                                    <History className="size-4" />

                                    {assessment.started
                                        ? 'Sesi pengulangan'
                                        : 'Pernah dikerjakan'}
                                </span>
                            )}
                        </div>
                    </div>
                </header>

                {!assessment.started ? (
                    <section className="neo-card mt-6 p-6 sm:p-8">
                        <div className="grid gap-7 lg:grid-cols-[minmax(0,1fr)_280px] lg:items-start">
                            <div>
                                <div className="flex size-11 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-blue)] text-[#171717]">
                                    {isRepeat ? (
                                        <RotateCcw className="size-5" />
                                    ) : (
                                        <Play className="size-5" />
                                    )}
                                </div>

                                <h2 className="mt-4 text-2xl font-black">
                                    {isRepeat
                                        ? 'Mulai assessment baru'
                                        : 'Sebelum mulai'}
                                </h2>

                                <p className="mt-3 max-w-2xl text-sm leading-6 font-medium text-muted-foreground">
                                    Pilih satu jawaban untuk setiap soal.
                                    Jawaban disimpan di browser selama sesi
                                    aktif, jadi refresh tidak menghapus progres
                                    yang sudah dikerjakan.
                                </p>

                                <Button
                                    type="button"
                                    className="mt-6"
                                    disabled={startProcessing}
                                    onClick={() => setStartDialogOpen(true)}
                                >
                                    {isRepeat ? <RotateCcw /> : <Play />}

                                    {startProcessing
                                        ? 'Menyiapkan...'
                                        : isRepeat
                                          ? 'Mulai ulang assessment'
                                          : 'Mulai assessment'}
                                </Button>
                            </div>

                            <div className="rounded-[12px] border-2 border-foreground bg-muted/30 p-5">
                                <p className="text-xs font-black tracking-wide uppercase">
                                    Yang perlu diketahui
                                </p>

                                <div className="mt-4 grid gap-3 text-sm leading-6 font-medium">
                                    <p>
                                        <strong>
                                            {assessment.question_limit}
                                        </strong>{' '}
                                        soal, {sections.length} bagian.
                                    </p>

                                    <p>
                                        Setiap bagian berisi maksimal{' '}
                                        <strong>{SECTION_SIZE}</strong> soal.
                                    </p>

                                    <p>
                                        Kamu bisa membersihkan jawaban jika
                                        salah memilih sebelum mengirim hasil.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                ) : (
                    <div className="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1fr)_260px]">
                        <main className="min-w-0 space-y-5">
                            <section className="neo-card p-5 sm:p-6">
                                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                    <div>
                                        <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                            Bagian {currentSection + 1} dari{' '}
                                            {sections.length}
                                        </p>

                                        <h2 className="mt-1 text-2xl font-black">
                                            Soal{' '}
                                            {currentSection * SECTION_SIZE + 1}–
                                            {Math.min(
                                                (currentSection + 1) *
                                                    SECTION_SIZE,
                                                assessment.questions.length,
                                            )}
                                        </h2>
                                    </div>

                                    <p className="text-sm font-black">
                                        {
                                            currentQuestions.filter(
                                                (question) =>
                                                    Boolean(
                                                        answers[question.id],
                                                    ),
                                            ).length
                                        }
                                        /{currentQuestions.length} dijawab
                                    </p>
                                </div>

                                <div className="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-5">
                                    {sections.map((section, sectionIndex) => {
                                        const answered = section.filter(
                                            (question) =>
                                                Boolean(answers[question.id]),
                                        ).length;

                                        const sectionComplete =
                                            answered === section.length;

                                        return (
                                            <div
                                                key={sectionIndex}
                                                className={`rounded-[10px] border-2 border-foreground px-3 py-3 ${
                                                    sectionIndex ===
                                                    currentSection
                                                        ? 'bg-secondary text-[#171717]'
                                                        : sectionComplete
                                                          ? 'bg-[var(--neo-lime)] text-[#171717]'
                                                          : 'bg-card'
                                                }`}
                                            >
                                                <p className="text-xs font-black">
                                                    Bagian {sectionIndex + 1}
                                                </p>

                                                <p className="mt-1 text-[11px] font-semibold opacity-70">
                                                    {sectionIndex *
                                                        SECTION_SIZE +
                                                        1}
                                                    –
                                                    {Math.min(
                                                        (sectionIndex + 1) *
                                                            SECTION_SIZE,
                                                        assessment.questions
                                                            .length,
                                                    )}
                                                </p>

                                                <p className="mt-2 font-mono text-xs font-black">
                                                    {answered}/{section.length}
                                                </p>
                                            </div>
                                        );
                                    })}
                                </div>
                            </section>

                            {currentQuestions.map((question, questionIndex) => {
                                const questionNumber =
                                    currentSection * SECTION_SIZE +
                                    questionIndex +
                                    1;

                                const currentAnswer =
                                    answers[question.id] ?? '';

                                return (
                                    <article
                                        key={question.id}
                                        className="neo-card p-5 sm:p-6"
                                    >
                                        <div className="flex flex-wrap items-center justify-between gap-3">
                                            <div className="flex items-center gap-3">
                                                <span className="flex size-8 items-center justify-center rounded-[8px] border-2 border-foreground bg-muted font-mono text-xs font-black">
                                                    {questionNumber}
                                                </span>

                                                <span className="text-xs font-bold text-muted-foreground">
                                                    {question.difficulty}
                                                </span>
                                            </div>

                                            {currentAnswer && (
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        clearAnswer(question.id)
                                                    }
                                                >
                                                    <RotateCcw className="size-3.5" />
                                                    Bersihkan jawaban
                                                </Button>
                                            )}
                                        </div>

                                        <h3 className="mt-5 max-w-4xl text-lg leading-7 font-black sm:text-xl">
                                            {question.prompt}
                                        </h3>

                                        <div className="mt-5 grid gap-2.5">
                                            {Object.entries(
                                                question.options,
                                            ).map(([key, value]) => {
                                                const selected =
                                                    currentAnswer === key;

                                                return (
                                                    <button
                                                        key={key}
                                                        type="button"
                                                        aria-pressed={selected}
                                                        onClick={() =>
                                                            selectAnswer(
                                                                question.id,
                                                                key,
                                                            )
                                                        }
                                                        className={`flex w-full items-start gap-3 rounded-[10px] border-2 border-foreground px-4 py-3.5 text-left text-sm transition-[background-color,transform,box-shadow] ${
                                                            selected
                                                                ? 'translate-x-[1px] translate-y-[1px] bg-secondary text-[#171717] shadow-none'
                                                                : 'bg-card shadow-[2px_2px_0_var(--neo-shadow-color)] hover:-translate-y-[1px]'
                                                        }`}
                                                    >
                                                        <span
                                                            className={`flex size-7 shrink-0 items-center justify-center rounded-full border-2 border-foreground font-mono text-xs font-black ${
                                                                selected
                                                                    ? 'bg-[#171717] text-[#fffdf7]'
                                                                    : 'bg-background'
                                                            }`}
                                                        >
                                                            {key}
                                                        </span>

                                                        <span className="pt-0.5 leading-6 font-semibold">
                                                            {value}
                                                        </span>
                                                    </button>
                                                );
                                            })}
                                        </div>
                                    </article>
                                );
                            })}

                            {answerError && (
                                <div className="rounded-[12px] border-2 border-destructive bg-card p-4 text-sm font-bold text-destructive">
                                    {answerError}
                                </div>
                            )}

                            <section className="neo-card flex flex-col gap-3 p-5 sm:flex-row sm:items-center sm:justify-between">
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={goPreviousSection}
                                    disabled={currentSection === 0}
                                >
                                    <ArrowLeft />
                                    Bagian sebelumnya
                                </Button>

                                {isLastSection ? (
                                    <Button
                                        type="button"
                                        onClick={submit}
                                        disabled={
                                            !complete ||
                                            submitProcessing ||
                                            !isOnline
                                        }
                                    >
                                        <CheckCircle2 />

                                        {submitProcessing
                                            ? 'Menyimpan hasil...'
                                            : !isOnline
                                              ? 'Menunggu koneksi'
                                              : 'Kirim hasil assessment'}
                                    </Button>
                                ) : (
                                    <Button
                                        type="button"
                                        onClick={goNextSection}
                                        disabled={!currentSectionComplete}
                                    >
                                        Lanjut ke bagian {currentSection + 2}
                                        <ArrowRight />
                                    </Button>
                                )}
                            </section>
                        </main>

                        <aside className="space-y-4 xl:sticky xl:top-6 xl:self-start">
                            <section className="neo-card p-5">
                                <div className="flex items-end justify-between gap-3">
                                    <div>
                                        <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                            Progres
                                        </p>

                                        <p className="mt-1 text-3xl font-black">
                                            {progress}%
                                        </p>
                                    </div>

                                    <p className="font-mono text-sm font-black">
                                        {completedCount}/
                                        {assessment.questions.length}
                                    </p>
                                </div>

                                <div className="mt-4 h-3 overflow-hidden rounded-full border-2 border-foreground bg-background">
                                    <div
                                        className="h-full bg-secondary transition-[width]"
                                        style={{
                                            width: `${progress}%`,
                                        }}
                                    />
                                </div>

                                <p className="mt-4 text-xs leading-5 font-medium text-muted-foreground">
                                    Jawaban disimpan otomatis selama sesi ini
                                    masih aktif.
                                </p>
                            </section>

                            <Button
                                type="button"
                                variant="outline"
                                className="w-full"
                                onClick={() => requestLeaveAssessment()}
                            >
                                <X />
                                Batalkan assessment
                            </Button>
                        </aside>
                    </div>
                )}
            </div>

            <Dialog open={startDialogOpen} onOpenChange={setStartDialogOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {isRepeat
                                ? 'Mulai assessment baru?'
                                : 'Mulai assessment?'}
                        </DialogTitle>

                        <DialogDescription>
                            Sistem akan menyiapkan {assessment.question_limit}{' '}
                            soal dalam {sections.length} bagian. Setelah
                            dimulai, jawaban yang dipilih akan disimpan selama
                            sesi masih aktif.
                        </DialogDescription>
                    </DialogHeader>

                    <DialogFooter>
                        <DialogClose asChild>
                            <Button type="button" variant="outline">
                                Belum
                            </Button>
                        </DialogClose>

                        <Button
                            type="button"
                            onClick={beginAssessment}
                            disabled={startProcessing}
                        >
                            <Play />

                            {startProcessing
                                ? 'Menyiapkan...'
                                : 'Mulai sekarang'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <Dialog
                open={leaveDialogOpen}
                onOpenChange={(open) => {
                    if (!open) {
                        cancelLeaveAssessment();
                    } else {
                        setLeaveDialogOpen(true);
                    }
                }}
            >
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Batalkan sesi assessment?</DialogTitle>

                        <DialogDescription>
                            Jika keluar, jawaban sementara dan sesi assessment
                            aktif akan dihapus. Saat memulai kembali, kamu akan
                            kembali ke bagian pertama.
                        </DialogDescription>
                    </DialogHeader>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={cancelLeaveAssessment}
                        >
                            Lanjut mengerjakan
                        </Button>

                        <Button
                            type="button"
                            onClick={confirmLeaveAssessment}
                            disabled={abandonProcessing || !isOnline}
                        >
                            {abandonProcessing
                                ? 'Menghapus sesi...'
                                : !isOnline
                                  ? 'Menunggu koneksi'
                                  : 'Hapus progres dan keluar'}
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
