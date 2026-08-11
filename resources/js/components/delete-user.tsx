import { Form } from '@inertiajs/react';
import { useRef } from 'react';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';

export default function DeleteUser() {
    const passwordInput = useRef<HTMLInputElement>(null);

    return (
        <div className="rounded-[10px] border-2 border-red-400/60 bg-red-50 p-4 text-red-950 sm:p-5 dark:border-red-400/30 dark:bg-red-950/40 dark:text-red-100">
            <div className="space-y-1">
                <p className="font-bold">Hapus akun</p>

                <p className="text-sm text-red-800 dark:text-red-200">
                    Hapus akun beserta seluruh data yang terkait
                </p>
            </div>

            <div className="my-4 border-t border-red-300 dark:border-red-300/20" />

            <div className="space-y-1">
                <p className="font-bold">Peringatan</p>

                <p className="text-sm text-red-800 dark:text-red-200">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div className="mt-4">
                <Dialog>
                    <DialogTrigger asChild>
                        <Button
                            variant="destructive"
                            data-test="delete-user-button"
                        >
                            Hapus akun
                        </Button>
                    </DialogTrigger>

                    <DialogContent>
                        <DialogTitle>
                            Apakah Anda yakin ingin menghapus akun?
                        </DialogTitle>

                        <DialogDescription>
                            Setelah akun dihapus, seluruh data terkait juga akan
                            dihapus secara permanen. Masukkan kata sandi untuk
                            mengonfirmasi.
                        </DialogDescription>

                        <Form
                            {...ProfileController.destroy.form()}
                            options={{
                                preserveScroll: true,
                            }}
                            onError={() => passwordInput.current?.focus()}
                            resetOnSuccess
                            className="space-y-6"
                        >
                            {({ resetAndClearErrors, processing, errors }) => (
                                <>
                                    <div className="grid gap-2">
                                        <Label
                                            htmlFor="password"
                                            className="sr-only"
                                        >
                                            Password
                                        </Label>

                                        <PasswordInput
                                            id="password"
                                            name="password"
                                            ref={passwordInput}
                                            placeholder="Kata sandi"
                                            autoComplete="current-password"
                                        />

                                        <InputError message={errors.password} />
                                    </div>

                                    <DialogFooter className="gap-2">
                                        <DialogClose asChild>
                                            <Button
                                                variant="secondary"
                                                onClick={() =>
                                                    resetAndClearErrors()
                                                }
                                            >
                                                Batal
                                            </Button>
                                        </DialogClose>

                                        <Button
                                            variant="destructive"
                                            disabled={processing}
                                            asChild
                                        >
                                            <button
                                                type="submit"
                                                data-test="confirm-delete-user-button"
                                            >
                                                Hapus akun
                                            </button>
                                        </Button>
                                    </DialogFooter>
                                </>
                            )}
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>
        </div>
    );
}
