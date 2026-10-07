import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { getStudyProgramDefinition } from '@/lib/academic-programs';
import {
    ArrayFields,
    InputField,
    SelectField,
    TextareaField,
} from '../components/form-controls';
import type { Career, Project } from '../types';

type Props = {
    project?: Project;
    careers: Career[];
};

export function ProjectForm({ project, careers }: Props) {
    const action = project
        ? `/admin/projects/${project.slug}`
        : '/admin/projects';

    const program = getStudyProgramDefinition(project?.career?.name ?? '');

    const projectSkillNames =
        project?.skills
            .map((skill) => skill.name)
            .sort((first, second) => first.localeCompare(second)) ?? [];

    const areaIndex =
        program?.areas.findIndex((area) => {
            const areaSkillNames = [...area.skills].sort((first, second) =>
                first.localeCompare(second),
            );

            return (
                areaSkillNames.length === projectSkillNames.length &&
                areaSkillNames.every(
                    (name, index) => name === projectSkillNames[index],
                )
            );
        }) ?? -1;

    const isAcademicProject = Boolean(program);

    const projectNumber = areaIndex >= 0 ? areaIndex + 1 : null;

    const projectLabel = projectNumber
        ? `Proyek ${projectNumber}`
        : (project?.difficulty ?? 'Proyek tambahan');

    return (
        <Form
            action={action}
            method={project ? 'put' : 'post'}
            className="grid min-w-0 gap-5"
        >
            {({ processing, errors }) => (
                <>
                    <input
                        type="hidden"
                        name="difficulty"
                        value={projectLabel}
                    />

                    <div className="grid min-w-0 gap-4 md:grid-cols-2">
                        <div>
                            <SelectField
                                label="Jurusan"
                                name={
                                    isAcademicProject ? undefined : 'career_id'
                                }
                                defaultValue={project?.career_id ?? ''}
                                disabled={isAcademicProject}
                                required
                            >
                                <option value="">Pilih jurusan</option>

                                {careers.map((career) => (
                                    <option key={career.id} value={career.id}>
                                        {career.name}
                                    </option>
                                ))}
                            </SelectField>

                            {isAcademicProject && (
                                <input
                                    type="hidden"
                                    name="career_id"
                                    value={project?.career_id}
                                />
                            )}
                        </div>

                        <InputField
                            label="Estimasi pengerjaan (jam)"
                            type="number"
                            name="estimated_hours"
                            min={1}
                            max={500}
                            defaultValue={project?.estimated_hours ?? 8}
                            required
                        />
                    </div>

                    {program && (
                        <div className="rounded-xl border-2 border-foreground/15 bg-muted/20 p-4">
                            <p className="text-sm font-black">{projectLabel}</p>

                            <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                {areaIndex >= 0
                                    ? `Proyek ini mencakup bidang ${
                                          program.areas[areaIndex].name
                                      } dan tiga kemampuan di dalamnya.`
                                    : 'Hubungan kemampuan proyek ini perlu diperiksa karena belum cocok dengan salah satu bidang akademik.'}
                            </p>

                            <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                Admin dapat memperbarui tugas dan kriteria
                                pemeriksaan, tetapi tidak memindahkan proyek ke
                                jurusan lain.
                            </p>
                        </div>
                    )}

                    <InputField
                        label="Judul proyek"
                        name="title"
                        defaultValue={project?.title ?? ''}
                        maxLength={180}
                        required
                    />

                    <TextareaField
                        label="Ringkasan proyek"
                        name="summary"
                        rows={4}
                        maxLength={3000}
                        defaultValue={project?.summary ?? ''}
                        required
                    />

                    <TextareaField
                        label="Masalah yang harus diselesaikan"
                        name="problem_statement"
                        rows={6}
                        maxLength={4000}
                        defaultValue={project?.problem_statement ?? ''}
                        required
                    />

                    <ArrayFields
                        name="minimum_features"
                        label="Pekerjaan wajib"
                        values={project?.minimum_features}
                    />

                    <ArrayFields
                        name="stretch_features"
                        label="Pengembangan tambahan (opsional)"
                        values={project?.stretch_features}
                        required={false}
                    />

                    <ArrayFields
                        name="completion_criteria"
                        label="Kriteria pemeriksaan admin"
                        values={project?.completion_criteria}
                    />

                    <div className="rounded-xl border-2 border-foreground/15 p-4">
                        <p className="text-sm font-black">
                            Pengumpulan dan penilaian proyek
                        </p>

                        <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                            Mahasiswa mengumpulkan hasil pekerjaan melalui
                            folder Google Drive. Admin memeriksa fitur wajib,
                            dokumentasi, pengujian, dan kriteria penyelesaian.
                            Proyek dinyatakan lulus dengan nilai minimal 80.
                        </p>
                    </div>

                    {Object.keys(errors).length > 0 && (
                        <div className="rounded-xl border-2 border-destructive/30 p-4">
                            {Object.entries(errors).map(([field, message]) => (
                                <p
                                    key={field}
                                    className="text-xs leading-5 font-bold text-destructive"
                                >
                                    {message}
                                </p>
                            ))}
                        </div>
                    )}

                    <div>
                        <Button
                            type="submit"
                            disabled={processing}
                            className="w-full sm:w-auto"
                        >
                            {project ? (
                                <Save className="size-4" />
                            ) : (
                                <Plus className="size-4" />
                            )}

                            {processing
                                ? 'Menyimpan...'
                                : project
                                  ? 'Simpan perubahan proyek'
                                  : 'Tambah proyek'}
                        </Button>
                    </div>
                </>
            )}
        </Form>
    );
}
