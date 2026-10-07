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
import type { Career } from '../types';

export function CareerForm({ career }: { career?: Career }) {
    const action = career ? `/admin/careers/${career.slug}` : '/admin/careers';

    const isAcademicCareer = Boolean(
        career && getStudyProgramDefinition(career.name),
    );

    return (
        <Form
            action={action}
            method={career ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing, errors }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <div>
                            <InputField
                                label="Nama jurusan"
                                name={isAcademicCareer ? undefined : 'name'}
                                defaultValue={career?.name}
                                disabled={isAcademicCareer}
                                required
                            />

                            {isAcademicCareer && (
                                <input
                                    type="hidden"
                                    name="name"
                                    value={career?.name}
                                />
                            )}
                        </div>

                        <div>
                            <InputField
                                label="Tingkat umum jurusan"
                                name="difficulty"
                                defaultValue={career?.difficulty ?? 'Menengah'}
                                maxLength={50}
                                required
                            />

                            <p className="mt-2 text-xs leading-5 text-muted-foreground">
                                Ini bukan tahap Amatir, Menengah, atau Ahli pada
                                jalur belajar.
                            </p>
                        </div>

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
                        maxLength={180}
                        required
                    />

                    <TextareaField
                        label="Deskripsi jurusan"
                        name="description"
                        rows={4}
                        maxLength={4000}
                        defaultValue={career?.description}
                        required
                    />

                    <ArrayFields
                        name="responsibilities"
                        label="Tiga bidang utama jurusan"
                        values={career?.responsibilities}
                    />

                    <SelectField
                        label="Status jurusan"
                        name="is_active"
                        defaultValue={career?.is_active === false ? '0' : '1'}
                    >
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </SelectField>

                    {Object.keys(errors).length > 0 && (
                        <div className="rounded-[10px] border-2 border-destructive/30 p-4">
                            {Object.entries(errors).map(([field, message]) => (
                                <p
                                    key={field}
                                    className="text-sm font-bold text-destructive"
                                >
                                    {message}
                                </p>
                            ))}
                        </div>
                    )}

                    <Button disabled={processing} className="w-full sm:w-fit">
                        {career ? (
                            <Save className="size-4" />
                        ) : (
                            <Plus className="size-4" />
                        )}

                        {processing
                            ? 'Menyimpan...'
                            : career
                              ? 'Simpan perubahan'
                              : 'Tambah jurusan'}
                    </Button>
                </>
            )}
        </Form>
    );
}
