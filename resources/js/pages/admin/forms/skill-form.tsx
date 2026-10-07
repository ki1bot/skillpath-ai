import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { academicPrograms } from '@/lib/academic-programs';
import { InputField, TextareaField } from '../components/form-controls';
import type { Skill } from '../types';

export function SkillForm({ skill }: { skill?: Skill }) {
    const action = skill ? `/admin/skills/${skill.slug}` : '/admin/skills';

    const isAcademicSkill = Boolean(
        skill &&
        academicPrograms.some((program) =>
            program.areas.some((area) => area.skills.includes(skill.name)),
        ),
    );

    return (
        <Form
            action={action}
            method={skill ? 'put' : 'post'}
            className="grid gap-5"
        >
            {({ processing, errors }) => (
                <>
                    <div className="grid gap-4 md:grid-cols-3">
                        <div>
                            <InputField
                                label="Nama kemampuan"
                                name={isAcademicSkill ? undefined : 'name'}
                                defaultValue={skill?.name}
                                disabled={isAcademicSkill}
                                maxLength={120}
                                required
                            />

                            {isAcademicSkill && (
                                <input
                                    type="hidden"
                                    name="name"
                                    value={skill?.name}
                                />
                            )}
                        </div>

                        <InputField
                            label="Kategori kemampuan"
                            name="category"
                            defaultValue={skill?.category}
                            maxLength={120}
                            required
                        />

                        <div>
                            <InputField
                                label="Tingkat umum kemampuan"
                                name="difficulty"
                                defaultValue={skill?.difficulty ?? 'Umum'}
                                maxLength={50}
                                required
                            />

                            <p className="mt-2 text-xs leading-5 text-muted-foreground">
                                Tingkat ini hanya keterangan kemampuan. Tahap
                                Amatir, Menengah, dan Ahli diatur pada materi
                                pembelajaran.
                            </p>
                        </div>
                    </div>

                    <TextareaField
                        label="Deskripsi kemampuan"
                        name="description"
                        rows={4}
                        maxLength={2000}
                        defaultValue={skill?.description}
                        required
                    />

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

                    {isAcademicSkill && (
                        <p className="text-sm leading-6 text-muted-foreground">
                            Nama kemampuan akademik dipertahankan agar hubungan
                            dengan jurusan, Assessment, roadmap, dan proyek
                            tetap sesuai.
                        </p>
                    )}

                    <Button disabled={processing} className="w-full sm:w-fit">
                        {skill ? (
                            <Save className="size-4" />
                        ) : (
                            <Plus className="size-4" />
                        )}

                        {processing
                            ? 'Menyimpan...'
                            : skill
                              ? 'Simpan perubahan'
                              : 'Tambah kemampuan'}
                    </Button>
                </>
            )}
        </Form>
    );
}
