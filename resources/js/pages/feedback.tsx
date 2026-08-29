import { Head, useForm } from '@inertiajs/react';
import { MessageSquareText, Send } from 'lucide-react';
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
    pending: 'Menunggu',
    reviewing: 'Ditinjau',
    resolved: 'Selesai',
};

export default function FeedbackPage({ feedbacks }: { feedbacks: Feedback[] }) {
    const form = useForm({
        category: 'general',
        subject: '',
        message: '',
        rating: '',
    });

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
            <Head title="Masukan" />

            <div className="neo-page py-8 md:py-10">
                <section className="neo-hero neo-accent-blue border-[#171717]">
                    <span className="neo-label bg-[#fffdf7]">
                        <MessageSquareText className="size-4" />
                        Masukkan pengguna
                    </span>

                    <h1 className="mt-5 text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                        Beri tahu bagian sistem yang perlu diperbaiki.
                    </h1>

                    <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold sm:text-base">
                        Masukan digunakan administrator untuk memperbaiki
                        materi, rekomendasi, pengalaman penggunaan, dan masalah
                        teknis SkillPath AI.
                    </p>
                </section>

                <div className="mt-7 grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
                    <form onSubmit={submit} className="neo-card p-6">
                        <h2 className="text-2xl font-black">Kirim masukan</h2>

                        <div className="mt-5 grid gap-4">
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
                                    className="h-10 w-full rounded-[9px] border-2 border-foreground bg-background px-3 text-sm font-bold"
                                >
                                    <option value="general">Umum</option>
                                    <option value="content">Materi</option>
                                    <option value="recommendation">
                                        Rekomendasi
                                    </option>
                                    <option value="usability">UI/UX</option>
                                    <option value="bug">Bug</option>
                                </select>
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
                                    required
                                />
                            </label>

                            <label>
                                <span className="mb-2 block text-sm font-black">
                                    Pesan
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
                                    required
                                />
                            </label>

                            <label>
                                <span className="mb-2 block text-sm font-black">
                                    Assesment opsional
                                </span>

                                <select
                                    value={form.data.rating}
                                    onChange={(event) =>
                                        form.setData(
                                            'rating',
                                            event.target.value,
                                        )
                                    }
                                    className="h-10 w-full rounded-[9px] border-2 border-foreground bg-background px-3 text-sm font-bold"
                                >
                                    <option value="">Tanpa assesment</option>
                                    <option value="1">1 / 5</option>
                                    <option value="2">2 / 5</option>
                                    <option value="3">3 / 5</option>
                                    <option value="4">4 / 5</option>
                                    <option value="5">5 / 5</option>
                                </select>
                            </label>
                        </div>

                        <Button className="mt-5" disabled={form.processing}>
                            <Send />
                            Kirim masukan
                        </Button>
                    </form>

                    <section className="neo-card p-6">
                        <h2 className="text-2xl font-black">Riwayat masukan</h2>

                        <div className="mt-5 space-y-4">
                            {feedbacks.length === 0 && (
                                <p className="text-sm font-bold text-muted-foreground">
                                    Belum ada masukan yang dikirim.
                                </p>
                            )}

                            {feedbacks.map((feedback) => (
                                <article
                                    key={feedback.id}
                                    className="neo-card-flat p-5"
                                >
                                    <div className="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <span className="text-xs font-black tracking-wide text-muted-foreground uppercase">
                                                {categoryLabels[
                                                    feedback.category
                                                ] ?? feedback.category}
                                            </span>

                                            <h3 className="mt-1 text-lg font-black">
                                                {feedback.subject}
                                            </h3>
                                        </div>

                                        <span className="neo-label bg-muted">
                                            {statusLabels[feedback.status] ??
                                                feedback.status}
                                        </span>
                                    </div>

                                    <p className="mt-3 text-sm leading-relaxed font-medium">
                                        {feedback.message}
                                    </p>

                                    {feedback.rating && (
                                        <p className="mt-3 text-xs font-black">
                                            Assesment: {feedback.rating}/5
                                        </p>
                                    )}

                                    {feedback.admin_response && (
                                        <div className="mt-4 rounded-[12px] border-2 border-foreground bg-secondary p-4 text-sm font-semibold text-[#171717]">
                                            <p className="font-black">
                                                Tanggapan administrator
                                            </p>

                                            <p className="mt-1">
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
            title: 'Masukan',
            href: '/feedback',
        },
    ],
};
