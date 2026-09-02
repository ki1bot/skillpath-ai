import { Form, Head } from '@inertiajs/react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

type Props = {
    passwordRules: string;
};

export default function Register({ passwordRules }: Props) {
    return (
        <>
            <Head title="Daftar" />

            <Form
                {...store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-5">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Nama lengkap</Label>

                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="name"
                                    name="name"
                                    placeholder="Nama lengkap"
                                />

                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">
                                    Alamat email aktif
                                </Label>

                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={2}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="nama@email.com"
                                />

                                <p className="text-xs leading-5 font-medium text-muted-foreground">
                                    Gunakan Gmail atau alamat email lain yang
                                    benar-benar dapat Anda buka. Setelah akun
                                    dibuat, kode verifikasi 6 digit akan dikirim
                                    ke alamat ini.
                                </p>

                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Kata sandi</Label>

                                <PasswordInput
                                    id="password"
                                    required
                                    minLength={8}
                                    tabIndex={3}
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Minimal 8 karakter"
                                    passwordrules={passwordRules}
                                />

                                <p className="text-xs font-medium text-muted-foreground">
                                    Gunakan minimal 8 karakter. Boleh berupa
                                    huruf, angka, simbol, atau kombinasinya.
                                </p>

                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    Konfirmasi kata sandi
                                </Label>

                                <PasswordInput
                                    id="password_confirmation"
                                    required
                                    minLength={8}
                                    tabIndex={4}
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Ulangi kata sandi"
                                    passwordrules={passwordRules}
                                />

                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            <div className="rounded-[10px] border-2 border-foreground bg-muted p-4 text-xs leading-5 font-semibold">
                                Akun belum dapat menggunakan Dashboard,
                                Assessment, Jalur Belajar, Proyek, atau fitur
                                lainnya sebelum alamat email berhasil
                                diverifikasi.
                            </div>

                            <Button
                                type="submit"
                                className="mt-2 w-full"
                                size="lg"
                                tabIndex={5}
                                disabled={processing}
                                data-test="register-user-button"
                            >
                                {processing && <Spinner />}

                                {processing
                                    ? 'Membuat akun...'
                                    : 'Buat akun dan verifikasi email'}
                            </Button>
                        </div>

                        <div className="text-center text-sm font-medium text-muted-foreground">
                            Sudah memiliki akun?{' '}
                            <TextLink href={login()} tabIndex={6}>
                                Masuk
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </>
    );
}

Register.layout = {
    title: 'Buat akun baru',
    description:
        'Gunakan alamat email aktif karena verifikasi email wajib sebelum SkillPath AI dapat digunakan.',
};
