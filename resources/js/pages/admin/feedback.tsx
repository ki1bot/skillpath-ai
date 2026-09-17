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
import { Input } from '@/components/ui/input';
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
            <div className="grid gap-5 border-b-2 border-foreground p-5 sm:p-6 lg:grid-cols-[minmax(0,1fr)_220px]">
                <div className="min-w-0">
                    <div className="flex flex-wrap gap-2">
                        <span className="rounded-full border-2 border-foreground/20 bg-muted/30 px-2.5 py-1 text-[10px] font-black">
                            {categoryLabels[feedback.category] ??
                                feedback.category}
                        </span>

                        <span
                            className={`rounded-full border-2 border-[#171717] px-2.5 py-1 text-[10px] font-black text-[#171717] ${statusClasses[feedback.status]}`}
                        >
                            {statusLabels[feedback.status]}
                        </span>
                    </div>

                    <h2 className="mt-3 text-xl font-black break-words sm:text-2xl">
                        {feedback.subject}
                    </h2>

                    <div className="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-xs font-medium text-muted-foreground">
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

                <div className="flex items-start justify-between gap-3 lg:justify-end">
                    {feedback.rating ? (
                        <div className="flex items-center gap-2 rounded-[10px] border-2 border-foreground bg-[var(--neo-yellow)] px-3 py-2 text-[#171717]">
                            <Star className="size-4 fill-current" />

                            <span className="font-mono text-lg font-black">
                                {feedback.rating}/5
                            </span>
                        </div>
                    ) : (
                        <span className="text-xs font-medium text-muted-foreground">
                            Tanpa penilaian
                        </span>
                    )}
                </div>
            </div>

            <div className="grid gap-5 p-5 sm:p-6 lg:grid-cols-[minmax(0,1fr)_minmax(320px,.8fr)]">
                <section>
                    <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                        Pesan pengguna
                    </p>

                    <div className="mt-3 rounded-[10px] border-2 border-foreground/15 bg-muted/20 p-4">
                        <p className="text-sm leading-7 font-medium whitespace-pre-wrap">
                            {feedback.message}
                        </p>
                    </div>

                    {feedback.reviewer && (
                        <p className="mt-4 text-xs leading-5 font-medium text-muted-foreground">
                            Terakhir ditinjau oleh{' '}
                            <strong className="text-foreground">
                                {feedback.reviewer.name}
                            </strong>{' '}
                            pada {formatDate(feedback.reviewed_at)}.
                        </p>
                    )}
                </section>

                <Form
                    action={`/admin/feedback/${feedback.id}`}
                    method="patch"
                    className="grid content-start gap-4 rounded-[12px] border-2 border-foreground bg-card p-4"
                >
                    {({ processing, errors }) => (
                        <>
                            <div>
                                <p className="text-sm font-black">
                                    Penanganan laporan
                                </p>

                                <p className="mt-1 text-xs leading-5 font-medium text-muted-foreground">
                                    Perbarui status dan tulis tanggapan yang
                                    bisa dipahami pengguna.
                                </p>
                            </div>

                            <label>
                                <span className="mb-2 block text-sm font-black">
                                    Status
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

                                {errors.status && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {errors.status}
                                    </p>
                                )}
                            </label>

                            <label>
                                <span className="mb-2 block text-sm font-black">
                                    Tanggapan
                                </span>

                                <Textarea
                                    name="admin_response"
                                    defaultValue={feedback.admin_response ?? ''}
                                    rows={6}
                                    maxLength={3000}
                                    minLength={
                                        status === 'resolved' ? 10 : undefined
                                    }
                                    required={status === 'resolved'}
                                    placeholder="Jelaskan hasil pemeriksaan atau tindakan yang dilakukan."
                                />

                                <p className="mt-2 text-xs leading-5 font-medium text-muted-foreground">
                                    {status === 'resolved'
                                        ? 'Untuk status Selesai, tanggapan wajib diisi minimal 10 karakter.'
                                        : 'Tanggapan boleh dikosongkan selama laporan belum selesai.'}
                                </p>

                                {errors.admin_response && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {errors.admin_response}
                                    </p>
                                )}
                            </label>

                            <Button className="w-fit" disabled={processing}>
                                <Save />

                                {processing
                                    ? 'Menyimpan...'
                                    : 'Simpan perubahan'}
                            </Button>
                        </>
                    )}
                </Form>
            </div>
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

    const [searchQuery, setSearchQuery] = useState('');

    const pendingCount = feedbacks.filter(
        (feedback) => feedback.status === 'pending',
    ).length;

    const reviewingCount = feedbacks.filter(
        (feedback) => feedback.status === 'reviewing',
    ).length;

    const resolvedCount = feedbacks.filter(
        (feedback) => feedback.status === 'resolved',
    ).length;

    const filteredFeedbacks = useMemo(() => {
        const normalizedSearch = searchQuery.trim().toLowerCase();

        return feedbacks.filter((feedback) => {
            const statusMatches =
                statusFilter === 'all' || feedback.status === statusFilter;

            const categoryMatches =
                categoryFilter === 'all' ||
                feedback.category === categoryFilter;

            const searchMatches =
                normalizedSearch.length === 0 ||
                feedback.subject.toLowerCase().includes(normalizedSearch) ||
                feedback.message.toLowerCase().includes(normalizedSearch) ||
                feedback.user.name.toLowerCase().includes(normalizedSearch) ||
                feedback.user.email.toLowerCase().includes(normalizedSearch);

            return statusMatches && categoryMatches && searchMatches;
        });
    }, [categoryFilter, feedbacks, searchQuery, statusFilter]);

    return (
        <>
            <Head title="Masukan Pengguna" />

            <div className="neo-page py-7 md:py-9">
                <header className="border-b-2 border-foreground pb-6">
                    <div className="max-w-3xl">
                        <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                            Administrasi
                        </p>

                        <h1 className="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                            Masukan pengguna
                        </h1>

                        <p className="mt-3 max-w-2xl text-sm leading-7 font-medium text-muted-foreground">
                            Tinjau laporan yang masuk, beri tanggapan yang
                            jelas, lalu tandai selesai setelah masalah
                            benar-benar ditangani.
                        </p>
                    </div>
                </header>

                <section className="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                    <div className="rounded-[10px] border-2 border-foreground bg-card p-4">
                        <p className="text-xs font-black text-muted-foreground uppercase">
                            Semua
                        </p>

                        <p className="mt-2 text-2xl font-black">
                            {feedbacks.length}
                        </p>
                    </div>

                    <div className="rounded-[10px] border-2 border-[#171717] bg-[var(--neo-yellow)] p-4 text-[#171717]">
                        <div className="flex items-center justify-between gap-2">
                            <p className="text-xs font-black uppercase">
                                Menunggu
                            </p>

                            <Clock3 className="size-4" />
                        </div>

                        <p className="mt-2 text-2xl font-black">
                            {pendingCount}
                        </p>
                    </div>

                    <div className="rounded-[10px] border-2 border-[#171717] bg-[var(--neo-blue)] p-4 text-[#171717]">
                        <div className="flex items-center justify-between gap-2">
                            <p className="text-xs font-black uppercase">
                                Ditinjau
                            </p>

                            <Search className="size-4" />
                        </div>

                        <p className="mt-2 text-2xl font-black">
                            {reviewingCount}
                        </p>
                    </div>

                    <div className="rounded-[10px] border-2 border-[#171717] bg-[var(--neo-lime)] p-4 text-[#171717]">
                        <div className="flex items-center justify-between gap-2">
                            <p className="text-xs font-black uppercase">
                                Selesai
                            </p>

                            <CheckCircle2 className="size-4" />
                        </div>

                        <p className="mt-2 text-2xl font-black">
                            {resolvedCount}
                        </p>
                    </div>
                </section>

                <section className="neo-card mt-6 p-5">
                    <div className="flex items-center gap-2">
                        <Filter className="size-5" />
                        <h2 className="font-black">Cari dan filter</h2>
                    </div>

                    <div className="mt-4 grid gap-4 lg:grid-cols-[minmax(0,1fr)_220px_220px]">
                        <label>
                            <span className="mb-2 block text-xs font-black uppercase">
                                Cari laporan
                            </span>

                            <div className="relative">
                                <Input
                                    value={searchQuery}
                                    onChange={(event) =>
                                        setSearchQuery(event.target.value)
                                    }
                                    placeholder="Judul, pesan, nama, atau email..."
                                />

                                <Search className="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-muted-foreground" />
                            </div>
                        </label>

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

                    <p className="mt-4 text-xs font-medium text-muted-foreground">
                        Menampilkan {filteredFeedbacks.length} dari{' '}
                        {feedbacks.length} masukan.
                    </p>
                </section>

                <div className="mt-6 grid gap-5">
                    {filteredFeedbacks.length === 0 && (
                        <div className="neo-card p-8 text-center">
                            <MessageSquareText className="mx-auto size-8 text-muted-foreground" />

                            <p className="mt-3 font-black">
                                Tidak ada masukan yang cocok
                            </p>

                            <p className="mt-1 text-sm font-medium text-muted-foreground">
                                Ubah kata pencarian atau filter yang digunakan.
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
