import { Form, Head, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/delete-user';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { FacebookIcon, GoogleIcon } from '@/components/social-auth-buttons';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import type { Auth } from '@/types';

type SocialConnections = {
    google: boolean;
    facebook: boolean;
};

type PageProps = {
    auth: Auth;
    socialConnections: SocialConnections;
    errors: {
        social?: string;
    };
};

type SocialConnectionItemProps = {
    name: string;
    connected: boolean;
    href: string;
    icon: ReactNode;
};

function SocialConnectionItem({
    name,
    connected,
    href,
    icon,
}: SocialConnectionItemProps) {
    return (
        <div className="grid gap-4 rounded-[10px] border-2 border-foreground p-4 sm:grid-cols-[minmax(0,1fr)_190px] sm:items-center sm:gap-6">
            <div className="flex min-w-0 items-center gap-3">
                <div className="flex size-11 shrink-0 items-center justify-center rounded-[8px] border-2 border-foreground bg-background">
                    {icon}
                </div>

                <div className="min-w-0">
                    <p className="text-sm leading-none font-bold">{name}</p>

                    <p className="mt-2 text-sm leading-relaxed text-muted-foreground">
                        {connected
                            ? `Akun ${name} sudah terhubung ke akun SkillPath AI ini.`
                            : `Hubungkan akun ${name} agar dapat digunakan untuk masuk ke akun SkillPath AI yang sama.`}
                    </p>
                </div>
            </div>

            <div className="flex w-full sm:justify-end">
                {connected ? (
                    <span className="inline-flex h-10 w-full items-center justify-center rounded-[8px] border-2 border-[#171717] bg-secondary px-4 text-sm font-bold whitespace-nowrap text-secondary-foreground shadow-[2px_2px_0_var(--neo-shadow-color)]">
                        Terhubung
                    </span>
                ) : (
                    <Button
                        asChild
                        variant="outline"
                        className="h-10 w-full whitespace-nowrap"
                    >
                        <a href={href}>Hubungkan {name}</a>
                    </Button>
                )}
            </div>
        </div>
    );
}

export default function Profile() {
    const { auth, socialConnections, errors } = usePage<PageProps>().props;

    const user = auth.user;

    if (!user) {
        return null;
    }

    return (
        <>
            <Head title="Pengaturan profil" />

            <h1 className="sr-only">Pengaturan profil</h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title="Profil"
                    description="Perbarui nama dan alamat email Anda"
                />

                <Form
                    {...ProfileController.update.form()}
                    options={{
                        preserveScroll: true,
                    }}
                    className="space-y-6"
                >
                    {({ processing, errors: formErrors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Nama</Label>

                                <Input
                                    id="name"
                                    className="mt-1 block w-full"
                                    defaultValue={user.name}
                                    name="name"
                                    required
                                    autoComplete="name"
                                    placeholder="Nama lengkap"
                                />

                                <InputError
                                    className="mt-2"
                                    message={formErrors.name}
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Alamat email</Label>

                                <Input
                                    id="email"
                                    type="email"
                                    className="mt-1 block w-full"
                                    defaultValue={user.email}
                                    name="email"
                                    required
                                    autoComplete="username"
                                    placeholder="Alamat email"
                                />

                                <InputError
                                    className="mt-2"
                                    message={formErrors.email}
                                />
                            </div>

                            <div className="flex items-center gap-4">
                                <Button
                                    disabled={processing}
                                    data-test="update-profile-button"
                                >
                                    Simpan
                                </Button>
                            </div>
                        </>
                    )}
                </Form>

                <div className="rounded-[10px] border-2 border-foreground p-4">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div className="min-w-0">
                            <p className="text-sm font-bold">
                                Verifikasi email
                            </p>

                            <p className="mt-1 text-sm text-muted-foreground">
                                {user.email_verified_at
                                    ? `Email ${user.email} sudah terverifikasi.`
                                    : `Verifikasi bersifat opsional. Kode akan dikirim ke email akun yang tersimpan: ${user.email}.`}
                            </p>
                        </div>

                        {user.email_verified_at ? (
                            <span className="inline-flex w-fit shrink-0 rounded-full border-2 border-[#171717] bg-secondary px-3 py-1 text-xs font-bold text-secondary-foreground shadow-[2px_2px_0_var(--neo-shadow-color)]">
                                Terverifikasi
                            </span>
                        ) : (
                            <Form
                                action="/settings/profile/verify-email/send"
                                method="post"
                            >
                                {({ processing }) => (
                                    <Button
                                        type="submit"
                                        variant="secondary"
                                        disabled={processing}
                                        className="shrink-0"
                                    >
                                        {processing
                                            ? 'Mengirim kode...'
                                            : 'Verifikasi email'}
                                    </Button>
                                )}
                            </Form>
                        )}
                    </div>
                </div>

                <div className="space-y-4">
                    <div>
                        <p className="text-sm font-bold">Akun terhubung</p>

                        <p className="mt-1 text-sm text-muted-foreground">
                            Hubungkan Google atau Facebook agar beberapa metode
                            masuk tetap mengarah ke akun SkillPath AI yang sama.
                        </p>
                    </div>

                    <InputError message={errors.social} />

                    <div className="grid gap-3">
                        <SocialConnectionItem
                            name="Google"
                            connected={socialConnections.google}
                            href="/settings/connections/google/redirect"
                            icon={<GoogleIcon />}
                        />

                        <SocialConnectionItem
                            name="Facebook"
                            connected={socialConnections.facebook}
                            href="/settings/connections/facebook/redirect"
                            icon={<FacebookIcon />}
                        />
                    </div>
                </div>
            </div>

            <DeleteUser />
        </>
    );
}

Profile.layout = {
    breadcrumbs: [
        {
            title: 'Pengaturan profil',
            href: edit(),
        },
    ],
};
