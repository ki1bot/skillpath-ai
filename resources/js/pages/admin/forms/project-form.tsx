import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
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

    return (
        <Form
            action={action}
            method={project ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <SelectField
                            label="Jurusan"
                            name="career_id"
                            defaultValue={project?.career_id ?? ''}
                            required
                        >
                            <option value="">Pilih jurusan</option>

                            {careers.map((career) => (
                                <option key={career.id} value={career.id}>
                                    {career.name}
                                </option>
                            ))}
                        </SelectField>

                        <InputField
                            label="Tingkat kesulitan"
                            name="difficulty"
                            defaultValue={project?.difficulty ?? 'Pemula'}
                            required
                        />

                        <InputField
                            label="Estimasi jam"
                            type="number"
                            name="estimated_hours"
                            min={1}
                            defaultValue={project?.estimated_hours ?? 8}
                            required
                        />
                    </div>

                    <InputField
                        label="Judul proyek"
                        name="title"
                        defaultValue={project?.title}
                        required
                    />

                    <TextareaField
                        label="Ringkasan"
                        name="summary"
                        rows={4}
                        defaultValue={project?.summary}
                        required
                    />

                    <TextareaField
                        label="Masalah yang diselesaikan"
                        name="problem_statement"
                        rows={4}
                        defaultValue={project?.problem_statement}
                        required
                    />

                    <ArrayFields
                        name="minimum_features"
                        label="Fitur minimum"
                        values={project?.minimum_features}
                    />

                    <ArrayFields
                        name="stretch_features"
                        label="Fitur pengembangan"
                        values={project?.stretch_features}
                        required={false}
                    />

                    <ArrayFields
                        name="completion_criteria"
                        label="Kriteria penyelesaian"
                        values={project?.completion_criteria}
                    />

                    <Button disabled={processing} className="w-full sm:w-fit">
                        {project ? (
                            <Save className="size-4" />
                        ) : (
                            <Plus className="size-4" />
                        )}

                        {project ? 'Simpan proyek' : 'Tambah proyek'}
                    </Button>
                </>
            )}
        </Form>
    );
}
