import * as React from 'react';
import { cn } from '@/lib/utils';

function Card({
    className,
    ...props
}: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="card"
            className={cn(
                'flex min-w-0 flex-col overflow-hidden rounded-[16px] border-2 border-foreground bg-card text-card-foreground shadow-[5px_5px_0_var(--neo-shadow-color)]',
                className,
            )}
            {...props}
        />
    );
}

function CardHeader({
    className,
    ...props
}: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="card-header"
            className={cn(
                'grid gap-2 p-5 sm:p-6',
                className,
            )}
            {...props}
        />
    );
}

function CardTitle({
    className,
    ...props
}: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="card-title"
            className={cn(
                'leading-tight font-black tracking-[-0.025em]',
                className,
            )}
            {...props}
        />
    );
}

function CardDescription({
    className,
    ...props
}: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="card-description"
            className={cn(
                'text-sm leading-relaxed font-medium text-muted-foreground',
                className,
            )}
            {...props}
        />
    );
}

function CardAction({
    className,
    ...props
}: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="card-action"
            className={cn(className)}
            {...props}
        />
    );
}

function CardContent({
    className,
    ...props
}: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="card-content"
            className={cn(
                'px-5 pb-5 sm:px-6 sm:pb-6',
                className,
            )}
            {...props}
        />
    );
}

function CardFooter({
    className,
    ...props
}: React.ComponentProps<'div'>) {
    return (
        <div
            data-slot="card-footer"
            className={cn(
                'flex items-center p-5 pt-0 sm:p-6 sm:pt-0',
                className,
            )}
            {...props}
        />
    );
}

export {
    Card,
    CardHeader,
    CardFooter,
    CardTitle,
    CardAction,
    CardDescription,
    CardContent,
};
