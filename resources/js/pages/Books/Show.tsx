import { Head } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import AddToCartButton from '@/components/add-to-cart';
import { Book, Category} from '@/types';

interface Props {
    book: Book;
}

export default function BookShow({ book }: Props) {
    return (
        <AppSidebarLayout>
            <Head title={book.name} />

            <div className="h-full bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa] py-10 px-6">
                <div className="flex flex-col md:flex-row gap-8">
                    <img
                        src={book.image}
                        alt={book.name}
                        className="w-full md:w-64 h-auto object-cover rounded-lg shadow"
                    />

                    <div className="flex-1">
                        <h1 className="text-4xl font-extrabold text-[#4B3B2A] font-serif mb-2">{book.name}</h1>
                        <p className="text-sm text-[#5C4033] italic mb-1">by {book.author}</p>
                        <p className="text-sm text-[#8B5E3C] mb-4 underline">
                            Category: <a href={`/categories/${book.category.slug}`} className="underline">{book.category.cat_name}</a>
                        </p>

                        <p className="text-[#5C4033] text-sm font-sans mb-6 whitespace-pre-line">{book.desc}</p>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-[#4B3B2A]">
                            <p><strong>Price:</strong> ₹{book.price}</p>
                            <p><strong>Published In:</strong> {book.published_in}</p>
                            <p><strong>Publisher:</strong> {book.publisher}</p>
                            <p><strong>Language:</strong> {book.language}</p>
                            <p><strong>ISBN:</strong> {book.ISBN}</p>
                        </div>
                        <div className="mt-6 flex gap-4">
                            <AddToCartButton bookId={book.book_id}/>
                            <button className="border border-[#8B5E3C] text-[#8B5E3C] hover:bg-[#f3eae3] px-6 py-2 rounded-lg font-medium transition">
                                Add to Wishlist
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </AppSidebarLayout>
    );
}