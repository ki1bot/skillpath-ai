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
                    title="Verifikasi email"
                    description={`Masukkan kode 6 digit yang dikirim ke ${email}`}
                />

                {status === 'verification-code-sent' && (
                    <div className="rounded-[10px] border-2 border-foreground bg-secondary/70 p-4 text-sm font-medium">
                        Kode verifikasi telah dikirim. Kode berlaku selama 10
                        menit.
                    </div>
                )}

                <Form
                    action="/settings/profile/verify-email"
                    method="post"
                    className="space-y-5"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="code">Kode verifikasi</Label>

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
                        <Link href={edit()}>Kembali ke profil</Link>
                    </Button>
                </div>

                <p className="text-sm text-muted-foreground">
                    Verifikasi email tidak wajib untuk menggunakan SkillPath AI.
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
