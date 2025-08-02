import { NavUser } from '@/components/nav-user'; 
import { NavMain } from '@/components/nav-main';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { LayoutGrid,BookOpen,UserRound, Megaphone, Sparkles, Phone, ClipboardCheck, Library, Tag, House } from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Home',
        href: '/',
        icon: House,
    },
    {
        title: 'Account',
        href: '/user/dashboard',
        icon: UserRound,
    },
    {
        title: 'Books',
        href: '/books',
        icon: Library,
    },
    {
        title: 'Categories',
        href: '/categories',
        icon: LayoutGrid,
    },
    {
        title: 'Orders',
        href: '/orders',
        icon: Tag,
    },
    {
        title: 'FAQ',
        href: '/faq',
        icon: ClipboardCheck,
    },
    {
        title: 'Contact Us',
        href: '/contact',
        icon: Phone,
    },
    
];

export function AppSidebar() {
    const { auth } = usePage<{ auth?: { user?: any } }>().props;
    return (
        <Sidebar
            collapsible="icon"
            variant="inset"
            className=""
        >
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" className='px-2' asChild>
                                <AppLogo />
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>
        </Sidebar>
    );
}
