import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import type { PropsWithChildren } from 'react';

export default function AppHeaderLayout({ children }: PropsWithChildren) {
    return (
        <AppShell>
            <AppContent>{children}</AppContent>
        </AppShell>
    );
}
