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
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import {
    BookOpen,
    ClipboardList,
    FileText,
    Folder,
    LayoutGrid,
    ShieldCheck,
    ToggleLeft,
    Users,
} from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Cases',
        href: '/cases',
        icon: ClipboardList,
    },
    {
        title: 'Channels',
        href: '/channels',
        icon: FileText,
    },
    {
        title: 'Reports',
        href: '/reports',
        icon: ShieldCheck,
    },
    {
        title: 'Breaches',
        href: '/breaches',
        icon: ShieldCheck,
    },
    {
        title: 'Compliance Overview',
        href: '/compliance/overview',
        icon: ShieldCheck,
    },
    {
        title: 'Members',
        href: '/settings/members',
        icon: Users,
    },
    {
        title: 'Features',
        href: '/settings/features',
        icon: ToggleLeft,
    },
    {
        title: 'Audit Log',
        href: '/settings/audit',
        icon: ClipboardList,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
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
