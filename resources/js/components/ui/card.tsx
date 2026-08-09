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
                'flex flex-col gap-6 rounded-[18px] border-2 border-foreground bg-card py-6 text-card-foreground shadow-[5px_5px_0_var(--foreground)]',
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
            className={cn('grid gap-2 px-6', className)}
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
                'font-black leading-tight tracking-tight',
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
                'text-sm leading-relaxed text-muted-foreground',
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
            className={cn('px-6', className)}
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
            className={cn('flex items-center px-6', className)}
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
