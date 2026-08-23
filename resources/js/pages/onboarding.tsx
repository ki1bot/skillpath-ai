import { Head, useForm } from '@inertiajs/react';
import { ArrowRight, Check, Clock3, GraduationCap, Target } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { STUDY_PROGRAMS } from '@/lib/academic-programs';

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
                            ? 'Perbarui arah belajarmu.'
                            : 'Mulai dari jurusan dan tujuan yang benar.'}
                    </h1>

                    <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold sm:text-base">
                        Pilih jurusan yang sedang kamu tempuh, ceritakan sedikit
                        pengalamanmu, lalu tentukan target karier. Setelah itu
                        kamu akan mengerjakan asesmen singkat untuk melihat
                        kemampuan awalmu.
                    </p>
                </section>

                <form onSubmit={submit} className="mt-7 space-y-6">
                    <section className="neo-card p-5 sm:p-6">
                        <div className="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                            <div className="max-w-3xl">
                                <div className="flex items-center gap-3">
                                    <span className="flex size-10 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-[var(--neo-yellow)] text-[#171717]">
                                        <GraduationCap className="size-5" />
                                    </span>

                                    <div>
                                        <p className="text-xs font-black tracking-[0.14em] text-muted-foreground uppercase">
                                            Jurusan kuliah
                                        </p>

                                        <h2 className="text-xl font-black">
                                            Pilih jurusanmu
                                        </h2>
                                    </div>
                                </div>

                                <p className="mt-4 text-sm leading-6 font-medium text-muted-foreground">
                                    Setiap jurusan memiliki tiga bidang utama
                                    dan sembilan kemampuan yang akan dinilai.
                                    Pilih yang sesuai dengan program studi yang
                                    sedang kamu jalani sekarang.
                                </p>
                            </div>

                            {form.data.study_program && (
                                <span className="w-fit rounded-full border-2 border-[#171717] bg-secondary px-3 py-1.5 text-xs font-black text-[#171717]">
                                    Dipilih: {form.data.study_program}
                                </span>
                            )}
                        </div>

                        <div className="mt-6 grid gap-5 xl:grid-cols-2">
                            {STUDY_PROGRAMS.map((program) => {
                                const selected =
                                    form.data.study_program === program.name;

                                return (
                                    <button
                                        key={program.name}
                                        type="button"
                                        aria-pressed={selected}
                                        onClick={() =>
                                            form.setData(
                                                'study_program',
                                                program.name,
                                            )
                                        }
                                        className={`rounded-[16px] border-2 p-5 text-left transition-[transform,box-shadow,background-color] ${
                                            selected
                                                ? 'translate-x-[2px] translate-y-[2px] border-[#171717] bg-secondary text-[#171717] shadow-none'
                                                : 'border-foreground bg-card text-foreground shadow-[4px_4px_0_var(--neo-shadow-color)] hover:-translate-y-[1px]'
                                        }`}
                                    >
                                        <div className="flex items-start justify-between gap-4">
                                            <div className="min-w-0">
                                                <p className="text-xs font-black tracking-[0.14em] uppercase opacity-60">
                                                    Jurusan
                                                </p>

                                                <h3 className="mt-1 text-2xl font-black tracking-tight">
                                                    {program.name}
                                                </h3>

                                                <p className="mt-2 text-sm leading-6 font-semibold opacity-75">
                                                    {program.description}
                                                </p>
                                            </div>

                                            <span
                                                className={`flex size-7 shrink-0 items-center justify-center rounded-full border-2 ${
                                                    selected
                                                        ? 'border-[#171717] bg-[#171717] text-white'
                                                        : 'border-foreground bg-background'
                                                }`}
                                            >
                                                {selected && (
                                                    <Check className="size-4" />
                                                )}
                                            </span>
                                        </div>

                                        <div className="mt-5 grid gap-3 md:grid-cols-3 xl:grid-cols-1 2xl:grid-cols-3">
                                            {program.areas.map(
                                                (area, areaIndex) => (
                                                    <div
                                                        key={area.name}
                                                        className={`rounded-[12px] border-2 p-3 ${
                                                            selected
                                                                ? 'border-[#171717]/35 bg-white/45'
                                                                : 'border-foreground/20 bg-muted'
                                                        }`}
                                                    >
                                                        <div className="flex items-start gap-2">
                                                            <span className="flex size-6 shrink-0 items-center justify-center rounded-full border border-current text-[10px] font-black">
                                                                {areaIndex + 1}
                                                            </span>

                                                            <p className="text-sm leading-5 font-black">
                                                                {area.name}
                                                            </p>
                                                        </div>

                                                        <ul className="mt-3 space-y-2">
                                                            {area.skills.map(
                                                                (skill) => (
                                                                    <li
                                                                        key={
                                                                            skill
                                                                        }
                                                                        className="flex gap-2 text-xs leading-5 font-semibold opacity-80"
                                                                    >
                                                                        <span className="mt-[7px] size-1.5 shrink-0 rounded-full bg-current" />

                                                                        <span>
                                                                            {
                                                                                skill
                                                                            }
                                                                        </span>
                                                                    </li>
                                                                ),
                                                            )}
                                                        </ul>
                                                    </div>
                                                ),
                                            )}
                                        </div>
                                    </button>
                                );
                            })}
                        </div>

                        {form.errors.study_program && (
                            <p className="mt-4 text-sm font-bold text-destructive">
                                {form.errors.study_program}
                            </p>
                        )}
                    </section>

                    <div className="grid gap-6 lg:grid-cols-[0.92fr_1.08fr]">
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

                                        {form.errors.semester && (
                                            <p className="mt-2 text-xs font-bold text-destructive">
                                                {form.errors.semester}
                                            </p>
                                        )}
                                    </label>

                                    <label className="block">
                                        <span className="mb-2 flex items-center gap-2 text-sm font-black">
                                            <Clock3 className="size-4" />
                                            Waktu belajar per minggu
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

                                        {form.errors.weekly_study_hours && (
                                            <p className="mt-2 text-xs font-bold text-destructive">
                                                {form.errors.weekly_study_hours}
                                            </p>
                                        )}
                                    </label>
                                </div>

                                <label className="block">
                                    <span className="mb-2 block text-sm font-black">
                                        Bidang yang paling ingin kamu dalami
                                    </span>

                                    <Input
                                        value={form.data.interest_area}
                                        onChange={(event) =>
                                            form.setData(
                                                'interest_area',
                                                event.target.value,
                                            )
                                        }
                                        placeholder="Contoh: analisis data, pengembangan web, pemasaran digital"
                                    />

                                    {form.errors.interest_area && (
                                        <p className="mt-2 text-xs font-bold text-destructive">
                                            {form.errors.interest_area}
                                        </p>
                                    )}
                                </label>

                                <label className="block">
                                    <span className="mb-2 block text-sm font-black">
                                        Pengalaman belajar atau proyek yang
                                        pernah kamu coba
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
                                        placeholder="Ceritakan mata kuliah, proyek, organisasi, tools, atau pengalaman lain yang menurutmu relevan."
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
                                        Arah setelah asesmen
                                    </p>

                                    <h2 className="text-xl font-black">
                                        Target karier
                                    </h2>
                                </div>
                            </div>

                            <p className="mt-3 text-sm leading-6 font-medium text-muted-foreground">
                                Jurusan menentukan kemampuan yang dinilai.
                                Target karier membantu SkillPath menyusun arah
                                belajar dan roadmap setelah hasil asesmenmu
                                diketahui.
                            </p>

                            <div className="mt-6 space-y-4">
                                {careers.map((career) => {
                                    const selected =
                                        form.data.target_career_id ===
                                        career.id;

                                    return (
                                        <button
                                            key={career.id}
                                            type="button"
                                            aria-pressed={selected}
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
                                Setelah profil disimpan, kamu akan menjawab 9
                                pertanyaan dari jurusan yang dipilih. Jawab
                                sesuai kemampuanmu sekarang, bukan berdasarkan
                                jawaban yang menurutmu paling terlihat bagus.
                            </div>

                            <Button
                                type="submit"
                                size="lg"
                                className="mt-6 w-full"
                                disabled={
                                    form.processing || !form.data.study_program
                                }
                            >
                                {editing
                                    ? 'Simpan perubahan'
                                    : 'Lanjut ke asesmen'}
                                <ArrowRight />
                            </Button>
                        </section>
                    </div>
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
