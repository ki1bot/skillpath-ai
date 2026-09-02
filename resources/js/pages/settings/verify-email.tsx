import { Form, Head, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';

type Props = {
    email: string;
    status?: string;
};

export default function VerifyEmail({ email, status }: Props) {
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

                {status === 'verification-required' && (
                    <div className="rounded-[10px] border-2 border-foreground bg-[var(--neo-yellow)] p-4 text-sm font-semibold text-[#171717]">
                        Verifikasi email wajib sebelum Dashboard, Assessment,
                        Jalur Belajar, Proyek, dan fitur utama SkillPath AI
                        dapat digunakan.
                    </div>
                )}

                {status === 'verification-code-sent' && (
                    <div className="rounded-[10px] border-2 border-foreground bg-secondary p-4 text-sm font-semibold text-[#171717]">
                        Kode verifikasi 6 digit telah dikirim ke {email}. Kode
                        berlaku selama 10 menit.
                    </div>
                )}

                {status === 'verification-code-failed' && (
                    <div className="rounded-[10px] border-2 border-foreground bg-[var(--neo-pink)] p-4 text-sm font-semibold text-[#171717]">
                        Kode belum berhasil dikirim. Pastikan alamat email
                        benar, lalu gunakan tombol Kirim ulang kode.
                    </div>
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
                    <Form
                        action="/settings/profile/verify-email/send"
                        method="post"
                    >
                        {({ processing }) => (
                            <Button
                                type="submit"
                                variant="outline"
                                disabled={processing}
                            >
                                {processing
                                    ? 'Mengirim ulang...'
                                    : 'Kirim ulang kode'}
                            </Button>
                        )}
                    </Form>

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
