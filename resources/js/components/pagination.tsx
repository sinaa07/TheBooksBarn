import React from 'react';
import { router } from '@inertiajs/react';
import { PaginationLink } from '@/types';

interface PaginationProps {
  links: PaginationLink[];
}

export default function Pagination({ links }: PaginationProps) {
  return (
  <div className="flex justify-center items-center space-x-2 mt-6">
    {links.map((link, index) => (
      <button
        key={index}
        onClick={() => router.visit(link.url || '#')}
        disabled={!link.url || link.active}
        className={`px-3 py-1 text-sm rounded ${
          link.active
            ? 'bg-[#4B2E2B] text-white'
            : 'text-[#4B2E2B] hover:bg-[#EDE6DD]'
        }`}
      >
        {link.label.match(/^\d+$/) ? link.label : null}
      </button>
    ))}
  </div>
  );
}