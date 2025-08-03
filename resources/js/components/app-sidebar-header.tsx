import { LogIn, LogOut, Heart, ShoppingCart } from 'lucide-react';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { usePage, Link } from '@inertiajs/react';
import SearchBar from './search/search-bar';

export function AppSidebarHeader() {
    const { auth } = usePage<{ auth?: { user?: any } }>().props;

    return (
        <header className="sticky top-0 z-30 bg-[#f9f5f0] border-b border-[#d6c2aa] flex h-16 shrink-0 items-center gap-2 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
            <div className="flex items-center gap-4">
                <SidebarTrigger className="text-[#4B3B2A] hover:text-[#8B5E3C] cursor-pointer" />
                <Link href="/" className="text-lg font-semibold text-[#4B3B2A] font-serif hover:text-[#8B5E3C]">
                    The Books Barn
                </Link>
            </div>
            <SearchBar/>
            <div className="ml-auto flex items-center gap-4">
                {auth?.user ? (
                    <Link href="/logout" method="post" as="button" className="flex items-center gap-1 text-sm font-medium text-[#4B3B2A] hover:text-[#8B5E3C]">
                        <LogOut className="w-4 h-4" />
                        Logout
                    </Link>
                ) : (
                    <Link href="/login" className="flex items-center gap-1 text-sm font-medium text-[#4B3B2A] hover:text-[#8B5E3C]">
                        <LogIn className="w-4 h-4" />
                        Login
                    </Link>
                )}
                <Link href="/wishlist" className="text-[#4B3B2A] hover:text-[#8B5E3C]" aria-label="Wishlist">
                    <Heart className="w-5 h-5" />
                </Link>
                <Link href ="/cart" className="text-[#4B3B2A] hover:text-[#8B5E3C]" aria-label="Cart">
                    <ShoppingCart className="w-5 h-5" />
                </Link>
            </div>
        </header>
    );
}
