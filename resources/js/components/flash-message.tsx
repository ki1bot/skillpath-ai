import { usePage } from '@inertiajs/react';
import { AlertCircle, CheckCircle2 } from 'lucide-react';

type FlashProps = {
    flash?: {
        success?: string | null;
        error?: string | null;
    };
};

export function FlashMessage() {
    const { flash } = usePage().props as FlashProps;
    const message = flash?.success ?? flash?.error;

    if (!message) {
        return null;
    }

    const success = Boolean(flash?.success);

    return (
        <div className="px-4 pt-4 md:px-6">
            <div
                className={`flex items-start gap-3 rounded-xl border-2 border-foreground p-3 text-sm font-bold shadow-[3px_3px_0_var(--foreground)] ${
                    success
                        ? 'bg-secondary text-[#171717]'
                        : 'bg-destructive text-white'
                }`}
            >
                {success ? (
                    <CheckCircle2 className="mt-0.5 size-4 shrink-0" />
                ) : (
                    <AlertCircle className="mt-0.5 size-4 shrink-0" />
                )}

                <span>{message}</span>
            </div>
        </div>
    );
}
