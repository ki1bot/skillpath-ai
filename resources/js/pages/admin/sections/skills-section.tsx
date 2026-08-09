import { Form } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { AdminDetails } from '../components/admin-details';
import { AdminPanel } from '../components/admin-panel';
import { DeleteButton } from '../components/delete-button';
import { InputField, SelectField } from '../components/form-controls';
import { SkillForm } from '../forms/skill-form';
import type { Prerequisite, Skill } from '../types';

type Props = {
    skills: Skill[];
    prerequisites: Prerequisite[];
};

export function SkillsSection({ skills, prerequisites }: Props) {
    return (
        <AdminPanel
            title="Skill & prasyarat"
            description="Pisahkan definisi skill dari hubungan prasyarat agar urutan roadmap dapat dihitung secara nyata."
            accentClass="bg-[var(--neo-blue)] text-[#171717]"
        >
            <AdminDetails title="Tambah skill baru">
                <SkillForm />
            </AdminDetails>

            <div className="grid gap-4 md:grid-cols-2">
                {skills.map((skill) => (
                    <AdminDetails
                        key={skill.id}
                        title={skill.name}
                        meta={skill.category}
                    >
                        <div className="grid gap-5">
                            <SkillForm skill={skill} />

                            <DeleteButton
                                action={`/admin/skills/${skill.slug}`}
                            />
                        </div>
                    </AdminDetails>
                ))}
            </div>

            <div className="border-t-2 border-foreground pt-6">
                <h3 className="text-lg font-black">Hubungan prasyarat</h3>

                <p className="mt-1 text-sm text-muted-foreground">
                    Tentukan skill apa yang harus dikuasai sebelum skill lain.
                </p>

                <Form
                    action="/admin/prerequisites"
                    method="post"
                    className="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                >
                    {({ processing }) => (
                        <>
                            <SelectField
                                label="Skill tujuan"
                                name="skill_id"
                                defaultValue=""
                                required
                            >
                                <option value="">Pilih skill tujuan</option>

                                {skills.map((skill) => (
                                    <option key={skill.id} value={skill.id}>
                                        {skill.name}
                                    </option>
                                ))}
                            </SelectField>

                            <SelectField
                                label="Skill prasyarat"
                                name="prerequisite_skill_id"
                                defaultValue=""
                                required
                            >
                                <option value="">Pilih prasyarat</option>

                                {skills.map((skill) => (
                                    <option key={skill.id} value={skill.id}>
                                        {skill.name}
                                    </option>
                                ))}
                            </SelectField>

                            <InputField
                                label="Faktor"
                                type="number"
                                name="factor"
                                min={1}
                                max={2}
                                step={0.05}
                                defaultValue={1.15}
                                required
                            />

                            <div className="flex items-end">
                                <Button
                                    disabled={processing}
                                    className="w-full"
                                >
                                    <Plus className="size-4" />
                                    Simpan relasi
                                </Button>
                            </div>
                        </>
                    )}
                </Form>

                <div className="mt-5 grid gap-3 md:grid-cols-2">
                    {prerequisites.map((item) => (
                        <div
                            key={item.id}
                            className="flex min-w-0 items-center justify-between gap-3 rounded-xl border-2 border-foreground bg-muted p-3 text-sm"
                        >
                            <span className="min-w-0 break-words">
                                <strong>{item.prerequisite_name}</strong>
                                {' → '}
                                {item.skill_name}
                                {' · ×'}
                                {item.factor}
                            </span>

                            <DeleteButton
                                action={`/admin/prerequisites/${item.id}`}
                                compact
                            />
                        </div>
                    ))}
                </div>
            </div>
        </AdminPanel>
    );
}
