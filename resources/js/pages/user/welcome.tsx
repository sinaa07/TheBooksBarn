// Example: resources/js/pages/landing.tsx

import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

const LandingPage = () => {
  return (
    <>
      <Head title="Welcome" />
      <div className="p-6">
        <h1 className="text-2xl font-bold">Landing Page</h1>
        {/* Add your sections: Category, Book list, Promotions, etc. */}
      </div>
    </>
  );
};

LandingPage.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;

export default LandingPage;