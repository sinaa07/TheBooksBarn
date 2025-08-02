import React from 'react';
import { Head } from '@inertiajs/react';
import { PaginatedBooks } from '@/types';
import BookGrid from '@/components/book-grid';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';

interface Props {
  books: PaginatedBooks;
  query: string;
}

const SearchResults: React.FC<Props> = ({ books, query }) => {
    console.log("Books received:", books);
  return (
    <>
    <AppSidebarLayout>
      <Head title={`Search results for "${query}"`} />
      <div className="h-full px-4 py-6 bg-[#F5F0EB]">
        <h2 className="text-2xl font-semibold text-[#4B2E2B] mb-4">
          Search results for "{query}"
        </h2>
        

        {books.data.length > 0 ? (
          <BookGrid books={books.data} />
        ) : (
          <p className="text-[#7A5E52] text-lg">No results found.</p>
        )}
      </div>
      </AppSidebarLayout>
    </>
  );
};

export default SearchResults;