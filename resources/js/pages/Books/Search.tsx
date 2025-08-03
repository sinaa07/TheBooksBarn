import React from 'react';
import { Head } from '@inertiajs/react';
import { Book, Category, Filters, PaginatedBooks, PriceRange } from '@/types';
import BookGrid from '@/components/book-grid';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import FiltersSidebar from '@/components/search/filters-sidebar';

interface Props {
  books: PaginatedBooks;
  filters: Filters;
  categories: Category[];
  formats: Book['format'][];
  priceRange: PriceRange;
}

const SearchResults: React.FC<Props> = ({ books, filters, categories, formats, priceRange    }) => {
  const query = filters.q || '';

  return (
    <AppSidebarLayout>
      <Head title={`Search results for "${query}"`} />
      <div className="h-full px-4 py-6 bg-[#F5F0EB]">
        <h2 className="text-2xl font-semibold text-[#4B2E2B] mb-4">
          Search results for "{query}"
        </h2>
        <div className="flex">
            <div className="w-1/4">
                <FiltersSidebar categories={categories} formats={formats} priceRange={priceRange} currentFilters={filters} />
            </div>
        </div>
        {books.data.length > 0 ? (
          <BookGrid books={books.data} />
        ) : (
          <p className="text-[#7A5E52] text-lg">No results found.</p>
        )}
      </div>
    </AppSidebarLayout>
  );
};

export default SearchResults;