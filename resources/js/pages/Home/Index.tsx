import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { ShoppingBag, BookOpen } from 'lucide-react';
import { Book, Category } from '@/types';
import BookCarousel from '@/components/carasoul';

interface Props {
    featuredBooks: Book[];
    popularCategories: Category[];
}

export default function Home({ featuredBooks, popularCategories }: Props) {
    return (
        <>
            <Head title="Online Book Shop" />

            <div className="min-h-screen bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa] flex flex-col">
              { /* <BookCarousel books={featuredBooks}/>*/}
                {/* Hero Section */}
                <section className="flex items-center justify-center px-4 py-16">
                    <div className="max-w-2xl text-center">
                        <h2 className="text-4xl sm:text-5xl font-extrabold text-[#4B3B2A] font-serif mb-6 leading-tight">
                            Your Favorite Books. One Store. Infinite Stories.
                        </h2>
                        <p className="text-[#5C4033] text-lg mb-8 font-sans">
                            Shop new releases, classics, bestsellers and more — all in one place. Discover your next read now.
                        </p>
                        <a
                            href={route('login')}
                            className="inline-flex items-center bg-[#8B5E3C] hover:bg-[#704832] text-white px-6 py-3 rounded-xl text-base font-semibold transition"
                        >
                            <BookOpen className="w-4 h-4 mr-2" />
                            Start Shopping
                        </a>
                    </div>
                </section>

                {/* Popular Categories */}
                <section className="bg-white py-12 px-4 border-t">
                    <div className="max-w-7xl mx-auto">
                        <h2 className="text-2xl font-bold text-[#4B3B2A] font-serif mb-6">Popular Categories</h2>
                        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            {popularCategories.map(category => (
                                <a
                                    key={category.id}
                                    href={`/categories/${category.slug}`}
                                    className="bg-white hover:bg-[#f3eae3] text-[#4B3B2A] py-6 px-4 rounded-xl text-center font-medium shadow-sm transition border border-[#e0d4c3]"
                                >
                                    {category.category_name}
                                </a>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Featured Books */}
                <section className="bg-white py-12 px-4 border-t">
                    <div className="max-w-7xl mx-auto">
                        <h2 className="text-2xl font-bold text-[#4B3B2A] font-serif mb-6">Featured Books</h2>
                        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                            {featuredBooks.map(book => (
                                <a
                                    key={book.id}
                                    href={`/books/${book.id}`}
                                    className="block bg-white border border-[#e0d4c3] rounded-xl shadow hover:shadow-md transition overflow-hidden"
                                >
                                    <div className="w-full h-48 bg-gray-200 flex items-center justify-center">
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
                                    <div className="p-3">
                                        <h3 className="text-sm font-semibold text-[#4B3B2A] font-serif truncate mb-1">
                                            {book.title}
                                        </h3>
                                        <p className="text-xs text-[#5C4033] truncate mb-2">
                                            by {book.author}
                                        </p>
                                        <div className="flex items-center justify-between">
                                            <span className="text-sm font-bold text-[#8B5E3C]">
                                                ₹{book.price}
                                            </span>
                                            <span className="text-xs text-gray-500 capitalize">
                                                {book.format}
                                            </span>
                                        </div>
                                        {book.stock_quantity === 0 && (
                                            <span className="text-xs text-red-500 font-medium">
                                                Out of Stock
                                            </span>
                                        )}
                                    </div>
                                </a>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Features Section */}
                <section className="bg-white py-12 px-4 border-t">
                    <div className="max-w-6xl mx-auto grid gap-8 md:grid-cols-3 text-center">
                        <div>
                            <h3 className="text-lg font-semibold text-[#8B5E3C] mb-2 font-serif">Huge Collection</h3>
                            <p className="text-[#5C4033] text-sm font-sans">Thousands of titles across all genres. Something for every reader.</p>
                        </div>
                        <div>
                            <h3 className="text-lg font-semibold text-[#8B5E3C] mb-2 font-serif">Fast Delivery</h3>
                            <p className="text-[#5C4033] text-sm font-sans">Quick and reliable shipping to your doorstep.</p>
                        </div>
                        <div>
                            <h3 className="text-lg font-semibold text-[#8B5E3C] mb-2 font-serif">Secure Checkout</h3>
                            <p className="text-[#5C4033] text-sm font-sans">Your data and payments are encrypted and protected.</p>
                        </div>
                    </div>
                </section>

                {/* Footer */}
                <footer className="text-center text-sm text-[#7D5A4F] py-6 border-t font-sans">
                    &copy; {new Date().getFullYear()} BookCart. All rights reserved.
                </footer>
            </div>
        </>
    );
}

Home.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;