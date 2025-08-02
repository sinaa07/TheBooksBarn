import { usePage } from '@inertiajs/react';

export default function FlashMessage() {
  const { flash } = usePage().props;

  if (!flash.success && !flash.error) return null;

  const type = flash.success ? 'success' : 'error';
  const message = flash.success || flash.error;

  return (
    <div
      className={`fixed top-5 right-5 z-50 px-4 py-2 rounded-lg shadow-lg transition ${
        type === 'success'
          ? 'bg-green-600 text-white'
          : 'bg-red-600 text-white'
      }`}
    >
      {message}
    </div>
  );
}