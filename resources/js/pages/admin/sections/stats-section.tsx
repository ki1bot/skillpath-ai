import type { LucideIcon } from 'lucide-react';
import {
    BookOpenCheck,
    BriefcaseBusiness,
    Database,
    Layers3,
    UsersRound,
    Wrench,
} from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';
import type { AdminStats } from '../types';

export function StatsSection({ stats }: { stats: AdminStats }) {
    const items: Array<{
        label: string;
        value: number;
        icon: LucideIcon;
    }> = [
        {
            label: 'Mahasiswa',
            value: stats.users,
            icon: UsersRound,
        },
        {
            label: 'Karier',
            value: stats.careers,
            icon: BriefcaseBusiness,
        },
        {
            label: 'Skill',
            value: stats.skills,
            icon: Layers3,
        },
        {
            label: 'Materi',
            value: stats.materials,
            icon: BookOpenCheck,
        },
        {
            label: 'Proyek',
            value: stats.projects,
            icon: Wrench,
        },
        {
            label: 'Percobaan asesmen',
            value: stats.assessmentAttempts,
            icon: Database,
        },
    ];

    return (
        <section className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            {items.map(({ label, value, icon: Icon }) => (
                <Card key={label} className="min-w-0">
                    <CardContent className="pt-6">
                        <Icon className="size-5" />

                        <p className="mt-5 text-3xl font-black break-words">
                            {value}
                        </p>

                        <p className="mt-1 text-xs font-black tracking-wide break-words uppercase">
                            {label}
                        </p>
                    </CardContent>
                </Card>
            ))}
        </section>
    );
}
