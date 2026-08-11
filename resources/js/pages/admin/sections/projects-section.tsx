import { Form } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
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

export function ProjectsSection({ projects, careers, skills }: Props) {
    return (
        <AdminPanel
            title="Proyek portofolio"
            description="Proyek digunakan sebagai bukti penerapan kemampuan, bukan sebagai pengganti assesment."
            accentClass="bg-[var(--neo-lime)] text-[#171717]"
        >
            <AdminDetails title="Tambah proyek baru">
                <ProjectForm careers={careers} />
            </AdminDetails>

            <div className="grid gap-4">
                {projects.map((project) => (
                    <AdminDetails
                        key={project.id}
                        title={project.title}
                        meta={`${project.career?.name ?? 'Tanpa karier'} · ${project.skills.length} kebutuhan kemampuan`}
                    >
                        <div className="grid gap-7">
                            <ProjectForm project={project} careers={careers} />

                            <div className="border-t-2 border-foreground/15 pt-6">
                                <h3 className="font-black">
                                    Kebutuhan kemampuan proyek
                                </h3>

                                <Form
                                    action={`/admin/projects/${project.slug}/skills`}
                                    method="post"
                                    className="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                                >
                                    {({ processing }) => (
                                        <>
                                            <SelectField
                                                label="Keahlian"
                                                name="skill_id"
                                                defaultValue=""
                                                required
                                            >
                                                <option value="">
                                                    Pilih keahlian
                                                </option>

                                                {skills.map((skill) => (
                                                    <option
                                                        key={skill.id}
                                                        value={skill.id}
                                                    >
                                                        {skill.name}
                                                    </option>
                                                ))}
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
                                                    disabled={processing}
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
                                            className="flex max-w-full items-center gap-2 rounded-[11px] border-2 border-foreground bg-muted px-3 py-2 text-xs font-black"
                                        >
                                            <span className="min-w-0 break-words">
                                                {skill.name} · minimum{' '}
                                                {skill.pivot?.required_level}
                                            </span>

                                            <DeleteButton
                                                action={`/admin/projects/${project.slug}/skills/${skill.slug}`}
                                                compact
                                            />
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="border-t-2 border-foreground/15 pt-5">
                                <DeleteButton
                                    action={`/admin/projects/${project.slug}`}
                                />
                            </div>
                        </div>
                    </AdminDetails>
                ))}
            </div>
        </AdminPanel>
    );
}
