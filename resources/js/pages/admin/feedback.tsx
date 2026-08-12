import { Form, Head } from '@inertiajs/react';
import { MessageSquareText, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
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

export default function AdminFeedbackPage({
    feedbacks,
}: {
    feedbacks: Feedback[];
}) {
    return (
        <>
            <Head title="Masukan Pengguna" />

            <div className="neo-page py-8 md:py-10">
                <section className="neo-hero neo-accent-orange border-[#171717]">
                    <span className="neo-label bg-[#fffdf7]">
                        <MessageSquareText className="size-4" />
                        Administrator
                    </span>

                    <h1 className="mt-5 text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                        Tinjau masukan pengguna.
                    </h1>

                    <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold sm:text-base">
                        Gunakan masukan untuk menemukan masalah materi,
                        rekomendasi, antarmuka, dan bug yang perlu diperbaiki.
                    </p>
                </section>

                <div className="mt-7 grid gap-5">
                    {feedbacks.length === 0 && (
                        <div className="neo-card p-6 text-sm font-bold">
                            Belum ada masukan pengguna.
                        </div>
                    )}

                    {feedbacks.map((feedback) => (
                        <article key={feedback.id} className="neo-card p-6">
                            <div className="flex flex-col justify-between gap-4 md:flex-row">
                                <div>
                                    <div className="flex flex-wrap gap-2">
                                        <span className="neo-label bg-muted">
                                            {categoryLabels[
                                                feedback.category
                                            ] ?? feedback.category}
                                        </span>

                                        <span className="neo-label bg-[var(--neo-yellow)]">
                                            {feedback.status}
                                        </span>
                                    </div>

                                    <h2 className="mt-4 text-2xl font-black">
                                        {feedback.subject}
                                    </h2>

                                    <p className="mt-2 text-sm font-bold text-muted-foreground">
                                        {feedback.user.name} ·{' '}
                                        {feedback.user.email}
                                    </p>
                                </div>

                                {feedback.rating && (
                                    <span className="font-mono text-2xl font-black">
                                        {feedback.rating}/5
                                    </span>
                                )}
                            </div>

                            <p className="mt-5 text-sm leading-relaxed font-medium whitespace-pre-wrap">
                                {feedback.message}
                            </p>

                            <Form
                                action={`/admin/feedback/${feedback.id}`}
                                method="patch"
                                className="mt-6 grid gap-4 border-t-2 border-foreground/15 pt-5"
                            >
                                {({ processing }) => (
                                    <>
                                        <label>
                                            <span className="mb-2 block text-sm font-black">
                                                Status
                                            </span>

                                            <select
                                                name="status"
                                                defaultValue={feedback.status}
                                                className="h-10 w-full rounded-[9px] border-2 border-foreground bg-background px-3 text-sm font-bold md:max-w-xs"
                                            >
                                                <option value="pending">
                                                    Menunggu
                                                </option>
                                                <option value="reviewing">
                                                    Sedang ditinjau
                                                </option>
                                                <option value="resolved">
                                                    Selesai
                                                </option>
                                            </select>
                                        </label>

                                        <label>
                                            <span className="mb-2 block text-sm font-black">
                                                Tanggapan administrator
                                            </span>

                                            <Textarea
                                                name="admin_response"
                                                defaultValue={
                                                    feedback.admin_response ??
                                                    ''
                                                }
                                                rows={4}
                                            />
                                        </label>

                                        <Button
                                            className="w-fit"
                                            disabled={processing}
                                        >
                                            <Save />
                                            Simpan tanggapan
                                        </Button>
                                    </>
                                )}
                            </Form>
                        </article>
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
