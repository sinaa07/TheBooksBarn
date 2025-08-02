<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f5f0; padding: 40px;">
    <div style="max-width: 600px; margin: auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h1 style="color: #4B2E2B;">Thank you for your order, {{ $order->user->name }}!</h1>

        <p style="font-size: 16px; color: #4B2E2B;">
            Your order <strong>#{{ $order->order_id }}</strong> has been placed successfully.
        </p>

        <p style="font-size: 16px; color: #4B2E2B;">
            <strong>Order Summary:</strong>
        </p>

        <ul style="padding-left: 20px; color: #4B2E2B;">
            @foreach ($order_items as $item)
                <li><div>
                    <p>{{ $item->book->name }}</p>
                    <span>Quantity: {{ $item->quantity }}</span> <span>Price: ₹{{ number_format($item->amount, 2) }}</span></li>
            @endforeach
        </ul>

        <p style="font-size: 16px; color: #4B2E2B;">
            <strong>Total Amount:</strong> ₹{{ number_format($order->total_amt, 2) }}
        </p>

        <p style="font-size: 16px; color: #4B2E2B;">
            We will notify you again once your order is shipped.
        </p>

        <p style="font-size: 14px; color: #7A5E52;">
            Thank you for shopping with us.<br> The Books Barn
        </p>
    </div>
</body>
</html>