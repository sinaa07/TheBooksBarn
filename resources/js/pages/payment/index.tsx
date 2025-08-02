import { router } from '@inertiajs/react';
import React from 'react';
import { Payment } from '@/types';


export default function PaymentIndex({ order_id, transaction_amt }: Payment) {
  const [paymentMethod, setPaymentMethod] = React.useState('COD');
  console.log('PaymentIndex props:', { order_id, transaction_amt, type: typeof transaction_amt });
  const handleConfirm = () => {
    router.post(route('payment.confirm', {order: order_id}),{
      payment_method: paymentMethod,
    });
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-[#F5F0EB] px-4 py-10">
      <div className="max-w-md w-full bg-white rounded-xl shadow-xl p-8">
        <h2 className="text-2xl font-bold text-[#4B2E2B] mb-4 text-center">Payment</h2>
        <p className="text-[#4B2E2B] mb-6 text-lg text-center">Total Amount: ₹{Number(transaction_amt)}</p>

        <div className="space-y-3">
          <label className="block">
            <input type="radio" name="payment_method" defaultChecked className="mr-2" onChange={() => setPaymentMethod('COD')}/>
            <span className="text-[#4B2E2B]">Cash on Delivery (COD)</span>
          </label>

          <label className="block ">
            <input type="radio" name="payment_method" className="mr-2" onChange={() => setPaymentMethod('UPI')}/>
            <span className="text-[#4B2E2B]">UPI</span>
          </label>

          <label className="block ">
            <input type="radio" name="payment_method" className="mr-2" onChange={() => setPaymentMethod('Card')}/>
            <span className="text-[#4B2E2B]">Credit/Debit Card</span>
          </label>
          <label className="block opacity-40 cursor-not-allowed ">
            <input type="radio" name="payment_method" className="mr-2" disabled onChange={() => setPaymentMethod('Card')}/>
            <span className="text-[#4B2E2B]">QR Code</span>
          </label>
        </div>

        <button
          onClick={handleConfirm}
          className="mt-6 w-full bg-[#4B2E2B] text-white py-3 rounded-lg hover:bg-[#3B1F1B] transition"
        >
          Confirm Payment
        </button>
      </div>
    </div>
  );
}