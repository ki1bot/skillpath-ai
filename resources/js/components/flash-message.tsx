import { usePage } from '@inertiajs/react';
import { AlertCircle, CheckCircle2 } from 'lucide-react';
import { useEffect, useState } from 'react';

type FlashProps = {
    flash?: {
        success?: string | null;
        error?: string | null;
    };
};

type TimedFlashMessageProps = {
    message: string;
    success: boolean;
};

function TimedFlashMessage({ message, success }: TimedFlashMessageProps) {
    const [visible, setVisible] = useState(true);

    useEffect(() => {
        let timeout: number | undefined;

        if (success) {
            timeout = window.setTimeout(() => {
                setVisible(false);
            }, 5000);
        }

        return () => {
            if (timeout !== undefined) {
                window.clearTimeout(timeout);
            }
        };
    }, [success]);

    if (!visible) {
        return null;
    }

    return (
        <div className="px-4 pt-4 md:px-6">
            <div
                className={`flex items-start gap-3 rounded-xl border-2 border-foreground p-3 text-sm font-bold shadow-[3px_3px_0_var(--neo-shadow-color)] ${
                    success
                        ? 'bg-secondary text-[#171717]'
                        : 'bg-destructive text-white'
                }`}
                role={success ? 'status' : 'alert'}
                aria-live={success ? 'polite' : 'assertive'}
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

export function FlashMessage() {
    const { flash } = usePage().props as FlashProps;

    const successMessage = flash?.success;
    const errorMessage = flash?.error;
    const message = successMessage ?? errorMessage;

    if (!message) {
        return null;
    }

    const success = Boolean(successMessage);

    return (
        <TimedFlashMessage
            key={`${success ? 'success' : 'error'}:${message}`}
            message={message}
            success={success}
        />
    );
}
