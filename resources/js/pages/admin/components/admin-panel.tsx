import type { ReactNode } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Props = {
    title: string;
    description?: string;
    accentClass: string;
    children: ReactNode;
};

export function AdminPanel({
    title,
    description,
    accentClass,
    children,
}: Props) {
    return (
        <Card className="min-w-0 overflow-hidden">
            <CardHeader
                className={`border-b-2 border-foreground ${accentClass}`}
            >
                <CardTitle className="text-xl sm:text-2xl">{title}</CardTitle>

                {description && (
                    <p className="max-w-3xl text-sm leading-relaxed font-semibold text-[#171717]/70">
                        {description}
                    </p>
                )}
            </CardHeader>

            <CardContent className="min-w-0 space-y-5 pt-6">
                {children}
            </CardContent>
        </Card>
    );
}
