import type { LucideIcon } from 'lucide-react';
import { BookOpenCheck, GraduationCap, Layers3, Wrench } from 'lucide-react';
import type { AdminStats } from '../types';

export function StatsSection({ stats }: { stats: AdminStats }) {
    const items: Array<{
        label: string;
        value: number;
        icon: LucideIcon;
        accent: string;
    }> = [
        {
            label: 'Jurusan',
            value: stats.careers,
            icon: GraduationCap,
            accent: 'bg-[var(--neo-blue)]',
        },
        {
            label: 'Materi',
            value: stats.skills,
            icon: Layers3,
            accent: 'bg-[var(--neo-yellow)]',
        },
        {
            label: 'Soal',
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
    ];

    return (
        <section
            aria-label="Statistik data administrator"
            className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
        >
            {items.map(({ label, value, icon: Icon, accent }) => (
                <article
                    key={label}
                    className={`neo-interactive flex min-h-[170px] min-w-0 flex-col justify-between rounded-[14px] border-2 border-[#171717] p-5 text-[#171717] shadow-[4px_4px_0_var(--neo-shadow-color)] sm:p-6 ${accent}`}
                >
                    <div className="flex items-start justify-between gap-3">
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-[9px] border-2 border-[#171717] bg-[#fffdf7]">
                            <Icon className="size-5" />
                        </span>

                        <span className="text-[10px] font-black tracking-[0.12em] uppercase opacity-60">
                            Data
                        </span>
                    </div>

                    <div className="mt-6">
                        <p className="text-4xl font-black tracking-[-0.04em]">
                            {value}
                        </p>

                        <p className="mt-1 text-xs font-black tracking-wide uppercase">
                            {label}
                        </p>
                    </div>
                </article>
            ))}
        </section>
    );
}
