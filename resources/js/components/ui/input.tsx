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
                'h-11 w-full min-w-0 rounded-[11px] border-2 border-foreground bg-card px-3 py-2 text-base font-semibold text-foreground shadow-[3px_3px_0_var(--neo-shadow-color)] transition-[transform,box-shadow,border-color] outline-none placeholder:font-medium placeholder:text-muted-foreground focus:-translate-y-[1px] focus:ring-2 focus:ring-secondary disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                className,
            )}
            {...props}
        />
    );
}

export { Input };
