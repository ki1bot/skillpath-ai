import type { LucideIcon } from 'lucide-react';
import { Monitor, Moon, Sun } from 'lucide-react';
import type { HTMLAttributes } from 'react';
import type { Appearance } from '@/hooks/use-appearance';
import { useAppearance } from '@/hooks/use-appearance';
import { cn } from '@/lib/utils';

export default function AppearanceToggleTab({
    className = '',
    ...props
}: HTMLAttributes<HTMLDivElement>) {
    const { appearance, updateAppearance } = useAppearance();

    const tabs: {
        value: Appearance;
        icon: LucideIcon;
        label: string;
    }[] = [
        {
            value: 'light',
            icon: Sun,
            label: 'Terang',
        },
        {
            value: 'dark',
            icon: Moon,
            label: 'Gelap',
        },
        {
            value: 'system',
            icon: Monitor,
            label: 'Sistem',
        },
    ];

    return (
        <div
            className={cn('grid w-full grid-cols-3 gap-2', className)}
            {...props}
        >
            {tabs.map(({ value, icon: Icon, label }) => (
                <button
                    key={value}
                    type="button"
                    onClick={() => updateAppearance(value)}
                    className={cn(
                        'flex min-w-0 items-center justify-center gap-2 rounded-xl border-2 border-foreground px-3 py-2.5 text-sm font-black transition-[transform,box-shadow,background-color] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none',
                        appearance === value
                            ? 'bg-secondary text-[#171717] shadow-[3px_3px_0_var(--foreground)]'
                            : 'bg-card text-foreground shadow-[3px_3px_0_var(--foreground)] hover:bg-muted',
                    )}
                >
                    <Icon className="size-4 shrink-0" />

                    <span className="truncate">{label}</span>
                </button>
            ))}
        </div>
    );
}
