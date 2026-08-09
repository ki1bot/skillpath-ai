import { usePasskeyRegister } from '@laravel/passkeys/react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Props = {
    onSuccess: () => void;
};

export default function PasskeyRegistration({ onSuccess }: Props) {
    const [name, setName] = useState(() => {
        const ua = navigator.userAgent;

        const browser = [
            { pattern: /Edg|Edge/, name: 'Edge' },
            { pattern: /OPR|Opera|OPiOS/, name: 'Opera' },
            { pattern: /Firefox|FxiOS/, name: 'Firefox' },
            { pattern: /Chrome|CriOS/, name: 'Chrome' },
            { pattern: /Safari/, name: 'Safari' },
        ].find(({ pattern }) => pattern.test(ua))?.name;

        const os = [
            { pattern: /iPhone/, name: 'iPhone' },
            { pattern: /iPad|Macintosh(?=.*Mobile)/, name: 'iPad' },
            { pattern: /Android/, name: 'Android' },
            { pattern: /Mac/, name: 'Mac' },
            { pattern: /Windows/, name: 'Windows' },
        ].find(({ pattern }) => pattern.test(ua))?.name;

        return [browser, os].filter(Boolean).join(' di ') || '';
    });

    const [showForm, setShowForm] = useState(false);

    const { register, isLoading, error, isSupported } = usePasskeyRegister({
        onSuccess: () => {
            setName('');
            setShowForm(false);
            onSuccess();
        },
    });

    const handleSubmit = async (event: React.FormEvent) => {
        event.preventDefault();

        if (!name.trim()) {
            return;
        }

        await register(name);
    };

    const handleBatal = () => {
        setShowForm(false);
        setName('');
    };

    if (!isSupported) {
        return (
            <div className="text-sm text-muted-foreground">
                Browser ini belum mendukung kunci akses.
            </div>
        );
    }

    if (!showForm) {
        return (
            <Button variant="outline" onClick={() => setShowForm(true)}>
                Tambah kunci akses
            </Button>
        );
    }

    return (
        <form
            onSubmit={handleSubmit}
            className="space-y-4 rounded-[12px] border-2 border-foreground bg-muted/50 p-4"
        >
            <div className="grid gap-2">
                <Label htmlFor="passkey-name">Nama kunci akses</Label>

                <Input
                    id="passkey-name"
                    type="text"
                    value={name}
                    onChange={(event) => setName(event.target.value)}
                    placeholder="Contoh: Laptop pribadi"
                    autoFocus
                />

                <p className="text-xs text-muted-foreground">
                    Gunakan nama yang memudahkanmu mengenali perangkat ini
                    nanti.
                </p>
            </div>

            {error && <InputError message={error} />}

            <div className="flex flex-wrap gap-2">
                <Button type="submit" disabled={isLoading || !name.trim()}>
                    {isLoading ? 'Mendaftarkan...' : 'Daftarkan kunci akses'}
                </Button>

                <Button type="button" variant="ghost" onClick={handleBatal}>
                    Batal
                </Button>
            </div>
        </form>
    );
}
