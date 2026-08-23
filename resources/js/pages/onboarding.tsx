import { Head, useForm } from '@inertiajs/react';
import { ArrowRight, Check, Clock3, GraduationCap } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { getStudyProgramDefinition } from '@/lib/academic-programs';

type Career = {
    id: number;
    name: string;
    slug: string;
    tagline: string;
    description: string;
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

    const currentCareerAvailable = careers.some(
        (career) => career.id === profile.target_career_id,
    );

    const initialCareerId = currentCareerAvailable
        ? (profile.target_career_id ?? 0)
        : (careers[0]?.id ?? 0);

    const form = useForm({
        semester: profile.semester ?? 1,
        interest_area: profile.interest_area ?? '',
        experience: profile.experience ?? '',
        weekly_study_hours: profile.weekly_study_hours ?? 6,
        target_career_id: initialCareerId,
    });

    const selectedCareer = careers.find(
        (career) => career.id === form.data.target_career_id,
    );

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
                            ? 'Perbarui jurusan dan arah belajarmu.'
                            : 'Pilih jurusan yang sedang kamu jalani.'}
                    </h1>

                    <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold sm:text-base">
                        Setiap jurusan memiliki tiga bidang utama dan sembilan
                        kemampuan yang akan dinilai. Pilih jurusanmu, isi profil
                        belajar, lalu kerjakan asesmen awal agar SkillPath dapat
                        melihat kemampuanmu sekarang.
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
                                            Jurusan
                                        </p>

                                        <h2 className="text-xl font-black">
                                            Pilih jurusanmu
                                        </h2>
                                    </div>
                                </div>

                                <p className="mt-4 text-sm leading-6 font-medium text-muted-foreground">
                                    Klik salah satu jurusan untuk melihat bidang
                                    dan kemampuan yang akan dinilai dalam
                                    asesmen.
                                </p>
                            </div>

                            {selectedCareer && (
                                <span className="w-fit rounded-full border-2 border-[#171717] bg-secondary px-3 py-1.5 text-xs font-black text-[#171717]">
                                    Dipilih: {selectedCareer.name}
                                </span>
                            )}
                        </div>

                        <div className="mt-6 grid gap-5 xl:grid-cols-2">
                            {careers.map((career) => {
                                const selected =
                                    form.data.target_career_id === career.id;

                                const program = getStudyProgramDefinition(
                                    career.name,
                                );

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
                                        className={`rounded-[16px] border-2 p-5 text-left transition-[transform,box-shadow,background-color] ${
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
                                                <p className="text-xs font-black tracking-[0.14em] uppercase opacity-60">
                                                    Jurusan
                                                </p>

                                                <h3 className="mt-1 text-2xl font-black tracking-tight">
                                                    {career.name}
                                                </h3>

                                                <p className="mt-2 text-sm leading-6 font-semibold opacity-75">
                                                    {career.tagline}
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

                                        {program && (
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
                                                                    {areaIndex +
                                                                        1}
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
                                        )}
                                    </button>
                                );
                            })}
                        </div>

                        {form.errors.target_career_id && (
                            <p className="mt-4 text-sm font-bold text-destructive">
                                {form.errors.target_career_id}
                            </p>
                        )}
                    </section>

                    <section className="neo-card p-5 sm:p-6">
                        <div className="flex items-center gap-3">
                            <span className="flex size-10 items-center justify-center rounded-[10px] border-2 border-[#171717] bg-secondary text-[#171717]">
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

                        <p className="mt-3 max-w-3xl text-sm leading-6 font-medium text-muted-foreground">
                            Tidak perlu membuat jawaban yang terdengar hebat.
                            Isi berdasarkan kondisi yang sebenarnya agar
                            rekomendasi belajar lebih masuk akal.
                        </p>

                        <div className="mt-6 grid gap-5 lg:grid-cols-2">
                            <div className="space-y-5">
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
                                        placeholder="Contoh: Analisis Data, Artificial Intelligence, Marketing"
                                    />

                                    {form.errors.interest_area && (
                                        <p className="mt-2 text-xs font-bold text-destructive">
                                            {form.errors.interest_area}
                                        </p>
                                    )}
                                </label>
                            </div>

                            <label className="block">
                                <span className="mb-2 block text-sm font-black">
                                    Pengalaman belajar atau proyek sejauh ini
                                </span>

                                <Textarea
                                    value={form.data.experience}
                                    onChange={(event) =>
                                        form.setData(
                                            'experience',
                                            event.target.value,
                                        )
                                    }
                                    rows={8}
                                    placeholder="Ceritakan mata kuliah, proyek, organisasi, aplikasi, tools, atau pengalaman lain yang pernah kamu kerjakan."
                                />

                                {form.errors.experience && (
                                    <p className="mt-2 text-xs font-bold text-destructive">
                                        {form.errors.experience}
                                    </p>
                                )}
                            </label>
                        </div>

                        <div className="mt-7 rounded-[12px] border-2 border-[#171717] bg-[var(--neo-yellow)] p-4 text-sm leading-6 font-bold text-[#171717]">
                            Setelah profil disimpan, kamu akan mengerjakan 9
                            pertanyaan sesuai jurusan yang dipilih.
                        </div>

                        <Button
                            type="submit"
                            size="lg"
                            className="mt-6 w-full"
                            disabled={
                                form.processing || !form.data.target_career_id
                            }
                        >
                            {editing ? 'Simpan perubahan' : 'Lanjut ke asesmen'}
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
