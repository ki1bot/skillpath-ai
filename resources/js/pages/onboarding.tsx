import { Head, useForm } from '@inertiajs/react';
import { ArrowRight, Clock3, GraduationCap, Target } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

type Career = {
    id: number;
    name: string;
    slug: string;
    tagline: string;
    difficulty: string;
    accent: string;
};

type Profile = {
    study_program?: string | null;
    semester?: number | null;
    interest_area?: string | null;
    experience?: string | null;
    weekly_study_hours?: number | null;
    target_career_id?: number | null;
    onboarding_completed_at?: string | null;
};

export default function Onboarding({
    careers,
    profile,
}: {
    careers: Career[];
    profile: Profile;
}) {
    const editing = Boolean(profile.onboarding_completed_at);

    const form = useForm({
        study_program: profile.study_program ?? '',
        semester: profile.semester ?? 1,
        interest_area: profile.interest_area ?? '',
        experience: profile.experience ?? '',
        weekly_study_hours: profile.weekly_study_hours ?? 6,
        target_career_id: profile.target_career_id ?? careers[0]?.id ?? 0,
    });

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        form.put('/onboarding');
    };

    return (
        <>
            <Head title="Onboarding" />

            <div className="mx-auto w-full max-w-6xl px-4 py-8 md:px-6 md:py-10">
                <div className="max-w-3xl">
                    <span className="neo-label">
                        {editing ? 'Learning profile' : 'Setup 01'}
                    </span>

                    <h1 className="neo-heading mt-5 text-4xl sm:text-5xl">
                        {editing
                            ? 'Perbarui konteks belajarmu.'
                            : 'Kasih sistem konteks yang benar.'}
                    </h1>

                    <p className="mt-4 text-base leading-relaxed font-medium text-muted-foreground">
                        Waktu belajar memengaruhi estimasi roadmap. Target
                        karier menentukan standar skill yang dibandingkan. Isi
                        sesuai kondisi sekarang, bukan versi idealmu.
                    </p>
                </div>

                <form
                    onSubmit={submit}
                    className="mt-9 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]"
                >
                    <section className="neo-card p-6">
                        <div className="flex items-center gap-3">
                            <GraduationCap className="size-6" />
                            <h2 className="text-xl font-black">
                                Profil belajar
                            </h2>
                        </div>

                        <div className="mt-6 space-y-5">
                            <label className="block">
                                <span className="mb-2 block text-sm font-black">
                                    Program studi
                                </span>
                                <Input
                                    value={form.data.study_program}
                                    onChange={(event) =>
                                        form.setData(
                                            'study_program',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Contoh: Sistem Informasi"
                                />
                                {form.errors.study_program && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {form.errors.study_program}
                                    </p>
                                )}
                            </label>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <label className="block">
                                    <span className="mb-2 block text-sm font-black">
                                        Semester
                                    </span>
                                    <Input
                                        type="number"
                                        min={1}
                                        max={14}
                                        value={form.data.semester}
                                        onChange={(event) =>
                                            form.setData(
                                                'semester',
                                                Number(event.target.value),
                                            )
                                        }
                                    />
                                </label>

                                <label className="block">
                                    <span className="mb-2 flex items-center gap-2 text-sm font-black">
                                        <Clock3 className="size-4" />
                                        Jam belajar / minggu
                                    </span>
                                    <Input
                                        type="number"
                                        min={1}
                                        max={60}
                                        value={form.data.weekly_study_hours}
                                        onChange={(event) =>
                                            form.setData(
                                                'weekly_study_hours',
                                                Number(event.target.value),
                                            )
                                        }
                                    />
                                </label>
                            </div>

                            <label className="block">
                                <span className="mb-2 block text-sm font-black">
                                    Bidang yang membuatmu penasaran
                                </span>
                                <Input
                                    value={form.data.interest_area}
                                    onChange={(event) =>
                                        form.setData(
                                            'interest_area',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Contoh: backend, data, produk web"
                                />
                            </label>

                            <label className="block">
                                <span className="mb-2 block text-sm font-black">
                                    Pengalaman sejauh ini
                                </span>

                                <textarea
                                    value={form.data.experience}
                                    onChange={(event) =>
                                        form.setData(
                                            'experience',
                                            event.target.value,
                                        )
                                    }
                                    rows={5}
                                    className="w-full resize-none rounded-xl border-2 border-foreground bg-card p-3 text-sm font-medium shadow-[3px_3px_0_var(--foreground)] outline-none focus:ring-2 focus:ring-secondary"
                                    placeholder="Ceritakan project, tools, atau materi yang pernah kamu kerjakan."
                                />

                                {form.errors.experience && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {form.errors.experience}
                                    </p>
                                )}
                            </label>
                        </div>
                    </section>

                    <section className="neo-card p-6">
                        <div className="flex items-center gap-3">
                            <Target className="size-6" />
                            <h2 className="text-xl font-black">
                                Target karier pertama
                            </h2>
                        </div>

                        <p className="mt-2 text-sm leading-relaxed font-medium text-muted-foreground">
                            Target bisa diubah nanti, tetapi asesmen dan roadmap
                            akan mengikuti target aktif.
                        </p>

                        <div className="mt-6 space-y-4">
                            {careers.map((career) => {
                                const selected =
                                    form.data.target_career_id === career.id;

                                return (
                                    <button
                                        key={career.id}
                                        type="button"
                                        onClick={() =>
                                            form.setData(
                                                'target_career_id',
                                                career.id,
                                            )
                                        }
                                        className={`w-full rounded-2xl border-2 border-foreground p-5 text-left transition-transform ${
                                            selected
                                                ? 'translate-x-1 translate-y-1 shadow-none'
                                                : 'bg-card shadow-[4px_4px_0_var(--foreground)] hover:-translate-y-0.5'
                                        }`}
                                        style={
                                            selected
                                                ? {
                                                      backgroundColor:
                                                          career.accent,
                                                  }
                                                : undefined
                                        }
                                    >
                                        <div className="flex items-start justify-between gap-4">
                                            <div>
                                                <h3 className="text-lg font-black">
                                                    {career.name}
                                                </h3>
                                                <p className="mt-2 text-sm leading-relaxed font-semibold text-muted-foreground">
                                                    {career.tagline}
                                                </p>
                                            </div>

                                            <span
                                                className={`mt-1 size-5 shrink-0 rounded-full border-2 border-foreground ${
                                                    selected
                                                        ? 'bg-foreground'
                                                        : 'bg-card'
                                                }`}
                                            />
                                        </div>
                                    </button>
                                );
                            })}
                        </div>

                        {form.errors.target_career_id && (
                            <p className="mt-3 text-xs font-bold text-destructive">
                                {form.errors.target_career_id}
                            </p>
                        )}

                        <div className="mt-7 rounded-xl border-2 border-foreground bg-muted p-4 text-sm leading-relaxed font-semibold">
                            Sesudah ini kamu akan mengerjakan asesmen singkat.
                            Roadmap baru dibuat setelah ada data kemampuan awal.
                        </div>

                        <Button
                            type="submit"
                            size="lg"
                            className="mt-6 w-full"
                            disabled={form.processing}
                        >
                            {editing ? 'Simpan profil' : 'Lanjut ke asesmen'}
                            <ArrowRight />
                        </Button>
                    </section>
                </form>
            </div>
        </>
    );
}

Onboarding.layout = {
    breadcrumbs: [
        {
            title: 'Onboarding',
            href: '/onboarding',
        },
    ],
};
