import { Form, Head } from '@inertiajs/react';
import {
    CheckCircle2,
    Clock3,
    Filter,
    MessageSquareText,
    Save,
    Search,
    Star,
    UserRound,
} from 'lucide-react';
import { useMemo, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';

type FeedbackStatus = 'pending' | 'reviewing' | 'resolved';

type Feedback = {
    id: number;
    category: string;
    subject: string;
    message: string;
    rating?: number | null;
    status: FeedbackStatus;
    admin_response?: string | null;
    created_at: string;
    reviewed_at?: string | null;
    user: {
        id: number;
        name: string;
        email: string;
    };
    reviewer?: {
        id: number;
        name: string;
        email: string;
    } | null;
};

const categoryLabels: Record<string, string> = {
    general: 'Umum',
    content: 'Materi',
    recommendation: 'Rekomendasi',
    usability: 'UI/UX',
    bug: 'Bug',
};

const statusLabels: Record<FeedbackStatus, string> = {
    pending: 'Menunggu',
    reviewing: 'Sedang ditinjau',
    resolved: 'Selesai',
};

const statusClasses: Record<FeedbackStatus, string> = {
    pending: 'bg-[var(--neo-yellow)]',
    reviewing: 'bg-[var(--neo-blue)]',
    resolved: 'bg-[var(--neo-lime)]',
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

function FeedbackReviewCard({ feedback }: { feedback: Feedback }) {
    const [status, setStatus] = useState<FeedbackStatus>(feedback.status);

    return (
        <article className="neo-card overflow-hidden">
            <div className="border-b-2 border-foreground p-5 sm:p-6">
                <div className="flex flex-col justify-between gap-5 lg:flex-row">
                    <div className="min-w-0">
                        <div className="flex flex-wrap gap-2">
                            <span className="neo-label bg-[#fffdf7] text-[#171717]">
                                {categoryLabels[feedback.category] ??
                                    feedback.category}
                            </span>

                            <span
                                className={`neo-label ${statusClasses[feedback.status]}`}
                            >
                                {statusLabels[feedback.status]}
                            </span>
                        </div>

                        <h2 className="mt-4 text-2xl font-black break-words">
                            {feedback.subject}
                        </h2>

                        <div className="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-xs font-bold text-muted-foreground">
                            <span className="flex items-center gap-1.5">
                                <UserRound className="size-4" />
                                {feedback.user.name}
                            </span>

                            <span>{feedback.user.email}</span>

                            <span className="flex items-center gap-1.5">
                                <Clock3 className="size-4" />
                                {formatDate(feedback.created_at)}
                            </span>
                        </div>
                    </div>

                    {feedback.rating && (
                        <div className="flex h-fit items-center gap-2 rounded-[12px] border-2 border-foreground bg-[var(--neo-yellow)] px-4 py-3 text-[#171717]">
                            <Star className="size-5 fill-current" />

                            <span className="font-mono text-2xl font-black">
                                {feedback.rating}/5
                            </span>
                        </div>
                    )}
                </div>

                <div className="mt-5 rounded-[12px] border-2 border-foreground bg-muted p-4">
                    <p className="text-xs font-black tracking-wide uppercase">
                        Pesan pengguna
                    </p>

                    <p className="mt-2 text-sm leading-7 font-medium whitespace-pre-wrap">
                        {feedback.message}
                    </p>
                </div>
            </div>

            <Form
                action={`/admin/feedback/${feedback.id}`}
                method="patch"
                className="grid gap-5 p-5 sm:p-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-5 md:grid-cols-[280px_1fr]">
                            <label>
                                <span className="mb-2 block text-sm font-black">
                                    Status penanganan
                                </span>

                                <select
                                    name="status"
                                    value={status}
                                    onChange={(event) =>
                                        setStatus(
                                            event.target
                                                .value as FeedbackStatus,
                                        )
                                    }
                                    className="h-11 w-full rounded-[9px] border-2 border-foreground bg-background px-3 text-sm font-bold"
                                >
                                    <option value="pending">Menunggu</option>

                                    <option value="reviewing">
                                        Sedang ditinjau
                                    </option>

                                    <option value="resolved">Selesai</option>
                                </select>

                                <p className="mt-2 text-xs leading-5 font-semibold text-muted-foreground">
                                    Gunakan “Selesai” hanya ketika masalah sudah
                                    ditinjau dan pengguna telah mendapatkan
                                    tanggapan.
                                </p>

                                {errors.status && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {errors.status}
                                    </p>
                                )}
                            </label>

                            <label>
                                <span className="mb-2 block text-sm font-black">
                                    Tanggapan administrator
                                </span>

                                <Textarea
                                    name="admin_response"
                                    defaultValue={feedback.admin_response ?? ''}
                                    rows={5}
                                    maxLength={3000}
                                    minLength={
                                        status === 'resolved' ? 10 : undefined
                                    }
                                    required={status === 'resolved'}
                                    placeholder="Jelaskan hasil peninjauan, tindakan yang dilakukan, atau alasan masalah belum dapat diselesaikan."
                                />

                                <div className="mt-2 flex flex-wrap items-center justify-between gap-2 text-xs font-semibold text-muted-foreground">
                                    <span>
                                        {status === 'resolved'
                                            ? 'Wajib minimal 10 karakter untuk status Selesai.'
                                            : 'Opsional selama masukan masih ditinjau.'}
                                    </span>

                                    <span>Maksimal 3000 karakter.</span>
                                </div>

                                {errors.admin_response && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {errors.admin_response}
                                    </p>
                                )}
                            </label>
                        </div>

                        {feedback.reviewer && (
                            <div className="rounded-[10px] border-2 border-foreground/30 bg-muted p-3 text-xs font-semibold text-muted-foreground">
                                Terakhir ditinjau oleh{' '}
                                <strong className="text-foreground">
                                    {feedback.reviewer.name}
                                </strong>{' '}
                                pada {formatDate(feedback.reviewed_at)}.
                            </div>
                        )}

                        <Button className="w-fit" disabled={processing}>
                            <Save />

                            {processing ? 'Menyimpan...' : 'Simpan perubahan'}
                        </Button>
                    </>
                )}
            </Form>
        </article>
    );
}

export default function AdminFeedbackPage({
    feedbacks,
}: {
    feedbacks: Feedback[];
}) {
    const [statusFilter, setStatusFilter] = useState('all');
    const [categoryFilter, setCategoryFilter] = useState('all');

    const pendingCount = feedbacks.filter(
        (feedback) => feedback.status === 'pending',
    ).length;

    const reviewingCount = feedbacks.filter(
        (feedback) => feedback.status === 'reviewing',
    ).length;

    const resolvedCount = feedbacks.filter(
        (feedback) => feedback.status === 'resolved',
    ).length;

    const filteredFeedbacks = useMemo(
        () =>
            feedbacks.filter((feedback) => {
                const statusMatches =
                    statusFilter === 'all' || feedback.status === statusFilter;

                const categoryMatches =
                    categoryFilter === 'all' ||
                    feedback.category === categoryFilter;

                return statusMatches && categoryMatches;
            }),
        [categoryFilter, feedbacks, statusFilter],
    );

    return (
        <>
            <Head title="Masukan Pengguna" />

            <div className="neo-page py-8 md:py-10">
                <section className="neo-hero neo-accent-orange border-[#171717]">
                    <span className="neo-label bg-[#fffdf7]">
                        <MessageSquareText className="size-4" />
                        Administrator
                    </span>

                    <h1 className="mt-5 max-w-4xl text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                        Kelola masukan pengguna.
                    </h1>

                    <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold sm:text-base">
                        Prioritaskan laporan yang belum ditinjau, berikan
                        tanggapan yang jelas, dan tandai sebagai selesai hanya
                        setelah masalah benar-benar ditangani.
                    </p>
                </section>

                <section className="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div className="neo-card-flat p-5">
                        <p className="text-xs font-black tracking-wide uppercase">
                            Semua
                        </p>

                        <p className="mt-3 font-mono text-3xl font-black">
                            {feedbacks.length}
                        </p>
                    </div>

                    <div className="neo-card-flat bg-[var(--neo-yellow)] p-5 text-[#171717]">
                        <div className="flex items-center justify-between gap-3">
                            <p className="text-xs font-black tracking-wide uppercase">
                                Menunggu
                            </p>

                            <Clock3 className="size-5" />
                        </div>

                        <p className="mt-3 font-mono text-3xl font-black">
                            {pendingCount}
                        </p>
                    </div>

                    <div className="neo-card-flat bg-[var(--neo-blue)] p-5 text-[#171717]">
                        <div className="flex items-center justify-between gap-3">
                            <p className="text-xs font-black tracking-wide uppercase">
                                Ditinjau
                            </p>

                            <Search className="size-5" />
                        </div>

                        <p className="mt-3 font-mono text-3xl font-black">
                            {reviewingCount}
                        </p>
                    </div>

                    <div className="neo-card-flat bg-[var(--neo-lime)] p-5 text-[#171717]">
                        <div className="flex items-center justify-between gap-3">
                            <p className="text-xs font-black tracking-wide uppercase">
                                Selesai
                            </p>

                            <CheckCircle2 className="size-5" />
                        </div>

                        <p className="mt-3 font-mono text-3xl font-black">
                            {resolvedCount}
                        </p>
                    </div>
                </section>

                <section className="neo-card mt-6 p-5">
                    <div className="flex items-center gap-2">
                        <Filter className="size-5" />

                        <h2 className="font-black">Filter masukan</h2>
                    </div>

                    <div className="mt-4 grid gap-4 sm:grid-cols-2">
                        <label>
                            <span className="mb-2 block text-xs font-black uppercase">
                                Status
                            </span>

                            <select
                                value={statusFilter}
                                onChange={(event) =>
                                    setStatusFilter(event.target.value)
                                }
                                className="h-11 w-full rounded-[9px] border-2 border-foreground bg-background px-3 text-sm font-bold"
                            >
                                <option value="all">Semua status</option>
                                <option value="pending">Menunggu</option>
                                <option value="reviewing">
                                    Sedang ditinjau
                                </option>
                                <option value="resolved">Selesai</option>
                            </select>
                        </label>

                        <label>
                            <span className="mb-2 block text-xs font-black uppercase">
                                Kategori
                            </span>

                            <select
                                value={categoryFilter}
                                onChange={(event) =>
                                    setCategoryFilter(event.target.value)
                                }
                                className="h-11 w-full rounded-[9px] border-2 border-foreground bg-background px-3 text-sm font-bold"
                            >
                                <option value="all">Semua kategori</option>
                                <option value="general">Umum</option>
                                <option value="content">Materi</option>
                                <option value="recommendation">
                                    Rekomendasi
                                </option>
                                <option value="usability">UI/UX</option>
                                <option value="bug">Bug</option>
                            </select>
                        </label>
                    </div>
                </section>

                <div className="mt-6 grid gap-5">
                    {filteredFeedbacks.length === 0 && (
                        <div className="neo-card p-8 text-center">
                            <MessageSquareText className="mx-auto size-8 text-muted-foreground" />

                            <p className="mt-3 font-black">Tidak ada masukan</p>

                            <p className="mt-1 text-sm font-medium text-muted-foreground">
                                Tidak ada data yang cocok dengan filter saat
                                ini.
                            </p>
                        </div>
                    )}

                    {filteredFeedbacks.map((feedback) => (
                        <FeedbackReviewCard
                            key={feedback.id}
                            feedback={feedback}
                        />
                    ))}
                </div>
            </div>
        </>
    );
}

AdminFeedbackPage.layout = {
    breadcrumbs: [
        {
            title: 'Masukan Pengguna',
            href: '/admin/feedback',
        },
    ],
};
