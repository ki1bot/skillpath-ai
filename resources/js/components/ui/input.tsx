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
                'h-11 w-full min-w-0 rounded-[10px] border-2 border-foreground bg-card px-3 py-2 text-base font-semibold text-foreground shadow-[2px_2px_0_var(--neo-shadow-color)] transition-[box-shadow,border-color,background-color] outline-none placeholder:font-medium placeholder:text-muted-foreground focus:border-foreground focus:bg-card focus:ring-2 focus:ring-secondary disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                className,
            )}
            {...props}
        />
    );
}

export { Input };
