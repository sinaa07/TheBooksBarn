import { Head } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import AddToCartButton from '@/components/add-to-cart';
import { Book } from '@/types';
import { BookOpen } from 'lucide-react';

interface Props {
    book: Book;
}

export default function BookShow({ book }: Props) {
    return (
        <AppSidebarLayout>
            <Head title={book.title} />

            <div className="h-full bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa] py-10 px-6">
                <div className="max-w-7xl mx-auto">
                    <div className="flex flex-col md:flex-row gap-10">
                        
                        {/* LEFT COLUMN */}
                        <div className="w-full md:w-1/3">
                            <div className="bg-white border border-[#e0d4c3] rounded-xl shadow-sm overflow-hidden flex flex-col items-center p-4">
                                {/* Image */}
                                <div className="w-full aspect-square bg-gray-100 flex items-center justify-center rounded-lg overflow-hidden">
                                    {book.cover_image_url ? (
                                        <img
                                            src={book.cover_image_url}
                                            alt={book.title}
                                            className="w-full h-full object-cover"
                                        />
                                    ) : (
                                        <BookOpen className="w-12 h-12 text-gray-400" />
                                    )}
                                </div>

                                {/* Name & Author */}
                                <div className="mt-4 text-center">
                                    <h1 className="text-base font-bold text-[#4B3B2A] font-serif">{book.title}</h1>
                                    <p className="text-sm text-[#5C4033]">
                                        by {book.author}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* RIGHT COLUMN */}
                        <div className="flex-1 flex flex-col">
                            {/* Title */}
                            <h1 className="text-3xl md:text-4xl font-extrabold text-[#4B3B2A] font-serif mb-2">
                                {book.title}
                            </h1>
                            <p className="text-sm text-[#5C4033] mb-1">
                                by {book.author}
                            </p>
                            {book.category && (
                                <p className="text-sm text-[#8B5E3C] mb-4">
                                    Category:{' '}
                                    <a
                                        href={`/categories/${book.category.slug}`}
                                        className="underline hover:text-[#5C4033]"
                                    >
                                        {book.category.category_name}
                                    </a>
                                </p>
                            )}

                            {/* Description */}
                            {book.description && (
                                <div className="mb-8">
                                    <h2 className="text-lg font-semibold text-[#4B3B2A] mb-3">Description</h2>
                                    <p className="text-[#5C4033] whitespace-pre-line leading-relaxed">
                                        {book.description}
                                    </p>
                                </div>
                            )}

                            {/* Book Details */}
                            <div className="mb-8">
                                <h2 className="text-lg font-semibold text-[#4B3B2A] mb-3">Book Details</h2>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-y-3 text-sm text-[#4B3B2A]">
                                    {book.format && (
                                        <p>
                                            <strong>Format:</strong> {book.format}
                                        </p>
                                    )}
                                    {book.isbn && (
                                        <p>
                                            <strong>ISBN:</strong> {book.isbn}
                                        </p>
                                    )}
                                    {book.stock_quantity !== undefined && (
                                        <p>
                                            <strong>Stock:</strong>{' '}
                                            {book.stock_quantity > 0
                                                ? `${book.stock_quantity} available`
                                                : 'Out of Stock'}
                                        </p>
                                    )}
                                    {book.created_at && (
                                        <p>
                                            <strong>Created:</strong>{' '}
                                            {new Date(book.created_at).toLocaleDateString('en-IN')}
                                        </p>
                                    )}
                                    {book.updated_at && (
                                        <p>
                                            <strong>Updated:</strong>{' '}
                                            {new Date(book.updated_at).toLocaleDateString('en-IN')}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {/* Buttons at bottom */}
                            <div className="mt-auto flex flex-col sm:flex-row gap-4">
                                <AddToCartButton bookId={book.id}/>
                                <button className="flex-1 border border-[#8B5E3C] text-[#8B5E3C] hover:bg-[#f3eae3] px-4 py-2 rounded-lg font-medium transition">
                                    Add to Wishlist
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Placeholder for Similar Books */}
                    <div className="mt-14">
                        {/* <SimilarBooks books={relatedBooks} /> */}
                    </div>
                </div>
            </div>
        </AppSidebarLayout>
    );
}