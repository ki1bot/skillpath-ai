import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { InputField, TextareaField } from '../components/form-controls';
import type { Skill } from '../types';

export function SkillForm({ skill }: { skill?: Skill }) {
    const action = skill ? `/admin/skills/${skill.slug}` : '/admin/skills';

    return (
        <Form
            action={action}
            method={skill ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <InputField
                            label="Nama skill"
                            name="name"
                            defaultValue={skill?.name}
                            required
                        />

                        <InputField
                            label="Kategori"
                            name="category"
                            defaultValue={skill?.category}
                            required
                        />

                        <InputField
                            label="Tingkat kesulitan"
                            name="difficulty"
                            defaultValue={skill?.difficulty ?? 'Dasar'}
                            required
                        />
                    </div>

                    <TextareaField
                        label="Deskripsi"
                        name="description"
                        rows={4}
                        defaultValue={skill?.description}
                        required
                    />

                    <Button disabled={processing} className="w-full sm:w-fit">
                        {skill ? (
                            <Save className="size-4" />
                        ) : (
                            <Plus className="size-4" />
                        )}

                        {skill ? 'Simpan perubahan' : 'Tambah skill'}
                    </Button>
                </>
            )}
        </Form>
    );
}
