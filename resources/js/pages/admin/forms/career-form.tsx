import { Form } from '@inertiajs/react';
import { Plus, Save, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { getStudyProgramDefinition } from '@/lib/academic-programs';
import {
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

    const initialAreas =
        career?.responsibilities && career.responsibilities.length > 0
            ? career.responsibilities
            : program
              ? program.areas.map((area) => area.name)
              : [''];

    const [areaNames, setAreaNames] = useState<string[]>(initialAreas);

    const updateArea = (index: number, value: string) => {
        setAreaNames((previous) =>
            previous.map((item, itemIndex) =>
                itemIndex === index ? value : item,
            ),
        );
    };

    const addArea = () => {
        setAreaNames((previous) =>
            previous.length < 12 ? [...previous, ''] : previous,
        );
    };

    const removeArea = (index: number) => {
        setAreaNames((previous) =>
            previous.length > 1
                ? previous.filter((_, itemIndex) => itemIndex !== index)
                : previous,
        );
    };

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

                    {isAcademicCareer && career && (
                        <input type="hidden" name="name" value={career.name} />
                    )}

                    <div className="grid min-w-0 gap-4 md:grid-cols-2">
                        <div className="min-w-0">
                            <InputField
                                label="Nama jurusan"
                                name={
                                    isAcademicCareer ? 'display_name' : 'name'
                                }
                                defaultValue={
                                    career
                                        ? career.display_name?.trim() ||
                                          career.name
                                        : ''
                                }
                                maxLength={120}
                                required
                            />
                        </div>

                        <div className="min-w-0">
                            <InputField
                                label="Warna aksen"
                                name="accent"
                                type="color"
                                defaultValue={career?.accent ?? '#C7FF5E'}
                                required
                            />
                        </div>
                    </div>

                    {!isAcademicCareer && career && (
                        <InputField
                            label="Nama tampilan alternatif (opsional)"
                            name="display_name"
                            defaultValue={career.display_name ?? ''}
                            placeholder={career.name}
                            maxLength={120}
                        />
                    )}

                    <TextareaField
                        label="Deskripsi jurusan"
                        name="description"
                        rows={4}
                        maxLength={4000}
                        defaultValue={career?.description ?? ''}
                        required
                    />

                    <div className="grid gap-4">
                        <div>
                            <h3 className="text-sm font-black">
                                Bidang utama jurusan
                            </h3>

                            <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                {isAcademicCareer
                                    ? 'Anda dapat mengedit nama ketiga bidang jurusan.'
                                    : 'Anda dapat menambahkan, mengedit, atau menghapus nama bidang sesuai kebutuhan jurusan.'}
                            </p>
                        </div>

                        <div className="grid gap-3 lg:grid-cols-3">
                            {areaNames.map((areaName, index) => (
                                <div
                                    key={`${career?.id ?? 'new'}-${index}`}
                                    className="min-w-0 rounded-xl border-2 border-foreground/15 bg-muted/20 p-4"
                                >
                                    <p className="mb-3 text-xs font-black tracking-wide text-muted-foreground uppercase">
                                        Bidang {index + 1}
                                    </p>

                                    <InputField
                                        label="Nama bidang"
                                        name="responsibilities[]"
                                        value={areaName}
                                        onChange={(event) =>
                                            updateArea(
                                                index,
                                                event.target.value,
                                            )
                                        }
                                        maxLength={255}
                                        required
                                    />

                                    {program?.areas[index] && (
                                        <div className="mt-4 rounded-lg border border-foreground/15 bg-card p-3">
                                            <p className="text-xs font-black">
                                                Kemampuan yang terhubung
                                            </p>

                                            <div className="mt-2 grid gap-2">
                                                {program.areas[
                                                    index
                                                ].skills.map(
                                                    (skillName, skillIndex) => (
                                                        <p
                                                            key={skillName}
                                                            className="text-xs leading-5 font-medium text-muted-foreground"
                                                        >
                                                            {skillIndex + 1}.{' '}
                                                            {skillName}
                                                        </p>
                                                    ),
                                                )}
                                            </div>
                                        </div>
                                    )}

                                    {!isAcademicCareer &&
                                        areaNames.length > 1 && (
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                size="sm"
                                                className="mt-4"
                                                onClick={() =>
                                                    removeArea(index)
                                                }
                                            >
                                                <Trash2 className="size-4" />
                                                Hapus bidang
                                            </Button>
                                        )}
                                </div>
                            ))}
                        </div>

                        {!isAcademicCareer && areaNames.length < 12 && (
                            <div>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={addArea}
                                >
                                    <Plus className="size-4" />
                                    Tambah bidang
                                </Button>
                            </div>
                        )}
                    </div>

                    {isAcademicCareer ? (
                        <>
                            <input type="hidden" name="is_active" value="1" />

                            <div className="rounded-xl border-2 border-foreground/15 bg-muted/20 p-4">
                                <p className="text-sm font-black">
                                    Status jurusan
                                </p>

                                <p className="mt-2 text-sm leading-6 font-medium text-muted-foreground">
                                    Aktif.
                                </p>
                            </div>
                        </>
                    ) : (
                        <SelectField
                            label="Status jurusan"
                            name="is_active"
                            defaultValue={
                                career?.is_active === false ? '0' : '1'
                            }
                            required
                        >
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </SelectField>
                    )}

                    {Object.keys(errors).length > 0 && (
                        <div className="rounded-xl border-2 border-destructive/30 p-4">
                            <p className="mb-2 text-sm font-black text-destructive">
                                Perubahan belum dapat disimpan
                            </p>

                            <div className="grid gap-2">
                                {Object.entries(errors).map(
                                    ([field, message]) => (
                                        <p
                                            key={field}
                                            className="text-xs leading-5 font-bold text-destructive"
                                        >
                                            {message}
                                        </p>
                                    ),
                                )}
                            </div>
                        </div>
                    )}

                    <div className="flex flex-wrap items-center gap-3">
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
