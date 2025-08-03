import React from 'react';
import { router } from '@inertiajs/react';

interface SortDropdownProps {
  currentSort: string;
  currentFilters: Record<string, any>;
  className?: string;
}

const SortDropdown: React.FC<SortDropdownProps> = ({
  currentSort,
  currentFilters,
  className = ""
}) => {
  const sortOptions = [
    { value: 'relevance', label: 'Most Relevant' },
    { value: 'newest', label: 'Newest First' },
    { value: 'oldest', label: 'Oldest First' },
    { value: 'price_asc', label: 'Price: Low to High' },
    { value: 'price_desc', label: 'Price: High to Low' },
  ];

  const handleSortChange = (newSortBy: string) => {
    const newFilters = {
      ...currentFilters,
      sort_by: newSortBy,
    };

    router.visit(route('books.search'), {
      data: newFilters,
      preserveState: true,
      preserveScroll: true,
    });
  };

  return (
    <div className={`flex items-center space-x-2 ${className}`}>
      <label className="text-sm text-gray-600 whitespace-nowrap">
        Sort by:
      </label>
      <select
        value={currentSort}
        onChange={(e) => handleSortChange(e.target.value)}
        className="px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
      >
        {sortOptions.map((option) => (
          <option key={option.value} value={option.value}>
            {option.label}
          </option>
        ))}
      </select>
    </div>
  );
};

export default SortDropdown;