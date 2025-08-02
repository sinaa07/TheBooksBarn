import { router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { QuantityControlProps } from '@/types';

export default function QuantityControl({ bookId, quantity }: QuantityControlProps) {
  const updateQuantity = (newQty: number) => {
    if (newQty < 1) return;
    router.patch(route('cart.update', bookId), { quantity: newQty });
  };

  return (
    <div className="flex items-center gap-2">
      <button
        onClick={() => updateQuantity(quantity - 1)}
        disabled={quantity <= 1}
        className="px-2 py-1 bg-[#E8DDD2] text-[#4B2E2B] rounded hover:bg-[#D2C2B5] disabled:opacity-50"
      >
        –
      </button>
      <span className="px-3 text-[#4B2E2B] font-medium">{quantity}</span>
      <button
        onClick={() => updateQuantity(quantity + 1)}
        className="px-2 py-1 bg-[#E8DDD2] text-[#4B2E2B] rounded hover:bg-[#D2C2B5]"
      >
        +
      </button>
    </div>
  );
}