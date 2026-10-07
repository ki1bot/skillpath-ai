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

    const isAcademicProject = Boolean(
        project?.career && getStudyProgramDefinition(project.career.name),
    );

    return (
        <Form
            action={action}
            method={project ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing, errors }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
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

                            {errors.career_id && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {errors.career_id}
                                </p>
                            )}
                        </div>

                        <div>
                            <InputField
                                label="Tingkat pengerjaan proyek"
                                name="difficulty"
                                defaultValue={project?.difficulty ?? 'Menengah'}
                                maxLength={50}
                                required
                            />

                            <p className="mt-2 text-xs leading-5 text-muted-foreground">
                                Tingkat proyek berbeda dari tahap Amatir,
                                Menengah, dan Ahli pada roadmap.
                            </p>

                            {errors.difficulty && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {errors.difficulty}
                                </p>
                            )}
                        </div>

                        <div>
                            <InputField
                                label="Estimasi pengerjaan (jam)"
                                type="number"
                                name="estimated_hours"
                                min={1}
                                max={500}
                                defaultValue={project?.estimated_hours ?? 8}
                                required
                            />

                            {errors.estimated_hours && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {errors.estimated_hours}
                                </p>
                            )}
                        </div>
                    </div>

                    {isAcademicProject && (
                        <div className="rounded-[10px] border-2 border-foreground/15 bg-muted/30 p-4">
                            <p className="text-sm font-black">
                                Proyek akademik
                            </p>

                            <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                Proyek ini mewakili satu bidang dari jurusan
                                yang dipilih dan menggunakan tiga kemampuan.
                                Admin dapat memperbarui isi tugas, fitur wajib,
                                dan kriteria penilaian tanpa memindahkan proyek
                                ke jurusan lain.
                            </p>
                        </div>
                    )}

                    <div>
                        <InputField
                            label="Judul proyek"
                            name="title"
                            defaultValue={project?.title}
                            maxLength={180}
                            required
                        />

                        {errors.title && (
                            <p className="mt-2 text-xs font-bold text-destructive">
                                {errors.title}
                            </p>
                        )}
                    </div>

                    <div>
                        <TextareaField
                            label="Ringkasan proyek"
                            name="summary"
                            rows={4}
                            maxLength={3000}
                            defaultValue={project?.summary}
                            required
                        />

                        {errors.summary && (
                            <p className="mt-2 text-xs font-bold text-destructive">
                                {errors.summary}
                            </p>
                        )}
                    </div>

                    <div>
                        <TextareaField
                            label="Masalah yang harus diselesaikan"
                            name="problem_statement"
                            rows={6}
                            maxLength={4000}
                            defaultValue={project?.problem_statement}
                            required
                        />

                        {errors.problem_statement && (
                            <p className="mt-2 text-xs font-bold text-destructive">
                                {errors.problem_statement}
                            </p>
                        )}
                    </div>

                    <div>
                        <ArrayFields
                            name="minimum_features"
                            label="Pekerjaan wajib"
                            values={project?.minimum_features}
                        />

                        {errors.minimum_features && (
                            <p className="mt-2 text-xs font-bold text-destructive">
                                {errors.minimum_features}
                            </p>
                        )}
                    </div>

                    <div>
                        <ArrayFields
                            name="stretch_features"
                            label="Pengembangan tambahan (opsional)"
                            values={project?.stretch_features}
                            required={false}
                        />

                        {errors.stretch_features && (
                            <p className="mt-2 text-xs font-bold text-destructive">
                                {errors.stretch_features}
                            </p>
                        )}
                    </div>

                    <div>
                        <ArrayFields
                            name="completion_criteria"
                            label="Kriteria pemeriksaan admin"
                            values={project?.completion_criteria}
                        />

                        {errors.completion_criteria && (
                            <p className="mt-2 text-xs font-bold text-destructive">
                                {errors.completion_criteria}
                            </p>
                        )}
                    </div>

                    <div className="rounded-[10px] border-2 border-foreground/15 p-4">
                        <p className="text-sm font-black">
                            Pemeriksaan hasil proyek
                        </p>

                        <p className="mt-2 text-sm leading-6 text-muted-foreground">
                            Mahasiswa mengumpulkan hasil pekerjaan melalui
                            folder Google Drive. Admin memeriksa bagian wajib
                            dan kriteria penyelesaian yang telah ditentukan.
                            Proyek dinyatakan lulus apabila memperoleh nilai
                            minimal 80.
                        </p>
                    </div>

                    <Button
                        type="submit"
                        disabled={processing}
                        className="w-full sm:w-fit"
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
                </>
            )}
        </Form>
    );
}
