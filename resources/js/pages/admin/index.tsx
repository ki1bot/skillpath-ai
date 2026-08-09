import { Head } from '@inertiajs/react';
import { Database, ShieldCheck } from 'lucide-react';
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
            <Head title="Administrator" />

            <div className="neo-page flex flex-col gap-7 py-6 sm:py-8 lg:py-10">
                <section className="neo-hero neo-accent-orange border-[#171717]">
                    <div className="flex flex-col justify-between gap-7 lg:flex-row lg:items-end">
                        <div className="flex max-w-4xl flex-col gap-5 sm:flex-row sm:items-start">
                            <span className="flex size-14 shrink-0 items-center justify-center rounded-[13px] border-2 border-[#171717] bg-[#fffdf7] shadow-[4px_4px_0_#171717]">
                                <ShieldCheck className="size-7" />
                            </span>

                            <div>
                                <span className="neo-label bg-[#fffdf7]">
                                    Ruang kerja administrator
                                </span>

                                <h1 className="mt-5 text-4xl font-black tracking-[-0.045em] sm:text-5xl">
                                    Kelola standar sistem dengan struktur yang
                                    jelas.
                                </h1>

                                <p className="mt-4 max-w-3xl text-sm leading-7 font-semibold sm:text-base">
                                    Karier, skill, hubungan prasyarat, asesmen,
                                    materi belajar, dan proyek portofolio
                                    dikelola dari satu tempat tanpa
                                    mencampurkannya dengan hasil individual
                                    mahasiswa.
                                </p>
                            </div>
                        </div>

                        <div className="flex w-fit items-center gap-3 rounded-[12px] border-2 border-[#171717] bg-[#fffdf7] px-4 py-3 text-[#171717]">
                            <Database className="size-5" />

                            <div>
                                <p className="text-[10px] font-black tracking-[0.14em] uppercase">
                                    Sumber utama
                                </p>

                                <p className="text-sm font-black">PostgreSQL</p>
                            </div>
                        </div>
                    </div>
                </section>

                <StatsSection stats={stats} />

                <CareersSection careers={careers} skills={skills} />

                <SkillsSection skills={skills} prerequisites={prerequisites} />

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
        </>
    );
}

AdminIndex.layout = {
    breadcrumbs: [
        {
            title: 'Administrator',
            href: '/admin',
        },
    ],
};
