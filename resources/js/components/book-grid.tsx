import React from 'react';
import { Link } from '@inertiajs/react';
import { Book } from '@/types';

interface BookGridProps {
  books: Book[];
}

const BookGrid: React.FC<BookGridProps> = ({ books }) => {
  const formatPrice = (price: number): string => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'INR'
    }).format(price);
  };

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      {books.map(book => (
        <Link 
          key={book.book_id}
          href={`/books/${book.slug}`}
          className="bg-[#FDF9F4] rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200 block"
        >
          <div className="aspect-w-3 aspect-h-4 bg-[#EADDC8]">
            {book.image ? (
              <img 
                src={book.image} 
                alt={book.name}
                className="w-full h-48 object-cover"
              />
            ) : (
              <div className="w-full h-48 bg-gradient-to-br from-[#EADDC8] to-[#D6C4B0] flex items-center justify-center">
                <svg className="w-12 h-12 text-[#B6A692]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
            )}
          </div>

          <div className="p-4">
            <h3 className="font-semibold text-[#4B2E2B] mb-1 line-clamp-2">
              {book.name}
            </h3>
            <p className="text-sm text-[#7A5E52] mb-2">by {book.author}</p>
            {book.category && (
              <span className="inline-block px-2 py-1 text-xs font-medium text-[#4B2E2B] bg-[#EADDC8] rounded-full mb-2">
                {book.category.cat_name}
              </span>
            )}
            <div className="flex items-center justify-between">
              <span className="text-lg font-bold text-[#4B2E2B]">
                {formatPrice(book.price)}
              </span>
            </div>
          </div>
        </Link>
      ))}
    </div>
  );
};

export default BookGrid;