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

const stages = ['Amatir', 'Menengah', 'Ahli'] as const;

export function MaterialForm({ material, skills }: Props) {
    const action = material
        ? `/admin/materials/${material.slug}`
        : '/admin/materials';

    const isAcademicMaterial = Boolean(
        material &&
        /^(belajar|penguatan)-(amatir|menengah|ahli)-/.test(material.slug),
    );

    return (
        <Form
            action={action}
            method={material ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing, errors }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <div>
                            <SelectField
                                label="Kemampuan"
                                name={
                                    isAcademicMaterial ? undefined : 'skill_id'
                                }
                                defaultValue={material?.skill_id ?? ''}
                                disabled={isAcademicMaterial}
                                required
                            >
                                <option value="">Pilih kemampuan</option>

                                {skills.map((skill) => (
                                    <option key={skill.id} value={skill.id}>
                                        {skill.name}
                                    </option>
                                ))}
                            </SelectField>

                            {isAcademicMaterial && (
                                <input
                                    type="hidden"
                                    name="skill_id"
                                    value={material?.skill_id}
                                />
                            )}

                            {errors.skill_id && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {errors.skill_id}
                                </p>
                            )}
                        </div>

                        <div>
                            <SelectField
                                label="Tahap pembelajaran"
                                name={
                                    isAcademicMaterial
                                        ? undefined
                                        : 'difficulty'
                                }
                                defaultValue={material?.difficulty ?? 'Amatir'}
                                disabled={isAcademicMaterial}
                                required
                            >
                                {stages.map((stage) => (
                                    <option key={stage} value={stage}>
                                        {stage}
                                    </option>
                                ))}
                            </SelectField>

                            {isAcademicMaterial && (
                                <input
                                    type="hidden"
                                    name="difficulty"
                                    value={material?.difficulty}
                                />
                            )}

                            {errors.difficulty && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {errors.difficulty}
                                </p>
                            )}
                        </div>

                        <div>
                            <InputField
                                label="Estimasi waktu (menit)"
                                type="number"
                                name="estimated_minutes"
                                min={15}
                                max={3000}
                                defaultValue={material?.estimated_minutes ?? 90}
                                required
                            />

                            {errors.estimated_minutes && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {errors.estimated_minutes}
                                </p>
                            )}
                        </div>
                    </div>

                    {isAcademicMaterial && (
                        <div className="rounded-[10px] border-2 border-foreground/15 bg-muted/30 p-4">
                            <p className="text-sm font-black">
                                Materi ini sudah menjadi bagian dari roadmap.
                            </p>

                            <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                Kemampuan dan tahapnya tetap agar susunan
                                Amatir, Menengah, dan Ahli tidak berubah. Anda
                                tetap dapat memperbarui tugas, penjelasan,
                                referensi, dan waktu pengerjaan.
                            </p>
                        </div>
                    )}

                    <div>
                        <InputField
                            label="Judul materi"
                            name="title"
                            defaultValue={material?.title}
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
                            label="Ringkasan materi"
                            name="summary"
                            rows={4}
                            maxLength={3000}
                            defaultValue={material?.summary}
                            required
                        />

                        {errors.summary && (
                            <p className="mt-2 text-xs font-bold text-destructive">
                                {errors.summary}
                            </p>
                        )}
                    </div>

                    <div>
                        <ArrayFields
                            name="learning_objectives"
                            label="Tujuan pembelajaran"
                            values={material?.learning_objectives}
                        />

                        {errors.learning_objectives && (
                            <p className="mt-2 text-xs font-bold text-destructive">
                                {errors.learning_objectives}
                            </p>
                        )}
                    </div>

                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <InputField
                                label="Judul referensi"
                                name="resource_title"
                                defaultValue={material?.resource_title ?? ''}
                                maxLength={180}
                            />

                            {errors.resource_title && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {errors.resource_title}
                                </p>
                            )}
                        </div>

                        <div>
                            <InputField
                                label="URL referensi"
                                type="url"
                                name="resource_url"
                                defaultValue={material?.resource_url ?? ''}
                                maxLength={1000}
                            />

                            {errors.resource_url && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {errors.resource_url}
                                </p>
                            )}
                        </div>
                    </div>

                    <div>
                        <TextareaField
                            label="Tugas praktik mahasiswa"
                            name="practice_task"
                            rows={9}
                            maxLength={4000}
                            defaultValue={material?.practice_task}
                            required
                        />

                        <p className="mt-2 text-xs leading-5 text-muted-foreground">
                            Jelaskan pekerjaan yang harus dibuat, hasil yang
                            dikumpulkan, dan hal yang akan diperiksa admin.
                            Mahasiswa mengirim hasil melalui folder Google
                            Drive. Nilai minimal kelulusan adalah 70.
                        </p>

                        {errors.practice_task && (
                            <p className="mt-2 text-xs font-bold text-destructive">
                                {errors.practice_task}
                            </p>
                        )}
                    </div>

                    <Button disabled={processing} className="w-full sm:w-fit">
                        {material ? (
                            <Save className="size-4" />
                        ) : (
                            <Plus className="size-4" />
                        )}

                        {processing
                            ? 'Menyimpan...'
                            : material
                              ? 'Simpan perubahan materi'
                              : 'Tambah materi'}
                    </Button>
                </>
            )}
        </Form>
    );
}
