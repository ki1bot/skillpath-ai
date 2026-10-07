import { Head, Link, useForm } from '@inertiajs/react';
import { ExternalLink } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

type ReviewStatus = 'pending' | 'reviewed';

type ProjectSubmission = {
    id: number;
    status: string;
    review_status: ReviewStatus;
    evaluation_score?: number | null;
    repository_url?: string | null;
    admin_notes?: string | null;
    submitted_at?: string | null;
    reviewed_at?: string | null;
    user: {
        id: number;
        name: string;
        email: string;
        study_program?: string | null;
    };
    reviewer?: {
        id: number;
        name: string;
        email: string;
    } | null;
    project: {
        id: number;
        title: string;
        summary: string;
        problem_statement: string;
        minimum_features: string[];
        completion_criteria: string[];
        career: string;
        skills: string[];
    };
};

type SubmissionPaginator = {
    data: ProjectSubmission[];
    current_page: number;
    last_page: number;
    total: number;
    prev_page_url?: string | null;
    next_page_url?: string | null;
};

type Counts = {
    pending: number;
    reviewed: number;
    passed: number;
    failed: number;
};

const formatDate = (value?: string | null): string => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date);
};

function SubmissionCard({ submission }: { submission: ProjectSubmission }) {
    const form = useForm({
        score: '',
        admin_notes: '',
    });

    const submitReview = (event: React.FormEvent) => {
        event.preventDefault();

        form.patch(`/admin/project-submissions/${submission.id}`, {
            preserveScroll: true,
        });
    };

    const reviewed = submission.review_status === 'reviewed';

    const passed =
        reviewed &&
        submission.evaluation_score !== null &&
        submission.evaluation_score !== undefined &&
        submission.evaluation_score >= 80;

    return (
        <article className="neo-card overflow-hidden">
            <div className="border-b-2 border-foreground p-5 sm:p-6">
                <div className="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
                    <div>
                        <div className="flex flex-wrap gap-2">
                            <span
                                className={`rounded-full border-2 border-[#171717] px-3 py-1 text-xs font-black text-[#171717] ${
                                    reviewed
                                        ? passed
                                            ? 'bg-[var(--neo-lime)]'
                                            : 'bg-[var(--neo-pink)]'
                                        : 'bg-[var(--neo-yellow)]'
                                }`}
                            >
                                {reviewed
                                    ? passed
                                        ? 'Lulus'
                                        : 'Perlu diperbaiki'
                                    : 'Menunggu pemeriksaan'}
                            </span>

                            <span className="rounded-full border-2 border-foreground/20 bg-muted/30 px-3 py-1 text-xs font-black">
                                {submission.project.career}
                            </span>
                        </div>

                        <h2 className="mt-3 text-xl font-black sm:text-2xl">
                            {submission.project.title}
                        </h2>

                        <p className="mt-2 text-sm font-semibold">
                            {submission.user.name}
                        </p>

                        <p className="mt-1 text-xs font-medium text-muted-foreground">
                            {submission.user.email}
                            {submission.user.study_program
                                ? ` · ${submission.user.study_program}`
                                : ''}
                        </p>
                    </div>

                    <div className="text-xs font-medium text-muted-foreground lg:text-right">
                        <p>Dikirim {formatDate(submission.submitted_at)}</p>

                        {reviewed && (
                            <p className="mt-1">
                                Diperiksa {formatDate(submission.reviewed_at)}
                            </p>
                        )}
                    </div>
                </div>
            </div>

            <div className="grid gap-6 p-5 sm:p-6 xl:grid-cols-[minmax(0,1fr)_360px]">
                <div className="min-w-0 space-y-5">
                    <section>
                        <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                            Tugas proyek
                        </p>

                        <p className="mt-2 text-sm leading-7 font-medium">
                            {submission.project.problem_statement}
                        </p>
                    </section>

                    <section>
                        <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                            Kemampuan yang dinilai
                        </p>

                        <div className="mt-2 flex flex-wrap gap-2">
                            {submission.project.skills.map((skill) => (
                                <span
                                    key={skill}
                                    className="rounded-full border-2 border-foreground/15 bg-muted/30 px-3 py-1 text-xs font-bold"
                                >
                                    {skill}
                                </span>
                            ))}
                        </div>
                    </section>

                    <section>
                        <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                            Bagian wajib
                        </p>

                        <div className="mt-3 grid gap-2">
                            {submission.project.minimum_features.map(
                                (feature, index) => (
                                    <div
                                        key={feature}
                                        className="flex gap-3 rounded-[10px] border-2 border-foreground/15 bg-muted/20 p-3"
                                    >
                                        <span className="font-mono text-xs font-black">
                                            {String(index + 1).padStart(2, '0')}
                                        </span>

                                        <p className="text-sm leading-6 font-medium">
                                            {feature}
                                        </p>
                                    </div>
                                ),
                            )}
                        </div>
                    </section>

                    <section>
                        <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                            Kriteria pemeriksaan
                        </p>

                        <div className="mt-3 grid gap-2">
                            {submission.project.completion_criteria.map(
                                (criterion) => (
                                    <p
                                        key={criterion}
                                        className="text-sm leading-6 font-medium"
                                    >
                                        • {criterion}
                                    </p>
                                ),
                            )}
                        </div>
                    </section>

                    <section>
                        <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                            Folder hasil proyek
                        </p>

                        {submission.repository_url ? (
                            <Button asChild variant="outline" className="mt-3">
                                <a
                                    href={submission.repository_url}
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    <ExternalLink className="size-4" />
                                    Buka Google Drive
                                </a>
                            </Button>
                        ) : (
                            <p className="mt-2 text-sm text-muted-foreground">
                                Folder Google Drive tidak tersedia.
                            </p>
                        )}
                    </section>
                </div>

                {reviewed ? (
                    <aside className="h-fit rounded-[12px] border-2 border-foreground bg-card p-5">
                        <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                            Hasil pemeriksaan
                        </p>

                        <p className="mt-3 text-4xl font-black">
                            {submission.evaluation_score ?? 0}/100
                        </p>

                        <p className="mt-2 text-sm font-black">
                            {passed
                                ? 'Lulus'
                                : 'Belum mencapai nilai minimal 80'}
                        </p>

                        <div className="mt-4 border-t-2 border-foreground/15 pt-4">
                            <p className="text-xs font-black text-muted-foreground uppercase">
                                Catatan admin
                            </p>

                            <p className="mt-2 text-sm leading-6 font-medium whitespace-pre-wrap">
                                {submission.admin_notes || '-'}
                            </p>
                        </div>

                        {submission.reviewer && (
                            <p className="mt-4 text-xs leading-5 font-medium text-muted-foreground">
                                Diperiksa oleh {submission.reviewer.name}.
                            </p>
                        )}
                    </aside>
                ) : (
                    <form
                        onSubmit={submitReview}
                        className="h-fit rounded-[12px] border-2 border-foreground bg-card p-5"
                    >
                        <p className="text-lg font-black">Beri nilai proyek</p>

                        <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                            Periksa folder dan seluruh bagian wajib. Nilai 80
                            sampai 100 dinyatakan lulus. Nilai 0 sampai 79 harus
                            diperbaiki dan dikumpulkan ulang.
                        </p>

                        <label className="mt-5 block">
                            <span className="mb-2 block text-sm font-black">
                                Nilai
                            </span>

                            <Input
                                type="number"
                                min={0}
                                max={100}
                                step={1}
                                inputMode="numeric"
                                value={form.data.score}
                                onChange={(event) =>
                                    form.setData('score', event.target.value)
                                }
                                required
                            />

                            {form.errors.score && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {form.errors.score}
                                </p>
                            )}
                        </label>

                        <label className="mt-4 block">
                            <span className="mb-2 block text-sm font-black">
                                Catatan untuk mahasiswa
                            </span>

                            <Textarea
                                rows={7}
                                maxLength={3000}
                                value={form.data.admin_notes}
                                onChange={(event) =>
                                    form.setData(
                                        'admin_notes',
                                        event.target.value,
                                    )
                                }
                                placeholder="Jelaskan bagian yang sudah memenuhi kriteria dan bagian yang masih perlu diperbaiki."
                            />

                            {form.errors.admin_notes && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {form.errors.admin_notes}
                                </p>
                            )}
                        </label>

                        <Button
                            className="mt-5 w-full"
                            disabled={
                                form.processing || form.data.score.trim() === ''
                            }
                        >
                            {form.processing
                                ? 'Menyimpan...'
                                : 'Simpan nilai proyek'}
                        </Button>
                    </form>
                )}
            </div>
        </article>
    );
}

export default function ProjectSubmissionsPage({
    status,
    counts,
    submissions,
}: {
    status: 'pending' | 'reviewed' | 'all';
    counts: Counts;
    submissions: SubmissionPaginator;
}) {
    const filters = [
        {
            key: 'pending',
            label: `Menunggu (${counts.pending})`,
            href: '/admin/project-submissions?status=pending',
        },
        {
            key: 'reviewed',
            label: `Sudah diperiksa (${counts.reviewed})`,
            href: '/admin/project-submissions?status=reviewed',
        },
        {
            key: 'all',
            label: `Semua (${counts.pending + counts.reviewed})`,
            href: '/admin/project-submissions?status=all',
        },
    ] as const;

    return (
        <>
            <Head title="Penilaian Proyek" />

            <div className="neo-page py-7 md:py-9">
                <header className="border-b-2 border-foreground pb-6">
                    <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                        Administrasi
                    </p>

                    <h1 className="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                        Penilaian proyek
                    </h1>

                    <p className="mt-3 max-w-2xl text-sm leading-7 font-medium text-muted-foreground">
                        Buka folder Google Drive, bandingkan hasil proyek dengan
                        bagian wajib dan kriteria pemeriksaan, kemudian berikan
                        nilai serta catatan yang memang sesuai dengan pekerjaan
                        mahasiswa.
                    </p>
                </header>

                <section className="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div className="rounded-[10px] border-2 border-[#171717] bg-[var(--neo-yellow)] p-4 text-[#171717]">
                        <p className="text-xs font-black uppercase">Menunggu</p>
                        <p className="mt-2 text-2xl font-black">
                            {counts.pending}
                        </p>
                    </div>

                    <div className="rounded-[10px] border-2 border-foreground bg-card p-4">
                        <p className="text-xs font-black text-muted-foreground uppercase">
                            Sudah diperiksa
                        </p>
                        <p className="mt-2 text-2xl font-black">
                            {counts.reviewed}
                        </p>
                    </div>

                    <div className="rounded-[10px] border-2 border-[#171717] bg-[var(--neo-lime)] p-4 text-[#171717]">
                        <p className="text-xs font-black uppercase">Lulus</p>
                        <p className="mt-2 text-2xl font-black">
                            {counts.passed}
                        </p>
                    </div>

                    <div className="rounded-[10px] border-2 border-[#171717] bg-[var(--neo-pink)] p-4 text-[#171717]">
                        <p className="text-xs font-black uppercase">
                            Perlu diperbaiki
                        </p>
                        <p className="mt-2 text-2xl font-black">
                            {counts.failed}
                        </p>
                    </div>
                </section>

                <nav className="mt-6 flex flex-wrap gap-2">
                    {filters.map((filter) => (
                        <Link
                            key={filter.key}
                            href={filter.href}
                            preserveScroll
                            className={`rounded-[9px] border-2 border-foreground px-4 py-2 text-sm font-black ${
                                status === filter.key
                                    ? 'bg-foreground text-background'
                                    : 'bg-card'
                            }`}
                        >
                            {filter.label}
                        </Link>
                    ))}
                </nav>

                <div className="mt-6 grid gap-5">
                    {submissions.data.length === 0 && (
                        <div className="neo-card p-8 text-center">
                            <p className="font-black">
                                Tidak ada proyek pada daftar ini.
                            </p>
                        </div>
                    )}

                    {submissions.data.map((submission) => (
                        <SubmissionCard
                            key={submission.id}
                            submission={submission}
                        />
                    ))}
                </div>

                {submissions.last_page > 1 && (
                    <div className="mt-6 flex items-center justify-between gap-3 border-t-2 border-foreground pt-5">
                        <p className="text-sm font-semibold text-muted-foreground">
                            Halaman {submissions.current_page} dari{' '}
                            {submissions.last_page} · {submissions.total}{' '}
                            pengumpulan
                        </p>

                        <div className="flex gap-2">
                            {submissions.prev_page_url ? (
                                <Button asChild variant="outline">
                                    <Link href={submissions.prev_page_url}>
                                        Sebelumnya
                                    </Link>
                                </Button>
                            ) : (
                                <Button variant="outline" disabled>
                                    Sebelumnya
                                </Button>
                            )}

                            {submissions.next_page_url ? (
                                <Button asChild variant="outline">
                                    <Link href={submissions.next_page_url}>
                                        Berikutnya
                                    </Link>
                                </Button>
                            ) : (
                                <Button variant="outline" disabled>
                                    Berikutnya
                                </Button>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}

ProjectSubmissionsPage.layout = {
    breadcrumbs: [
        {
            title: 'Penilaian Proyek',
            href: '/admin/project-submissions',
        },
    ],
};
