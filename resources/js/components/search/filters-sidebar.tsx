import React, { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';
import { Category,PriceRange,Filters } from '@/types';

interface FiltersSidebarProps {
  categories: Category[];
  formats: string[];
  priceRange: PriceRange;
  currentFilters: Filters;
  className?: string;
}

const FiltersSidebar: React.FC<FiltersSidebarProps> = ({
    categories,
    formats,
    priceRange,
    currentFilters,
    className = ""
  }) => {
    // Local state for form inputs
    const [selectedCategoryId, setSelectedCategoryId] = useState<string>(
      currentFilters.category_id?.toString() || ''
    );
    const [selectedFormat, setSelectedFormat] = useState<string>(
      currentFilters.format || ''
    );
    const [minPrice, setMinPrice] = useState<number>(
      Number(currentFilters.min_price) || priceRange?.min_price || 0
    );
    const [maxPrice, setMaxPrice] = useState<number>(
      Number(currentFilters.max_price) || priceRange?.max_price || 100
    );
  
    // Update local state when currentFilters change (e.g., from URL changes)
    useEffect(() => {
      setSelectedCategoryId(currentFilters.category_id?.toString() || '');
      setSelectedFormat(currentFilters.format || '');
      setMinPrice(Number(currentFilters.min_price) || priceRange?.min_price || 0);
      setMaxPrice(Number(currentFilters.max_price) || priceRange?.max_price || 100);
    }, [currentFilters, priceRange]);
  
    const handleApplyFilters = () => {
      const newFilters: Record<string, any> = {
        // Keep existing search query
        ...(currentFilters.q && { q: currentFilters.q }),
        // Apply new filters
        ...(selectedCategoryId && { category_id: selectedCategoryId }),
        ...(selectedFormat && { format: selectedFormat }),
        ...(minPrice !== (priceRange?.min_price || 0) && { min_price: minPrice }),
        ...(maxPrice !== (priceRange?.max_price || 100) && { max_price: maxPrice }),
        // Keep existing sort
        ...(currentFilters.sort_by && { sort_by: currentFilters.sort_by }),
      };
  
      router.visit(route('books.search'), {
        data: newFilters,
        preserveState: true,
        preserveScroll: true,
      });
    };
  
    const handleClearFilters = () => {
      setSelectedCategoryId('');
      setSelectedFormat('');
      setMinPrice(priceRange?.min_price || 0);
      setMaxPrice(priceRange?.max_price || 100);
  
      const newFilters: Record<string, any> = {
        // Keep only search query and sort
        ...(currentFilters.q && { q: currentFilters.q }),
        ...(currentFilters.sort_by && { sort_by: currentFilters.sort_by }),
      };
  
      router.visit(route('books.search'), {
        data: newFilters,
        preserveState: true,
        preserveScroll: true,
      });
    };
  
    const hasActiveFilters = selectedCategoryId || selectedFormat || 
      minPrice !== (priceRange?.min_price || 0) || maxPrice !== (priceRange?.max_price || 100);
  
    return (
      <div className={`bg-white rounded-lg shadow-sm border p-6 ${className}`}>
        <div className="flex items-center justify-between mb-6">
          <h3 className="text-lg font-semibold text-gray-900">Filters</h3>
          {hasActiveFilters && (
            <button
              onClick={handleClearFilters}
              className="text-sm text-blue-600 hover:text-blue-800 underline"
            >
              Clear All
            </button>
          )}
        </div>
  
        <div className="space-y-6">
          {/* Category Filter */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-3">
              Category
            </label>
            <div className="space-y-2 max-h-48 overflow-y-auto">
              <label className="flex items-center">
                <input
                  type="radio"
                  name="category"
                  value=""
                  checked={selectedCategoryId === ''}
                  onChange={(e) => setSelectedCategoryId(e.target.value)}
                  className="mr-2 text-blue-600"
                />
                <span className="text-sm text-gray-600">All Categories</span>
              </label>
              {categories.map((category) => (
                <label key={category.id} className="flex items-center">
                  <input
                    type="radio"
                    name="category"
                    value={category.id}
                    checked={selectedCategoryId === category.id.toString()}
                    onChange={(e) => setSelectedCategoryId(e.target.value)}
                    className="mr-2 text-blue-600"
                  />
                  <span className="text-sm text-gray-700">{category.category_name}</span>
                </label>
              ))}
            </div>
          </div>
  
          {/* Format Filter */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-3">
              Format
            </label>
            <select
              value={selectedFormat}
              onChange={(e) => setSelectedFormat(e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Formats</option>
              {formats.map((format) => (
                <option key={format} value={format}>
                  {format}
                </option>
              ))}
            </select>
          </div>
  
          {/* Price Range Filter */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-3">
              Price Range
            </label>
            {priceRange && priceRange.max_price > priceRange.min_price ? (
              <div className="space-y-4">
                <div>
                  <label className="block text-xs text-gray-500 mb-1">
                    Min Price: ${minPrice}
                  </label>
                  <input
                    type="range"
                    min={priceRange.min_price}
                    max={priceRange.max_price}
                    value={minPrice}
                    onChange={(e) => {
                      const value = Number(e.target.value);
                      setMinPrice(value);
                      if (value > maxPrice) {
                        setMaxPrice(value);
                      }
                    }}
                    className="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                  />
                </div>
                <div>
                  <label className="block text-xs text-gray-500 mb-1">
                    Max Price: ${maxPrice}
                  </label>
                  <input
                    type="range"
                    min={priceRange.min_price}
                    max={priceRange.max_price}
                    value={maxPrice}
                    onChange={(e) => {
                      const value = Number(e.target.value);
                      setMaxPrice(value);
                      if (value < minPrice) {
                        setMinPrice(value);
                      }
                    }}
                    className="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                  />
                </div>
                <div className="flex justify-between text-xs text-gray-500">
                  <span>${priceRange.min_price}</span>
                  <span>${priceRange.max_price}</span>
                </div>
              </div>
            ) : priceRange && priceRange.min_price === priceRange.max_price && priceRange.min_price > 0 ? (
              <div className="text-sm text-gray-500 py-4">
                All books are priced at ${priceRange.min_price}
              </div>
            ) : (
              <div className="text-sm text-gray-500 py-4">
                No price data available
              </div>
            )}
          </div>
  
          {/* Apply Filters Button */}
          <button
            onClick={handleApplyFilters}
            className="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
          >
            Apply Filters
          </button>
        </div>
      </div>
    );
  };
  
  export default FiltersSidebar;