import React from 'react';
import { Link } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Order, Payment, Shipment } from '@/types';

interface OrderItemDetails {
  id: number;
  book_title: string;
  quantity: number;
  unit_price: number;
  total_price: number;
  book: {
    id: number;
    title: string;
    author: string;
    cover_image_url: string;
    format: string;
    category?: string; // This is transformed from book.category?.category_name
  };
}

interface Props {
  order: Order;
  orderItems: OrderItemDetails[];
  payment?: Payment;
  shipment?: Shipment;
}


export default function OrderShow({ order, orderItems, payment, shipment }: Props) {
  const getStatusBadgeColor = (status: string) => {
    const colors: { [key: string]: string } = {
      pending: 'bg-yellow-100 text-yellow-800',
      confirmed: 'bg-blue-100 text-blue-800',
      processing: 'bg-purple-100 text-purple-800',
      shipped: 'bg-indigo-100 text-indigo-800',
      delivered: 'bg-green-100 text-green-800',
      cancelled: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
  };

  const getPaymentStatusColor = (status: string) => {
    const colors: { [key: string]: string } = {
      pending: 'bg-yellow-100 text-yellow-800',
      completed: 'bg-green-100 text-green-800',
      failed: 'bg-red-100 text-red-800',
      refunded: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const handleCancelOrder = () => {
    if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
      router.post(`/orders/${order.id}/cancel`);
    }
  };

  const handleReorder = () => {
    router.post(`/orders/${order.id}/reorder`);
  };

  const canCancelOrder = order.order_status === 'pending' || order.order_status === 'confirmed';
  const canReorder = order.order_status === 'delivered' || order.order_status === 'cancelled';

  return (
    <AppSidebarLayout>
      <div className="min-h-screen p-12 bg-[#F5F0EB]">
        <div className="max-w-4xl mx-auto bg-white p-10 rounded-xl shadow space-y-8">
          {/* Header */}
          <div className="flex justify-between items-start">
            <div>
              <h1 className="text-3xl font-bold text-[#4B2E2B]">Order Details</h1>
              <p className="text-lg text-gray-600 mt-2">Order #{order.order_number}</p>
            </div>
            <span
              className={`px-4 py-2 rounded-full text-sm font-medium capitalize ${getStatusBadgeColor(
                order.order_status
              )}`}
            >
              {order.order_status}
            </span>
          </div>

          {/* Order Information */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div className="space-y-4">
              <h2 className="text-xl font-semibold text-[#4B2E2B] border-b border-gray-200 pb-2">
                Order Information
              </h2>
              <div className="space-y-2">
                <p className="text-[#4B2E2B]">
                  <span className="font-medium">Order Date:</span> {formatDate(order.created_at)}
                </p>
                {order.shipped_at && (
                  <p className="text-[#4B2E2B]">
                    <span className="font-medium">Shipped Date:</span> {formatDate(order.shipped_at)}
                  </p>
                )}
                {order.delivered_at && (
                  <p className="text-[#4B2E2B]">
                    <span className="font-medium">Delivered Date:</span> {formatDate(order.delivered_at)}
                  </p>
                )}
                {order.notes && (
                  <p className="text-[#4B2E2B]">
                    <span className="font-medium">Notes:</span> {order.notes}
                  </p>
                )}
              </div>
            </div>

            <div className="space-y-4">
              <h2 className="text-xl font-semibold text-[#4B2E2B] border-b border-gray-200 pb-2">
                Shipping Address
              </h2>
              <div className="text-[#4B2E2B] space-y-1">
                <p className="font-medium">{order.shipping_address.name}</p>
                <p>{order.shipping_address.phone}</p>
                <p>{order.shipping_address.address_line_1}</p>
                {order.shipping_address.address_line_2 && (
                  <p>{order.shipping_address.address_line_2}</p>
                )}
                <p>
                  {order.shipping_address.city}, {order.shipping_address.state} -{' '}
                  {order.shipping_address.postal_code}
                </p>
                <p>{order.shipping_address.country}</p>
              </div>
            </div>
          </div>

          {/* Order Items */}
          <div>
            <h2 className="text-2xl font-semibold text-[#4B2E2B] mb-6">Order Items</h2>
            <div className="space-y-4">
              {orderItems.map((item) => (
                <div
                  key={item.id}
                  className="flex items-center space-x-6 bg-[#FDF9F4] p-6 rounded-lg"
                >
                  {item.book.cover_image_url && (
                    <img
                      src={item.book.cover_image_url}
                      alt={item.book.title}
                      className="w-24 h-32 object-cover rounded-lg shadow-sm"
                    />
                  )}
                  <div className="flex-1">
                    <h3 className="text-lg font-semibold text-[#4B2E2B] mb-2">
                      {item.book_title}
                    </h3>
                    <div className="grid grid-cols-2 gap-4 text-sm text-gray-600">
                      <p><span className="font-medium">Author:</span> {item.book.author}</p>
                      <p><span className="font-medium">Format:</span> {item.book.format}</p>
                      {item.book.category && (
                        <p><span className="font-medium">Category:</span> {item.book.category}</p>
                      )}
                      <p><span className="font-medium">Quantity:</span> {item.quantity}</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="text-sm text-gray-600">Unit Price</p>
                    <p className="text-lg text-[#4B2E2B]">₹{Number(item.unit_price).toFixed(2)}</p>
                    <p className="text-sm text-gray-600 mt-2">Total</p>
                    <p className="text-xl font-bold text-[#4B2E2B]">₹{Number(item.total_price).toFixed(2)}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Order Summary */}
          <div className="bg-[#FDF9F4] p-6 rounded-lg">
            <h2 className="text-xl font-semibold text-[#4B2E2B] mb-4">Order Summary</h2>
            <div className="space-y-2">
              <div className="flex justify-between text-[#4B2E2B]">
                <span>Subtotal:</span>
                <span>₹{Number(order.subtotal).toFixed(2)}</span>
              </div>
              <div className="flex justify-between text-[#4B2E2B]">
                <span>Shipping Cost:</span>
                <span>₹{Number(order.shipping_cost).toFixed(2)}</span>
              </div>
              <div className="border-t border-gray-300 pt-2 mt-2">
                <div className="flex justify-between text-xl font-bold text-[#4B2E2B]">
                  <span>Total Amount:</span>
                  <span>₹{Number(order.total_amount).toFixed(2)}</span>
                </div>
              </div>
            </div>
          </div>

          {/* Payment Details */}
          {payment && (
            <div>
              <h2 className="text-2xl font-semibold text-[#4B2E2B] mb-4">Payment Details</h2>
              <div className="bg-[#FDF9F4] p-6 rounded-lg space-y-3">
                <div className="flex justify-between items-center">
                  <span className="text-[#4B2E2B] font-medium">Payment Method:</span>
                  <span className="text-[#4B2E2B] capitalize">
                    {payment.payment_method.replace('_', ' ')}
                  </span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-[#4B2E2B] font-medium">Payment Status:</span>
                  <span
                    className={`px-3 py-1 rounded-full text-sm font-medium capitalize ${getPaymentStatusColor(
                      payment.payment_status
                    )}`}
                  >
                    {payment.payment_status}
                  </span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-[#4B2E2B] font-medium">Amount Paid:</span>
                  <span className="text-[#4B2E2B] font-bold">₹{Number(payment.amount).toFixed(2)}</span>
                </div>
                {payment.transaction_id && (
                  <div className="flex justify-between items-center">
                    <span className="text-[#4B2E2B] font-medium">Transaction ID:</span>
                    <span className="text-[#4B2E2B] font-mono text-sm">{payment.transaction_id}</span>
                  </div>
                )}
                {payment.completed_at && (
                  <div className="flex justify-between items-center">
                    <span className="text-[#4B2E2B] font-medium">Completed At:</span>
                    <span className="text-[#4B2E2B]">{formatDate(payment.completed_at)}</span>
                  </div>
                )}
              </div>
            </div>
          )}

          {/* Shipment Details */}
          {shipment && (
            <div>
              <h2 className="text-2xl font-semibold text-[#4B2E2B] mb-4">Shipment Details</h2>
              <div className="bg-[#FDF9F4] p-6 rounded-lg space-y-3">
                {shipment.tracking_number && (
                  <div className="flex justify-between items-center">
                    <span className="text-[#4B2E2B] font-medium">Tracking Number:</span>
                    <span className="text-[#4B2E2B] font-mono text-sm">{shipment.tracking_number}</span>
                  </div>
                )}
                {shipment.carrier && (
                  <div className="flex justify-between items-center">
                    <span className="text-[#4B2E2B] font-medium">Carrier:</span>
                    <span className="text-[#4B2E2B]">{shipment.carrier}</span>
                  </div>
                )}
                <div className="flex justify-between items-center">
                  <span className="text-[#4B2E2B] font-medium">Shipment Status:</span>
                  <span className="text-[#4B2E2B] capitalize">{shipment.shipment_status}</span>
                </div>
                {shipment.shipped_at && (
                  <div className="flex justify-between items-center">
                    <span className="text-[#4B2E2B] font-medium">Shipped At:</span>
                    <span className="text-[#4B2E2B]">{formatDate(shipment.shipped_at)}</span>
                  </div>
                )}
                {shipment.delivered_at && (
                  <div className="flex justify-between items-center">
                    <span className="text-[#4B2E2B] font-medium">Delivered At:</span>
                    <span className="text-[#4B2E2B]">{formatDate(shipment.delivered_at)}</span>
                  </div>
                )}
                {shipment.notes && (
                  <div>
                    <span className="text-[#4B2E2B] font-medium">Notes:</span>
                    <p className="text-[#4B2E2B] mt-1">{shipment.notes}</p>
                  </div>
                )}
              </div>
            </div>
          )}

          {/* Action Buttons */}
          <div className="flex flex-wrap gap-4 justify-between items-center pt-6 border-t border-gray-200">
            <div className="space-x-4">
              <Link
                href="/orders"
                className="inline-block bg-gray-200 text-[#4B2E2B] px-6 py-3 rounded-lg hover:bg-gray-300 transition font-medium"
              >
                ← Back to Orders
              </Link>
              {shipment?.tracking_number && (
                <Link
                  href={`/orders/track/${order.order_number}`}
                  className="inline-block bg-[#4B2E2B] text-white px-6 py-3 rounded-lg hover:bg-[#3B1F1B] transition font-medium"
                >
                  Track Order
                </Link>
              )}
            </div>
            <div className="space-x-4">
              {canReorder && (
                <button
                  onClick={handleReorder}
                  className="bg-[#4B2E2B] text-white px-6 py-3 rounded-lg hover:bg-[#3B1F1B] transition font-medium"
                >
                  Reorder Items
                </button>
              )}
              {canCancelOrder && (
                <button
                  onClick={handleCancelOrder}
                  className="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-medium"
                >
                  Cancel Order
                </button>
              )}
            </div>
          </div>
        </div>
      </div>
    </AppSidebarLayout>
  );
}