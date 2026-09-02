import { Head, useForm } from '@inertiajs/react';
import {
    CheckCircle2,
    Clock3,
    MessageSquareText,
    Send,
    Star,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

type Feedback = {
    id: number;
    category: string;
    subject: string;
    message: string;
    rating?: number | null;
    status: 'pending' | 'reviewing' | 'resolved';
    admin_response?: string | null;
    created_at: string;
};

const categoryLabels: Record<string, string> = {
    general: 'Umum',
    content: 'Materi',
    recommendation: 'Rekomendasi',
    usability: 'UI/UX',
    bug: 'Bug',
};

const statusLabels: Record<string, string> = {
    pending: 'Menunggu ditinjau',
    reviewing: 'Sedang ditinjau',
    resolved: 'Selesai',
};

const statusClasses: Record<string, string> = {
    pending: 'bg-[var(--neo-yellow)]',
    reviewing: 'bg-[var(--neo-blue)]',
    resolved: 'bg-[var(--neo-lime)]',
};

const formatDate = (value: string): string => {
    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date);
};

export default function FeedbackPage({ feedbacks }: { feedbacks: Feedback[] }) {
    const form = useForm({
        category: 'general',
        subject: '',
        message: '',
        rating: '',
    });

    const pendingCount = feedbacks.filter(
        (feedback) => feedback.status === 'pending',
    ).length;

    const reviewingCount = feedbacks.filter(
        (feedback) => feedback.status === 'reviewing',
    ).length;

    const resolvedCount = feedbacks.filter(
        (feedback) => feedback.status === 'resolved',
    ).length;

    const submit = (event: React.FormEvent) => {
        event.preventDefault();

        form.post('/feedback', {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('subject', 'message', 'rating');
            },
        });
    };

    return (
        <>
            <Head title="Masukan Pengguna" />

            <div className="neo-page py-8 md:py-10">
                <section className="neo-hero neo-accent-blue border-[#171717]">
                    <span className="neo-label bg-[#fffdf7]">
                        <MessageSquareText className="size-4" />
                        Masukan pengguna
                    </span>

                    <h1 className="mt-5 max-w-4xl text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                        Bantu SkillPath AI menjadi lebih baik.
                    </h1>

                    <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold sm:text-base">
                        Laporkan bug, masalah materi, rekomendasi yang kurang
                        tepat, atau bagian antarmuka yang membingungkan.
                        Administrator dapat meninjau laporan dan memberikan
                        tanggapan langsung dari halaman ini.
                    </p>
                </section>

                <section className="mt-6 grid gap-4 sm:grid-cols-3">
                    <div className="neo-card-flat p-5">
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

                    <div className="neo-card-flat p-5">
                        <div className="flex items-center justify-between gap-3">
                            <p className="text-xs font-black tracking-wide uppercase">
                                Ditinjau
                            </p>
                            <MessageSquareText className="size-5" />
                        </div>

                        <p className="mt-3 font-mono text-3xl font-black">
                            {reviewingCount}
                        </p>
                    </div>

                    <div className="neo-card-flat p-5">
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

                <div className="mt-7 grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
                    <form
                        onSubmit={submit}
                        className="neo-card h-fit p-6 lg:sticky lg:top-24"
                    >
                        <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                            Formulir masukan
                        </p>

                        <h2 className="mt-1 text-2xl font-black">
                            Kirim masukan
                        </h2>

                        <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                            Jelaskan masalah secara spesifik agar administrator
                            lebih mudah memahami dan menindaklanjutinya.
                        </p>

                        <div className="mt-6 grid gap-5">
                            <label>
                                <span className="mb-2 block text-sm font-black">
                                    Kategori
                                </span>

                                <select
                                    value={form.data.category}
                                    onChange={(event) =>
                                        form.setData(
                                            'category',
                                            event.target.value,
                                        )
                                    }
                                    className="h-11 w-full rounded-[9px] border-2 border-foreground bg-background px-3 text-sm font-bold"
                                >
                                    <option value="general">Umum</option>
                                    <option value="content">Materi</option>
                                    <option value="recommendation">
                                        Rekomendasi
                                    </option>
                                    <option value="usability">UI/UX</option>
                                    <option value="bug">Bug</option>
                                </select>

                                {form.errors.category && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {form.errors.category}
                                    </p>
                                )}
                            </label>

                            <label>
                                <span className="mb-2 block text-sm font-black">
                                    Judul
                                </span>

                                <Input
                                    value={form.data.subject}
                                    onChange={(event) =>
                                        form.setData(
                                            'subject',
                                            event.target.value,
                                        )
                                    }
                                    maxLength={180}
                                    placeholder="Contoh: Tombol evaluasi tidak dapat diklik"
                                    required
                                />

                                <div className="mt-2 flex justify-between gap-3 text-xs font-semibold text-muted-foreground">
                                    <span>
                                        Ringkas masalah dalam satu kalimat.
                                    </span>

                                    <span className="font-mono font-black">
                                        {form.data.subject.length}/180
                                    </span>
                                </div>

                                {form.errors.subject && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {form.errors.subject}
                                    </p>
                                )}
                            </label>

                            <label>
                                <span className="mb-2 block text-sm font-black">
                                    Detail masukan
                                </span>

                                <Textarea
                                    value={form.data.message}
                                    onChange={(event) =>
                                        form.setData(
                                            'message',
                                            event.target.value,
                                        )
                                    }
                                    rows={7}
                                    minLength={10}
                                    maxLength={5000}
                                    placeholder="Jelaskan apa yang terjadi, halaman yang digunakan, dan hasil yang Anda harapkan."
                                    required
                                />

                                <div className="mt-2 flex justify-between gap-3 text-xs font-semibold text-muted-foreground">
                                    <span>Minimal 10 karakter.</span>

                                    <span className="font-mono font-black">
                                        {form.data.message.length}/5000
                                    </span>
                                </div>

                                {form.errors.message && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {form.errors.message}
                                    </p>
                                )}
                            </label>

                            <label>
                                <span className="mb-2 flex items-center gap-2 text-sm font-black">
                                    <Star className="size-4" />
                                    Penilaian pengalaman
                                </span>

                                <select
                                    value={form.data.rating}
                                    onChange={(event) =>
                                        form.setData(
                                            'rating',
                                            event.target.value,
                                        )
                                    }
                                    className="h-11 w-full rounded-[9px] border-2 border-foreground bg-background px-3 text-sm font-bold"
                                >
                                    <option value="">Tanpa penilaian</option>
                                    <option value="1">1 / 5 — Buruk</option>
                                    <option value="2">
                                        2 / 5 — Kurang baik
                                    </option>
                                    <option value="3">3 / 5 — Cukup</option>
                                    <option value="4">4 / 5 — Baik</option>
                                    <option value="5">
                                        5 / 5 — Sangat baik
                                    </option>
                                </select>

                                <p className="mt-2 text-xs font-semibold text-muted-foreground">
                                    Opsional. Gunakan untuk menilai pengalaman
                                    Anda secara keseluruhan.
                                </p>

                                {form.errors.rating && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {form.errors.rating}
                                    </p>
                                )}
                            </label>
                        </div>

                        <Button
                            className="mt-6 w-full"
                            disabled={form.processing}
                        >
                            <Send />

                            {form.processing ? 'Mengirim...' : 'Kirim masukan'}
                        </Button>
                    </form>

                    <section className="neo-card p-6">
                        <div className="flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Riwayat
                                </p>

                                <h2 className="mt-1 text-2xl font-black">
                                    Masukan Anda
                                </h2>
                            </div>

                            <span className="neo-label bg-muted">
                                {feedbacks.length} total
                            </span>
                        </div>

                        <div className="mt-6 space-y-4">
                            {feedbacks.length === 0 && (
                                <div className="rounded-[12px] border-2 border-dashed border-foreground/40 p-8 text-center">
                                    <MessageSquareText className="mx-auto size-8 text-muted-foreground" />

                                    <p className="mt-3 font-black">
                                        Belum ada masukan
                                    </p>

                                    <p className="mt-1 text-sm font-medium text-muted-foreground">
                                        Masukan yang Anda kirim akan muncul di
                                        sini beserta status peninjauannya.
                                    </p>
                                </div>
                            )}

                            {feedbacks.map((feedback) => (
                                <article
                                    key={feedback.id}
                                    className="neo-card-flat p-5"
                                >
                                    <div className="flex flex-wrap items-start justify-between gap-4">
                                        <div>
                                            <div className="flex flex-wrap gap-2">
                                                <span className="neo-label bg-muted">
                                                    {categoryLabels[
                                                        feedback.category
                                                    ] ?? feedback.category}
                                                </span>

                                                <span
                                                    className={`neo-label ${
                                                        statusClasses[
                                                            feedback.status
                                                        ] ?? 'bg-muted'
                                                    }`}
                                                >
                                                    {statusLabels[
                                                        feedback.status
                                                    ] ?? feedback.status}
                                                </span>
                                            </div>

                                            <h3 className="mt-4 text-xl font-black">
                                                {feedback.subject}
                                            </h3>

                                            <p className="mt-1 text-xs font-bold text-muted-foreground">
                                                {formatDate(
                                                    feedback.created_at,
                                                )}
                                            </p>
                                        </div>

                                        {feedback.rating && (
                                            <span className="flex items-center gap-1 font-mono text-xl font-black">
                                                <Star className="size-4 fill-current" />
                                                {feedback.rating}/5
                                            </span>
                                        )}
                                    </div>

                                    <p className="mt-4 text-sm leading-7 font-medium whitespace-pre-wrap">
                                        {feedback.message}
                                    </p>

                                    {feedback.admin_response && (
                                        <div className="mt-5 rounded-[12px] border-2 border-[#171717] bg-secondary p-4 text-[#171717]">
                                            <p className="text-xs font-black tracking-wide uppercase">
                                                Tanggapan administrator
                                            </p>

                                            <p className="mt-2 text-sm leading-6 font-semibold whitespace-pre-wrap">
                                                {feedback.admin_response}
                                            </p>
                                        </div>
                                    )}
                                </article>
                            ))}
                        </div>
                    </section>
                </div>
            </div>
        </>
    );
}

FeedbackPage.layout = {
    breadcrumbs: [
        {
            title: 'Masukan Pengguna',
            href: '/feedback',
        },
    ],
};
