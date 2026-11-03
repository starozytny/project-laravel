import { Link, usePage } from '@inertiajs/react';
import { Earth, LayoutGrid, UserGroup, MessageSquare } from 'lucide-react';
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

import type { NavItem } from '@/types';

import { home } from '@/routes';
import { dashboard as dashboardUser } from '@/routes/user';
import { dashboard as dashboardAdmin } from '@/routes/admin';
import { index as indexUsers } from '@/routes/admin/users';
import { index as indexContactMessages } from '@/routes/admin/contact-messages';

const footerNavItems: NavItem[] = [
    {
        title: 'Espace utilisateur',
        href: dashboardUser(),
        icon: LayoutGrid,
    },
    {
        title: "Page d'accueil",
        href: home(),
        icon: Earth,
    },
];

export function AdminSidebar() {
    const { unreadContactMessages } = usePage().props;

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
        {
            title: 'Messages',
            href: indexContactMessages(),
            badge: unreadContactMessages,
            icon: MessageSquare,
        },
    ];

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
