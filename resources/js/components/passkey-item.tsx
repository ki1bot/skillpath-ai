import { KeyRound, Trash2 } from 'lucide-react';
import { useState } from 'react';
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
import type { Passkey } from '@/types/auth';

type Props = {
    passkey: Passkey;
    onDelete: (id: number, onError: () => void) => void;
};

export default function PasskeyItem({ passkey, onDelete }: Props) {
    const [isDeleting, setIsDeleting] = useState(false);

    const handleDelete = () => {
        setIsDeleting(true);
        onDelete(passkey.id, () => setIsDeleting(false));
    };

    return (
        <div className="flex items-center justify-between gap-4 border-b-2 border-foreground/15 p-4 last:border-b-0">
            <div className="flex min-w-0 items-center gap-4">
                <div className="flex size-10 shrink-0 items-center justify-center rounded-[10px] border-2 border-foreground bg-muted">
                    <KeyRound className="size-5" />
                </div>

                <div className="min-w-0 space-y-1">
                    <div className="flex flex-wrap items-center gap-2.5">
                        <p className="truncate font-black tracking-tight">
                            {passkey.name}
                        </p>

                        {passkey.authenticator && (
                            <span className="inline-flex items-center gap-1 rounded-full border-2 border-foreground/20 bg-muted px-2 py-0.5 text-[10px] font-black tracking-wide uppercase">
                                {passkey.authenticator}
                            </span>
                        )}
                    </div>

                    <p className="text-sm text-muted-foreground">
                        Ditambahkan {passkey.created_at_diff}
                        {passkey.last_used_at_diff && (
                            <>
                                <span className="mx-1 text-muted-foreground/50">
                                    /
                                </span>
                                Terakhir digunakan {passkey.last_used_at_diff}
                            </>
                        )}
                    </p>
                </div>
            </div>

            <Dialog>
                <DialogTrigger asChild>
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        className="shrink-0 text-destructive hover:bg-destructive/10 hover:text-destructive"
                    >
                        <Trash2 className="size-4" />
                        <span className="sr-only">Hapus kunci akses</span>
                    </Button>
                </DialogTrigger>

                <DialogContent>
                    <DialogTitle>Hapus kunci akses</DialogTitle>

                    <DialogDescription>
                        Yakin ingin menghapus kunci akses &quot;{passkey.name}
                        &quot;? Setelah dihapus, kunci ini tidak dapat digunakan
                        lagi untuk masuk.
                    </DialogDescription>

                    <DialogFooter className="gap-2">
                        <DialogClose asChild>
                            <Button variant="secondary">Batal</Button>
                        </DialogClose>

                        <Button
                            variant="destructive"
                            onClick={handleDelete}
                            disabled={isDeleting}
                        >
                            {isDeleting ? 'Menghapus...' : 'Hapus kunci akses'}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    );
}
