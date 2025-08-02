import { useEffect, useState } from 'react';

interface ToastProps {
  message: string;
  type?: 'success' | 'error';
  duration?: number;
}

export default function Toast({ message, type = 'success', duration = 3000 }: ToastProps) {
  const [show, setShow] = useState(true);

  useEffect(() => {
    const timeout = setTimeout(() => setShow(false), duration);
    return () => clearTimeout(timeout);
  }, [duration]);

  if (!show) return null;

  return (
    <div
      className={`fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-2xl shadow-xl text-[#F4ECE6] text-base transition-opacity ${
        type === 'success' ? 'bg-[#75482c]' : 'bg-[#8B3A2E]'
      }`}
    >
      {message}
    </div>
  );
}