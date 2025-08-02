import { router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useState } from 'react';
import Toast from '@/components/ui/toast';
import { User } from '@/types';

interface AddToCartProps {
  bookId: number;
}

export default function AddToCartButton({ bookId }: AddToCartProps) {
  const [showToast, setShowToast] = useState(false);
  const [toastMessage, setToastMessage] = useState('');
  const { props } = usePage<{ auth?: { user?: User } }>();
  const user = props.auth?.user;

  const showSuccessToast = (message: string) => {
    setShowToast(false);
    setTimeout(() => {
      setToastMessage(message);
      setShowToast(true);
    }, 10);
  };

  const handleAddToCart = () => {
    if (!user) {
      // Redirect to login if not authenticated
      router.visit(route('login'));
      return;
    }

    router.post(
      route('cart.store', { book: bookId }),
      {},
      {
        onSuccess: () => {
          showSuccessToast('Book added to cart.');
        },
      }
    );
  };

  return (
    <>
      {showToast && <Toast message={toastMessage} type="success" />}
      <form
        onSubmit={(e) => {
          e.preventDefault();
          handleAddToCart();
        }}
      >
        <button
          type="submit"
          className="bg-[#8B5E3C] hover:bg-[#704832] text-white px-6 py-2 rounded-lg font-medium transition cursor-pointer"
        >
          Add to Cart
        </button>
      </form>
    </>
  );
}