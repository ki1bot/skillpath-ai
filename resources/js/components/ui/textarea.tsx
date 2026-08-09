import * as React from 'react';
import { cn } from '@/lib/utils';

function Textarea({
    className,
    ...props
}: React.ComponentProps<'textarea'>) {
    return (
        <textarea
            data-slot="textarea"
            className={cn(
                'min-h-28 w-full min-w-0 resize-y rounded-[11px] border-2 border-foreground bg-card px-3 py-3 text-base font-medium text-foreground shadow-[3px_3px_0_var(--neo-shadow-color)] transition-[transform,box-shadow] outline-none placeholder:text-muted-foreground focus:-translate-y-[1px] focus:ring-2 focus:ring-secondary disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                className,
            )}
            {...props}
        />
    );
}

export { Textarea };
