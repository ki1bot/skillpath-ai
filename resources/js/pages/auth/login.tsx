import { Form, Head } from '@inertiajs/react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

type Props = {
    status?: string;
    canResetPassword: boolean;
};

export default function Login({ status, canResetPassword }: Props) {
    const [remember, setRemember] = useState(false);

    return (
        <>
            <Head title="Masuk" />

            <Form
                {...store.form()}
                transform={(data) => ({
                    ...data,
                    remember: remember ? '1' : '0',
                })}
                resetOnSuccess={['password']}
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-5">
                            <div className="grid gap-2">
                                <Label htmlFor="email">Alamat email</Label>

                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="email"
                                    placeholder="nama@email.com"
                                />

                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <div className="flex flex-wrap items-center justify-between gap-2">
                                    <Label htmlFor="password">Kata sandi</Label>

                                    {canResetPassword && (
                                        <TextLink
                                            href={request()}
                                            className="text-sm"
                                            tabIndex={5}
                                        >
                                            Lupa kata sandi?
                                        </TextLink>
                                    )}
                                </div>

                                <PasswordInput
                                    id="password"
                                    name="password"
                                    required
                                    tabIndex={2}
                                    autoComplete="current-password"
                                    placeholder="Masukkan kata sandi"
                                />

                                <InputError message={errors.password} />
                            </div>

                            <Button
                                type="submit"
                                className="mt-1 w-full"
                                size="lg"
                                tabIndex={4}
                                disabled={processing}
                                data-test="login-button"
                            >
                                {processing && <Spinner />}

                                {processing ? 'Memproses...' : 'Masuk'}
                            </Button>
                        </div>

                        <div className="text-center text-sm font-medium text-muted-foreground">
                            Belum punya akun?{' '}
                            <TextLink href={register()} tabIndex={5}>
                                Buat akun
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>

            {status && (
                <div className="mt-5 rounded-[10px] border-2 border-foreground bg-secondary/70 p-3 text-center text-sm font-bold text-[#171717]">
                    {status}
                </div>
            )}
        </>
    );
}

Login.layout = {
    title: 'Selamat datang kembali',
    description: 'Masuk dengan email dan kata sandi akun SkillPath Anda.',
};
