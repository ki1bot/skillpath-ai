import { Form } from '@inertiajs/react';
import { Plus, Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { academicPrograms } from '@/lib/academic-programs';
import { InputField, TextareaField } from '../components/form-controls';
import type { Skill } from '../types';

type Props = {
    skill?: Skill;
};

const learningStages = ['Amatir', 'Menengah', 'Ahli'] as const;

export function SkillForm({ skill }: Props) {
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
                        <div className="min-w-0">
                            <InputField
                                label="Nama kemampuan"
                                name={isAcademicSkill ? undefined : 'name'}
                                defaultValue={skill?.name ?? ''}
                                disabled={isAcademicSkill}
                                maxLength={120}
                                required
                            />

                            {isAcademicSkill && (
                                <input
                                    type="hidden"
                                    name="name"
                                    value={skill?.name ?? ''}
                                />
                            )}

                            {errors.name && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {errors.name}
                                </p>
                            )}
                        </div>

                        <div className="min-w-0">
                            <InputField
                                label="Bidang kemampuan"
                                name="category"
                                defaultValue={skill?.category ?? ''}
                                maxLength={120}
                                required
                            />

                            {errors.category && (
                                <p className="mt-2 text-xs font-bold text-destructive">
                                    {errors.category}
                                </p>
                            )}
                        </div>
                    </div>

                    <div className="min-w-0 rounded-xl border-2 border-foreground/20 bg-muted/30 p-4 sm:p-5">
                        <p className="text-sm font-black">
                            Tahap pembelajaran kemampuan
                        </p>

                        <div className="mt-3 grid grid-cols-3 gap-2">
                            {learningStages.map((stage, index) => (
                                <div
                                    key={stage}
                                    className="min-w-0 rounded-lg border-2 border-foreground bg-card p-3 text-center"
                                >
                                    <p className="text-[10px] font-black tracking-wide text-muted-foreground uppercase">
                                        Tahap {index + 1}
                                    </p>

                                    <p className="mt-1 text-sm font-black">
                                        {stage}
                                    </p>
                                </div>
                            ))}
                        </div>

                        <p className="mt-4 text-sm leading-6 font-medium text-muted-foreground">
                            Satu kemampuan dipelajari pada ketiga tahap. Materi
                            dan tugasnya berbeda untuk setiap tahap. Pilihan
                            tahap berada pada pengelolaan materi, bukan pada
                            data kemampuan.
                        </p>
                    </div>

                    <div className="min-w-0">
                        <TextareaField
                            label="Deskripsi kemampuan"
                            name="description"
                            rows={4}
                            maxLength={2000}
                            defaultValue={skill?.description ?? ''}
                            required
                        />

                        {errors.description && (
                            <p className="mt-2 text-xs font-bold text-destructive">
                                {errors.description}
                            </p>
                        )}
                    </div>

                    {isAcademicSkill && (
                        <p className="rounded-lg border border-foreground/15 bg-muted/20 p-3 text-sm leading-6 font-medium text-muted-foreground">
                            Nama kemampuan akademik tidak dapat diubah karena
                            terhubung dengan jurusan, Assessment, roadmap, dan
                            proyek. Deskripsi serta bidang kemampuannya tetap
                            dapat diperbarui.
                        </p>
                    )}

                    {Object.keys(errors).length > 0 && (
                        <div className="rounded-lg border-2 border-destructive/30 p-3">
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

                    <div className="flex flex-wrap justify-start">
                        <Button
                            type="submit"
                            disabled={processing}
                            className="w-full sm:w-auto"
                        >
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
                    </div>
                </>
            )}
        </Form>
    );
}
