import React from 'react';
import { Link } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { CheckCircle, Package, CreditCard, Clock } from 'lucide-react';
import { Order } from '@/types';

interface Props {
  order: Order;
}

export default function OrderSuccess({ order }: Props) {
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  const getPaymentStatusColor = (status?: string) => {
    if (!status) return 'bg-gray-100 text-gray-800';
    
    const colors: { [key: string]: string } = {
      pending: 'bg-yellow-100 text-yellow-800',
      completed: 'bg-green-100 text-green-800',
      failed: 'bg-red-100 text-red-800',
      refunded: 'bg-gray-100 text-gray-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
  };

  const getOrderStatusColor = (status: string) => {
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

  // Calculate items count from order_items
  const itemsCount = order.order_items?.length || 0;

  return (
    <AppSidebarLayout>
      <div className="min-h-screen p-12 bg-[#F5F0EB]">
        <div className="max-w-3xl mx-auto bg-white p-10 rounded-xl shadow space-y-8">
          {/* Success Header */}
          <div className="text-center space-y-4">
            <div className="flex justify-center">
              <CheckCircle className="w-20 h-20 text-green-500" />
            </div>
            <h1 className="text-4xl font-bold text-[#4B2E2B]">Order Confirmed!</h1>
            <p className="text-lg text-gray-600">
              Thank you for your purchase. Your order has been successfully placed.
            </p>
          </div>

          {/* Order Summary Card */}
          <div className="bg-[#FDF9F4] p-8 rounded-xl border border-gray-200">
            <div className="flex justify-between items-start mb-6">
              <div>
                <h2 className="text-2xl font-semibold text-[#4B2E2B]">
                  Order #{order.order_number}
                </h2>
                <p className="text-gray-600 mt-1">
                  Placed on {formatDate(order.created_at)}
                </p>
              </div>
              <span
                className={`px-4 py-2 rounded-full text-sm font-medium capitalize ${getOrderStatusColor(
                  order.order_status
                )}`}
              >
                {order.order_status}
              </span>
            </div>

            {/* Order Details Grid */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
              <div className="flex items-center space-x-3">
                <Package className="w-8 h-8 text-[#4B2E2B]" />
                <div>
                  <p className="text-sm text-gray-500">Items</p>
                  <p className="text-lg font-semibold text-[#4B2E2B]">
                    {itemsCount} {itemsCount === 1 ? 'item' : 'items'}
                  </p>
                </div>
              </div>

              <div className="flex items-center space-x-3">
                <CreditCard className="w-8 h-8 text-[#4B2E2B]" />
                <div>
                  <p className="text-sm text-gray-500">Total Amount</p>
                  <p className="text-xl font-bold text-[#4B2E2B]">
                    ₹{Number(order.total_amount).toFixed(2)}
                  </p>
                </div>
              </div>

              <div className="flex items-center space-x-3">
                <Clock className="w-8 h-8 text-[#4B2E2B]" />
                <div>
                  <p className="text-sm text-gray-500">Payment Method</p>
                  <p className="text-lg font-semibold text-[#4B2E2B] capitalize">
                    {order.payment?.payment_method 
                      ? order.payment.payment_method.replace('_', ' ') 
                      : 'N/A'
                    }
                  </p>
                </div>
              </div>
            </div>

            {/* Payment Status */}
            {order.payment?.payment_status && (
              <div className="border-t border-gray-200 pt-4">
                <div className="flex justify-between items-center">
                  <span className="text-[#4B2E2B] font-medium">Payment Status:</span>
                  <span
                    className={`px-3 py-1 rounded-full text-sm font-medium capitalize ${getPaymentStatusColor(
                      order.payment.payment_status
                    )}`}
                  >
                    {order.payment.payment_status}
                  </span>
                </div>
              </div>
            )}
          </div>

          {/* What's Next Section */}
          <div className="bg-blue-50 p-6 rounded-xl">
            <h3 className="text-lg font-semibold text-[#4B2E2B] mb-3">What's Next?</h3>
            <div className="space-y-3 text-[#4B2E2B]">
              <div className="flex items-start space-x-3">
                <div className="w-2 h-2 bg-[#4B2E2B] rounded-full mt-2"></div>
                <p>You will receive an email confirmation with your order details shortly.</p>
              </div>
              <div className="flex items-start space-x-3">
                <div className="w-2 h-2 bg-[#4B2E2B] rounded-full mt-2"></div>
                <p>We'll notify you when your order is being prepared and shipped.</p>
              </div>
              <div className="flex items-start space-x-3">
                <div className="w-2 h-2 bg-[#4B2E2B] rounded-full mt-2"></div>
                <p>You can track your order status anytime from your orders page.</p>
              </div>
              <div className="flex items-start space-x-3">
                <div className="w-2 h-2 bg-[#4B2E2B] rounded-full mt-2"></div>
                <p>Estimated delivery time is 3-7 business days for standard shipping.</p>
              </div>
            </div>
          </div>

          {/* Contact Support */}
          <div className="bg-[#FDF9F4] p-6 rounded-xl border border-gray-200">
            <h3 className="text-lg font-semibold text-[#4B2E2B] mb-2">Need Help?</h3>
            <p className="text-gray-600 mb-4">
              If you have any questions about your order, please don't hesitate to contact our support team.
            </p>
            <div className="flex flex-wrap gap-4 text-sm">
              <div className="text-[#4B2E2B]">
                <span className="font-medium">Email:</span> support@bookstore.com
              </div>
              <div className="text-[#4B2E2B]">
                <span className="font-medium">Phone:</span> +91 1234567890
              </div>
              <div className="text-[#4B2E2B]">
                <span className="font-medium">Hours:</span> Mon-Fri 9AM-6PM
              </div>
            </div>
          </div>

          {/* Action Buttons */}
          <div className="flex flex-col sm:flex-row gap-4 justify-center items-center pt-6">
            <Link
              href={route('orders.show', order.id)}
              className="w-full sm:w-auto bg-[#4B2E2B] text-white px-8 py-3 rounded-lg hover:bg-[#3B1F1B] transition font-medium text-center"
            >
              View Order Details
            </Link>
            <Link
              href={route('orders.index')}
              className="w-full sm:w-auto bg-gray-200 text-[#4B2E2B] px-8 py-3 rounded-lg hover:bg-gray-300 transition font-medium text-center"
            >
              View All Orders
            </Link>
            <Link
              href={route('books.index')}
              className="w-full sm:w-auto border border-[#4B2E2B] text-[#4B2E2B] px-8 py-3 rounded-lg hover:bg-[#F5F0EB] transition font-medium text-center"
            >
              Continue Shopping
            </Link>
          </div>

          {/* Order ID for Reference */}
          <div className="text-center pt-6 border-t border-gray-200">
            <p className="text-sm text-gray-500">
              Order ID: <span className="font-mono text-[#4B2E2B]">{order.id}</span>
            </p>
            <p className="text-xs text-gray-400 mt-1">
              Please save this information for your records
            </p>
          </div>
        </div>
      </div>
    </AppSidebarLayout>
  );
}