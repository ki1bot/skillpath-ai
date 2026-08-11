import { Form, Head, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';

type ManagedUser = {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'student';
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type UsersPagination = {
    data: ManagedUser[];
    current_page: number;
    from: number | null;
    last_page: number;
    links: PaginationLink[];
    per_page: number;
    to: number | null;
    total: number;
};

type Props = {
    users: UsersPagination;
};

export default function Users({ users }: Props) {
    return (
        <>
            <Head title="Pengguna" />

            <div className="mx-auto w-full max-w-6xl space-y-7 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <Heading
                    title="Pengguna"
                    description="Kelola role akun yang terdaftar pada SkillPath AI."
                />

                <div className="neo-card overflow-hidden">
                    <div className="border-b-2 border-foreground px-5 py-4 sm:px-6">
                        <div className="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p className="font-bold">Daftar pengguna</p>

                                <p className="mt-1 text-sm text-muted-foreground">
                                    Total {users.total} akun terdaftar
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="hidden overflow-x-auto md:block">
                        <table className="w-full border-collapse text-left">
                            <thead>
                                <tr className="border-b-2 border-foreground bg-muted/50">
                                    <th className="px-5 py-3 text-sm font-bold">
                                        Nama
                                    </th>

                                    <th className="px-5 py-3 text-sm font-bold">
                                        Email
                                    </th>

                                    <th className="px-5 py-3 text-sm font-bold">
                                        Role
                                    </th>

                                    <th className="px-5 py-3 text-sm font-bold">
                                        Ubah role
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                {users.data.map((user) => (
                                    <tr
                                        key={user.id}
                                        className="border-b border-border last:border-b-0"
                                    >
                                        <td className="px-5 py-4">
                                            <div className="font-bold">
                                                {user.name}
                                            </div>
                                        </td>

                                        <td className="px-5 py-4 text-sm">
                                            {user.email}
                                        </td>

                                        <td className="px-5 py-4">
                                            <span className="inline-flex rounded-full border-2 border-[#171717] bg-secondary px-3 py-1 text-xs font-bold text-secondary-foreground capitalize shadow-[2px_2px_0_var(--neo-shadow-color)]">
                                                {user.role}
                                            </span>
                                        </td>

                                        <td className="px-5 py-4">
                                            <Form
                                                action={`/admin/users/${user.id}/role`}
                                                method="patch"
                                                options={{
                                                    preserveScroll: true,
                                                }}
                                                className="flex items-start gap-2"
                                            >
                                                {({ processing, errors }) => (
                                                    <>
                                                        <div>
                                                            <select
                                                                name="role"
                                                                defaultValue={
                                                                    user.role
                                                                }
                                                                className="h-10 min-w-32 rounded-[10px] border-2 border-foreground bg-card px-3 text-sm font-bold text-card-foreground outline-none focus:ring-2 focus:ring-ring"
                                                            >
                                                                <option value="student">
                                                                    Student
                                                                </option>

                                                                <option value="admin">
                                                                    Admin
                                                                </option>
                                                            </select>

                                                            <InputError
                                                                className="mt-2"
                                                                message={
                                                                    errors.role
                                                                }
                                                            />
                                                        </div>

                                                        <Button
                                                            type="submit"
                                                            size="sm"
                                                            disabled={
                                                                processing
                                                            }
                                                        >
                                                            {processing
                                                                ? 'Menyimpan...'
                                                                : 'Simpan'}
                                                        </Button>
                                                    </>
                                                )}
                                            </Form>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    <div className="divide-y-2 divide-foreground md:hidden">
                        {users.data.map((user) => (
                            <div key={user.id} className="space-y-4 p-5">
                                <div>
                                    <p className="font-bold">{user.name}</p>

                                    <p className="mt-1 text-sm break-all text-muted-foreground">
                                        {user.email}
                                    </p>
                                </div>

                                <div>
                                    <span className="inline-flex rounded-full border-2 border-[#171717] bg-secondary px-3 py-1 text-xs font-bold text-secondary-foreground capitalize shadow-[2px_2px_0_var(--neo-shadow-color)]">
                                        {user.role}
                                    </span>
                                </div>

                                <Form
                                    action={`/admin/users/${user.id}/role`}
                                    method="patch"
                                    options={{
                                        preserveScroll: true,
                                    }}
                                    className="space-y-3"
                                >
                                    {({ processing, errors }) => (
                                        <>
                                            <select
                                                name="role"
                                                defaultValue={user.role}
                                                className="h-10 w-full rounded-[10px] border-2 border-foreground bg-card px-3 text-sm font-bold text-card-foreground outline-none focus:ring-2 focus:ring-ring"
                                            >
                                                <option value="student">
                                                    Student
                                                </option>

                                                <option value="admin">
                                                    Admin
                                                </option>
                                            </select>

                                            <InputError message={errors.role} />

                                            <Button
                                                type="submit"
                                                size="sm"
                                                disabled={processing}
                                                className="w-full"
                                            >
                                                {processing
                                                    ? 'Menyimpan...'
                                                    : 'Simpan perubahan'}
                                            </Button>
                                        </>
                                    )}
                                </Form>
                            </div>
                        ))}
                    </div>

                    {users.last_page > 1 && (
                        <div className="flex flex-wrap items-center justify-between gap-4 border-t-2 border-foreground px-5 py-4 sm:px-6">
                            <p className="text-sm font-medium text-muted-foreground">
                                Menampilkan {users.from ?? 0}-{users.to ?? 0}{' '}
                                dari {users.total} pengguna
                            </p>

                            <div className="flex flex-wrap gap-2">
                                {users.links.map((link, index) => {
                                    const label = link.label
                                        .replace('&laquo;', '‹')
                                        .replace('&raquo;', '›');

                                    if (!link.url) {
                                        return (
                                            <Button
                                                key={`${label}-${index}`}
                                                size="sm"
                                                variant="outline"
                                                disabled
                                            >
                                                {label}
                                            </Button>
                                        );
                                    }

                                    return (
                                        <Button
                                            key={`${label}-${index}`}
                                            size="sm"
                                            variant={
                                                link.active
                                                    ? 'secondary'
                                                    : 'outline'
                                            }
                                            asChild
                                        >
                                            <Link
                                                href={link.url}
                                                preserveScroll
                                            >
                                                {label}
                                            </Link>
                                        </Button>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}

Users.layout = {
    breadcrumbs: [
        {
            title: 'Pengguna',
            href: '/admin/users',
        },
    ],
};
