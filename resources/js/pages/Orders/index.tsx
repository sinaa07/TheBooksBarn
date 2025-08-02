import React from 'react';
import { Link } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import OrderPage from './page';
import { Order } from '@/types';

interface Props {
  orders: Order[];
}

export default function OrderIndex({ orders }: Props) {
  return (
    <AppSidebarLayout>
    <OrderPage orders= {orders}/>
    </AppSidebarLayout>
  );
}