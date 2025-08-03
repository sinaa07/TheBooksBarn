import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Head } from '@inertiajs/react';
import { Book,Category, PaginatedBooks } from '@/types';
import BookGrid from '@/components/book-grid';
import Pagination from '@/components/pagination';
interface Props {
    books: PaginatedBooks;
    category:Category;
}

export default function BooksIndex({ books , category}: Props) {
    return (
        <AppSidebarLayout>
        <div className="container mx-auto px-4 py-8 bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa]">

            <div>
                <h1 className="text-4xl font-extrabold text-[#4B3B2A] font-serif mb-6">{category.category_name}</h1>

                {books.data.length === 0 ? (
                    <p className="text-center text-[#5C4033] italic font-sans">No books found.</p>
                ) : (
                    <BookGrid books={books.data}/>
                )}
            </div>
            <Pagination links={books.links} />
        </div>
        </AppSidebarLayout>
    );
}
