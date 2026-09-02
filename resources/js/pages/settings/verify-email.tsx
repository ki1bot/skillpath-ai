import { Form, Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';

type Props = {
    email: string;
    status?: string | null;
    resendAvailableIn: number;
    verificationStateId: string;
};

type VerificationStatusNoticeProps = {
    email: string;
    status: string;
};

type ResendCodeFormProps = {
    initialSeconds: number;
};

const formatRemainingTime = (seconds: number): string => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;

    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

function VerificationStatusNotice({
    email,
    status,
}: VerificationStatusNoticeProps) {
    const [isVisible, setIsVisible] = useState(true);

    useEffect(() => {
        const timeout = window.setTimeout(() => {
            setIsVisible(false);
        }, 5000);

        return () => {
            window.clearTimeout(timeout);
        };
    }, []);

    if (!isVisible) {
        return null;
    }

    if (status === 'verification-required') {
        return (
            <div className="rounded-[10px] border-2 border-foreground bg-[var(--neo-yellow)] p-4 text-sm font-semibold text-[#171717]">
                Verifikasi email wajib sebelum Dashboard, Assessment, Jalur
                Belajar, Proyek, dan fitur utama SkillPath AI dapat digunakan.
            </div>
        );
    }

    if (status === 'verification-code-sent') {
        return (
            <div className="rounded-[10px] border-2 border-foreground bg-secondary p-4 text-sm font-semibold text-[#171717]">
                Kode verifikasi 6 digit telah dikirim ke {email}. Kode berlaku
                selama 10 menit. Kode baru dapat diminta setelah 5 menit.
            </div>
        );
    }

    if (status === 'verification-code-cooldown') {
        return (
            <div className="rounded-[10px] border-2 border-foreground bg-[var(--neo-yellow)] p-4 text-sm font-semibold text-[#171717]">
                Kode baru belum dapat dikirim. Tunggu hingga waktu 5 menit
                selesai sebelum meminta kode berikutnya.
            </div>
        );
    }

    if (status === 'verification-code-failed') {
        return (
            <div className="rounded-[10px] border-2 border-foreground bg-[var(--neo-pink)] p-4 text-sm font-semibold text-[#171717]">
                Kode belum berhasil dikirim. Pastikan alamat email benar, lalu
                coba kirim ulang setelah beberapa saat.
            </div>
        );
    }

    return null;
}

function ResendCodeForm({ initialSeconds }: ResendCodeFormProps) {
    const [remainingSeconds, setRemainingSeconds] = useState(() =>
        Math.max(0, initialSeconds),
    );

    useEffect(() => {
        const totalSeconds = Math.max(0, initialSeconds);

        if (totalSeconds === 0) {
            return;
        }

        const deadline = Date.now() + totalSeconds * 1000;

        const interval = window.setInterval(() => {
            const remaining = Math.max(
                0,
                Math.ceil((deadline - Date.now()) / 1000),
            );

            setRemainingSeconds(remaining);

            if (remaining === 0) {
                window.clearInterval(interval);
            }
        }, 1000);

        return () => {
            window.clearInterval(interval);
        };
    }, [initialSeconds]);

    return (
        <Form action="/settings/profile/verify-email/send" method="post">
            {({ processing }) => (
                <Button
                    type="submit"
                    variant="outline"
                    disabled={processing || remainingSeconds > 0}
                >
                    {processing
                        ? 'Mengirim ulang...'
                        : remainingSeconds > 0
                          ? `Kirim ulang dalam ${formatRemainingTime(
                                remainingSeconds,
                            )}`
                          : 'Kirim ulang kode'}
                </Button>
            )}
        </Form>
    );
}

export default function VerifyEmail({
    email,
    status,
    resendAvailableIn,
    verificationStateId,
}: Props) {
    return (
        <>
            <Head title="Verifikasi email" />

            <h1 className="sr-only">Verifikasi email</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title="Verifikasi alamat email"
                    description={`Kode verifikasi akan dikirim ke ${email}`}
                />

                {status && (
                    <VerificationStatusNotice
                        key={`status-${verificationStateId}`}
                        email={email}
                        status={status}
                    />
                )}

                <div className="rounded-[12px] border-2 border-foreground bg-muted p-4">
                    <p className="text-xs font-black tracking-wide uppercase">
                        Email akun
                    </p>

                    <p className="mt-2 font-mono text-sm font-black break-all">
                        {email}
                    </p>

                    <p className="mt-2 text-xs leading-5 font-medium text-muted-foreground">
                        Jika alamat di atas salah atau tidak dapat Anda akses,
                        ubah alamat email melalui pengaturan profil.
                    </p>
                </div>

                <Form
                    action="/settings/profile/verify-email"
                    method="post"
                    className="space-y-5"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="code">
                                    Kode verifikasi 6 digit
                                </Label>

                                <Input
                                    id="code"
                                    name="code"
                                    inputMode="numeric"
                                    autoComplete="one-time-code"
                                    maxLength={6}
                                    pattern="[0-9]{6}"
                                    required
                                    autoFocus
                                    placeholder="123456"
                                    className="font-mono text-lg tracking-[0.3em]"
                                />

                                <InputError message={errors.code} />
                            </div>

                            <Button type="submit" disabled={processing}>
                                {processing
                                    ? 'Memverifikasi...'
                                    : 'Verifikasi email'}
                            </Button>
                        </>
                    )}
                </Form>

                <div className="flex flex-wrap items-center gap-3">
                    <ResendCodeForm
                        key={`resend-${verificationStateId}`}
                        initialSeconds={resendAvailableIn}
                    />

                    <Button asChild variant="ghost">
                        <Link href={edit()}>Ubah alamat email</Link>
                    </Button>
                </div>

                <p className="text-sm leading-6 font-medium text-muted-foreground">
                    Verifikasi membuktikan bahwa alamat email tersebut dapat
                    Anda akses. Tanpa kode yang benar, akun tidak dapat
                    menggunakan fitur utama SkillPath AI.
                </p>
            </div>
        </>
    );
}

VerifyEmail.layout = {
    breadcrumbs: [
        {
            title: 'Pengaturan profil',
            href: edit(),
        },
        {
            title: 'Verifikasi email',
            href: '/settings/profile/verify-email',
        },
    ],
};
