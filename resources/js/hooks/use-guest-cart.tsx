// hooks/useGuestCart.ts
import { useState, useEffect } from 'react';
import { CartItem, Book } from '@/types';

// Define a guest cart item type that only includes necessary properties
interface GuestCartItem {
  book: {
    book_id: number;
    name: string;
    author: string;
    price: number;
    image?: string;
    stock: number;
  };
  quantity: number;
  unit_price: number;
}

const GUEST_CART_KEY = 'guest_cart';

export function useGuestCart() {
  const [guestCart, setGuestCart] = useState<GuestCartItem[]>([]);

  // Load cart from localStorage on mount
  useEffect(() => {
    loadGuestCart();
  }, []);

  const loadGuestCart = () => {
    try {
      const storedCart = localStorage.getItem(GUEST_CART_KEY);
      if (storedCart) {
        const parsedCart = JSON.parse(storedCart);
        setGuestCart(Array.isArray(parsedCart) ? parsedCart : []);
      }
    } catch (error) {
      console.error('Error loading guest cart:', error);
      setGuestCart([]);
    }
  };

  const saveGuestCart = (cart: GuestCartItem[]) => {
    try {
      localStorage.setItem(GUEST_CART_KEY, JSON.stringify(cart));
      setGuestCart(cart);
    } catch (error) {
      console.error('Error saving guest cart:', error);
    }
  };

  const addToGuestCart = (book: Book, quantity: number = 1) => {
    const existingItemIndex = guestCart.findIndex(
      item => item.book.book_id === book.book_id
    );

    let updatedCart: CartItem[];

    if (existingItemIndex >= 0) {
      // Update quantity of existing item
      updatedCart = guestCart.map((item, index) =>
        index === existingItemIndex
          ? { ...item, quantity: item.quantity + quantity }
          : item
      );
    } else {
      // Add new item
      const newItem: CartItem = {
        book: {
          book_id: book.book_id,
          name: book.name,
          author: book.author,
          price: book.price,
          image: book.image,
          stock: book.stock,
        },
        quantity,
        unit_price: book.price,
      };
      updatedCart = [...guestCart, newItem];
    }

    saveGuestCart(updatedCart);
  };

  const updateGuestCartQuantity = (bookId: number, quantity: number) => {
    if (quantity <= 0) {
      removeFromGuestCart(bookId);
      return;
    }

    const updatedCart = guestCart.map(item =>
      item.book.book_id === bookId
        ? { ...item, quantity }
        : item
    );
    saveGuestCart(updatedCart);
  };

  const removeFromGuestCart = (bookId: number) => {
    const updatedCart = guestCart.filter(
      item => item.book.book_id !== bookId
    );
    saveGuestCart(updatedCart);
  };

  const clearGuestCart = () => {
    localStorage.removeItem(GUEST_CART_KEY);
    setGuestCart([]);
  };

  const getGuestCartTotal = () => {
    return guestCart.reduce((total, item) => total + (item.quantity * item.unit_price), 0);
  };

  const getGuestCartCount = () => {
    return guestCart.reduce((count, item) => count + item.quantity, 0);
  };

  const isInGuestCart = (bookId: number) => {
    return guestCart.some(item => 
      item.book.book_id === bookId
    );
  };

  return {
    guestCart,
    addToGuestCart,
    updateGuestCartQuantity,
    removeFromGuestCart,
    clearGuestCart,
    getGuestCartTotal,
    getGuestCartCount,
    isInGuestCart,
    loadGuestCart, // In case you need to manually reload
  };
}

// Utility function to migrate guest cart to user cart after login
export function migrateGuestCartToUser(guestCart: CartItem[]) {
  if (guestCart.length === 0) return;

  // Send guest cart items to backend to merge with user cart
  return fetch('/api/cart/migrate', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    },
    body: JSON.stringify({ items: guestCart }),
  })
  .then(response => response.json())
  .then(() => {
    // Clear guest cart after successful migration
    localStorage.removeItem(GUEST_CART_KEY);
  })
  .catch(error => {
    console.error('Error migrating guest cart:', error);
  });
}