// resources/js/Components/SearchFilters.tsx
import React from 'react';
import type { Category, SearchFilters } from '@/types';

interface Props {
  categories: Category[];
  filters: SearchFilters;
  onFilterChange: (filters: Partial<SearchFilters>) => void;
  onClear: () => void;
}

const SearchFilters: React.FC<Props> = ({ categories, filters, onFilterChange, onClear }) => {
  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    onFilterChange({ [name]: value });
  };

  const hasActiveFilters = Object.entries(filters).some(([key, val]) => val && val !== 'newest');

  const currentYear = new Date().getFullYear();

  return (
    <div className="bg-[#FDF9F4] border border-[#D2C2B5] rounded-lg p-6 mb-6">
      <div className="flex items-center justify-between mb-4">
        <h3 className="text-lg font-medium text-[#4B2E2B]">Filters</h3>
        {hasActiveFilters && (
          <button
            onClick={onClear}
            className="text-sm text-[#A94438] hover:text-[#8B3B2F] font-medium"
          >
            Clear all filters
          </button>
        )}
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div>
          <label className="block text-sm font-medium text-[#4B2E2B] mb-1">Category</label>
          <select
            name="category"
            value={filters.category || ''}
            onChange={handleChange}
            className="w-full px-3 py-2 border border-[#D2C2B5] rounded-md bg-[#FDF9F4] text-[#4B2E2B]"
          >
            <option value="">All Categories</option>
            {categories.map((cat) => (
              <option key={cat.cat_id} value={cat.cat_id.toString()}>
                {cat.cat_name}
              </option>
            ))}
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium text-[#4B2E2B] mb-1">Author</label>
          <input
            type="text"
            name="author"
            value={filters.author || ''}
            onChange={handleChange}
            placeholder="Filter by author"
            className="w-full px-3 py-2 border border-[#D2C2B5] rounded-md bg-[#FDF9F4] text-[#4B2E2B]"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-[#4B2E2B] mb-1">Sort By</label>
          <select
            name="sort"
            value={filters.sort || 'newest'}
            onChange={handleChange}
            className="w-full px-3 py-2 border border-[#D2C2B5] rounded-md bg-[#FDF9F4] text-[#4B2E2B]"
          >
            <option value="newest">Newest First</option>
            <option value="title">Title A-Z</option>
            <option value="author">Author A-Z</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
          </select>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label className="block text-sm font-medium text-[#4B2E2B] mb-1">Min Price</label>
          <input
            type="number"
            name="min_price"
            value={filters.min_price || ''}
            onChange={handleChange}
            placeholder="0"
            className="w-full px-3 py-2 border border-[#D2C2B5] rounded-md bg-[#FDF9F4] text-[#4B2E2B]"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-[#4B2E2B] mb-1">Max Price</label>
          <input
            type="number"
            name="max_price"
            value={filters.max_price || ''}
            onChange={handleChange}
            placeholder="1000"
            className="w-full px-3 py-2 border border-[#D2C2B5] rounded-md bg-[#FDF9F4] text-[#4B2E2B]"
          />
        </div>
        <div>
          <label className="block text-sm font-medium text-[#4B2E2B] mb-1">Publication Year</label>
          <div className="flex space-x-2">
            <input
              type="number"
              name="year_from"
              value={filters.year_from || ''}
              onChange={handleChange}
              placeholder="From"
              min="1800"
              max={currentYear}
              className="w-full px-3 py-2 border border-[#D2C2B5] rounded-md bg-[#FDF9F4] text-[#4B2E2B]"
            />
            <input
              type="number"
              name="year_to"
              value={filters.year_to || ''}
              onChange={handleChange}
              placeholder="To"
              min="1800"
              max={currentYear}
              className="w-full px-3 py-2 border border-[#D2C2B5] rounded-md bg-[#FDF9F4] text-[#4B2E2B]"
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default SearchFilters;