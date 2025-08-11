import React, { useState } from 'react';
import { router } from '@inertiajs/react';

const SearchBar: React.FC = () => {
  const [q, setQuery] = useState('');

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter' && q.trim() !== '') {
      router.visit(route('books.search', { q: q.trim() }));
    }
  };

  return (
    <div className="relative w-full max-w-md mx-auto ">
      <input
        type="text"
        value={q}
        onChange={(e) => setQuery(e.target.value)}
        onKeyDown={handleKeyDown}
        placeholder="Search books, authors, categories..."
        className="w-full px-4 py-2 border border-[#D2C2B5] rounded-md focus:outline-none focus:ring focus:border-[#4B2E2B] text-amber-900"
      />
    </div>
  );
};

export default SearchBar;