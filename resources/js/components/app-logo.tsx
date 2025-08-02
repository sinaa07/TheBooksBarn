import AppLogoIcon from './app-logo-icon';
import { BookOpen } from 'lucide-react';
export default function AppLogo() {
    return (
        <>
            {/*<div className="bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-8 items-center justify-center rounded-md">
                <AppLogoIcon className="size-5 fill-current text-white dark:text-black" />
            </div>*/}
            <div className="flex items-center justify-center mt-5 px-2">
                <span className=" truncate leading-none font-bold text-xl text-amber-900 "> The Books Barn </span>
            </div>
        </>
    );
}
