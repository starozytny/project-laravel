import { AppLayoutProps } from '@/types';
import { usePage } from '@inertiajs/react';
import AdminLayout from '@/layouts/admin/admin-layout';
import UserLayout from '@/layouts/user/user-layout';

export default function RoleAwareLayout({
    children,
    ...props
}: AppLayoutProps) {
    const { abilities } = usePage().props.auth;
    const Layout = abilities.accessAdmin ? AdminLayout : UserLayout;

    return <Layout {...props}>{children}</Layout>;
}
