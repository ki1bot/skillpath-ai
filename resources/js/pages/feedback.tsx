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

            <div className="neo-page py-7 md:py-9">
                <header className="border-b-2 border-foreground pb-6">
                    <div className="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div className="max-w-3xl">
                            <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                Masukan pengguna
                            </p>

                            <h1 className="mt-2 text-3xl font-black tracking-tight sm:text-4xl">
                                Laporkan masalah atau kirim saran
                            </h1>

                            <p className="mt-3 max-w-2xl text-sm leading-7 font-medium text-muted-foreground">
                                Tulis apa yang terjadi, di halaman mana, dan apa
                                yang kamu harapkan. Riwayat penanganannya bisa
                                dipantau dari halaman ini.
                            </p>
                        </div>

                        <div className="flex flex-wrap gap-2 text-xs font-black">
                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-2">
                                {pendingCount} menunggu
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-2">
                                {reviewingCount} ditinjau
                            </span>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-2">
                                {resolvedCount} selesai
                            </span>
                        </div>
                    </div>
                </header>

                <div className="mt-6 grid gap-6 xl:grid-cols-[380px_minmax(0,1fr)]">
                    <form
                        onSubmit={submit}
                        className="neo-card h-fit p-5 sm:p-6 xl:sticky xl:top-6"
                    >
                        <div className="flex items-start gap-3">
                            <span className="flex size-9 shrink-0 items-center justify-center rounded-[9px] border-2 border-foreground bg-[var(--neo-blue)] text-[#171717]">
                                <MessageSquareText className="size-5" />
                            </span>

                            <div>
                                <h2 className="text-xl font-black">
                                    Tulis masukan baru
                                </h2>

                                <p className="mt-1 text-sm leading-6 font-medium text-muted-foreground">
                                    Semakin spesifik laporannya, semakin mudah
                                    ditindaklanjuti.
                                </p>
                            </div>
                        </div>

                        <div className="mt-6 grid gap-5">
                            <label>
                                <span className="mb-2 block text-sm font-black">
                                    Jenis masukan
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
                                    Judul singkat
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

                                <div className="mt-2 flex items-center justify-between gap-3 text-xs font-medium text-muted-foreground">
                                    <span>
                                        Ringkas masalah dalam satu kalimat.
                                    </span>

                                    <span className="font-mono font-black">
                                        {form.data.subject.length}
                                        /180
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
                                    Apa yang terjadi?
                                </span>

                                <Textarea
                                    value={form.data.message}
                                    onChange={(event) =>
                                        form.setData(
                                            'message',
                                            event.target.value,
                                        )
                                    }
                                    rows={8}
                                    minLength={10}
                                    maxLength={5000}
                                    placeholder="Sebutkan halaman, langkah yang dilakukan, masalah yang muncul, dan hasil yang kamu harapkan."
                                    required
                                />

                                <div className="mt-2 flex items-center justify-between gap-3 text-xs font-medium text-muted-foreground">
                                    <span>Minimal 10 karakter.</span>

                                    <span className="font-mono font-black">
                                        {form.data.message.length}
                                        /5000
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
                                    <option value="">
                                        Tidak perlu memberi nilai
                                    </option>

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

                                <p className="mt-2 text-xs font-medium text-muted-foreground">
                                    Opsional dan tidak memengaruhi prioritas
                                    penanganan laporan.
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

                    <section className="neo-card p-5 sm:p-6">
                        <div className="flex flex-wrap items-end justify-between gap-3 border-b-2 border-foreground/15 pb-5">
                            <div>
                                <p className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                    Riwayat laporan
                                </p>

                                <h2 className="mt-1 text-2xl font-black">
                                    Masukan yang sudah dikirim
                                </h2>
                            </div>

                            <span className="rounded-full border-2 border-foreground bg-card px-3 py-1 text-xs font-black">
                                {feedbacks.length} total
                            </span>
                        </div>

                        <div className="mt-5 space-y-4">
                            {feedbacks.length === 0 && (
                                <div className="rounded-[12px] border-2 border-dashed border-foreground/30 p-8 text-center">
                                    <MessageSquareText className="mx-auto size-8 text-muted-foreground" />

                                    <p className="mt-3 font-black">
                                        Belum ada masukan
                                    </p>

                                    <p className="mt-1 text-sm leading-6 font-medium text-muted-foreground">
                                        Setelah mengirim laporan, statusnya akan
                                        muncul di sini.
                                    </p>
                                </div>
                            )}

                            {feedbacks.map((feedback) => (
                                <article
                                    key={feedback.id}
                                    className="rounded-[12px] border-2 border-foreground bg-card p-5"
                                >
                                    <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div className="min-w-0">
                                            <div className="flex flex-wrap gap-2">
                                                <span className="rounded-full border-2 border-foreground/20 bg-muted/30 px-2.5 py-1 text-[10px] font-black">
                                                    {categoryLabels[
                                                        feedback.category
                                                    ] ?? feedback.category}
                                                </span>

                                                <span
                                                    className={`rounded-full border-2 border-[#171717] px-2.5 py-1 text-[10px] font-black text-[#171717] ${
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

                                            <h3 className="mt-3 text-lg font-black break-words">
                                                {feedback.subject}
                                            </h3>

                                            <p className="mt-1 flex items-center gap-1.5 text-xs font-medium text-muted-foreground">
                                                <Clock3 className="size-3.5" />
                                                {formatDate(
                                                    feedback.created_at,
                                                )}
                                            </p>
                                        </div>

                                        {feedback.rating && (
                                            <span className="flex shrink-0 items-center gap-1 rounded-full border-2 border-foreground px-2.5 py-1 text-sm font-black">
                                                <Star className="size-3.5 fill-current" />
                                                {feedback.rating}
                                                /5
                                            </span>
                                        )}
                                    </div>

                                    <p className="mt-4 text-sm leading-7 font-medium whitespace-pre-wrap">
                                        {feedback.message}
                                    </p>

                                    {feedback.admin_response ? (
                                        <div className="mt-5 rounded-[10px] border-2 border-[#171717] bg-secondary p-4 text-[#171717]">
                                            <div className="flex items-center gap-2">
                                                <CheckCircle2 className="size-4" />

                                                <p className="text-xs font-black tracking-wide uppercase">
                                                    Tanggapan administrator
                                                </p>
                                            </div>

                                            <p className="mt-2 text-sm leading-6 font-semibold whitespace-pre-wrap">
                                                {feedback.admin_response}
                                            </p>
                                        </div>
                                    ) : (
                                        <p className="mt-4 text-xs font-medium text-muted-foreground">
                                            Belum ada tanggapan dari
                                            administrator.
                                        </p>
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
