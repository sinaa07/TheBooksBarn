import { router } from '@inertiajs/react';
import { useState } from 'react';
import { CartItem, Address, Cart, CartSummary, Payment } from '@/types';

interface CheckoutProps {
  cart: Cart;
  items: CartItem[];
  addresses: Address[];
  summary: CartSummary;
  payment_methods: Record<Payment['payment_method'], string>;
}

export default function Checkout({ cart, items, addresses, summary, payment_methods }: CheckoutProps) {
  const [selectedAddress, setSelectedAddress] = useState<number>(
    addresses.find(addr => addr.is_default)?.id || addresses[0]?.id || 0
  );
  const [selectedPaymentMethod, setSelectedPaymentMethod] = useState<string>('cash_on_delivery');
  const [notes, setNotes] = useState<string>('');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!selectedAddress) {
      alert('Please select a delivery address');
      return;
    }

    setIsProcessing(true);
    
    router.post(route('checkout.store'), {
      address_id: selectedAddress,
      payment_method: selectedPaymentMethod,
      notes: notes,
    }, {
      onFinish: () => setIsProcessing(false),
    });
  };

  return (
    <div className="min-h-screen flex items-start justify-center bg-[#F5F0EB] py-10 px-4">
      <div className="w-full max-w-6xl bg-white rounded-2xl shadow-lg p-8">
        <h2 className="text-3xl font-bold text-[#4B2E2B] mb-6 text-center">Checkout</h2>

        <div className="grid lg:grid-cols-3 gap-8">
          {/* Order Summary */}
          <div className="lg:col-span-1">
            <h3 className="text-xl font-semibold text-[#4B2E2B] mb-4 border-b pb-2">Order Summary</h3>
            
            <div className="space-y-3 mb-4">
              {items.map((item) => (
                <div key={item.id} className="flex justify-between items-start bg-[#FDF9F4] p-3 rounded-lg">
                  <div className="flex-1">
                    <p className="font-medium text-[#4B2E2B] text-sm">{item.book.title}</p>
                    <p className="text-xs text-[#7A5E52]">by {item.book.author}</p>
                    <p className="text-xs text-[#7A5E52] mt-1">
                      Qty: {item.quantity} × ₹{Number(item.unit_price).toFixed(2)}
                    </p>
                  </div>
                  <p className="text-[#4B2E2B] font-semibold text-sm">
                    ₹{Number((item.unit_price) * item.quantity).toFixed(2)}
                  </p>
                </div>
              ))}
            </div>

            <div className="space-y-2 border-t pt-3">
              <div className="flex justify-between text-[#4B2E2B]">
                <span>Subtotal ({summary.item_count} items):</span>
                <span>₹{Number(summary.subtotal).toFixed(2)}</span>
              </div>
              <div className="flex justify-between text-[#4B2E2B]">
                <span>Shipping:</span>
                <span>{summary.shipping_cost === 0 ? 'FREE' : `₹${Number(summary.shipping_cost).toFixed(2)}`}</span>
              </div>
              {summary.shipping_cost === 0 && summary.subtotal >= 500 && (
                <p className="text-xs text-green-600">🎉 Free shipping on orders over ₹500!</p>
              )}
              <div className="flex justify-between text-lg font-bold text-[#4B2E2B] pt-2 border-t">
                <span>Total:</span>
                <span>₹{Number(summary.total).toFixed(2)}</span>
              </div>
            </div>
          </div>

          {/* Checkout Form */}
          <div className="lg:col-span-2">
            <form onSubmit={handleSubmit} className="space-y-6">
              {/* Delivery Address */}
              <div>
                <h3 className="text-xl font-semibold text-[#4B2E2B] mb-4 border-b pb-2">Delivery Address</h3>
                {addresses.length === 0 ? (
                  <div className="text-center py-4">
                    <p className="text-[#7A5E52] mb-3">No saved addresses found</p>
                    <button
                      type="button"
                      onClick={() => router.visit(route('profile.addresses.create'))}
                      className="text-[#4B2E2B] underline hover:no-underline"
                    >
                      Add New Address
                    </button>
                  </div>
                ) : (
                  <div className="space-y-3">
                    {addresses.map((address) => (
                      <label
                        key={address.id}
                        className={`block p-4 rounded-lg cursor-pointer transition ${
                          selectedAddress === address.id
                            ? 'bg-[#4B2E2B] text-white'
                            : 'bg-[#FDF9F4] border border-[#D2C2B5] hover:bg-[#F3EDE7]'
                        }`}
                      >
                        <input
                          type="radio"
                          name="address"
                          value={address.id}
                          checked={selectedAddress === address.id}
                          onChange={(e) => setSelectedAddress(Number(e.target.value))}
                          className="sr-only"
                        />
                        <div className="flex justify-between items-start">
                          <div>
                            <p className="font-medium">
                              {address.name} {address.is_default && <span className="text-xs">(Default)</span>}
                            </p>
                            <p className="text-sm opacity-90">{address.phone}</p>
                            <p className="text-sm opacity-90 mt-1">
                              {address.address_line_1}
                              {address.address_line_2 && `, ${address.address_line_2}`}
                              <br />
                              {address.city}, {address.state} {address.postal_code}
                              <br />
                              {address.country}
                            </p>
                          </div>
                        </div>
                      </label>
                    ))}
                  </div>
                )}
              </div>

              {/* Payment Method */}
              <div>
                <h3 className="text-xl font-semibold text-[#4B2E2B] mb-4 border-b pb-2">Payment Method</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                  {Object.entries(payment_methods).map(([key, label]) => (
                    <label
                      key={key}
                      className={`block p-4 rounded-lg cursor-pointer transition ${
                        selectedPaymentMethod === key
                          ? 'bg-[#4B2E2B] text-white'
                          : 'bg-[#FDF9F4] border border-[#D2C2B5] hover:bg-[#F3EDE7]'
                      }`}
                    >
                      <input
                        type="radio"
                        name="payment_method"
                        value={key}
                        checked={selectedPaymentMethod === key}
                        onChange={(e) => setSelectedPaymentMethod(e.target.value)}
                        className="sr-only"
                      />
                      <p className="font-medium">{label}</p>
                    </label>
                  ))}
                </div>
              </div>

              {/* Order Notes */}
              <div>
                <h3 className="text-lg font-semibold text-[#4B2E2B] mb-3">Order Notes (Optional)</h3>
                <textarea
                  name="notes"
                  value={notes}
                  onChange={(e) => setNotes(e.target.value)}
                  placeholder="Any special instructions for delivery..."
                  className="w-full px-4 py-3 border border-[#D2C2B5] text-[#4B2E2B] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4B2E2B] focus:border-transparent"
                  rows={3}
                  maxLength={500}
                />
                <p className="text-xs text-[#7A5E52] mt-1">{notes.length}/500 characters</p>
              </div>

              {/* Action Buttons */}
              <div className="flex flex-col sm:flex-row gap-3 pt-4">
                <button
                  type="button"
                  onClick={() => router.visit(route('cart.index'))}
                  className="flex-1 border-2 border-[#4B2E2B] text-[#4B2E2B] py-3 px-6 rounded-lg hover:bg-[#F3EDE7] transition font-medium"
                >
                  ← Back to Cart
                </button>
                
                <button
                  type="submit"
                  disabled={isProcessing || addresses.length === 0}
                  className="flex-1 bg-[#4B2E2B] text-white py-3 px-6 rounded-lg hover:bg-[#3B1F1B] transition font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  {isProcessing ? 'Processing...' : 'Place Order'}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
}