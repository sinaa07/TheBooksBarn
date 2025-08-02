import { Head } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';

import { Category,Book } from '@/types';

interface Props {
    allCategories: Category[];
}

export default function CategoriesPage({ allCategories }: Props) {
    return (
        <AppSidebarLayout>
            <Head title="All Categories" />

            <div className="h-full bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa] py-10 px-6">
                <h1 className="text-4xl font-extrabold text-[#4B3B2A] font-serif mb-8 text-center">📚 All Categories</h1>

                {allCategories.length === 0 ? (
                    <p className="text-center text-[#5C4033] italic font-sans">No categories found.</p>
                ) : (
                    <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                        {allCategories.map((category) => (
                            <a
                                key={category.cat_id}
                                href={`/categories/${category.slug}`}
                                className="block bg-white hover:bg-[#f3eae3] text-[#4B3B2A] py-4 px-3 rounded-xl text-center font-medium shadow-md transition min-h-[160px] flex flex-col items-center justify-center text-sm sm:text-base border border-[#e0d4c3]"
                            >
                                <img
                                    src={category.image}
                                    alt={category.cat_name}
                                    className="h-20 w-20 object-cover rounded-full mb-2 shadow"
                                />
                                {category.cat_name}
                            </a>
                        ))}
                    </div>
                )}
            </div>
        </AppSidebarLayout>
    );
}