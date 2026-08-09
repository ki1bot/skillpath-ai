import { AdminDetails } from '../components/admin-details';
import { AdminPanel } from '../components/admin-panel';
import { DeleteButton } from '../components/delete-button';
import { MaterialForm } from '../forms/material-form';
import type { Material, Skill } from '../types';

type Props = {
    materials: Material[];
    skills: Skill[];
};

export function MaterialsSection({ materials, skills }: Props) {
    return (
        <AdminPanel
            title="Materi belajar"
            description="Materi terhubung langsung ke skill dan harus memiliki tujuan, latihan, serta evaluasi."
            accentClass="bg-[var(--neo-yellow)] text-[#171717]"
        >
            <AdminDetails title="Tambah materi baru">
                <MaterialForm skills={skills} />
            </AdminDetails>

            <div className="grid gap-4">
                {materials.map((material) => (
                    <AdminDetails
                        key={material.id}
                        title={material.title}
                        meta={`${material.skill?.name ?? 'Tanpa skill'} · ${material.estimated_minutes} menit`}
                    >
                        <div className="grid gap-5">
                            <MaterialForm material={material} skills={skills} />

                            <DeleteButton
                                action={`/admin/materials/${material.slug}`}
                            />
                        </div>
                    </AdminDetails>
                ))}
            </div>
        </AdminPanel>
    );
}
