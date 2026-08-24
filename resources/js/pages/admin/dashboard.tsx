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
                                    Lihat kondisi SkillPath sebelum mengelola
                                    datanya.
                                </h1>

                                <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold sm:text-base">
                                    Dashboard menampilkan ringkasan mahasiswa,
                                    jurusan, kemampuan, Assesment, materi, dan
                                    proyek yang tersimpan di sistem.
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
                            Assesment aktif
                        </p>

                        <p className="mt-2 text-4xl font-black tracking-[-0.04em]">
                            {overview.activeAssessments}
                        </p>

                        <p className="mt-2 text-sm leading-6 font-semibold text-muted-foreground">
                            Assesment yang saat ini tersedia untuk digunakan
                            oleh mahasiswa.
                        </p>
                    </article>
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
                                    Ringkasan kondisi sistem.
                                </h2>

                                <p className="mt-3 text-sm leading-6 font-semibold text-muted-foreground">
                                    Halaman ini digunakan untuk melihat jumlah
                                    mahasiswa, jurusan, kemampuan, materi,
                                    proyek, Assesment, dan status data aktif.
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
                                    Kelola data utama SkillPath.
                                </h2>

                                <p className="mt-3 text-sm leading-6 font-semibold text-muted-foreground">
                                    Buat, ubah, hubungkan, atau hapus data
                                    jurusan, kemampuan, Assesment, materi, dan
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
                                    Data yang membentuk rekomendasi SkillPath
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
                                Materi
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

                    <div className="border-t-2 border-foreground px-5 py-4 sm:px-6">
                        <p className="text-sm font-semibold text-muted-foreground">
                            Total{' '}
                            <span className="font-black text-foreground">
                                {contentTotal}
                            </span>{' '}
                            item kemampuan, materi, dan proyek saat ini tersedia
                            di database.
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
