import { Head, router } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Clock3,
    History,
    Play,
    RotateCcw,
    ShieldAlert,
    Wifi,
    WifiOff,
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

const SECTION_SIZE = 10;
const VALID_ANSWERS = ['A', 'B', 'C', 'D'];

export default function AssessmentPage({
    assessment,
    latestAttempt,
}: {
    assessment: Assessment;
    latestAttempt?: string | null;
}) {
    const [answers, setAnswers] = useState<Record<number, string>>({});
    const [currentSection, setCurrentSection] = useState(0);
    const [hydrated, setHydrated] = useState(false);
    const [isOnline, setIsOnline] = useState(true);
    const [startProcessing, setStartProcessing] = useState(false);
    const [submitProcessing, setSubmitProcessing] = useState(false);
    const [abandonProcessing, setAbandonProcessing] = useState(false);
    const [answerError, setAnswerError] = useState<string | null>(null);
    const [startDialogOpen, setStartDialogOpen] = useState(!assessment.started);
    const [leaveDialogOpen, setLeaveDialogOpen] = useState(false);

    const allowNavigationRef = useRef(false);
    const pendingNavigationRef = useRef<string | null>(null);

    const isRepeat = Boolean(latestAttempt);

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
        setIsOnline(window.navigator.onLine);

        const handleOnline = () => {
            setIsOnline(true);
        };

        const handleOffline = () => {
            setIsOnline(false);
        };

        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);

        return () => {
            window.removeEventListener('online', handleOnline);
            window.removeEventListener('offline', handleOffline);
        };
    }, []);

    useEffect(() => {
        if (!assessment.started) {
            removeDraft();
            setAnswers({});
            setCurrentSection(0);
            setHydrated(true);

            return;
        }

        let restoredAnswers: Record<number, string> = {};
        let restoredSection = 0;

        try {
            const stored = window.localStorage.getItem(storageKey);

            if (stored) {
                const parsed = JSON.parse(stored) as StoredDraft;

                const sameQuestionOrder =
                    Array.isArray(parsed.question_ids) &&
                    parsed.question_ids.length === questionIds.length &&
                    parsed.question_ids.every(
                        (id, index) =>
                            Number(id) === Number(questionIds[index]),
                    );

                if (sameQuestionOrder) {
                    assessment.questions.forEach((question) => {
                        const value = parsed.answers?.[String(question.id)];

                        if (
                            typeof value === 'string' &&
                            VALID_ANSWERS.includes(value)
                        ) {
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
                } else {
                    removeDraft();
                }
            }
        } catch {
            removeDraft();
        }

        setAnswers(restoredAnswers);
        setCurrentSection(restoredSection);
        setHydrated(true);
    }, [
        assessment.started,
        assessment.questions,
        questionIds,
        sections,
        storageKey,
    ]);

    useEffect(() => {
        if (!assessment.started || !hydrated) {
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
    }, [
        answers,
        assessment.started,
        currentSection,
        hydrated,
        questionIds,
        storageKey,
    ]);

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
        setStartDialogOpen(false);
        setStartProcessing(true);

        router.post(
            '/assessment/start',
            {},
            {
                preserveScroll: true,
                onFinish: () => {
                    setStartProcessing(false);
                },
            },
        );
    };

    const selectAnswer = (questionId: number, value: string) => {
        setAnswerError(null);

        setAnswers((current) => ({
            ...current,
            [questionId]: value,
        }));
    };

    const goNextSection = () => {
        if (!currentSectionComplete || isLastSection) {
            return;
        }

        setCurrentSection((current) =>
            Math.min(current + 1, sections.length - 1),
        );

        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    };

    const goPreviousSection = () => {
        setCurrentSection((current) => Math.max(current - 1, 0));

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
                'Hubungkan kembali internet sebelum membatalkan Assesment agar sesi di server dapat dihapus dengan benar.',
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

                    pendingNavigationRef.current = null;
                    setLeaveDialogOpen(false);

                    window.location.assign(destination);
                },
                onError: () => {
                    allowNavigationRef.current = false;
                },
                onFinish: () => {
                    setAbandonProcessing(false);
                },
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
                },
                onError: (errors) => {
                    allowNavigationRef.current = false;

                    const error = errors.answers;

                    setAnswerError(
                        typeof error === 'string'
                            ? error
                            : 'Jawaban Assesment belum dapat disimpan. Periksa kembali seluruh jawaban.',
                    );
                },
                onCancel: () => {
                    allowNavigationRef.current = false;
                },
                onFinish: () => {
                    setSubmitProcessing(false);
                },
            },
        );
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
                            <strong>{assessment.study_program}</strong> terdiri
                            dari{' '}
                            <strong>
                                {assessment.question_limit} pertanyaan
                            </strong>{' '}
                            yang dibagi menjadi 5 bagian.
                        </p>

                        <div className="mt-4 flex flex-wrap gap-2">
                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                Jurusan: {assessment.study_program}
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                {assessment.question_limit} soal
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                5 bagian
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

                    <div className="space-y-3">
                        <div className="neo-surface flex items-center gap-3 px-4 py-3 text-sm font-black">
                            <Clock3 className="size-5" />±{' '}
                            {assessment.duration_minutes} menit
                        </div>

                        {assessment.started && (
                            <div
                                className={`flex items-center gap-2 rounded-[12px] border-2 border-foreground px-4 py-3 text-xs font-black ${
                                    isOnline
                                        ? 'bg-secondary text-[#171717]'
                                        : 'bg-[var(--neo-yellow)] text-[#171717]'
                                }`}
                            >
                                {isOnline ? (
                                    <Wifi className="size-4" />
                                ) : (
                                    <WifiOff className="size-4" />
                                )}

                                {isOnline
                                    ? 'Terhubung'
                                    : 'Offline · jawaban tetap tersimpan'}
                            </div>
                        )}
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
                            Sistem akan menggunakan seluruh{' '}
                            {assessment.question_limit} soal dari kompetensi
                            inti jurusan. Soal dibagi menjadi lima bagian,
                            masing-masing berisi 10 soal. Jawaban dan bagian
                            terakhir disimpan pada browser selama sesi masih
                            aktif.
                        </p>

                        <div className="mt-5 grid gap-3 sm:grid-cols-3">
                            <div className="neo-card-flat p-4">
                                <p className="font-mono text-2xl font-black">
                                    {assessment.question_limit}
                                </p>

                                <p className="mt-1 text-xs font-bold">
                                    Total soal
                                </p>
                            </div>

                            <div className="neo-card-flat p-4">
                                <p className="font-mono text-2xl font-black">
                                    5 × 10
                                </p>

                                <p className="mt-1 text-xs font-bold">
                                    Pembagian bagian
                                </p>
                            </div>

                            <div className="neo-card-flat p-4">
                                <p className="font-mono text-2xl font-black">
                                    {assessment.skill_count}
                                </p>

                                <p className="mt-1 text-xs font-bold">
                                    Kompetensi inti
                                </p>
                            </div>
                        </div>

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
                                  ? 'Ulangi Assesment'
                                  : 'Mulai Assesment'}
                        </Button>
                    </section>
                ) : (
                    <div className="mt-8 grid gap-6 lg:grid-cols-[1fr_300px]">
                        <section className="space-y-5">
                            <div className="neo-card p-5 sm:p-6">
                                <div className="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <p className="text-xs font-black tracking-[0.15em] text-muted-foreground uppercase">
                                            Bagian {currentSection + 1} dari{' '}
                                            {sections.length}
                                        </p>

                                        <h2 className="mt-2 text-2xl font-black">
                                            Soal{' '}
                                            {currentSection * SECTION_SIZE + 1}–
                                            {Math.min(
                                                (currentSection + 1) *
                                                    SECTION_SIZE,
                                                assessment.questions.length,
                                            )}
                                        </h2>
                                    </div>

                                    <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] px-3 py-1 text-xs font-black text-[#171717]">
                                        {
                                            currentQuestions.filter(
                                                (question) =>
                                                    Boolean(
                                                        answers[question.id],
                                                    ),
                                            ).length
                                        }
                                        /{currentQuestions.length} dijawab
                                    </span>
                                </div>

                                <div className="mt-5 grid grid-cols-5 gap-2">
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
                                                className={`rounded-[10px] border-2 border-foreground px-2 py-3 text-center ${
                                                    sectionIndex ===
                                                    currentSection
                                                        ? 'bg-secondary text-[#171717]'
                                                        : sectionComplete
                                                          ? 'bg-[var(--neo-lime)] text-[#171717]'
                                                          : 'bg-card'
                                                }`}
                                            >
                                                <p className="text-xs font-black">
                                                    B{sectionIndex + 1}
                                                </p>

                                                <p className="mt-1 font-mono text-[10px] font-black">
                                                    {answered}/{section.length}
                                                </p>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>

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
                                        className="neo-card p-6 sm:p-8"
                                    >
                                        <div className="flex flex-wrap items-center justify-between gap-4">
                                            <p className="font-mono text-sm font-black">
                                                Soal {questionNumber}
                                            </p>

                                            <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] px-3 py-1 text-xs font-black text-[#171717]">
                                                {question.difficulty}
                                            </span>
                                        </div>

                                        <h3 className="mt-5 text-xl leading-snug font-black tracking-tight sm:text-2xl">
                                            {question.prompt}
                                        </h3>

                                        <div className="mt-6 grid gap-3">
                                            {Object.entries(
                                                question.options,
                                            ).map(([key, value]) => {
                                                const selected =
                                                    currentAnswer === key;

                                                return (
                                                    <button
                                                        key={key}
                                                        type="button"
                                                        onClick={() =>
                                                            selectAnswer(
                                                                question.id,
                                                                key,
                                                            )
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

                            <div className="neo-card flex flex-col-reverse gap-3 p-5 sm:flex-row sm:items-center sm:justify-between">
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
                                              : 'Selesaikan Assesment'}
                                    </Button>
                                ) : (
                                    <Button
                                        type="button"
                                        onClick={goNextSection}
                                        disabled={!currentSectionComplete}
                                    >
                                        Lanjut ke Bagian {currentSection + 2}
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
                                    Setiap jawaban dan bagian terakhir disimpan
                                    di browser. Jika halaman di-refresh atau
                                    internet terputus, progres tetap dapat
                                    dipulihkan selama sesi tidak dibatalkan.
                                </p>
                            </section>

                            <section
                                className={`rounded-[14px] border-2 border-[#171717] p-5 text-[#171717] ${
                                    isOnline
                                        ? 'bg-[var(--neo-lime)]'
                                        : 'bg-[var(--neo-yellow)]'
                                }`}
                            >
                                <div className="flex items-start gap-3">
                                    {isOnline ? (
                                        <Wifi className="mt-0.5 size-5 shrink-0" />
                                    ) : (
                                        <WifiOff className="mt-0.5 size-5 shrink-0" />
                                    )}

                                    <div>
                                        <p className="text-sm font-black">
                                            {isOnline
                                                ? 'Progres terlindungi'
                                                : 'Koneksi terputus'}
                                        </p>

                                        <p className="mt-2 text-xs leading-5 font-semibold">
                                            {isOnline
                                                ? 'Reload tidak akan menghapus jawaban yang sudah dipilih.'
                                                : 'Tetap kerjakan soal. Jawaban tersimpan secara lokal dan dapat dikirim setelah internet kembali.'}
                                        </p>
                                    </div>
                                </div>
                            </section>

                            <Button
                                type="button"
                                variant="outline"
                                className="w-full"
                                onClick={() => requestLeaveAssessment()}
                            >
                                <X />
                                Batalkan Assesment
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
                                ? 'Mulai Assesment baru?'
                                : 'Mulai Assesment?'}
                        </DialogTitle>

                        <DialogDescription>
                            Sistem akan mengacak {assessment.question_limit}{' '}
                            soal dan membaginya menjadi 5 bagian berisi 10 soal.
                            Setelah dimulai, refresh tidak akan menghapus
                            jawaban yang telah dipilih.
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
                        <DialogTitle>Batalkan sesi Assesment?</DialogTitle>

                        <DialogDescription>
                            Jika Anda memilih keluar, seluruh jawaban sementara
                            dan sesi Assesment aktif akan dihapus. Saat memulai
                            kembali, Assesment dimulai dari Bagian 1 dan soal
                            nomor 1. Reload biasa tidak menghapus progres.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="rounded-[12px] border-2 border-[#171717] bg-[var(--neo-yellow)] p-4 text-sm font-semibold text-[#171717]">
                        <div className="flex items-start gap-3">
                            <ShieldAlert className="mt-0.5 size-5 shrink-0" />

                            <p>
                                Pembatalan berbeda dengan refresh. Gunakan
                                pembatalan hanya jika memang ingin meninggalkan
                                Assesment dan menghapus progres.
                            </p>
                        </div>
                    </div>

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
            title: 'Assesment',
            href: '/assessment',
        },
    ],
};
