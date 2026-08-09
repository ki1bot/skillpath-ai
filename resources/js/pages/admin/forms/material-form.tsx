import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    ArrayFields,
    InputField,
    SelectField,
    TextareaField,
} from '../components/form-controls';
import type { Material, Skill } from '../types';

type Props = {
    material?: Material;
    skills: Skill[];
};

export function MaterialForm({ material, skills }: Props) {
    const action = material
        ? `/admin/materials/${material.slug}`
        : '/admin/materials';

    return (
        <Form
            action={action}
            method={material ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <SelectField
                            label="Keahlian"
                            name="skill_id"
                            defaultValue={material?.skill_id ?? ''}
                            required
                        >
                            <option value="">Pilih keahlian</option>

                            {skills.map((skill) => (
                                <option key={skill.id} value={skill.id}>
                                    {skill.name}
                                </option>
                            ))}
                        </SelectField>

                        <InputField
                            label="Tingkat kesulitan"
                            name="difficulty"
                            defaultValue={material?.difficulty ?? 'Dasar'}
                            required
                        />

                        <InputField
                            label="Estimasi waktu"
                            type="number"
                            name="estimated_minutes"
                            min={15}
                            defaultValue={material?.estimated_minutes ?? 90}
                            required
                        />
                    </div>

                    <InputField
                        label="Judul materi"
                        name="title"
                        defaultValue={material?.title}
                        required
                    />

                    <TextareaField
                        label="Ringkasan"
                        name="summary"
                        rows={4}
                        defaultValue={material?.summary}
                        required
                    />

                    <ArrayFields
                        name="learning_objectives"
                        label="Tujuan pembelajaran"
                        values={material?.learning_objectives}
                    />

                    <div className="grid gap-4 md:grid-cols-2">
                        <InputField
                            label="Judul referensi"
                            name="resource_title"
                            defaultValue={material?.resource_title ?? ''}
                        />

                        <InputField
                            label="URL referensi"
                            type="url"
                            name="resource_url"
                            defaultValue={material?.resource_url ?? ''}
                        />
                    </div>

                    <TextareaField
                        label="Latihan praktik"
                        name="practice_task"
                        rows={4}
                        defaultValue={material?.practice_task}
                        required
                    />

                    <TextareaField
                        label="Pertanyaan evaluasi"
                        name="quiz_question"
                        rows={3}
                        defaultValue={material?.quiz_question}
                        required
                    />

                    <div className="grid gap-4 sm:grid-cols-2">
                        {(['A', 'B', 'C', 'D'] as const).map((letter) => (
                            <InputField
                                key={letter}
                                label={`Pilihan ${letter}`}
                                name="quiz_options[]"
                                defaultValue={material?.quiz_options?.[letter]}
                                required
                            />
                        ))}
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <SelectField
                            label="Jawaban benar"
                            name="quiz_answer"
                            defaultValue={material?.quiz_answer ?? 'A'}
                        >
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </SelectField>

                        <InputField
                            label="Penjelasan jawaban"
                            name="quiz_explanation"
                            defaultValue={material?.quiz_explanation ?? ''}
                        />
                    </div>

                    <Button disabled={processing} className="w-full sm:w-fit">
                        {material ? (
                            <Save className="size-4" />
                        ) : (
                            <Plus className="size-4" />
                        )}

                        {material ? 'Simpan materi' : 'Tambah materi'}
                    </Button>
                </>
            )}
        </Form>
    );
}
