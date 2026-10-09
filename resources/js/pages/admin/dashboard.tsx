import { Head } from '@inertiajs/react';
import { LayoutDashboard } from 'lucide-react';
import { StatsSection } from './sections/stats-section';
import type { AdminStats } from './types';

type Props = {
    stats: AdminStats;
};

export default function AdminDashboard({ stats }: Props) {
    return (
        <>
            <Head title="Dashboard Administrator" />

            <div className="neo-page flex flex-col gap-7 py-6 sm:py-8 lg:py-10">
                <section className="neo-hero neo-accent-blue border-[#171717]">
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
                                materi, Assessment, dan proyek. Data materi juga
                                dipisahkan agar materi utama, penguatan, dan
                                arsip mudah dibedakan.
                            </p>
                        </div>
                    </div>
                </section>

                <StatsSection stats={stats} />
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
