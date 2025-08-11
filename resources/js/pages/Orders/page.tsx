import React from 'react';
import { Link } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Order } from '@/types';

interface Props {
  order: Order;
}

export default function OrderShow({ order }: Props) {
  console.log(order.order_items);
  return (
    <AppSidebarLayout>
      <div className="min-h-screen p-12 bg-[#F5F0EB]">
        <div className="max-w-4xl mx-auto bg-white p-10 rounded-xl shadow space-y-8">
          <h1 className="text-3xl font-bold text-[#4B2E2B]">Order Summary</h1>

          <div className="space-y-2">
            <p className="text-lg text-[#4B2E2B]">Order ID: {order.id}</p>
            <p className="text-lg text-[#4B2E2B]">Status: {order.order_status}</p>
            <p className="text-lg text-[#4B2E2B]">Mobile: {order.shipping_address.phone}</p>
            <p className="text-lg text-[#4B2E2B]">
              Address: {order.shipping_address.address_line_1}
              {order.shipping_address.address_line_2
                ? `, ${order.shipping_address.address_line_2}`
                : ''}
              , {order.shipping_address.city}, {order.shipping_address.state} -{' '}
              {order.shipping_address.postal_code}, {order.shipping_address.country}
            </p>
          </div>

          <div>
            <h2 className="text-2xl font-semibold text-[#4B2E2B] mb-4">Items</h2>
            <ul className="space-y-4">
              {order.order_items?.map((item, index) => (
                <li
                  key={index}
                  className="flex justify-between items-center bg-[#FDF9F4] px-4 py-3 rounded-lg"
                >
                  <div className="flex items-center space-x-4 text-[#4B2E2B]">
                    {item.book?.cover_image_url && (
                      <img
                        src={item.book.cover_image_url}
                        alt={item.book.title}
                        className="w-20 h-auto rounded"
                      />
                    )}
                    <div>
                      <p className="font-medium">{item.book_title}</p>
                      <p className="text-sm">Quantity: {item.quantity}</p>
                    </div>
                  </div>
                  <div className="text-[#4B2E2B] font-semibold">
                    ₹{item.total_price.toFixed(2)}
                  </div>
                </li>
              ))}
            </ul>
          </div>

          <div className="text-right text-lg font-bold text-[#4B2E2B]">
            Total: ₹{order.total_amount.toFixed(2)}
          </div>

          {order.payment && (
            <div>
              <h2 className="text-2xl font-semibold text-[#4B2E2B] mb-4">Payment Details</h2>
              <p className="text-[#4B2E2B]">Transaction ID: {order.payment.transaction_id}</p>
              <p className="text-[#4B2E2B]">Method: {order.payment.payment_method}</p>
              <p className="text-[#4B2E2B]">Status: {order.payment.payment_status}</p>
            </div>
          )}

          <div className="text-center">
            {/*<Link href='/' className="bg-[#4B2E2B] text-white px-8 py-3 rounded-lg hover:bg-[#3B1F1B] transition">
              Go Back to Shopping
            </Link>*/}
          </div>
        </div>
      </div>
    </AppSidebarLayout>
  );
}