import { useState } from 'react';
import { AdminDetails } from '../components/admin-details';
import { AdminPanel } from '../components/admin-panel';
import { MaterialForm } from '../forms/material-form';
import type { Material, Skill } from '../types';

type Props = {
    materials: Material[];
    skills: Skill[];
};

const PAGE_SIZE = 10;

const stageOrder: Record<string, number> = {
    Amatir: 1,
    Menengah: 2,
    Ahli: 3,
};

export function MaterialsSection({ materials, skills }: Props) {
    const [stage, setStage] = useState('all');
    const [materialType, setMaterialType] = useState('core');
    const [skillId, setSkillId] = useState('all');
    const [status, setStatus] = useState('active');
    const [page, setPage] = useState(1);

    const activeCore = materials.filter(
        (material) => material.is_active && material.material_type === 'core',
    ).length;

    const activeReinforcement = materials.filter(
        (material) =>
            material.is_active && material.material_type === 'reinforcement',
    ).length;

    const inactive = materials.filter((material) => !material.is_active).length;

    const filteredMaterials = materials
        .filter((material) => {
            if (stage !== 'all' && material.difficulty !== stage) {
                return false;
            }

            if (
                materialType !== 'all' &&
                material.material_type !== materialType
            ) {
                return false;
            }

            if (skillId !== 'all' && material.skill_id !== Number(skillId)) {
                return false;
            }

            if (status === 'active' && !material.is_active) {
                return false;
            }

            if (status === 'inactive' && material.is_active) {
                return false;
            }

            return true;
        })
        .sort((first, second) => {
            const firstStage = stageOrder[first.difficulty] ?? 99;
            const secondStage = stageOrder[second.difficulty] ?? 99;

            if (firstStage !== secondStage) {
                return firstStage - secondStage;
            }

            const skillComparison = (first.skill?.name ?? '').localeCompare(
                second.skill?.name ?? '',
                'id',
            );

            if (skillComparison !== 0) {
                return skillComparison;
            }

            return first.title.localeCompare(second.title, 'id');
        });

    const pageCount = Math.max(
        1,
        Math.ceil(filteredMaterials.length / PAGE_SIZE),
    );

    const currentPage = Math.min(page, pageCount);

    const visibleMaterials = filteredMaterials.slice(
        (currentPage - 1) * PAGE_SIZE,
        currentPage * PAGE_SIZE,
    );

    const selectClass =
        'w-full rounded-[10px] border-2 border-foreground bg-card px-3 py-2.5 text-sm font-semibold';

    return (
        <AdminPanel
            title="Materi belajar"
            description="Kelola tugas praktik untuk setiap kemampuan pada tahap Amatir, Menengah, dan Ahli."
            accentClass="bg-[var(--neo-yellow)] text-[#171717]"
        >
            <div className="grid gap-3 sm:grid-cols-3">
                <div className="rounded-[10px] border-2 border-foreground/15 p-4">
                    <p className="text-xs font-black text-muted-foreground uppercase">
                        Materi utama aktif
                    </p>

                    <p className="mt-2 text-2xl font-black">{activeCore}</p>
                </div>

                <div className="rounded-[10px] border-2 border-foreground/15 p-4">
                    <p className="text-xs font-black text-muted-foreground uppercase">
                        Penguatan aktif
                    </p>

                    <p className="mt-2 text-2xl font-black">
                        {activeReinforcement}
                    </p>
                </div>

                <div className="rounded-[10px] border-2 border-foreground/15 p-4">
                    <p className="text-xs font-black text-muted-foreground uppercase">
                        Materi nonaktif
                    </p>

                    <p className="mt-2 text-2xl font-black">{inactive}</p>
                </div>
            </div>

            <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <label className="block">
                    <span className="mb-2 block text-sm font-black">Tahap</span>

                    <select
                        value={stage}
                        className={selectClass}
                        onChange={(event) => {
                            setStage(event.target.value);
                            setPage(1);
                        }}
                    >
                        <option value="all">Semua tahap</option>
                        <option value="Amatir">Amatir</option>
                        <option value="Menengah">Menengah</option>
                        <option value="Ahli">Ahli</option>
                    </select>
                </label>

                <label className="block">
                    <span className="mb-2 block text-sm font-black">
                        Jenis materi
                    </span>

                    <select
                        value={materialType}
                        className={selectClass}
                        onChange={(event) => {
                            setMaterialType(event.target.value);
                            setPage(1);
                        }}
                    >
                        <option value="core">Materi utama</option>
                        <option value="reinforcement">Penguatan</option>
                        <option value="all">Semua jenis</option>
                    </select>
                </label>

                <label className="block">
                    <span className="mb-2 block text-sm font-black">
                        Kemampuan
                    </span>

                    <select
                        value={skillId}
                        className={selectClass}
                        onChange={(event) => {
                            setSkillId(event.target.value);
                            setPage(1);
                        }}
                    >
                        <option value="all">Semua kemampuan</option>

                        {skills.map((skill) => (
                            <option key={skill.id} value={skill.id}>
                                {skill.name}
                            </option>
                        ))}
                    </select>
                </label>

                <label className="block">
                    <span className="mb-2 block text-sm font-black">
                        Status
                    </span>

                    <select
                        value={status}
                        className={selectClass}
                        onChange={(event) => {
                            setStatus(event.target.value);
                            setPage(1);
                        }}
                    >
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                        <option value="all">Semua</option>
                    </select>
                </label>
            </div>

            <p className="text-sm font-semibold text-muted-foreground">
                Menampilkan {visibleMaterials.length} dari{' '}
                {filteredMaterials.length} materi yang sesuai filter.
            </p>

            <div className="grid gap-4">
                {visibleMaterials.map((material) => (
                    <AdminDetails
                        key={material.id}
                        title={material.title}
                        meta={`${material.difficulty} · ${
                            material.material_type === 'reinforcement'
                                ? 'Penguatan'
                                : 'Materi utama'
                        } · ${material.skill?.name ?? 'Tanpa kemampuan'} · ${
                            material.estimated_minutes
                        } menit`}
                    >
                        {material.is_active ? (
                            <div className="grid gap-5">
                                <MaterialForm
                                    material={material}
                                    skills={skills}
                                />
                            </div>
                        ) : (
                            <div className="rounded-[10px] border-2 border-foreground/15 p-4">
                                <p className="text-sm font-black">
                                    Materi nonaktif
                                </p>

                                <p className="mt-2 text-sm leading-6 text-muted-foreground">
                                    Materi ini tidak dipakai untuk membentuk
                                    roadmap baru. Data tetap disimpan agar
                                    riwayat belajar yang lama tidak hilang.
                                </p>
                            </div>
                        )}
                    </AdminDetails>
                ))}
            </div>

            {filteredMaterials.length === 0 && (
                <div className="rounded-[10px] border-2 border-foreground/15 p-6 text-center">
                    <p className="font-black">
                        Tidak ada materi yang sesuai dengan filter.
                    </p>
                </div>
            )}

            {pageCount > 1 && (
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <p className="text-sm font-semibold text-muted-foreground">
                        Halaman {currentPage} dari {pageCount}
                    </p>

                    <div className="flex gap-2">
                        <button
                            type="button"
                            disabled={currentPage === 1}
                            onClick={() => setPage(currentPage - 1)}
                            className="rounded-[9px] border-2 border-foreground px-4 py-2 text-sm font-black disabled:opacity-40"
                        >
                            Sebelumnya
                        </button>

                        <button
                            type="button"
                            disabled={currentPage === pageCount}
                            onClick={() => setPage(currentPage + 1)}
                            className="rounded-[9px] border-2 border-foreground px-4 py-2 text-sm font-black disabled:opacity-40"
                        >
                            Berikutnya
                        </button>
                    </div>
                </div>
            )}
        </AdminPanel>
    );
}
