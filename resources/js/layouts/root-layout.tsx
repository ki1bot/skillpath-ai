import { IdleSessionGuard } from '@/components/idle-session-guard';

export default function RootLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    return (
        <>
            <IdleSessionGuard />
            {children}
        </>
    );
}
