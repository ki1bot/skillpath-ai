import { Head, Link } from '@inertiajs/react';
import {
    ArrowRight,
    BookOpenCheck,
    CheckCircle2,
    ClipboardCheck,
    GraduationCap,
    LayoutDashboard,
    Settings2,
    ShieldCheck,
    UsersRound,
} from 'lucide-react';
import { Button } from '@/components/ui/button';
import { StatsSection } from './sections/stats-section';
import type { AdminStats } from './types';

type Props = {
    stats: AdminStats;
    overview: {
        activeCareers: number;
        activeAssessments: number;
        onboardedStudents: number;
        activeCoreMaterials: number;
        activeReinforcementMaterials: number;
        inactiveMaterials: number;
    };
};

export default function AdminDashboard({ stats, overview }: Props) {
    const contentTotal = stats.skills + stats.materials + stats.projects;

    return (
        <>
            <Head title="Dashboard Administrator" />

            <div className="neo-page flex flex-col gap-7 py-6 sm:py-8 lg:py-10">
                <section className="neo-hero neo-accent-blue border-[#171717]">
                    <div className="flex flex-col justify-between gap-7 lg:flex-row lg:items-end">
                        <div className="flex max-w-4xl flex-col gap-5 sm:flex-row sm:items-start">
                            <span className="flex size-14 shrink-0 items-center justify-center rounded-[13px] border-2 border-[#171717] bg-[#fffdf7] shadow-[4px_4px_0_#171717]">
                                <LayoutDashboard className="size-7" />
                            </span>

                            <div>
                                <span className="neo-label bg-[#fffdf7]">
                                    Dashboard administrator
                                </span>

                                <h1 className="mt-5 text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                                    Pantau kegiatan belajar dan data SkillPath.
                                </h1>

                                <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold sm:text-base">
                                    Lihat jumlah mahasiswa, jurusan, kemampuan,
                                    materi, Assessment, dan proyek. Data materi
                                    juga dipisahkan agar materi utama,
                                    penguatan, dan arsip mudah dibedakan.
                                </p>
                            </div>
                        </div>

                        <Button asChild className="shrink-0">
                            <Link href="/admin">
                                <Settings2 />
                                Kelola Sistem
                                <ArrowRight />
                            </Link>
                        </Button>
                    </div>
                </section>

                <StatsSection stats={stats} />

                <section className="grid gap-5 lg:grid-cols-3">
                    <article className="neo-card p-6">
                        <div className="flex items-start justify-between gap-4">
                            <span className="flex size-11 items-center justify-center rounded-[10px] border-2 border-foreground bg-[var(--neo-lime)] text-[#171717]">
                                <UsersRound className="size-5" />
                            </span>

                            <CheckCircle2 className="size-5 text-muted-foreground" />
                        </div>

                        <p className="mt-6 text-xs font-black tracking-[0.12em] text-muted-foreground uppercase">
                            Mahasiswa dengan profil belajar
                        </p>

                        <p className="mt-2 text-4xl font-black tracking-[-0.04em]">
                            {overview.onboardedStudents}
                        </p>

                        <p className="mt-2 text-sm leading-6 font-semibold text-muted-foreground">
                            Dari {stats.users} mahasiswa terdaftar telah
                            menyelesaikan profil awal.
                        </p>
                    </article>

                    <article className="neo-card p-6">
                        <div className="flex items-start justify-between gap-4">
                            <span className="flex size-11 items-center justify-center rounded-[10px] border-2 border-foreground bg-[var(--neo-blue)] text-[#171717]">
                                <GraduationCap className="size-5" />
                            </span>

                            <CheckCircle2 className="size-5 text-muted-foreground" />
                        </div>

                        <p className="mt-6 text-xs font-black tracking-[0.12em] text-muted-foreground uppercase">
                            Jurusan aktif
                        </p>

                        <p className="mt-2 text-4xl font-black tracking-[-0.04em]">
                            {overview.activeCareers}
                        </p>

                        <p className="mt-2 text-sm leading-6 font-semibold text-muted-foreground">
                            Dari {stats.careers} data jurusan yang tersimpan,
                            sejumlah ini sedang aktif digunakan oleh sistem.
                        </p>
                    </article>

                    <article className="neo-card p-6">
                        <div className="flex items-start justify-between gap-4">
                            <span className="flex size-11 items-center justify-center rounded-[10px] border-2 border-foreground bg-[var(--neo-yellow)] text-[#171717]">
                                <ClipboardCheck className="size-5" />
                            </span>

                            <CheckCircle2 className="size-5 text-muted-foreground" />
                        </div>

                        <p className="mt-6 text-xs font-black tracking-[0.12em] text-muted-foreground uppercase">
                            Assessment aktif
                        </p>

                        <p className="mt-2 text-4xl font-black tracking-[-0.04em]">
                            {overview.activeAssessments}
                        </p>

                        <p className="mt-2 text-sm leading-6 font-semibold text-muted-foreground">
                            Assessment yang saat ini tersedia untuk digunakan
                            oleh mahasiswa.
                        </p>
                    </article>
                </section>

                <section className="neo-card overflow-hidden">
                    <div className="border-b-2 border-foreground bg-secondary p-5 text-[#171717] sm:p-6">
                        <div className="flex items-center justify-between gap-4">
                            <div>
                                <p className="text-xs font-black tracking-[0.13em] uppercase">
                                    Struktur pembelajaran
                                </p>

                                <h2 className="mt-1 text-2xl font-black">
                                    Tiga tahap belajar untuk setiap jurusan
                                </h2>
                            </div>

                            <BookOpenCheck className="size-7 shrink-0" />
                        </div>
                    </div>

                    <div className="grid gap-4 p-5 sm:p-6 md:grid-cols-3">
                        <div className="rounded-[11px] border-2 border-foreground bg-muted/50 p-4">
                            <p className="text-sm font-black">
                                Tahap 1 — Amatir
                            </p>

                            <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                Mahasiswa mempelajari dasar dari sembilan
                                kemampuan yang berasal dari tiga bidang
                                jurusannya.
                            </p>
                        </div>

                        <div className="rounded-[11px] border-2 border-foreground bg-muted/50 p-4">
                            <p className="text-sm font-black">
                                Tahap 2 — Menengah
                            </p>

                            <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                Materi dilanjutkan dengan tugas yang lebih
                                lengkap, pemeriksaan hasil, dan penerapan yang
                                lebih mandiri.
                            </p>
                        </div>

                        <div className="rounded-[11px] border-2 border-foreground bg-muted/50 p-4">
                            <p className="text-sm font-black">Tahap 3 — Ahli</p>

                            <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                Mahasiswa mengerjakan kasus yang lebih mendekati
                                pekerjaan nyata, termasuk pengujian dan
                                pertimbangan keputusan.
                            </p>
                        </div>
                    </div>

                    <div className="border-t-2 border-foreground px-5 py-4 sm:px-6">
                        <p className="text-sm leading-6 font-medium text-muted-foreground">
                            Setiap tahap memiliki sembilan materi utama.
                            Mahasiswa mengerjakan materi secara berurutan dan
                            harus mendapat nilai minimal 70 dari admin sebelum
                            melanjutkan. Materi penguatan digunakan ketika tugas
                            belum memenuhi nilai kelulusan.
                        </p>
                    </div>
                </section>

                <section className="grid gap-6 xl:grid-cols-[1.05fr_0.95fr]">
                    <article className="neo-card p-6 sm:p-7">
                        <div className="flex items-start gap-4">
                            <span className="flex size-12 shrink-0 items-center justify-center rounded-[11px] border-2 border-foreground bg-secondary text-[#171717]">
                                <LayoutDashboard className="size-6" />
                            </span>

                            <div>
                                <span className="neo-label">Dashboard</span>

                                <h2 className="mt-4 text-2xl font-black tracking-[-0.035em]">
                                    Ringkasan kondisi sistem
                                </h2>

                                <p className="mt-3 text-sm leading-6 font-semibold text-muted-foreground">
                                    Halaman ini menampilkan jumlah mahasiswa,
                                    jurusan, kemampuan, materi, proyek,
                                    Assessment, dan status data aktif.
                                </p>
                            </div>
                        </div>

                        <div className="mt-6 rounded-[11px] border-2 border-foreground bg-muted/60 p-4">
                            <p className="text-sm font-black">
                                Perubahan data dilakukan melalui halaman Kelola
                                Sistem.
                            </p>
                        </div>
                    </article>

                    <article className="neo-card p-6 sm:p-7">
                        <div className="flex items-start gap-4">
                            <span className="flex size-12 shrink-0 items-center justify-center rounded-[11px] border-2 border-foreground bg-[var(--neo-orange)] text-[#171717]">
                                <ShieldCheck className="size-6" />
                            </span>

                            <div>
                                <span className="neo-label bg-[var(--neo-orange)]">
                                    Kelola Sistem
                                </span>

                                <h2 className="mt-4 text-2xl font-black tracking-[-0.035em]">
                                    Kelola data utama SkillPath
                                </h2>

                                <p className="mt-3 text-sm leading-6 font-semibold text-muted-foreground">
                                    Buat, ubah, hubungkan, atau hapus data
                                    jurusan, kemampuan, Assessment, materi, dan
                                    proyek dari halaman pengelolaan.
                                </p>
                            </div>
                        </div>

                        <Button asChild className="mt-6 w-full">
                            <Link href="/admin">
                                Buka Kelola Sistem
                                <ArrowRight />
                            </Link>
                        </Button>
                    </article>
                </section>

                <section className="neo-card overflow-hidden">
                    <div className="border-b-2 border-foreground bg-[var(--neo-pink)] p-5 text-[#171717] sm:p-6">
                        <div className="flex items-center justify-between gap-4">
                            <div>
                                <p className="text-xs font-black tracking-[0.13em] uppercase">
                                    Konten pembelajaran
                                </p>

                                <h2 className="mt-1 text-2xl font-black">
                                    Materi aktif dan data pembelajaran
                                </h2>
                            </div>

                            <BookOpenCheck className="size-7 shrink-0" />
                        </div>
                    </div>

                    <div className="grid gap-4 p-5 sm:grid-cols-3 sm:p-6">
                        <div className="rounded-[11px] border-2 border-foreground bg-muted/50 p-4">
                            <p className="text-xs font-black tracking-[0.12em] text-muted-foreground uppercase">
                                Kemampuan
                            </p>

                            <p className="mt-2 text-3xl font-black">
                                {stats.skills}
                            </p>
                        </div>

                        <div className="rounded-[11px] border-2 border-foreground bg-muted/50 p-4">
                            <p className="text-xs font-black tracking-[0.12em] text-muted-foreground uppercase">
                                Materi aktif
                            </p>

                            <p className="mt-2 text-3xl font-black">
                                {stats.materials}
                            </p>
                        </div>

                        <div className="rounded-[11px] border-2 border-foreground bg-muted/50 p-4">
                            <p className="text-xs font-black tracking-[0.12em] text-muted-foreground uppercase">
                                Proyek
                            </p>

                            <p className="mt-2 text-3xl font-black">
                                {stats.projects}
                            </p>
                        </div>
                    </div>

                    <div className="grid gap-4 border-t-2 border-foreground px-5 py-5 sm:grid-cols-3 sm:px-6">
                        <div>
                            <p className="text-xs font-black text-muted-foreground uppercase">
                                Materi utama aktif
                            </p>

                            <p className="mt-1 text-2xl font-black">
                                {overview.activeCoreMaterials}
                            </p>

                            <p className="mt-1 text-xs leading-5 font-medium text-muted-foreground">
                                Materi wajib yang membentuk tiga tahap belajar.
                            </p>
                        </div>

                        <div>
                            <p className="text-xs font-black text-muted-foreground uppercase">
                                Materi penguatan aktif
                            </p>

                            <p className="mt-1 text-2xl font-black">
                                {overview.activeReinforcementMaterials}
                            </p>

                            <p className="mt-1 text-xs leading-5 font-medium text-muted-foreground">
                                Tersedia ketika mahasiswa perlu memperbaiki
                                hasil tugas.
                            </p>
                        </div>

                        <div>
                            <p className="text-xs font-black text-muted-foreground uppercase">
                                Materi nonaktif
                            </p>

                            <p className="mt-1 text-2xl font-black">
                                {overview.inactiveMaterials}
                            </p>

                            <p className="mt-1 text-xs leading-5 font-medium text-muted-foreground">
                                Data lama atau materi yang tidak sedang
                                digunakan pada jalur belajar aktif.
                            </p>
                        </div>
                    </div>

                    <div className="border-t-2 border-foreground px-5 py-4 sm:px-6">
                        <p className="text-sm font-semibold text-muted-foreground">
                            Total{' '}
                            <span className="font-black text-foreground">
                                {contentTotal}
                            </span>{' '}
                            item kemampuan, materi aktif, dan proyek tercatat
                            pada ringkasan ini. Materi nonaktif tidak termasuk
                            dalam jumlah materi aktif.
                        </p>
                    </div>
                </section>
            </div>
        </>
    );
}

AdminDashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: '/admin/dashboard',
        },
    ],
};
