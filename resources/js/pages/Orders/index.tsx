import React from 'react';
import { Link } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Order } from '@/types';

// Order data as transformed by the controller's index method
interface OrderListItem {
  id: number;
  order_number: string;
  order_status: 'pending' | 'confirmed' | 'processing' | 'shipped' | 'delivered' | 'cancelled';
  subtotal: number;
  shipping_cost: number;
  total_amount: number;
  created_at: string;
  shipped_at: string | null;
  delivered_at: string | null;
  items_count: number;
  payment_status?: string;
  payment_method?: string;
  tracking_number?: string;
}

interface PaginatedOrders {
  data: OrderListItem[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
}

interface StatusCounts {
  all: number;
  pending: number;
  confirmed: number;
  processing: number;
  shipped: number;
  delivered: number;
  cancelled: number;
}

interface Props {
  orders: PaginatedOrders;
  statusCounts: StatusCounts;
  currentStatus?: string;
}

export default function OrderIndex({ orders, statusCounts, currentStatus }: Props) {
  const handleStatusFilter = (status: string | null) => {
    const url = status ? `/orders?status=${status}` : '/orders';
    router.get(url);
  };

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

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  };

  return (
    <AppSidebarLayout>
      <div className="min-h-screen p-12 bg-[#F5F0EB]">
        <div className="max-w-6xl mx-auto bg-white p-10 rounded-xl shadow space-y-8">
          <h1 className="text-3xl font-bold text-[#4B2E2B]">My Orders</h1>

          {/* Status Filter Tabs */}
          <div className="border-b border-gray-200">
            <nav className="flex space-x-8">
              <button
                onClick={() => handleStatusFilter(null)}
                className={`pb-4 px-1 border-b-2 font-medium text-sm ${
                  !currentStatus
                    ? 'border-[#4B2E2B] text-[#4B2E2B]'
                    : 'border-transparent text-gray-500 hover:text-[#4B2E2B] hover:border-gray-300'
                }`}
              >
                All ({statusCounts.all})
              </button>
              {Object.entries(statusCounts)
                .filter(([key]) => key !== 'all')
                .map(([status, count]) => (
                  <button
                    key={status}
                    onClick={() => handleStatusFilter(status)}
                    className={`pb-4 px-1 border-b-2 font-medium text-sm capitalize ${
                      currentStatus === status
                        ? 'border-[#4B2E2B] text-[#4B2E2B]'
                        : 'border-transparent text-gray-500 hover:text-[#4B2E2B] hover:border-gray-300'
                    }`}
                  >
                    {status} ({count})
                  </button>
                ))}
            </nav>
          </div>

          {/* Orders List */}
          {orders.data.length === 0 ? (
            <div className="text-center py-12">
              <p className="text-xl text-gray-500">No orders found</p>
              <Link
                href="/"
                className="inline-block mt-4 bg-[#4B2E2B] text-white px-6 py-3 rounded-lg hover:bg-[#3B1F1B] transition"
              >
                Start Shopping
              </Link>
            </div>
          ) : (
            <div className="space-y-6">
              {orders.data.map((order) => (
                <div
                  key={order.id}
                  className="bg-[#FDF9F4] p-6 rounded-lg border border-gray-200 hover:shadow-md transition-shadow"
                >
                  <div className="flex justify-between items-start mb-4">
                    <div>
                      <h3 className="text-lg font-semibold text-[#4B2E2B]">
                        Order #{order.order_number}
                      </h3>
                      <p className="text-sm text-gray-600">
                        Placed on {formatDate(order.created_at)}
                      </p>
                    </div>
                    <span
                      className={`px-3 py-1 rounded-full text-xs font-medium capitalize ${getStatusBadgeColor(
                        order.order_status
                      )}`}
                    >
                      {order.order_status}
                    </span>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                      <p className="text-sm text-gray-500">Items</p>
                      <p className="font-medium text-[#4B2E2B]">{order.items_count} items</p>
                    </div>
                    <div>
                      <p className="text-sm text-gray-500">Total Amount</p>
                      <p className="font-bold text-[#4B2E2B]">₹{Number(order.total_amount).toFixed(2)}</p>
                    </div>
                    <div>
                      <p className="text-sm text-gray-500">Payment</p>
                      <p className="font-medium text-[#4B2E2B]">
                        {order.payment_method ? (
                          <span className="capitalize">{order.payment_method.replace('_', ' ')}</span>
                        ) : (
                          'N/A'
                        )}
                        {order.payment_status && (
                          <span
                            className={`ml-2 px-2 py-1 rounded text-xs ${
                              order.payment_status === 'completed'
                                ? 'bg-green-100 text-green-800'
                                : order.payment_status === 'pending'
                                ? 'bg-yellow-100 text-yellow-800'
                                : order.payment_status === 'failed'
                                ? 'bg-red-100 text-red-800'
                                : 'bg-gray-100 text-gray-800'
                            }`}
                          >
                            {order.payment_status}
                          </span>
                        )}
                      </p>
                    </div>
                  </div>

                  {order.tracking_number && (
                    <div className="mb-4">
                      <p className="text-sm text-gray-500">Tracking Number</p>
                      <p className="font-medium text-[#4B2E2B]">{order.tracking_number}</p>
                    </div>
                  )}

                  <div className="flex justify-between items-center pt-4 border-t border-gray-200">
                    <div className="space-x-3">
                      <Link
                        href={`/orders/${order.id}`}
                        className="text-[#4B2E2B] hover:text-[#3B1F1B] font-medium"
                      >
                        View Details
                      </Link>
                      {order.tracking_number && (
                        <Link
                          href={`/orders/track/${order.order_number}`}
                          className="text-[#4B2E2B] hover:text-[#3B1F1B] font-medium"
                        >
                          Track Order
                        </Link>
                      )}
                    </div>
                    {(order.order_status === 'pending' || order.order_status === 'confirmed') && (
                      <button
                        onClick={() => {
                          if (confirm('Are you sure you want to cancel this order?')) {
                            router.post(`/orders/${order.id}/cancel`);
                          }
                        }}
                        className="text-red-600 hover:text-red-800 font-medium"
                      >
                        Cancel Order
                      </button>
                    )}
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* Pagination */}
          {orders.last_page > 1 && (
            <div className="flex justify-center items-center space-x-2">
              {orders.links.map((link, index) => {
                if (!link.url) {
                  return (
                    <span
                      key={index}
                      className="px-3 py-2 text-gray-400 cursor-not-allowed"
                      dangerouslySetInnerHTML={{ __html: link.label }}
                    />
                  );
                }

                return (
                  <Link
                    key={index}
                    href={link.url}
                    className={`px-3 py-2 rounded-md ${
                      link.active
                        ? 'bg-[#4B2E2B] text-white'
                        : 'bg-white text-[#4B2E2B] border border-gray-300 hover:bg-[#F5F0EB]'
                    }`}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                  />
                );
              })}
            </div>
          )}
        </div>
      </div>
    </AppSidebarLayout>
  );
}