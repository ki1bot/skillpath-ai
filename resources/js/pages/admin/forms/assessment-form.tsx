import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    InputField,
    SelectField,
    TextareaField,
} from '../components/form-controls';
import type { Assessment, Career } from '../types';

type Props = {
    assessment?: Assessment;
    careers: Career[];
};

export function AssessmentForm({ assessment, careers }: Props) {
    const action = assessment
        ? `/admin/assessments/${assessment.id}`
        : '/admin/assessments';

    return (
        <Form
            action={action}
            method={assessment ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-2">
                        <SelectField
                            label="Jurusan"
                            name="career_id"
                            defaultValue={assessment?.career_id ?? ''}
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
                            label="Durasi Assesment"
                            type="number"
                            name="duration_minutes"
                            min={5}
                            max={180}
                            defaultValue={assessment?.duration_minutes ?? 20}
                            required
                        />
                    </div>

                    <InputField
                        label="Judul"
                        name="title"
                        defaultValue={assessment?.title}
                        required
                    />

                    <TextareaField
                        label="Deskripsi"
                        name="description"
                        rows={4}
                        defaultValue={assessment?.description}
                        required
                    />

                    <SelectField
                        label="Status"
                        name="is_active"
                        defaultValue={
                            assessment?.is_active === false ? '0' : '1'
                        }
                    >
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </SelectField>

                    <Button disabled={processing} className="w-full sm:w-fit">
                        {assessment ? (
                            <Save className="size-4" />
                        ) : (
                            <Plus className="size-4" />
                        )}

                        {assessment ? 'Simpan Assesment' : 'Tambah Assesment'}
                    </Button>
                </>
            )}
        </Form>
    );
}
