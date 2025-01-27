<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Order Confirmation</title>
    </head>
    <body>
        <h1>Order Confirmation</h1>
        <p>Dear Customer,</p>
        <p>
            Thank you for placing your order. Please find the details of your
            order below:
        </p>
        <ul>
            <li><strong>Order No:</strong> {{ $order->id }}</li>
            <li>
                <strong>Date:</strong> {{ $order->created_at->format('d-M-Y') }}
            </li>
            <li><strong>Total Quantity:</strong> {{ $qty }}</li>
        </ul>
        <p>The PDF containing the full details is attached to this email.</p>
        <p>Best regards,<br />Essa Garments Private Limited &copy;</p>
    </body>
</html>
