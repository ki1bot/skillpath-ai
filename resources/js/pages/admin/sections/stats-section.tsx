import type { LucideIcon } from 'lucide-react';
import {
    BookOpenCheck,
    BriefcaseBusiness,
    Database,
    Layers3,
    UsersRound,
    Wrench,
} from 'lucide-react';
import type { AdminStats } from '../types';

export function StatsSection({ stats }: { stats: AdminStats }) {
    const items: Array<{
        label: string;
        value: number;
        icon: LucideIcon;
        accent: string;
    }> = [
        {
            label: 'Mahasiswa',
            value: stats.users,
            icon: UsersRound,
            accent: 'bg-[var(--neo-lime)]',
        },
        {
            label: 'Karier',
            value: stats.careers,
            icon: BriefcaseBusiness,
            accent: 'bg-[var(--neo-blue)]',
        },
        {
            label: 'Keahlian',
            value: stats.skills,
            icon: Layers3,
            accent: 'bg-[var(--neo-yellow)]',
        },
        {
            label: 'Materi',
            value: stats.materials,
            icon: BookOpenCheck,
            accent: 'bg-[var(--neo-orange)]',
        },
        {
            label: 'Proyek',
            value: stats.projects,
            icon: Wrench,
            accent: 'bg-[var(--neo-pink)]',
        },
        {
            label: 'Percobaan asesmen',
            value: stats.assessmentAttempts,
            icon: Database,
            accent: 'bg-[#fffdf7]',
        },
    ];

    return (
        <section className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            {items.map(({ label, value, icon: Icon, accent }) => (
                <article
                    key={label}
                    className={`neo-interactive min-w-0 rounded-[14px] border-2 border-[#171717] p-5 text-[#171717] shadow-[4px_4px_0_var(--neo-shadow-color)] ${accent}`}
                >
                    <div className="flex items-start justify-between gap-3">
                        <span className="flex size-10 items-center justify-center rounded-[9px] border-2 border-[#171717] bg-[#fffdf7]">
                            <Icon className="size-5" />
                        </span>

                        <span className="text-[10px] font-black tracking-[0.12em] uppercase opacity-60">
                            Data
                        </span>
                    </div>

                    <p className="mt-6 text-4xl font-black tracking-[-0.04em]">
                        {value}
                    </p>

                    <p className="mt-1 text-xs font-black tracking-wide uppercase">
                        {label}
                    </p>
                </article>
            ))}
        </section>
    );
}
