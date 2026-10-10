import { Form } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    academicPrograms,
    getStudyProgramDefinition,
} from '@/lib/academic-programs';
import { AdminDetails } from '../components/admin-details';
import { AdminPanel } from '../components/admin-panel';
import { DeleteButton } from '../components/delete-button';
import { InputField, SelectField } from '../components/form-controls';
import { ProjectForm } from '../forms/project-form';
import type { Career, Project, Skill } from '../types';

type Props = {
    projects: Project[];
    careers: Career[];
    skills: Skill[];
};

function getProjectArea(project: Project) {
    const program = getStudyProgramDefinition(project.career?.name ?? '');

    if (!program) {
        return null;
    }

    const skillNames = project.skills
        .map((skill) => skill.name)
        .sort((first, second) => first.localeCompare(second));

    const areaIndex = program.areas.findIndex((area) => {
        const areaSkills = [...area.skills].sort((first, second) =>
            first.localeCompare(second),
        );

        return (
            areaSkills.length === skillNames.length &&
            areaSkills.every((skill, index) => skill === skillNames[index])
        );
    });

    return areaIndex < 0
        ? null
        : {
              number: areaIndex + 1,
              name: program.areas[areaIndex].name,
          };
}

export function ProjectsSection({ projects, careers, skills }: Props) {
    const [careerId, setCareerId] = useState('all');

    const customCareers = careers.filter(
        (career) =>
            !academicPrograms.some((program) => program.name === career.name),
    );

    const filteredProjects = projects
        .filter(
            (project) =>
                careerId === 'all' || project.career_id === Number(careerId),
        )
        .sort((first, second) => {
            const careerComparison = (first.career?.name ?? '').localeCompare(
                second.career?.name ?? '',
                'id',
            );

            if (careerComparison !== 0) {
                return careerComparison;
            }

            const firstArea = getProjectArea(first)?.number ?? 99;
            const secondArea = getProjectArea(second)?.number ?? 99;

            if (firstArea !== secondArea) {
                return firstArea - secondArea;
            }

            return first.title.localeCompare(second.title, 'id');
        });

    return (
        <AdminPanel
            title="Proyek portofolio"
            description="Setiap jurusan akademik memiliki tiga proyek. Masing-masing proyek menggunakan tiga kemampuan dari satu bidang."
            accentClass="bg-[var(--neo-lime)] text-[#171717]"
        >
            {customCareers.length > 0 && (
                <AdminDetails title="Tambah proyek untuk jurusan nonakademik">
                    <ProjectForm careers={customCareers} />
                </AdminDetails>
            )}

            <label className="block max-w-md">
                <span className="mb-2 block text-sm font-black">
                    Filter jurusan
                </span>

                <select
                    value={careerId}
                    onChange={(event) => setCareerId(event.target.value)}
                    className="w-full rounded-[10px] border-2 border-foreground bg-card px-3 py-2.5 text-sm font-semibold"
                >
                    <option value="all">Semua jurusan</option>

                    {careers.map((career) => (
                        <option key={career.id} value={career.id}>
                            {career.name}
                        </option>
                    ))}
                </select>
            </label>

            <div className="grid gap-4">
                {filteredProjects.map((project) => {
                    const isAcademicProject = Boolean(
                        getStudyProgramDefinition(project.career?.name ?? ''),
                    );

                    const area = getProjectArea(project);

                    return (
                        <AdminDetails
                            key={project.id}
                            title={
                                area
                                    ? `Proyek ${area.number}: ${project.title}`
                                    : project.title
                            }
                            meta={`${
                                project.career?.name ?? 'Tanpa jurusan'
                            } · ${
                                area?.name ?? 'Bidang belum teridentifikasi'
                            } · ${project.skills.length} kemampuan`}
                        >
                            <div className="grid gap-7">
                                <ProjectForm
                                    project={project}
                                    careers={careers}
                                />

                                <div className="border-t-2 border-foreground/15 pt-6">
                                    <h3 className="font-black">
                                        Kemampuan yang digunakan proyek
                                    </h3>

                                    {isAcademicProject ? (
                                        <div className="mt-4">
                                            <div className="mt-4 grid gap-3 md:grid-cols-3">
                                                {project.skills.map((skill) => (
                                                    <div
                                                        key={skill.id}
                                                        className="rounded-[10px] border-2 border-foreground/15 bg-muted/20 p-4"
                                                    >
                                                        <p className="text-sm font-black">
                                                            {skill.name}
                                                        </p>

                                                        <p className="mt-2 text-xs font-semibold text-muted-foreground">
                                                            Tingkat minimum:{' '}
                                                            {skill.pivot
                                                                ?.required_level ??
                                                                '-'}
                                                        </p>
                                                    </div>
                                                ))}
                                            </div>

                                            {!area && (
                                                <p className="mt-4 text-sm font-bold text-destructive">
                                                    Hubungan kemampuan proyek
                                                    ini tidak sesuai dengan
                                                    salah satu bidang pada
                                                    katalog akademik. Periksa
                                                    datanya sebelum melakukan
                                                    perubahan lain.
                                                </p>
                                            )}
                                        </div>
                                    ) : (
                                        <>
                                            <Form
                                                action={`/admin/projects/${project.slug}/skills`}
                                                method="post"
                                                className="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                                            >
                                                {({ processing }) => (
                                                    <>
                                                        <SelectField
                                                            label="Kemampuan"
                                                            name="skill_id"
                                                            defaultValue=""
                                                            required
                                                        >
                                                            <option value="">
                                                                Pilih kemampuan
                                                            </option>

                                                            {skills.map(
                                                                (skill) => (
                                                                    <option
                                                                        key={
                                                                            skill.id
                                                                        }
                                                                        value={
                                                                            skill.id
                                                                        }
                                                                    >
                                                                        {
                                                                            skill.name
                                                                        }
                                                                    </option>
                                                                ),
                                                            )}
                                                        </SelectField>

                                                        <InputField
                                                            label="Tingkat minimum"
                                                            type="number"
                                                            name="required_level"
                                                            min={1}
                                                            max={100}
                                                            required
                                                        />

                                                        <InputField
                                                            label="Bobot"
                                                            type="number"
                                                            name="weight"
                                                            min={0.1}
                                                            max={3}
                                                            step={0.1}
                                                            required
                                                        />

                                                        <div className="flex items-end">
                                                            <Button
                                                                disabled={
                                                                    processing
                                                                }
                                                                className="w-full"
                                                            >
                                                                <Plus className="size-4" />
                                                                Simpan
                                                            </Button>
                                                        </div>
                                                    </>
                                                )}
                                            </Form>

                                            <div className="mt-5 flex flex-wrap gap-2">
                                                {project.skills.map((skill) => (
                                                    <div
                                                        key={skill.id}
                                                        className="flex items-center gap-2 rounded-[10px] border-2 border-foreground bg-muted px-3 py-2 text-xs font-black"
                                                    >
                                                        <span>
                                                            {skill.name} ·
                                                            minimum{' '}
                                                            {
                                                                skill.pivot
                                                                    ?.required_level
                                                            }
                                                        </span>

                                                        <DeleteButton
                                                            action={`/admin/projects/${project.slug}/skills/${skill.slug}`}
                                                            compact
                                                        />
                                                    </div>
                                                ))}
                                            </div>
                                        </>
                                    )}
                                </div>

                                {!isAcademicProject && (
                                    <div className="border-t-2 border-foreground/15 pt-5">
                                        <DeleteButton
                                            action={`/admin/projects/${project.slug}`}
                                        />
                                    </div>
                                )}
                            </div>
                        </AdminDetails>
                    );
                })}
            </div>

            {filteredProjects.length === 0 && (
                <p className="rounded-[10px] border-2 border-foreground/15 p-5 text-sm font-semibold">
                    Tidak ada proyek pada jurusan yang dipilih.
                </p>
            )}
        </AdminPanel>
    );
}
