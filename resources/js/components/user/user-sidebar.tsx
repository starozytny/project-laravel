import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid } from 'lucide-react';
import AppLogo from '@/components/app-logo';
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
import { dashboard as dashboardUser } from '@/routes/user';
import { dashboard as dashboardAdmin } from '@/routes/admin';
import type { NavItem } from '@/types';
import { NavFooter } from '@/components/nav-footer';

export function UserSidebar() {
    const { roles } = usePage().props.auth;

    const mainNavItems: NavItem[] = [
        {
            title: 'Tableau de bord',
            href: dashboardUser(),
            icon: LayoutGrid,
        },
    ];

    const footerNavItems: NavItem[] = roles.includes('admin')
        ? [
              {
                  title: 'Espace administrateur',
                  href: dashboardAdmin(),
                  icon: LayoutGrid,
              },
          ]
        : [];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboardUser()} prefetch>
                                <AppLogo nameLogo="Espace utilisateur" />
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
