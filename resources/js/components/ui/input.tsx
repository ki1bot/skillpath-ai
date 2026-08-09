import * as React from 'react';
import { cn } from '@/lib/utils';

function Input({
    className,
    type,
    ...props
}: React.ComponentProps<'input'>) {
    return (
        <input
            type={type}
            data-slot="input"
            className={cn(
                'h-11 w-full min-w-0 rounded-xl border-2 border-foreground bg-card px-3 py-2 text-base font-medium shadow-[3px_3px_0_var(--foreground)] transition-[box-shadow,transform] outline-none placeholder:text-muted-foreground focus:-translate-y-0.5 focus:ring-2 focus:ring-secondary disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                className,
            )}
            {...props}
        />
    );
}

export { Input };
