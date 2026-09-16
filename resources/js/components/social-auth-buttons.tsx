import { Button } from '@/components/ui/button';

type Props = {
    mode: 'login' | 'register';
};

function GoogleIcon() {
    return (
        <svg viewBox="0 0 24 24" aria-hidden="true" className="size-5">
            <path
                fill="#4285F4"
                d="M21.6 12.227c0-.709-.064-1.391-.182-2.045H12v3.868h5.382a4.6 4.6 0 0 1-1.995 3.018v2.509h3.232c1.891-1.741 2.981-4.305 2.981-7.35Z"
            />
            <path
                fill="#34A853"
                d="M12 22c2.7 0 4.964-.895 6.619-2.423l-3.232-2.509c-.895.6-2.041.955-3.387.955-2.605 0-4.809-1.759-5.596-4.123H3.064v2.591A10 10 0 0 0 12 22Z"
            />
            <path
                fill="#FBBC05"
                d="M6.404 13.9A6.018 6.018 0 0 1 6.091 12c0-.659.114-1.3.313-1.9V7.509h-3.34A10 10 0 0 0 2 12c0 1.614.386 3.141 1.064 4.491L6.404 13.9Z"
            />
            <path
                fill="#EA4335"
                d="M12 5.977c1.468 0 2.786.505 3.823 1.496l2.868-2.868C16.959 2.991 14.695 2 12 2a10 10 0 0 0-8.936 5.509l3.34 2.591C7.191 7.736 9.395 5.977 12 5.977Z"
            />
        </svg>
    );
}

function FacebookIcon() {
    return (
        <svg viewBox="0 0 24 24" aria-hidden="true" className="size-5">
            <circle cx="12" cy="12" r="10" fill="#1877F2" />

            <path
                fill="#FFFFFF"
                d="M15.893 12.647h-2.465V21h-3.66v-8.353H8V9.535h1.768V7.523C9.768 4.79 11.004 3 14.509 3c.74 0 1.48.059 2.216.177v2.86h-1.509c-1.19 0-1.788.626-1.788 1.846v1.652h3.18l-.715 3.112Z"
            />
        </svg>
    );
}

export default function SocialAuthButtons({ mode }: Props) {
    const action = mode === 'register' ? 'Daftar' : 'Masuk';

    return (
        <div className="grid gap-4">
            <div className="flex items-center">
                <div className="h-px flex-1 bg-border" />

                <span className="px-3 text-xs font-bold tracking-[0.14em] text-muted-foreground uppercase">
                    atau
                </span>

                <div className="h-px flex-1 bg-border" />
            </div>

            <div className="grid gap-3">
                <Button
                    asChild
                    variant="outline"
                    size="lg"
                    className="relative w-full px-12 text-sm sm:text-base"
                >
                    <a href={`/auth/google/redirect?source=${mode}`}>
                        <span className="absolute left-4 flex size-5 items-center justify-center">
                            <GoogleIcon />
                        </span>

                        <span className="text-center">
                            {action} dengan Google
                        </span>
                    </a>
                </Button>

                <Button
                    asChild
                    variant="outline"
                    size="lg"
                    className="relative w-full px-12 text-sm sm:text-base"
                >
                    <a href={`/auth/facebook/redirect?source=${mode}`}>
                        <span className="absolute left-4 flex size-5 items-center justify-center">
                            <FacebookIcon />
                        </span>

                        <span className="text-center">
                            {action} dengan Facebook
                        </span>
                    </a>
                </Button>
            </div>
        </div>
    );
}
