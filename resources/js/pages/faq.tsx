import React from 'react';
import AppLayout from '@/layouts/app-layout';

const FaqPage = () => {
  return (
    <AppLayout>
      <div className="h-full bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa] px-6 py-10">
        <h1 className="text-4xl font-extrabold text-[#4B3B2A] font-serif text-center mb-10">
          📖 Frequently Asked Questions
        </h1>

        <div className="max-w-2xl mx-auto space-y-6">
          <details className="bg-white border border-[#d6c2aa] rounded-lg p-4 shadow-sm">
            <summary className="cursor-pointer text-[#4B3B2A] font-semibold font-serif mb-2">
              How long does delivery take?
            </summary>
            <p className="text-[#5C4033] font-sans text-sm mt-2">
              Our standard delivery time is 3–5 business days depending on your location.
            </p>
          </details>

          <details className="bg-white border border-[#d6c2aa] rounded-lg p-4 shadow-sm">
            <summary className="cursor-pointer text-[#4B3B2A] font-semibold font-serif mb-2">
              Can I return a book if I don’t like it?
            </summary>
            <p className="text-[#5C4033] font-sans text-sm mt-2">
              Yes, we offer a 7-day return window for eligible books in original condition.
            </p>
          </details>

          <details className="bg-white border border-[#d6c2aa] rounded-lg p-4 shadow-sm">
            <summary className="cursor-pointer text-[#4B3B2A] font-semibold font-serif mb-2">
              How do I track my order?
            </summary>
            <p className="text-[#5C4033] font-sans text-sm mt-2">
              You can track your order using the tracking ID provided in your shipment confirmation email.
            </p>
          </details>

          <details className="bg-white border border-[#d6c2aa] rounded-lg p-4 shadow-sm">
            <summary className="cursor-pointer text-[#4B3B2A] font-semibold font-serif mb-2">
              Do you ship internationally?
            </summary>
            <p className="text-[#5C4033] font-sans text-sm mt-2">
              Currently, we only ship within India. International delivery is coming soon!
            </p>
          </details>

          <details className="bg-white border border-[#d6c2aa] rounded-lg p-4 shadow-sm">
            <summary className="cursor-pointer text-[#4B3B2A] font-semibold font-serif mb-2">
              Can I get a gift-wrapped order?
            </summary>
            <p className="text-[#5C4033] font-sans text-sm mt-2">
              Yes! You can select the gift-wrap option during checkout for an additional ₹50.
            </p>
          </details>
        </div>
      </div>
    </AppLayout>
  );
};

export default FaqPage;