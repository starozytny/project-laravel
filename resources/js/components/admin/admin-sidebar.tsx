import { Link } from '@inertiajs/react';
import { Earth, LayoutGrid, UserGroup } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { home } from '@/routes';
import { dashboard as dashboardUser } from '@/routes/user';
import { dashboard as dashboardAdmin } from '@/routes/admin';
import { index as indexUsers } from '@/routes/admin/users';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Tableau de bord',
        href: dashboardAdmin(),
        icon: LayoutGrid,
    },
    {
        title: 'Utilisateurs',
        href: indexUsers(),
        icon: UserGroup,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Espace utilisateur',
        href: dashboardUser(),
        icon: LayoutGrid,
    },
    {
        title: 'Pag d\'accueil',
        href: home(),
        icon: Earth,
    },
];

export function AdminSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboardAdmin()} prefetch>
                                <AppLogo nameLogo="Administration" />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
