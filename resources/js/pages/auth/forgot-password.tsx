import { Form, Head } from '@inertiajs/react';
import { LoaderCircle, Mail } from 'lucide-react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';
import { email } from '@/routes/password';

export default function ForgotPassword({ status }: { status?: string }) {
    return (
        <>
            <Head title="Lupa kata sandi" />

            {status && (
                <div className="mb-6 rounded-[11px] border-2 border-[#171717] bg-secondary p-3 text-center text-sm font-bold text-[#171717]">
                    {status}
                </div>
            )}

            <Form {...email.form()}>
                {({ processing, errors }) => (
                    <div className="space-y-6">
                        <div className="grid gap-2">
                            <Label htmlFor="email">Alamat email</Label>

                            <div className="relative">
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    autoComplete="email"
                                    autoFocus
                                    placeholder="nama@email.com"
                                    className="pr-11"
                                />

                                <Mail className="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-muted-foreground" />
                            </div>

                            <InputError message={errors.email} />
                        </div>

                        <Button
                            className="w-full"
                            size="lg"
                            disabled={processing}
                            data-test="email-password-reset-link-button"
                        >
                            {processing && (
                                <LoaderCircle className="size-4 animate-spin" />
                            )}

                            {processing
                                ? 'Mengirim...'
                                : 'Kirim tautan reset kata sandi'}
                        </Button>

                        <div className="text-center text-sm font-medium text-muted-foreground">
                            Sudah ingat kata sandi?{' '}
                            <TextLink href={login()}>
                                Kembali ke halaman masuk
                            </TextLink>
                        </div>
                    </div>
                )}
            </Form>
        </>
    );
}

ForgotPassword.layout = {
    title: 'Lupa kata sandi',
    description:
        'Masukkan alamat email Anda. Kami akan mengirim tautan untuk membuat kata sandi baru.',
};
