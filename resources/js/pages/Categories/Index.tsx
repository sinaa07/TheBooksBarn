import { Head } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Category } from '@/types';

interface Props {
    categories?: Category[]; // made optional for safety
}

export default function CategoriesPage({ categories = [] }: Props) {
    return (
        <AppSidebarLayout>
            <Head title="All Categories" />

            <div className="min-h-screen bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa] py-10 px-6">
                <h1 className="text-4xl font-extrabold text-[#4B3B2A] font-serif mb-8 text-center">
                    📚 All Categories
                </h1>

                {categories.length === 0 ? (
                    <p className="text-center text-[#5C4033] italic font-sans">
                        No categories found.
                    </p>
                ) : (
                    <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                        {categories.map((category) => (
                            <a
                                key={category.id}
                                href={`/categories/${category.slug}`}
                                className="group block bg-white hover:bg-[#f3eae3] py-5 px-4 rounded-xl text-center shadow-md hover:shadow-lg transition transform hover:-translate-y-1 min-h-[180px] flex flex-col items-center justify-center border border-[#e0d4c3]"
                            >
                                {/* Placeholder if no image available */}
                                <div className="relative w-20 h-20 mb-3 bg-[#f0e6da] rounded-full flex items-center justify-center shadow">
                                    <span className="text-2xl font-bold text-[#8B7355]">
                                        {category.category_name.charAt(0)}
                                    </span>
                                </div>
                                
                                <span className="text-[#4B3B2A] font-medium font-serif text-sm sm:text-base">
                                    {category.category_name}
                                </span>
                            </a>
                        ))}
                    </div>
                )}
            </div>
        </AppSidebarLayout>
    );
}