import { Head } from '@inertiajs/react';
import { ShieldCheck } from 'lucide-react';
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

            <div className="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <section className="neo-card overflow-hidden bg-[var(--neo-orange)] p-5 text-[#171717] sm:p-7 lg:p-8">
                    <div className="flex flex-col gap-5 sm:flex-row sm:items-start">
                        <div className="flex size-14 shrink-0 items-center justify-center rounded-2xl border-2 border-[#171717] bg-[#fffdf7] shadow-[4px_4px_0_#171717]">
                            <ShieldCheck className="size-7" />
                        </div>

                        <div className="min-w-0">
                            <span className="neo-label bg-[#fffdf7]">
                                Ruang kerja administrator
                            </span>

                            <h1 className="neo-heading mt-5 max-w-4xl text-4xl sm:text-5xl">
                                Kelola standar sistem, bukan hasil pengguna.
                            </h1>

                            <p className="mt-4 max-w-3xl text-sm leading-relaxed font-semibold sm:text-base">
                                Karier, skill, hubungan prasyarat, asesmen,
                                materi belajar, dan proyek portofolio dikelola
                                dari satu tempat dengan struktur data yang tetap
                                dapat diaudit.
                            </p>
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
