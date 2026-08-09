import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { Button } from '@/components/ui/button';

type Props = {
    action: string;
    label?: string;
    compact?: boolean;
};

export function DeleteButton({
    action,
    label = 'Hapus',
    compact = false,
}: Props) {
    return (
        <Form action={action} method="delete">
            {({ processing }) => (
                <Button
                    type="submit"
                    variant="destructive"
                    size={compact ? 'icon-xs' : 'sm'}
                    disabled={processing}
                    aria-label={compact ? 'Hapus' : undefined}
                >
                    <Trash2 className="size-4" />
                    {!compact && label}
                </Button>
            )}
        </Form>
    );
}
