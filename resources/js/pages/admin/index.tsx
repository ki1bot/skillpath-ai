import { Head } from '@inertiajs/react';
import { LayoutDashboard, ShieldCheck } from 'lucide-react';
import { AssessmentsSection } from './sections/assessments-section';
import { CareersSection } from './sections/careers-section';
import { MaterialsSection } from './sections/materials-section';
import { ProjectsSection } from './sections/projects-section';
import { SkillsSection } from './sections/skills-section';
import { StatsSection } from './sections/stats-section';
import type { AdminPageProps } from './types';

export default function AdminIndex({
    stats,
    careers,
    skills,
    prerequisites,
    assessments,
    materials,
    projects,
}: AdminPageProps) {
    return (
        <>
            <Head title="Dashboard Administrator" />

            <div className="neo-page flex flex-col gap-7 py-6 sm:py-8 lg:py-10">
                <section className="neo-hero neo-accent-blue border-[#171717]">
                    <div className="flex max-w-4xl flex-col gap-5 sm:flex-row sm:items-start">
                        <span className="flex size-14 shrink-0 items-center justify-center rounded-[13px] border-2 border-[#171717] bg-[#fffdf7] shadow-[4px_4px_0_#171717]">
                            <LayoutDashboard className="size-7" />
                        </span>

                        <div className="min-w-0">
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

                <section
                    aria-labelledby="admin-management-heading"
                    className="scroll-mt-6"
                >
                    <div className="neo-card flex flex-col gap-5 p-5 sm:flex-row sm:items-start sm:p-6">
                        <span className="flex size-12 shrink-0 items-center justify-center rounded-[12px] border-2 border-[#171717] bg-[var(--neo-orange)] text-[#171717] shadow-[3px_3px_0_var(--neo-shadow-color)]">
                            <ShieldCheck className="size-6" />
                        </span>

                        <div className="min-w-0 flex-1">
                            <span className="neo-label bg-[var(--neo-orange)] text-[#171717]">
                                Kelola Sistem
                            </span>

                            <h2
                                id="admin-management-heading"
                                className="mt-4 text-2xl font-black tracking-[-0.04em] sm:text-3xl"
                            >
                                Kelola data utama SkillPath dari satu tempat.
                            </h2>

                            <p className="mt-3 max-w-3xl text-sm leading-7 font-semibold text-muted-foreground sm:text-base">
                                Jurusan, kemampuan, prasyarat, Assessment,
                                materi belajar, dan proyek portofolio dapat
                                dikelola tanpa mencampurkannya dengan hasil
                                belajar masing-masing mahasiswa.
                            </p>
                        </div>
                    </div>
                </section>

                <div className="flex flex-col gap-7">
                    <CareersSection careers={careers} skills={skills} />

                    <SkillsSection
                        skills={skills}
                        prerequisites={prerequisites}
                    />

                    <AssessmentsSection
                        assessments={assessments}
                        careers={careers}
                        skills={skills}
                    />

                    <MaterialsSection materials={materials} skills={skills} />

                    <ProjectsSection
                        projects={projects}
                        careers={careers}
                        skills={skills}
                    />
                </div>
            </div>
        </>
    );
}

AdminIndex.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard Administrator',
            href: '/admin/dashboard',
        },
    ],
};
