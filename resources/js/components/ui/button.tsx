import * as React from 'react';
import {
    cva,
    type VariantProps,
} from 'class-variance-authority';
import { Slot } from 'radix-ui';
import { cn } from '@/lib/utils';

const buttonVariants = cva(
    "inline-flex shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-[10px] border-2 text-sm font-bold transition-[transform,box-shadow,background-color,color,border-color] duration-150 outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 active:translate-x-[2px] active:translate-y-[2px] active:shadow-none [&_svg]:pointer-events-none [&_svg]:shrink-0 [&_svg:not([class*='size-'])]:size-4",
    {
        variants: {
            variant: {
                default:
                    'border-foreground bg-foreground text-background shadow-[3px_3px_0_var(--secondary)] hover:-translate-x-[1px] hover:-translate-y-[1px] hover:shadow-[4px_4px_0_var(--secondary)]',

                destructive:
                    'border-foreground bg-destructive text-destructive-foreground shadow-[3px_3px_0_var(--neo-shadow-color)] hover:-translate-x-[1px] hover:-translate-y-[1px]',

                outline:
                    'border-foreground bg-card text-foreground shadow-[3px_3px_0_var(--neo-shadow-color)] hover:-translate-x-[1px] hover:-translate-y-[1px] hover:bg-muted',

                secondary:
                    'border-[#171717] bg-secondary text-[#171717] shadow-[3px_3px_0_var(--neo-shadow-color)] hover:-translate-x-[1px] hover:-translate-y-[1px]',

                ghost:
                    'border-transparent bg-transparent text-foreground shadow-none hover:border-foreground/30 hover:bg-muted',

                link:
                    'border-transparent bg-transparent text-foreground shadow-none underline decoration-2 underline-offset-4',
            },

            size: {
                default:
                    'h-10 px-4 py-2 has-[>svg]:px-3',

                xs:
                    'h-7 gap-1 rounded-[8px] px-2 text-xs has-[>svg]:px-1.5 [&_svg:not([class*=size-])]:size-3',

                sm:
                    'h-9 gap-1.5 rounded-[9px] px-3 has-[>svg]:px-2.5',

                lg:
                    'h-12 rounded-[11px] px-6 text-base has-[>svg]:px-4',

                icon:
                    'size-10',

                'icon-xs':
                    'size-7 rounded-[8px] [&_svg:not([class*=size-])]:size-3',

                'icon-sm':
                    'size-9 rounded-[9px]',

                'icon-lg':
                    'size-12 rounded-[11px]',
            },
        },

        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

function Button({
    className,
    variant = 'default',
    size = 'default',
    asChild = false,
    ...props
}: React.ComponentProps<'button'>
    & VariantProps<
        typeof buttonVariants
    >
    & {
        asChild?: boolean;
    }) {
    const Comp = asChild
        ? Slot.Root
        : 'button';

    return (
        <Comp
            data-slot="button"
            data-variant={variant}
            data-size={size}
            className={cn(
                buttonVariants({
                    variant,
                    size,
                    className,
                }),
            )}
            {...props}
        />
    );
}

export {
    Button,
    buttonVariants,
};
