import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { UserSidebar } from '@/components/user/user-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import type { BreadcrumbItem } from '@/types';

export default function UserLayout({
    breadcrumbs = [],
    children,
}: {
    breadcrumbs?: BreadcrumbItem[];
    children: React.ReactNode;
}) {
    return (
        <AppShell variant="sidebar">
            <UserSidebar />
            <AppContent variant="sidebar" className="min-w-0 overflow-x-clip">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
