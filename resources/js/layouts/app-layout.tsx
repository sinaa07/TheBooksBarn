import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { type BreadcrumbItem } from '@/types';
import { type ReactNode } from 'react';
import { JSX } from 'react';

interface AppLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
    [key: string]: unknown;
}

const AppLayout = ({ children, breadcrumbs, ...props }: AppLayoutProps): JSX.Element => (
    <AppLayoutTemplate {...props}>
        {children}
    </AppLayoutTemplate>
);

export default AppLayout;
