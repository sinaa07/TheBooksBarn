import { router } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import QuantityControl from '@/components/cart-control';
import { CartItem } from '@/types';

interface CartPageProps {
  cartItems: CartItem[]; 
}

export default function CartPage({ cartItems = [] }: CartPageProps) {
  const handleQuantityChange = (bookId: number, quantity: number) => {
    router.patch(route('cart.update', bookId), { quantity }, {
      preserveState: true,
    });
  };

  const removeItem = (bookId: number) => {
    router.delete(route('cart.destroy', bookId));
  };
  
  const cartTotal = cartItems.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);

  return (
    <AppSidebarLayout>
      <div className="h-full bg-[#F5F0EB] border border-[#D5C9BE] p-6 shadow-md">
        <h1 className="text-2xl font-semibold mb-4 text-[#4B2E2B]">Your Cart</h1>

        {cartItems.length === 0 ? (
          <div className="text-center py-8">
            <p className="text-[#4B2E2B] text-lg mb-4">Your cart is empty.</p>
            <button
              onClick={() => router.visit(route('books.index'))}
              className="bg-[#4B2E2B] text-white px-6 py-3 rounded-lg hover:bg-[#3B1F1B] transition"
            >
              Continue Shopping
            </button>
          </div>
        ) : (
          <div className="space-y-4">
            {cartItems.map((item) => (
              <div
                key={`cart-item-${item.book.book_id}`}
                className="bg-[#FDF9F4] border border-[#E6DDD5] rounded-lg p-4 flex justify-between items-center shadow-sm"
              >
                <div className="flex-1">
                  <h2 className="text-lg font-bold text-[#4B2E2B]">
                    {item.book.name}
                  </h2>
                  <p className="text-base text-[#6B4C47]">{item.book.author}</p>
                  <div className="text-sm mt-1 text-[#5A3E3A] space-y-1">
                    <div>
                      <span className="font-semibold">Quantity: </span>
                      {item.quantity}
                    </div>
                    <div>
                      <span className="font-semibold">Unit Price: </span>
                      ₹{Number(item.unit_price).toFixed(2)}
                    </div>
                    <div>
                      <span className="font-semibold">Amount: </span>
                      ₹{(Number(item.unit_price) *item.quantity).toFixed(2)}
                    </div>
                  </div>
                </div>
                
                <div className="flex flex-col items-center justify-center gap-2 ml-4">
                  <QuantityControl 
                    bookId={item.book.book_id} 
                    quantity={item.quantity} 
                  />
                  
                  <button
                    onClick={() => removeItem(item.book.book_id)}
                    className="text-sm bg-[#A94438] hover:bg-[#8B3B2F] text-white px-3 py-1 rounded-lg transition-colors duration-200"
                  >
                    Remove
                  </button>
                </div>
              </div>
            ))}

            <div className="bg-[#FDF9F4] border border-[#E6DDD5] rounded-lg p-4 mt-6">
              <div className="flex items-center justify-between">
                <span className="text-xl font-bold text-[#4B2E2B]">
                  Total: ₹{cartTotal.toFixed(2)}
                </span>

                <div className="flex gap-3">
                  <button
                    onClick={() => router.visit(route('home'))}
                    className="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors duration-200"
                  >
                    Continue Shopping
                  </button>
                  
                  <button
                    onClick={() => router.visit(route('checkout.index'))}
                    className="bg-[#4B2E2B] text-white px-6 py-3 rounded-lg hover:bg-[#3B1F1B] transition-colors duration-200"
                  >
                    Proceed to Checkout
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </AppSidebarLayout>
  );
}