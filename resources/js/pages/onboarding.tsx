import { Head, useForm } from '@inertiajs/react';
import { ArrowRight, Check, Clock3, GraduationCap, Target } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

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
            <Head title="Profil Belajar" />

            <div className="neo-page py-6 sm:py-8 lg:py-10">
                <section className="neo-hero neo-accent-blue border-[#171717]">
                    <span className="neo-label bg-[#fffdf7]">
                        {editing ? 'Profil belajar' : 'Tahap 01'}
                    </span>

                    <h1 className="mt-5 max-w-4xl text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                        {editing
                            ? 'Perbarui konteks belajarmu.'
                            : 'Berikan konteks yang benar kepada sistem.'}
                    </h1>

                    <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold sm:text-base">
                        Waktu belajar memengaruhi estimasi roadmap. Target
                        karier menentukan standar skill yang dibandingkan. Isi
                        berdasarkan kondisi saat ini, bukan versi ideal.
                    </p>
                </section>

                <form
                    onSubmit={submit}
                    className="mt-7 grid gap-6 lg:grid-cols-[0.92fr_1.08fr]"
                >
                    <section className="neo-card p-5 sm:p-6">
                        <div className="flex items-center gap-3">
                            <span className="flex size-10 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-yellow)] text-[#171717]">
                                <GraduationCap className="size-5" />
                            </span>

                            <div>
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Tentang kamu
                                </p>

                                <h2 className="text-xl font-black">
                                    Profil belajar
                                </h2>
                            </div>
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
                                        Jam belajar per minggu
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

                                <Textarea
                                    value={form.data.experience}
                                    onChange={(event) =>
                                        form.setData(
                                            'experience',
                                            event.target.value,
                                        )
                                    }
                                    rows={6}
                                    placeholder="Ceritakan proyek, tools, teknologi, atau materi yang pernah kamu kerjakan."
                                />

                                {form.errors.experience && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {form.errors.experience}
                                    </p>
                                )}
                            </label>
                        </div>
                    </section>

                    <section className="neo-card p-5 sm:p-6">
                        <div className="flex items-center gap-3">
                            <span className="flex size-10 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-secondary text-[#171717]">
                                <Target className="size-5" />
                            </span>

                            <div>
                                <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                    Tujuan utama
                                </p>

                                <h2 className="text-xl font-black">
                                    Target karier pertama
                                </h2>
                            </div>
                        </div>

                        <p className="mt-3 text-sm leading-6 font-medium text-muted-foreground">
                            Target dapat diubah nanti. Asesmen dan roadmap akan
                            selalu mengikuti target yang sedang aktif.
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
                                        className={`w-full rounded-[14px] border-2 p-5 text-left transition-[transform,box-shadow] ${
                                            selected
                                                ? 'translate-x-[2px] translate-y-[2px] border-[#171717] text-[#171717] shadow-none'
                                                : 'border-foreground bg-card text-foreground shadow-[4px_4px_0_var(--neo-shadow-color)] hover:-translate-y-[1px]'
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
                                            <div className="min-w-0">
                                                <div className="flex flex-wrap items-center gap-2">
                                                    <h3 className="text-lg font-black">
                                                        {career.name}
                                                    </h3>

                                                    <span
                                                        className={`rounded-full border px-2 py-0.5 text-[10px] font-black uppercase ${
                                                            selected
                                                                ? 'border-[#171717]/30 text-[#171717]/70'
                                                                : 'border-foreground/25 text-muted-foreground'
                                                        }`}
                                                    >
                                                        {career.difficulty}
                                                    </span>
                                                </div>

                                                <p
                                                    className={`mt-2 text-sm leading-6 font-semibold ${
                                                        selected
                                                            ? 'text-[#171717]/75'
                                                            : 'text-muted-foreground'
                                                    }`}
                                                >
                                                    {career.tagline}
                                                </p>
                                            </div>

                                            <span
                                                className={`mt-1 flex size-6 shrink-0 items-center justify-center rounded-full border-2 ${
                                                    selected
                                                        ? 'border-[#171717] bg-[#171717] text-white'
                                                        : 'border-foreground bg-card'
                                                }`}
                                            >
                                                {selected && (
                                                    <Check className="size-3.5" />
                                                )}
                                            </span>
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

                        <div className="mt-7 rounded-[12px] border-2 border-[#171717] bg-[var(--neo-yellow)] p-4 text-sm leading-6 font-bold text-[#171717]">
                            Sesudah langkah ini kamu akan mengerjakan asesmen
                            singkat. Roadmap baru dibuat setelah sistem memiliki
                            data kemampuan awal.
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
            title: 'Profil Belajar',
            href: '/onboarding',
        },
    ],
};
