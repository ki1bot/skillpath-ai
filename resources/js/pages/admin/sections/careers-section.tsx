import { Form } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { getStudyProgramDefinition } from '@/lib/academic-programs';
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
            title="Jurusan dan standar kemampuan"
            description="Setiap jurusan akademik menggunakan tiga bidang dan sembilan kemampuan. Hubungan kemampuan yang sudah menjadi bagian katalog tetap dipertahankan."
            accentClass="bg-[var(--neo-lime)] text-[#171717]"
        >
            <AdminDetails title="Tambah jurusan nonakademik">
                <CareerForm />
            </AdminDetails>

            <div className="grid gap-4">
                {careers.map((career) => {
                    const isAcademicCareer = Boolean(
                        getStudyProgramDefinition(career.name),
                    );

                    const program = getStudyProgramDefinition(career.name);

                    return (
                        <AdminDetails
                            key={career.id}
                            title={career.name}
                            meta={`${career.skills.length} kemampuan · ${
                                isAcademicCareer
                                    ? 'Katalog akademik'
                                    : 'Jurusan tambahan'
                            }`}
                        >
                            <div className="grid gap-7">
                                <CareerForm career={career} />

                                <div className="border-t-2 border-foreground/15 pt-6">
                                    <h3 className="text-base font-black">
                                        Standar kemampuan jurusan
                                    </h3>

                                    {isAcademicCareer && program ? (
                                        <>
                                            <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                                Jurusan ini menggunakan tiga
                                                bidang, masing-masing dengan
                                                tiga kemampuan. Kesembilan
                                                kemampuan tersebut digunakan
                                                dalam Assessment dan diulang
                                                pada tahap Amatir, Menengah,
                                                serta Ahli.
                                            </p>

                                            <div className="mt-5 grid gap-4 lg:grid-cols-3">
                                                {program.areas.map((area) => (
                                                    <div
                                                        key={area.name}
                                                        className="rounded-[10px] border-2 border-foreground/15 bg-muted/20 p-4"
                                                    >
                                                        <p className="text-sm font-black">
                                                            {area.name}
                                                        </p>

                                                        <div className="mt-3 grid gap-2">
                                                            {area.skills.map(
                                                                (
                                                                    name,
                                                                    index,
                                                                ) => {
                                                                    const skill =
                                                                        career.skills.find(
                                                                            (
                                                                                item,
                                                                            ) =>
                                                                                item.name ===
                                                                                name,
                                                                        );

                                                                    return (
                                                                        <p
                                                                            key={
                                                                                name
                                                                            }
                                                                            className="text-xs leading-5 font-semibold"
                                                                        >
                                                                            {index +
                                                                                1}
                                                                            .{' '}
                                                                            {
                                                                                name
                                                                            }
                                                                            {skill
                                                                                ?.pivot
                                                                                ?.target_level !==
                                                                                undefined &&
                                                                                ` · Target ${skill.pivot.target_level}`}
                                                                        </p>
                                                                    );
                                                                },
                                                            )}
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </>
                                    ) : (
                                        <>
                                            <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                                Tentukan kemampuan, target
                                                penguasaan, dan bobot untuk
                                                jurusan tambahan.
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

                                                            {skills.map(
                                                                (skill) => (
                                                                    <option
                                                                        key={
                                                                            skill.id
                                                                        }
                                                                        value={
                                                                            skill.id
                                                                        }
                                                                    >
                                                                        {
                                                                            skill.name
                                                                        }
                                                                    </option>
                                                                ),
                                                            )}
                                                        </SelectField>

                                                        <InputField
                                                            label="Target penguasaan"
                                                            type="number"
                                                            name="target_level"
                                                            min={1}
                                                            max={100}
                                                            required
                                                        />

                                                        <InputField
                                                            label="Bobot"
                                                            type="number"
                                                            name="importance_weight"
                                                            min={0.1}
                                                            max={3}
                                                            step={0.05}
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
                                                                disabled={
                                                                    processing
                                                                }
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
                                                        className="flex items-center gap-2 rounded-[11px] border-2 border-foreground bg-muted px-3 py-2 text-xs font-black"
                                                    >
                                                        <span>
                                                            {skill.name} ·
                                                            target{' '}
                                                            {
                                                                skill.pivot
                                                                    ?.target_level
                                                            }
                                                        </span>

                                                        <DeleteButton
                                                            action={`/admin/careers/${career.slug}/skills/${skill.slug}`}
                                                            compact
                                                        />
                                                    </div>
                                                ))}
                                            </div>
                                        </>
                                    )}
                                </div>

                                {!isAcademicCareer && (
                                    <div className="border-t-2 border-foreground/15 pt-5">
                                        <DeleteButton
                                            action={`/admin/careers/${career.slug}`}
                                        />
                                    </div>
                                )}
                            </div>
                        </AdminDetails>
                    );
                })}
            </div>
        </AdminPanel>
    );
}
