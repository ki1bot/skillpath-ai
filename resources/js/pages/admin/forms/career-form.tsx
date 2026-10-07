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

type Props = {
    career?: Career;
};

export function CareerForm({ career }: Props) {
    const action = career ? `/admin/careers/${career.slug}` : '/admin/careers';

    const program = getStudyProgramDefinition(career?.name ?? '');

    const isAcademicCareer = Boolean(program);

    return (
        <Form
            action={action}
            method={career ? 'put' : 'post'}
            className="grid min-w-0 gap-5"
        >
            {({ processing, errors }) => (
                <>
                    <input
                        type="hidden"
                        name="difficulty"
                        value="Lintas tahap"
                    />

                    <div className="grid min-w-0 gap-4 md:grid-cols-2">
                        <div>
                            <InputField
                                label="Nama jurusan"
                                name={isAcademicCareer ? undefined : 'name'}
                                defaultValue={career?.name ?? ''}
                                disabled={isAcademicCareer}
                                maxLength={120}
                                required
                            />

                            {isAcademicCareer && (
                                <input
                                    type="hidden"
                                    name="name"
                                    value={career?.name ?? ''}
                                />
                            )}
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
                        defaultValue={career?.tagline ?? ''}
                        maxLength={180}
                        required
                    />

                    <TextareaField
                        label="Deskripsi jurusan"
                        name="description"
                        rows={4}
                        maxLength={4000}
                        defaultValue={career?.description ?? ''}
                        required
                    />

                    {program ? (
                        <div className="grid gap-4">
                            <p className="text-sm font-black">
                                Tiga bidang utama jurusan
                            </p>

                            <div className="grid gap-3 lg:grid-cols-3">
                                {program.areas.map((area, index) => (
                                    <div
                                        key={area.name}
                                        className="rounded-xl border-2 border-foreground/15 bg-muted/20 p-4"
                                    >
                                        <p className="text-xs font-black text-muted-foreground">
                                            Bidang {index + 1}
                                        </p>

                                        <p className="mt-2 text-sm font-black">
                                            {area.name}
                                        </p>

                                        <p className="mt-2 text-xs font-semibold text-muted-foreground">
                                            3 kemampuan · 9 materi
                                        </p>

                                        <input
                                            type="hidden"
                                            name="responsibilities[]"
                                            value={area.name}
                                        />
                                    </div>
                                ))}
                            </div>

                            <p className="text-sm leading-6 font-medium text-muted-foreground">
                                Setiap bidang menjadi dasar satu proyek
                                portofolio. Kesembilan kemampuan jurusan
                                digunakan kembali pada tahap Amatir, Menengah,
                                dan Ahli.
                            </p>
                        </div>
                    ) : (
                        <ArrayFields
                            name="responsibilities"
                            label="Bidang utama jurusan"
                            values={career?.responsibilities}
                        />
                    )}

                    <SelectField
                        label="Status jurusan"
                        name="is_active"
                        defaultValue={career?.is_active === false ? '0' : '1'}
                        required
                    >
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </SelectField>

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
                    </div>
                </>
            )}
        </Form>
    );
}
