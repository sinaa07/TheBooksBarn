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

            <div className="min-h-screen bg-gradient-to-br from-[#faf6f1] via-[#f0e6dd] to-[#dbcbb9] flex flex-col">
                {/* Hero Section */}
                <section className="flex items-center justify-center px-4 py-20 bg-gradient-to-br from-[#fefaf6] to-[#f6ece2]">
                    <div className="max-w-3xl text-center">
                        <h2 className="text-4xl sm:text-5xl font-extrabold text-[#3e2d1e] font-serif mb-6 leading-tight drop-shadow-sm">
                            Your Favorite Books. One Store. Infinite Stories.
                        </h2>
                        <p className="text-[#5a4332] text-lg mb-10 font-sans max-w-xl mx-auto">
                            Shop new releases, classics, bestsellers and more — all in one place.
                            Discover your next read today.
                        </p>
                        <a
                            href={route('login')}
                            className="inline-flex items-center bg-[#8B5E3C] hover:bg-[#724a2e] text-white px-7 py-3.5 rounded-2xl text-base font-semibold transition-all shadow-md hover:shadow-lg"
                        >
                            <BookOpen className="w-5 h-5 mr-2" />
                            Start Shopping
                        </a>
                    </div>
                </section>

                {/* Popular Categories */}
                <section className="bg-[#fcfaf8] py-14 px-4 border-t border-[#e7d9ca]">
                    <div className="max-w-7xl mx-auto">
                        <h2 className="text-2xl font-bold text-[#3e2d1e] font-serif mb-8">
                            Popular Categories
                        </h2>
                        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-5">
                            {popularCategories.map(category => (
                                <a
                                    key={category.id}
                                    href={`/categories/${category.slug}`}
                                    className="bg-gradient-to-br from-[#fffdfc] to-[#f6ede6] hover:from-[#f5e9e1] hover:to-[#ebddd2] text-[#3e2d1e] py-6 px-4 rounded-xl text-center font-medium shadow-sm hover:shadow-md transition-all border border-[#e8dccf]"
                                >
                                    {category.category_name}
                                </a>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Featured Books */}
                <section className="bg-[#fffdfb] py-14 px-4 border-t border-[#e7d9ca]">
                    <div className="max-w-7xl mx-auto">
                        <h2 className="text-2xl font-bold text-[#3e2d1e] font-serif mb-8">
                            Featured Books
                        </h2>
                        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-7">
                            {featuredBooks.map(book => (
                                <a
                                    key={book.id}
                                    href={`/books/${book.id}`}
                                    className="block bg-white border border-[#e7d9ca] rounded-xl shadow-sm hover:shadow-lg transition-all overflow-hidden"
                                >
                                    <div className="w-full h-52 bg-gray-100 flex items-center justify-center">
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
                                    <div className="p-4">
                                        <h3 className="text-sm font-semibold text-[#3e2d1e] font-serif truncate mb-1">
                                            {book.title}
                                        </h3>
                                        <p className="text-xs text-[#5a4332] truncate mb-3">
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
                <section className="bg-[#fcfaf8] py-14 px-4 border-t border-[#e7d9ca]">
                    <div className="max-w-6xl mx-auto grid gap-10 md:grid-cols-3 text-center">
                        <div>
                            <h3 className="text-lg font-semibold text-[#8B5E3C] mb-2 font-serif">
                                Huge Collection
                            </h3>
                            <p className="text-[#5a4332] text-sm font-sans">
                                Thousands of titles across all genres. Something for every reader.
                            </p>
                        </div>
                        <div>
                            <h3 className="text-lg font-semibold text-[#8B5E3C] mb-2 font-serif">
                                Fast Delivery
                            </h3>
                            <p className="text-[#5a4332] text-sm font-sans">
                                Quick and reliable shipping to your doorstep.
                            </p>
                        </div>
                        <div>
                            <h3 className="text-lg font-semibold text-[#8B5E3C] mb-2 font-serif">
                                Secure Checkout
                            </h3>
                            <p className="text-[#5a4332] text-sm font-sans">
                                Your data and payments are encrypted and protected.
                            </p>
                        </div>
                    </div>
                </section>

                {/* Footer */}
                <footer className="text-center text-sm text-[#7d5a4f] py-8 border-t border-[#e7d9ca] font-sans">
                    &copy; {new Date().getFullYear()} BookCart. All rights reserved.
                </footer>
            </div>
        </>
    );
}

Home.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;