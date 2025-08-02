import React from 'react';
import { Link } from '@inertiajs/react';
import { Order } from '@/types';

interface Props {
  orders: Order[];
}

export default function OrderPage({ orders }: Props) {
  return (
    <div className="min-h-screen p-8 bg-[#F5F0EB] shadow">
      <h1 className="text-3xl font-bold text-[#4B2E2B] mb-6">Your Orders</h1>
      {orders.length === 0 ? (
        <p className="text-[#4B2E2B]">You have not placed any orders yet.</p>
      ) : (
        <ul className="space-y-4">
          {orders.map((order) => (
            <li key={order.order_id} className="flex justify-between bg-[#FDF9F4] p-4 rounded-lg">
              <div className="text-[#4B2E2B]">
                <p className="font-medium">Order ID: {order.order_id}</p>
                <p>Status: {order.status}</p>
                <p>Placed on: {new Date(order.created_at).toLocaleDateString()}</p>
              </div>
              <div className="flex items-center space-x-4">
                <span className="font-bold text-[#4B2E2B]">₹{Number(order.total_amt).toFixed(2)}</span>
                <Link
                  href={route('orders.show', order.order_id)}
                  className="bg-[#4B2E2B] text-white px-4 py-2 rounded-lg hover:bg-[#3B1F1B] transition"
                >
                  View Details
                </Link>
              </div>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}