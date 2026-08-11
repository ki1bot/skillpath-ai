import * as CheckboxPrimitive from '@radix-ui/react-checkbox';
import { CheckIcon } from 'lucide-react';
import * as React from 'react';
import { cn } from '@/lib/utils';

function Checkbox({
    className,
    ...props
}: React.ComponentProps<
    typeof CheckboxPrimitive.Root
>) {
    return (
        <CheckboxPrimitive.Root
            data-slot="checkbox"
            className={cn(
                'peer size-5 shrink-0 rounded-[5px] border-2 border-foreground bg-card shadow-[2px_2px_0_var(--neo-shadow-color)] transition-[transform,box-shadow,background-color] outline-none data-[state=checked]:bg-secondary data-[state=checked]:text-[#171717] focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 active:translate-x-[1px] active:translate-y-[1px] active:shadow-none',
                className,
            )}
            {...props}
        >
            <CheckboxPrimitive.Indicator
                data-slot="checkbox-indicator"
                className="flex items-center justify-center text-current"
            >
                <CheckIcon className="size-3.5 stroke-[3]" />
            </CheckboxPrimitive.Indicator>
        </CheckboxPrimitive.Root>
    );
}

export { Checkbox };
