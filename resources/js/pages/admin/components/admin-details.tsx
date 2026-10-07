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
            className={[
                'group min-w-0 overflow-hidden rounded-xl',
                'border-2 border-foreground bg-card',
                subtle ? 'p-4' : 'p-4 sm:p-5',
            ].join(' ')}
            onToggle={(event) => {
                setIsOpen(event.currentTarget.open);
            }}
        >
            <summary className="flex min-w-0 cursor-pointer list-none items-start justify-between gap-4 marker:hidden">
                <span className="min-w-0 flex-1">
                    <span className="block text-sm leading-6 font-black wrap-break-word sm:text-base">
                        {title}
                    </span>

                    {meta && (
                        <span className="mt-1 block text-xs leading-5 font-semibold wrap-break-word text-muted-foreground">
                            {meta}
                        </span>
                    )}
                </span>

                <span
                    aria-hidden="true"
                    className="flex size-8 shrink-0 items-center justify-center rounded-lg border-2 border-foreground bg-muted text-lg leading-none font-black transition-transform group-open:rotate-45"
                >
                    +
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
