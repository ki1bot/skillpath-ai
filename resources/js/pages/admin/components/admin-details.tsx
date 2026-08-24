import { useState } from 'react';
import type { ReactNode } from 'react';

type Props = {
    title: string;
    meta?: string;
    children: ReactNode;
    subtle?: boolean;
};

export function AdminDetails({ title, meta, children, subtle = false }: Props) {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <details
            className={
                subtle
                    ? 'group rounded-2xl border-2 border-foreground/40 bg-card p-4'
                    : 'group neo-card-flat bg-card p-4 sm:p-5'
            }
            onToggle={(event) => {
                setIsOpen(event.currentTarget.open);
            }}
        >
            <summary className="flex cursor-pointer list-none items-center justify-between gap-4 font-black marker:hidden">
                <span className="min-w-0 break-words">{title}</span>

                <span className="flex shrink-0 items-center gap-3">
                    {meta && (
                        <span className="hidden text-xs text-muted-foreground sm:inline">
                            {meta}
                        </span>
                    )}

                    <span className="flex size-7 items-center justify-center rounded-lg border-2 border-foreground bg-muted text-lg leading-none transition-transform group-open:rotate-45">
                        +
                    </span>
                </span>
            </summary>

            {isOpen && (
                <div className="mt-5 min-w-0 border-t-2 border-foreground/15 pt-5">
                    {children}
                </div>
            )}
        </details>
    );
}
