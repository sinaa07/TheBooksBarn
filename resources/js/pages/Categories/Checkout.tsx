import { router } from '@inertiajs/react';
import { useState } from 'react';
import { Book,Category,Address,CartItem } from '@/types';

interface CheckoutProps {
  cartItems: CartItem[];
  total: number;
  user: {
    name: string;
    email: string;
    mobile?: string;
    address?: Address;
  };
}

export default function Checkout({ cartItems, total, user }: CheckoutProps) {
  console.log(user.address);
  const [form, setForm] = useState({
    name: user.name || '',
    mobile: user.mobile || '',
    address: user.address
    ? `${user.address.address_line1}, ${user.address.city}, ${user.address.state}, ${user.address.country} - ${user.address.postal_code}`
    : '',
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    router.post(route('checkout.store'), form);
  };

  return (
    
  <div className="min-h-screen flex items-start justify-center bg-[#F5F0EB] py-10 px-4">
    <div className="w-full max-w-5xl bg-white rounded-2xl shadow-lg p-8">
      <h2 className="text-3xl font-bold text-[#4B2E2B] mb-6 text-center">Checkout</h2>
  
      <div className="grid md:grid-cols-2 gap-10">
        {/* 🛒 Cart Summary */}
        <div>
          <h3 className="text-xl font-semibold text-[#4B2E2B] mb-3 border-b pb-1">Your Cart</h3>
          <ul className="space-y-3">
            {cartItems.map((item, index) => (
              <li key={index} className="flex justify-between items-center bg-[#FDF9F4] px-4 py-3 rounded-lg">
                <div>
                  <p className="font-medium text-[#4B2E2B]">{item.book.name}</p>
                  <p className="text-sm text-[#7A5E52]">{item.quantity} × ₹{item.unit_price}</p>
                </div>
                <p className="text-[#4B2E2B] font-semibold">
                  ₹{item.quantity * item.unit_price}
                </p>
              </li>
            ))}
          </ul>
          <div className="text-right mt-5 text-lg font-bold text-[#4B2E2B] border-t pt-3">
            Total: ₹{total.toFixed(2)}
          </div>
        </div>
  
        {/* 📦 Shipping Details */}
        <div>
          <h3 className="text-xl font-semibold text-[#4B2E2B] mb-3 border-b pb-1">Shipping Info</h3>
          <form className="space-y-5" onSubmit={handleSubmit}>
            <div>
              <label className="block text-[#4B2E2B] mb-1">Full Name</label>
              <input
                type="text"
                name="name"
                value={form.name}
                onChange={handleChange}
                className="w-full px-4 py-2 border border-[#D2C2B5] text-amber-900 rounded-lg focus:outline-none focus:ring"
                required
              />
            </div>
            <div>
              <label className="block text-[#4B2E2B] mb-1">Mobile</label>
              <input
                type="text"
                name="mobile"
                value={form.mobile}
                onChange={handleChange}
                className="w-full px-4 py-2 border border-[#D2C2B5] text-amber-900 rounded-lg focus:outline-none focus:ring"
                required
              />
            </div>
            <div>
              <label className="block text-[#4B2E2B] mb-1">Address</label>
              <textarea
                name="address"
                value={form.address}
                onChange={handleChange}
                className="w-full px-4 py-2 border border-[#D2C2B5] text-amber-900 rounded-lg focus:outline-none focus:ring"
                rows={3}
                required
              ></textarea>
            </div>

            <button
              type="submit" 
              className="w-full bg-[#4B2E2B] text-white py-3 rounded-lg hover:bg-[#3B1F1B] transition"
            >
              Proceed to Pay
            </button>

            <button type="button" onClick={() => router.visit(route('cart.index'))} 
            className="w-full mt-2 border border-[#4B2E2B] text-[#4B2E2B] py-3 rounded-lg hover:bg-[#F3EDE7] transition" 
            > 
            Cancel
            </button>
            
          </form>
        </div>
      </div>
    </div>
  </div>
  );
}