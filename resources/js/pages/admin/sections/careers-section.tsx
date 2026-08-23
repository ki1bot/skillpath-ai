import { Form } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { AdminDetails } from '../components/admin-details';
import { AdminPanel } from '../components/admin-panel';
import { DeleteButton } from '../components/delete-button';
import { InputField, SelectField } from '../components/form-controls';
import { CareerForm } from '../forms/career-form';
import type { Career, Skill } from '../types';

type Props = {
    careers: Career[];
    skills: Skill[];
};

export function CareersSection({ careers, skills }: Props) {
    return (
        <AdminPanel
            title="Jurusan & standar kemampuan"
            description="Kelola lima jurusan dan kemampuan yang digunakan sebagai target penguasaan di dalam SkillPath."
            accentClass="bg-[var(--neo-lime)] text-[#171717]"
        >
            <AdminDetails title="Tambah jurusan baru">
                <CareerForm />
            </AdminDetails>

            <div className="grid gap-4">
                {careers.map((career) => (
                    <AdminDetails
                        key={career.id}
                        title={career.name}
                        meta={`${career.skills.length} kemampuan`}
                    >
                        <div className="grid gap-7">
                            <CareerForm career={career} />

                            <div className="border-t-2 border-foreground/15 pt-6">
                                <h3 className="text-base font-black">
                                    Standar kemampuan jurusan
                                </h3>

                                <p className="mt-1 text-sm text-muted-foreground">
                                    Tentukan target penguasaan dan bobot setiap
                                    kemampuan untuk jurusan ini.
                                </p>

                                <Form
                                    action={`/admin/careers/${career.slug}/skills`}
                                    method="post"
                                    className="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                                >
                                    {({ processing }) => (
                                        <>
                                            <SelectField
                                                label="Kemampuan"
                                                name="skill_id"
                                                defaultValue=""
                                                required
                                            >
                                                <option value="">
                                                    Pilih kemampuan
                                                </option>

                                                {skills.map((skill) => (
                                                    <option
                                                        key={skill.id}
                                                        value={skill.id}
                                                    >
                                                        {skill.name}
                                                    </option>
                                                ))}
                                            </SelectField>

                                            <InputField
                                                label="Target penguasaan"
                                                type="number"
                                                name="target_level"
                                                min={1}
                                                max={100}
                                                placeholder="0-100"
                                                required
                                            />

                                            <InputField
                                                label="Bobot"
                                                type="number"
                                                name="importance_weight"
                                                min={0.1}
                                                max={3}
                                                step={0.05}
                                                placeholder="1.00"
                                                required
                                            />

                                            <div className="grid gap-3">
                                                <SelectField
                                                    label="Kebutuhan"
                                                    name="is_required"
                                                    defaultValue="1"
                                                >
                                                    <option value="1">
                                                        Utama
                                                    </option>

                                                    <option value="0">
                                                        Pendukung
                                                    </option>
                                                </SelectField>

                                                <Button
                                                    disabled={processing}
                                                    className="w-full"
                                                >
                                                    <Plus className="size-4" />
                                                    Simpan
                                                </Button>
                                            </div>
                                        </>
                                    )}
                                </Form>

                                <div className="mt-5 flex flex-wrap gap-2">
                                    {career.skills.map((skill) => (
                                        <div
                                            key={skill.id}
                                            className="flex max-w-full items-center gap-2 rounded-[11px] border-2 border-foreground bg-muted px-3 py-2 text-xs font-black"
                                        >
                                            <span className="min-w-0 break-words">
                                                {skill.name} · target{' '}
                                                {skill.pivot?.target_level}
                                            </span>

                                            <DeleteButton
                                                action={`/admin/careers/${career.slug}/skills/${skill.slug}`}
                                                compact
                                            />
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="border-t-2 border-foreground/15 pt-5">
                                <DeleteButton
                                    action={`/admin/careers/${career.slug}`}
                                />
                            </div>
                        </div>
                    </AdminDetails>
                ))}
            </div>
        </AdminPanel>
    );
}
