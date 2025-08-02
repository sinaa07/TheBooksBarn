import React, { useState, useEffect } from 'react';
import { Head, router } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import SearchBar from '@/components/search-bar';
import SearchFilters from '@/components/search-filters';
import BookGrid from '@/components/book-grid';
import Pagination from '@/components/pagination';
import { useDebouncedCallback } from 'use-debounce';
import { Book, Category, PaginatedBooks, SearchFilters as SearchFiltersType, SearchStats } from '@/types';

interface BooksIndexProps {
  books: PaginatedBooks;
  categories: Category[];
  filters: SearchFiltersType;
  searchStats: SearchStats;
}

const BooksIndex: React.FC<BooksIndexProps> = ({ 
  books, 
  categories,
  searchStats 
}) => {
  

  return (
    <AppSidebarLayout>
      <Head title="Books" />
      
      <div className="container mx-auto px-4 py-8 bg-[#F5F0EB]">
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-[#4B2E2B] mb-4">
            Book Collection
          </h1>
           <div className="text-sm text-[#7A5E52]">
              {searchStats.hasSearch ? (
                <>Showing {searchStats.showing} of {searchStats.total} results</>
              ) : (
                <>{searchStats.total} books total</>
              )}
            </div>
        </div>


        <BookGrid books={books.data} />

        {books.data.length === 0 && (
          <div className="text-center py-12">
            <svg className="mx-auto h-12 w-12 text-[#D2C2B5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.034 0-3.935.616-5.5 1.665a10 10 0 006.933-2.165 5.002 5.002 0 011.568-6.933A10 10 0 0112 9z" />
            </svg>
            <h3 className="mt-2 text-sm font-medium text-[#4B2E2B]">No books found</h3>
            <p className="mt-1 text-sm text-[#7A5E52]">
              {searchStats.hasSearch 
                ? 'Try adjusting your search terms or filters.'
                : 'No books are currently available.'
              }
            </p>
          </div>
        )}

        <Pagination links={books.links} />
      </div>
    </AppSidebarLayout>
  );
};

export default BooksIndex;