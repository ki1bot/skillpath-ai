import { Form } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    academicPrograms,
    getStudyProgramDefinition,
} from '@/lib/academic-programs';
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

const EXTRA_TAB = '__additional__';

const academicSkillNames = new Set(
    academicPrograms.flatMap((program) =>
        program.areas.flatMap((area) => area.skills),
    ),
);

export function SkillsSection({ skills, prerequisites }: Props) {
    const [selectedProgram, setSelectedProgram] = useState(
        academicPrograms[0]?.name ?? EXTRA_TAB,
    );

    const [search, setSearch] = useState('');
    const [extraPage, setExtraPage] = useState(1);

    const selectedDefinition = getStudyProgramDefinition(selectedProgram);

    const normalizedSearch = search.trim().toLocaleLowerCase('id');

    const additionalSkills = skills
        .filter((skill) => !academicSkillNames.has(skill.name))
        .filter(
            (skill) =>
                skill.name.toLocaleLowerCase('id').includes(normalizedSearch) ||
                skill.category
                    .toLocaleLowerCase('id')
                    .includes(normalizedSearch),
        )
        .sort((first, second) => first.name.localeCompare(second.name, 'id'));

    const additionalPageCount = Math.max(
        1,
        Math.ceil(additionalSkills.length / 12),
    );

    const currentAdditionalPage = Math.min(extraPage, additionalPageCount);

    const visibleAdditionalSkills = additionalSkills.slice(
        (currentAdditionalPage - 1) * 12,
        currentAdditionalPage * 12,
    );

    const changeProgram = (name: string) => {
        setSelectedProgram(name);
        setSearch('');
        setExtraPage(1);
    };

    return (
        <AdminPanel
            title="Kemampuan dan prasyarat"
            description="Setiap jurusan memiliki tiga bidang dan sembilan kemampuan. Semua kemampuan akademik dipelajari pada tahap Amatir, Menengah, dan Ahli."
            accentClass="bg-[var(--neo-blue)] text-[#171717]"
        >
            <div className="rounded-xl border-2 border-foreground/15 bg-muted/20 p-4">
                <p className="text-sm font-black">
                    Cara kerja kemampuan di SkillPath
                </p>

                <div className="mt-4 grid gap-3 sm:grid-cols-3">
                    {[
                        ['Tahap 1', 'Amatir'],
                        ['Tahap 2', 'Menengah'],
                        ['Tahap 3', 'Ahli'],
                    ].map(([label, stage]) => (
                        <div
                            key={stage}
                            className="rounded-lg border-2 border-foreground bg-card p-3"
                        >
                            <p className="text-xs font-black text-muted-foreground">
                                {label}
                            </p>

                            <p className="mt-1 font-black">{stage}</p>
                        </div>
                    ))}
                </div>
            </div>

            <div className="flex flex-wrap gap-2">
                {academicPrograms.map((program) => (
                    <button
                        key={program.name}
                        type="button"
                        aria-pressed={selectedProgram === program.name}
                        onClick={() => changeProgram(program.name)}
                        className={[
                            'rounded-lg border-2 border-foreground',
                            'px-3 py-2 text-xs font-black',
                            'transition-colors',
                            selectedProgram === program.name
                                ? 'bg-secondary text-[#171717]'
                                : 'bg-card text-foreground hover:bg-muted',
                        ].join(' ')}
                    >
                        {program.name}
                    </button>
                ))}

                <button
                    type="button"
                    aria-pressed={selectedProgram === EXTRA_TAB}
                    onClick={() => changeProgram(EXTRA_TAB)}
                    className={[
                        'rounded-lg border-2 border-foreground',
                        'px-3 py-2 text-xs font-black',
                        'transition-colors',
                        selectedProgram === EXTRA_TAB
                            ? 'bg-secondary text-[#171717]'
                            : 'bg-card text-foreground hover:bg-muted',
                    ].join(' ')}
                >
                    Di luar katalog akademik
                </button>
            </div>

            <div className="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                <label className="grid min-w-0 gap-2">
                    <span className="text-sm font-black">Cari kemampuan</span>

                    <input
                        type="search"
                        value={search}
                        onChange={(event) => {
                            setSearch(event.target.value);
                            setExtraPage(1);
                        }}
                        placeholder="Cari nama atau bidang kemampuan..."
                        className="h-11 w-full min-w-0 rounded-lg border-2 border-foreground bg-card px-3 text-sm font-semibold"
                    />
                </label>

                <p className="text-sm font-semibold text-muted-foreground">
                    {selectedDefinition
                        ? '3 bidang · 9 kemampuan'
                        : `${additionalSkills.length} kemampuan tambahan`}
                </p>
            </div>

            {selectedDefinition ? (
                <div className="grid gap-6">
                    {selectedDefinition.areas.map((area, areaIndex) => {
                        const matchingSkills = area.skills
                            .map((name) =>
                                skills.find((skill) => skill.name === name),
                            )
                            .filter(
                                (skill): skill is Skill => skill !== undefined,
                            )
                            .filter(
                                (skill) =>
                                    skill.name
                                        .toLocaleLowerCase('id')
                                        .includes(normalizedSearch) ||
                                    skill.category
                                        .toLocaleLowerCase('id')
                                        .includes(normalizedSearch) ||
                                    area.name
                                        .toLocaleLowerCase('id')
                                        .includes(normalizedSearch),
                            );

                        if (normalizedSearch && matchingSkills.length === 0) {
                            return null;
                        }

                        return (
                            <section key={area.name} className="min-w-0">
                                <div className="mb-3 flex flex-wrap items-center justify-between gap-3 border-b-2 border-foreground/15 pb-3">
                                    <div className="min-w-0">
                                        <p className="text-xs font-black tracking-wider text-muted-foreground uppercase">
                                            Bidang {areaIndex + 1}
                                        </p>

                                        <h3 className="mt-1 text-lg font-black">
                                            {area.name}
                                        </h3>
                                    </div>

                                    <span className="rounded-lg border-2 border-foreground bg-muted px-3 py-1.5 text-xs font-black">
                                        3 kemampuan
                                    </span>
                                </div>

                                <div className="grid min-w-0 gap-3">
                                    {matchingSkills.map((skill) => (
                                        <AdminDetails
                                            key={`${selectedProgram}-${skill.id}`}
                                            title={skill.name}
                                            meta={`${skill.category} · Amatir / Menengah / Ahli`}
                                        >
                                            <SkillForm skill={skill} />
                                        </AdminDetails>
                                    ))}
                                </div>
                            </section>
                        );
                    })}
                </div>
            ) : (
                <div className="grid gap-3">
                    <AdminDetails
                        title="Tambah kemampuan di luar katalog"
                        meta="Digunakan untuk data tambahan yang tidak termasuk sembilan kemampuan wajib jurusan."
                    >
                        <SkillForm />
                    </AdminDetails>

                    {visibleAdditionalSkills.map((skill) => (
                        <AdminDetails
                            key={skill.id}
                            title={skill.name}
                            meta={skill.category}
                        >
                            <div className="grid gap-5">
                                <SkillForm skill={skill} />

                                <div className="border-t-2 border-foreground/15 pt-4">
                                    <DeleteButton
                                        action={`/admin/skills/${skill.slug}`}
                                    />
                                </div>
                            </div>
                        </AdminDetails>
                    ))}

                    {additionalSkills.length === 0 && (
                        <p className="rounded-lg border-2 border-foreground/15 p-4 text-sm font-semibold text-muted-foreground">
                            Tidak ada kemampuan tambahan yang sesuai pencarian.
                        </p>
                    )}

                    {additionalPageCount > 1 && (
                        <div className="flex flex-wrap items-center justify-between gap-3">
                            <p className="text-sm font-semibold text-muted-foreground">
                                Halaman {currentAdditionalPage} dari{' '}
                                {additionalPageCount}
                            </p>

                            <div className="flex gap-2">
                                <button
                                    type="button"
                                    disabled={currentAdditionalPage === 1}
                                    onClick={() =>
                                        setExtraPage(currentAdditionalPage - 1)
                                    }
                                    className="rounded-lg border-2 border-foreground px-3 py-2 text-xs font-black disabled:opacity-40"
                                >
                                    Sebelumnya
                                </button>

                                <button
                                    type="button"
                                    disabled={
                                        currentAdditionalPage ===
                                        additionalPageCount
                                    }
                                    onClick={() =>
                                        setExtraPage(currentAdditionalPage + 1)
                                    }
                                    className="rounded-lg border-2 border-foreground px-3 py-2 text-xs font-black disabled:opacity-40"
                                >
                                    Berikutnya
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            )}

            <AdminDetails
                title="Hubungan prasyarat kemampuan"
                meta="Pengaturan hubungan antarkemampuan untuk analisis gap dan prioritas belajar."
            >
                <div className="grid gap-5">
                    <Form
                        action="/admin/prerequisites"
                        method="post"
                        className="grid gap-4"
                    >
                        {({ processing }) => (
                            <>
                                <div className="grid gap-4 md:grid-cols-2">
                                    <SelectField
                                        label="Kemampuan tujuan"
                                        name="skill_id"
                                        defaultValue=""
                                        required
                                    >
                                        <option value="">
                                            Pilih kemampuan tujuan
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

                                    <SelectField
                                        label="Kemampuan prasyarat"
                                        name="prerequisite_skill_id"
                                        defaultValue=""
                                        required
                                    >
                                        <option value="">
                                            Pilih kemampuan prasyarat
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
                                        label="Faktor hubungan"
                                        type="number"
                                        name="factor"
                                        min={1}
                                        max={2}
                                        step={0.05}
                                        defaultValue={1.15}
                                        required
                                    />
                                </div>

                                <div>
                                    <Button type="submit" disabled={processing}>
                                        <Plus className="size-4" />
                                        Simpan hubungan
                                    </Button>
                                </div>
                            </>
                        )}
                    </Form>

                    <div className="grid gap-3">
                        {prerequisites.map((item) => (
                            <div
                                key={item.id}
                                className="flex min-w-0 flex-wrap items-center justify-between gap-3 rounded-lg border-2 border-foreground/15 bg-muted/20 p-3"
                            >
                                <span className="min-w-0 flex-1 text-sm leading-6 font-semibold">
                                    <strong>{item.prerequisite_name}</strong>
                                    {' → '}
                                    {item.skill_name}
                                    {' · Faktor '}
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
            </AdminDetails>
        </AdminPanel>
    );
}
