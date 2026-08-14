import { Form, Head, Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';
import { edit as editProfile } from '@/routes/profile';

export default function ConfirmPassword() {
    return (
        <>
            <Head title="Konfirmasi kata sandi" />

            <div className="space-y-6">
                <Button asChild variant="outline" className="w-full">
                    <Link href={editProfile()}>
                        <ArrowLeft className="size-4" />
                        Kembali ke profil
                    </Link>
                </Button>

                <Form {...store.form()} resetOnSuccess={['password']}>
                    {({ processing, errors }) => (
                        <div className="space-y-6">
                            <div className="grid gap-2">
                                <Label htmlFor="password">Kata sandi</Label>

                                <PasswordInput
                                    id="password"
                                    name="password"
                                    placeholder="Masukkan kata sandi"
                                    autoComplete="current-password"
                                    autoFocus
                                />

                                <InputError message={errors.password} />
                            </div>

                            <Button
                                className="w-full"
                                size="lg"
                                disabled={processing}
                                data-test="confirm-password-button"
                            >
                                {processing && <Spinner />}

                                {processing ? 'Memverifikasi...' : 'Konfirmasi'}
                            </Button>
                        </div>
                    )}
                </Form>
            </div>
        </>
    );
}

ConfirmPassword.layout = {
    title: 'Konfirmasi kata sandi',
    description:
        'Masukkan kembali kata sandi Anda untuk melanjutkan ke area yang dilindungi.',
};
