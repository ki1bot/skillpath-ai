import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import {
    ArrayFields,
    InputField,
    SelectField,
    TextareaField,
} from '../components/form-controls';
import type { Career } from '../types';

export function CareerForm({ career }: { career?: Career }) {
    const action = career ? `/admin/careers/${career.slug}` : '/admin/careers';

    return (
        <Form
            action={action}
            method={career ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing, errors }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <InputField
                            label="Nama jurusan"
                            name="name"
                            defaultValue={career?.name}
                            required
                        />

                        <InputField
                            label="Tingkat"
                            name="difficulty"
                            defaultValue={career?.difficulty ?? 'Menengah'}
                            required
                        />

                        <InputField
                            label="Warna aksen"
                            name="accent"
                            type="color"
                            defaultValue={career?.accent ?? '#C7FF5E'}
                            required
                        />
                    </div>

                    <InputField
                        label="Ringkasan singkat"
                        name="tagline"
                        defaultValue={career?.tagline}
                        required
                    />

                    <TextareaField
                        label="Deskripsi"
                        name="description"
                        rows={4}
                        defaultValue={career?.description}
                        required
                    />

                    <ArrayFields
                        name="responsibilities"
                        label="Bidang utama"
                        values={career?.responsibilities}
                    />

                    <SelectField
                        label="Status"
                        name="is_active"
                        defaultValue={career?.is_active === false ? '0' : '1'}
                    >
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </SelectField>

                    {Object.keys(errors).length > 0 && (
                        <p className="text-sm font-bold text-destructive">
                            Periksa kembali data jurusan yang dimasukkan.
                        </p>
                    )}

                    <Button disabled={processing} className="w-full sm:w-fit">
                        {career ? (
                            <Save className="size-4" />
                        ) : (
                            <Plus className="size-4" />
                        )}

                        {career ? 'Simpan perubahan' : 'Tambah jurusan'}
                    </Button>
                </>
            )}
        </Form>
    );
}
