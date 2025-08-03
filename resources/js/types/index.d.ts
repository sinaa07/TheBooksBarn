import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    [key: string]: unknown;
}

// Updated to match users migration
export interface User {
    id: number;
    username: string;
    email: string;
    email_verified_at: string | null;
    password?: string; // Usually not included in responses
    first_name: string;
    last_name: string;
    phone: string | null;
    is_active: boolean;
    remember_token?: string; // Usually not included in responses
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
}

// Updated to match books migration
export interface Book {
    id: number;
    isbn: string | null;
    title: string;
    author: string;
    category_id: number | null;
    description: string | null;
    price: number;
    stock_quantity: number;
    format: 'hardcover' | 'paperback' | 'ebook';
    cover_image_url: string | null;
    is_active: boolean;
    featured: boolean;
    created_at: string;
    updated_at: string;
    category?: Category;
}

// Updated to match categories migration
export interface Category {
    id: number;
    category_name: string;
    description: string | null;
    slug: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface PaginatedBooks {
    data: Book[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
    links: PaginationLink[];
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface SearchFilters extends Record<string, FormDataConvertible | undefined> {
    search?: string;
    category?: string;
    min_price?: string;
    max_price?: string;
    author?: string;
    year_from?: string;
    year_to?: string;
    sort?: 'newest' | 'title' | 'author' | 'price_low' | 'price_high';
}

interface PriceRange {
    min_price: number;
    max_price: number;
  }
  
  interface Filters {
    q?: string;
    category_id?: string | number;
    format: string;
    min_price: number;
    max_price: number;
    sort_by?: string;
  }
export interface SearchStats {
    total: number;
    showing: number;
    hasSearch: boolean;
}

export interface BookSuggestion {
    id: number;
    title: string;
    author: string;
    display: string;
}

// Updated to match addresses migration
export interface Address {
    id: number;
    user_id: number;
    name: string;
    phone: string;
    address_line_1: string;
    address_line_2: string | null;
    city: string;
    state: string;
    postal_code: string;
    country: string;
    address_type: 'billing' | 'shipping' | 'both';
    is_default: boolean;
    created_at: string;
    updated_at: string;
}

// New Cart interface to match migration
export interface Cart {
    id: number;
    user_id: number;
    created_at: string;
    updated_at: string;
    expires_at: string | null;
}

// Updated to match cart_items migration
export interface CartItem {
    id: number;
    cart_id: number;
    book_id: number;
    quantity: number;
    unit_price: number;
    created_at: string;
    updated_at: string;
    book?: Book;
}

// Updated to match orders migration
export interface Order {
    id: number;
    user_id: number;
    order_number: string;
    order_status: 'pending' | 'confirmed' | 'processing' | 'shipped' | 'delivered' | 'cancelled';
    subtotal: number;
    shipping_cost: number;
    total_amount: number;
    shipping_address: {
        name: string;
        phone: string;
        address_line_1: string;
        address_line_2?: string;
        city: string;
        state: string;
        postal_code: string;
        country: string;
    };
    notes: string | null;
    created_at: string;
    updated_at: string;
    shipped_at: string | null;
    delivered_at: string | null;
    order_items?: OrderItem[];
    payment?: Payment;
    shipment?: Shipment;
}

// Updated to match order_items migration
export interface OrderItem {
    id: number;
    order_id: number;
    book_id: number;
    book_title: string;
    quantity: number;
    unit_price: number;
    total_price: number;
    created_at: string;
    updated_at: string;
    book?: Book;
}

// Updated to match payments migration
export interface Payment {
    id: number;
    order_id: number;
    payment_method: 'credit_card' | 'debit_card' | 'paypal' | 'cash_on_delivery';
    payment_status: 'pending' | 'completed' | 'failed' | 'refunded';
    amount: number;
    transaction_id: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
    completed_at: string | null;
}

// New Shipment interface to match migration
export interface Shipment {
    id: number;
    order_id: number;
    tracking_number: string | null;
    carrier: string | null;
    shipment_status: 'preparing' | 'shipped' | 'in_transit' | 'delivered';
    notes: string | null;
    created_at: string;
    updated_at: string;
    shipped_at: string | null;
    delivered_at: string | null;
}

// New Admin interface to match migration
export interface Admin {
    id: number;
    user_id: number;
    role: 'admin' | 'manager';
    created_at: string;
    updated_at: string;
    user?: User;
}

export interface GuestCartHelpers {
    getGuestCart: () => CartItem[];
    addToGuestCart: (book: Book, quantity: number) => void;
    updateGuestCartQuantity: (bookId: number, quantity: number) => void;
    removeFromGuestCart: (bookId: number) => void;
    clearGuestCart: () => void;
    getGuestCartTotal: () => number;
    getGuestCartCount: () => number;
}

export interface QuantityControlProps {
    bookId: number;
    quantity: number;
    onQuantityChange?: (bookId: number, quantity: number) => void;
    disabled?: boolean;
    min?: number;
    max?: number;
}