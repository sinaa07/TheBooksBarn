import React from 'react';
import { Head } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import BookGrid from '@/components/book-grid';
import Pagination from '@/components/pagination';
import { Book, PaginatedBooks, Category } from '@/types';

interface BooksIndexProps {
  books: PaginatedBooks;
  categories?: Category[]; // optional for now
  filters: {
    q?: string; // search term from backend
    category_id?: string;
    sort_by?: string;
    min_price?: string;
    max_price?: string;
  };
}

export default function BooksIndex({ books, categories, filters }: BooksIndexProps) {
  // Basic search stats from backend data
  const hasSearch =
    !!filters.q || !!filters.category_id || !!filters.sort_by || !!filters.min_price || !!filters.max_price;

  const searchStats = {
    total: books.total,
    showing: books.data.length,
    hasSearch,
  };

  return (
    <AppSidebarLayout>
      <Head title="Books" />
      
      <div className="container h-full mx-auto px-4 py-8 bg-[#F5F0EB]">
        {/* Page Heading */}
        <div className="mb-8">
          <h1 className="text-4xl font-extrabold text-[#4B3B2A] font-serif mb-3">
            Book Collection
          </h1>
          <div className="text-sm text-[#7A5E52]">
            {searchStats.hasSearch ? (
              <>Showing {searchStats.showing} of {searchStats.total} results</>
            ) : (
              <>{searchStats.total} books available</>
            )}
          </div>
        </div>

        {/* Book Grid */}
        <BookGrid books={books.data} />

        {/* Empty State */}
        {books.data.length === 0 && (
          <div className="text-center py-12">
            <svg
              className="mx-auto h-12 w-12 text-[#D2C2B5]"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.034 0-3.935.616-5.5 1.665a10 10 0 006.933-2.165 5.002 5.002 0 011.568-6.933A10 10 0 0112 9z"
              />
            </svg>
            <h3 className="mt-2 text-sm font-medium text-[#4B2E2B]">No books found</h3>
            <p className="mt-1 text-sm text-[#7A5E52]">
              {searchStats.hasSearch
                ? 'Try adjusting your search terms or filters.'
                : 'No books are currently available.'}
            </p>
          </div>
        )}

        {/* Pagination */}
        <Pagination links={books.links} />
      </div>
    </AppSidebarLayout>
  );
}