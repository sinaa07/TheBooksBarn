import React from 'react';
import { Link } from '@inertiajs/react';
import { Book } from '@/types';
import { BookOpen } from 'lucide-react';

interface BookGridProps {
  books: Book[];
}

const BookGrid: React.FC<BookGridProps> = ({ books }) => {
  return (
    <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-7">
      {books.map(book => (
        <Link
          key={book.id}
          href={`/books/${book.id}`}
          className="block bg-white border border-[#e0d4c3] rounded-xl shadow-sm hover:shadow-md transition overflow-hidden"
        >
          {/* Book Cover */}
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

          {/* Book Info */}
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
        </Link>
      ))}
    </div>
  );
};

export default BookGrid;