import { Head, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    Clock3,
    FileCheck2,
    History,
} from 'lucide-react';
import { useMemo, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

type Question = {
    id: number;
    question_type: 'multiple_choice' | 'case' | 'practical';
    prompt: string;
    practical_instructions?: string | null;
    evidence_required: boolean;
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
    responses: Record<number, string>;
    evidence_urls: Record<number, string>;
    experience_notes: Record<number, string>;
    experience_evidence_urls: Record<number, string>;
};

const typeLabels = {
    multiple_choice: 'Pilihan ganda',
    case: 'Studi kasus',
    practical: 'Tugas praktik',
};

const PRACTICAL_MIN_RESPONSE_LENGTH = 20;

const isValidHttpUrl = (value: string) => {
    if (!value.trim()) {
        return false;
    }

    try {
        const url = new URL(value);

        return url.protocol === 'http:' || url.protocol === 'https:';
    } catch {
        return false;
    }
};

export default function AssessmentPage({
    assessment,
    latestAttempt,
    profileExperience,
}: {
    assessment: Assessment;
    latestAttempt?: string | null;
    profileExperience?: string | null;
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
        responses: {},
        evidence_urls: {},
        experience_notes: {},
        experience_evidence_urls: {},
    });

    const getQuestionIncompleteReason = (item: Question) => {
        if (!form.data.answers[item.id]) {
            return 'Pilih salah satu jawaban terlebih dahulu.';
        }

        if (item.question_type !== 'practical') {
            return null;
        }

        const response = form.data.responses[item.id]?.trim() ?? '';

        if (response.length < PRACTICAL_MIN_RESPONSE_LENGTH) {
            return `Jelaskan hasil praktik minimal ${PRACTICAL_MIN_RESPONSE_LENGTH} karakter. Saat ini baru ${response.length} karakter.`;
        }

        const evidenceUrl = form.data.evidence_urls[item.id]?.trim() ?? '';

        if (item.evidence_required && !evidenceUrl) {
            return 'Bukti praktik wajib diisi sebelum melanjutkan.';
        }

        if (evidenceUrl && !isValidHttpUrl(evidenceUrl)) {
            return 'Bukti praktik harus berupa URL http:// atau https:// yang valid.';
        }

        return null;
    };

    const isQuestionComplete = (item: Question) => {
        return getQuestionIncompleteReason(item) === null;
    };

    const completedCount =
        assessment.questions.filter(isQuestionComplete).length;

    const progress =
        assessment.questions.length > 0
            ? Math.round((completedCount / assessment.questions.length) * 100)
            : 0;

    const currentRating = form.data.self_ratings[question?.id] ?? 50;

    const currentResponse =
        question?.question_type === 'practical'
            ? (form.data.responses[question.id] ?? '').trim()
            : '';

    const currentResponseLength = currentResponse.length;

    const currentEvidenceUrl =
        question?.question_type === 'practical'
            ? (form.data.evidence_urls[question.id] ?? '').trim()
            : '';

    const currentIncompleteReason = question
        ? getQuestionIncompleteReason(question)
        : null;

    const canMove = question ? isQuestionComplete(question) : false;

    const complete = assessment.questions.every(isQuestionComplete);

    const statusText = latestAttempt
        ? 'Hasil Assesment terbaru akan menjadi dasar skor kemampuan aktif dan roadmap berikutnya.'
        : 'Jawab sesuai kemampuan saat ini. Tugas praktik membutuhkan bukti agar hasil tidak hanya berasal dari penilaian diri.';

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

    const setRecordValue = (
        field:
            | 'responses'
            | 'evidence_urls'
            | 'experience_notes'
            | 'experience_evidence_urls',
        value: string,
    ) => {
        form.setData(field, {
            ...form.data[field],
            [question.id]: value,
        });
    };

    const submit = () => {
        form.post('/assessment');
    };

    return (
        <>
            <Head title="Assesment Awal" />

            <div className="neo-page py-8 md:py-10">
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

                    <div className="neo-surface flex items-center gap-3 px-4 py-3 text-sm font-black">
                        <Clock3 className="size-5" />±{' '}
                        {assessment.duration_minutes} menit
                    </div>
                </div>

                <div className="mt-8 grid gap-6 lg:grid-cols-[1fr_280px]">
                    <section className="neo-card p-6 sm:p-8">
                        <div className="mb-7 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p className="text-xs font-black tracking-[0.15em] text-muted-foreground uppercase">
                                    Pertanyaan {index + 1} dari{' '}
                                    {assessment.questions.length}
                                </p>

                                <p className="mt-1 text-sm font-bold">
                                    {question.skill.name}
                                </p>
                            </div>

                            <div className="flex flex-wrap gap-2">
                                <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-blue)] px-3 py-1 text-xs font-black text-[#171717]">
                                    {question.skill.category}
                                </span>

                                <span className="rounded-full border-2 border-[#171717] bg-[var(--neo-yellow)] px-3 py-1 text-xs font-black text-[#171717]">
                                    {typeLabels[question.question_type]}
                                </span>
                            </div>
                        </div>

                        {question.question_type === 'case' && (
                            <p className="mb-3 text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Analisis situasi berikut
                            </p>
                        )}

                        <h2 className="text-2xl leading-snug font-black tracking-tight">
                            {question.prompt}
                        </h2>

                        {question.question_type === 'practical' &&
                            question.practical_instructions && (
                                <div className="mt-6 rounded-[14px] border-2 border-[#171717] bg-[var(--neo-yellow)] p-5 text-[#171717]">
                                    <div className="flex items-center gap-2">
                                        <FileCheck2 className="size-5" />

                                        <p className="font-black">
                                            Tugas praktik
                                        </p>
                                    </div>

                                    <p className="mt-3 text-sm leading-relaxed font-semibold">
                                        {question.practical_instructions}
                                    </p>
                                </div>
                            )}

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

                        {question.question_type === 'practical' && (
                            <div className="mt-7 grid gap-4 rounded-[14px] border-2 border-foreground bg-muted p-5">
                                <label>
                                    <div className="mb-2 flex flex-wrap items-end justify-between gap-2">
                                        <span className="block text-sm font-black">
                                            Jelaskan hasil praktik
                                        </span>

                                        <span
                                            className={`font-mono text-xs font-black ${
                                                currentResponseLength >=
                                                PRACTICAL_MIN_RESPONSE_LENGTH
                                                    ? 'text-foreground'
                                                    : 'text-muted-foreground'
                                            }`}
                                        >
                                            {currentResponseLength}/
                                            {PRACTICAL_MIN_RESPONSE_LENGTH}{' '}
                                            karakter
                                        </span>
                                    </div>

                                    <Textarea
                                        value={
                                            form.data.responses[question.id] ??
                                            ''
                                        }
                                        onChange={(event) =>
                                            setRecordValue(
                                                'responses',
                                                event.target.value,
                                            )
                                        }
                                        rows={5}
                                        placeholder="Jelaskan apa yang dikerjakan, hasil yang diperoleh, dan bagian yang masih sulit."
                                    />

                                    <p className="mt-2 text-xs leading-relaxed font-medium text-muted-foreground">
                                        Tuliskan minimal{' '}
                                        {PRACTICAL_MIN_RESPONSE_LENGTH} karakter
                                        agar hasil praktik dapat dievaluasi.
                                    </p>
                                </label>

                                <label>
                                    <span className="mb-2 block text-sm font-black">
                                        Bukti praktik
                                        {question.evidence_required
                                            ? ' wajib'
                                            : ' opsional'}
                                    </span>

                                    <Input
                                        type="url"
                                        value={
                                            form.data.evidence_urls[
                                                question.id
                                            ] ?? ''
                                        }
                                        onChange={(event) =>
                                            setRecordValue(
                                                'evidence_urls',
                                                event.target.value,
                                            )
                                        }
                                        placeholder="https://github.com/... atau https://..."
                                    />

                                    <p className="mt-2 text-xs leading-relaxed font-medium text-muted-foreground">
                                        Gunakan tautan repository, deployment,
                                        dokumen, atau bukti lain yang dapat
                                        diakses melalui HTTP atau HTTPS.
                                    </p>

                                    {currentEvidenceUrl &&
                                        !isValidHttpUrl(currentEvidenceUrl) && (
                                            <p className="mt-2 text-xs font-bold text-destructive">
                                                URL belum valid. Gunakan alamat
                                                yang diawali http:// atau
                                                https://.
                                            </p>
                                        )}
                                </label>
                            </div>
                        )}

                        <div className="mt-8 rounded-[14px] border-2 border-foreground bg-muted p-5">
                            <div className="flex items-center justify-between gap-4">
                                <div>
                                    <p className="text-sm font-black">
                                        Seberapa yakin kamu dengan kemampuan
                                        ini?
                                    </p>

                                    <p className="mt-1 text-xs font-medium text-muted-foreground">
                                        Penilaian diri memiliki bobot 20%.
                                        Jawaban objektif tetap menjadi komponen
                                        utama.
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

                        <div className="mt-6 rounded-[14px] border-2 border-foreground bg-card p-5">
                            <div className="flex items-center gap-2">
                                <History className="size-5" />

                                <p className="text-sm font-black">
                                    Riwayat pengalaman terkait skill ini
                                </p>
                            </div>

                            {profileExperience && (
                                <p className="mt-3 text-xs leading-relaxed font-medium text-muted-foreground">
                                    Profil sebelumnya: {profileExperience}
                                </p>
                            )}

                            <div className="mt-4 grid gap-4">
                                <Textarea
                                    rows={3}
                                    value={
                                        form.data.experience_notes[
                                            question.id
                                        ] ?? ''
                                    }
                                    onChange={(event) =>
                                        setRecordValue(
                                            'experience_notes',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Opsional: pernah menggunakan skill ini pada tugas, proyek, organisasi, pekerjaan, atau pengalaman lain?"
                                />

                                <Input
                                    type="url"
                                    value={
                                        form.data.experience_evidence_urls[
                                            question.id
                                        ] ?? ''
                                    }
                                    onChange={(event) =>
                                        setRecordValue(
                                            'experience_evidence_urls',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Tautan repository, portfolio, dokumen, atau bukti lain"
                                />
                            </div>
                        </div>

                        {currentIncompleteReason && (
                            <div className="mt-6 rounded-[12px] border-2 border-[#171717] bg-[var(--neo-yellow)] p-4 text-sm leading-relaxed font-bold text-[#171717]">
                                <span className="font-black">
                                    Belum bisa melanjutkan:
                                </span>{' '}
                                {currentIncompleteReason}
                            </div>
                        )}

                        {Object.keys(form.errors).length > 0 && (
                            <div className="mt-6 rounded-[12px] border-2 border-[#171717] bg-[var(--neo-pink)] p-4 text-sm font-bold text-[#171717]">
                                Periksa kembali jawaban, tugas praktik, dan
                                bukti yang diwajibkan sebelum melanjutkan.
                            </div>
                        )}

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
                                    Selesaikan Assesment
                                </Button>
                            )}
                        </div>
                    </section>

                    <aside className="space-y-4">
                        <div className="neo-card-flat p-5">
                            <div className="flex items-end justify-between">
                                <span className="text-sm font-black">
                                    Selesai diisi
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
                                        className={`flex aspect-square items-center justify-center rounded-[8px] border-2 border-foreground text-xs font-black ${
                                            itemIndex === index
                                                ? 'bg-[var(--neo-blue)] text-[#171717]'
                                                : isQuestionComplete(item)
                                                  ? 'bg-secondary text-[#171717]'
                                                  : 'bg-card'
                                        }`}
                                    >
                                        {itemIndex + 1}
                                    </button>
                                ))}
                            </div>
                        </div>

                        <div className="rounded-[14px] border-2 border-[#171717] bg-[var(--neo-yellow)] p-5 text-sm leading-relaxed font-bold text-[#171717]">
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
            title: 'Assesment',
            href: '/assessment',
        },
    ],
};
