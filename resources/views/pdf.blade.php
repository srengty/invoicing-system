<!DOCTYPE html>
<html>
<head>
    <title>Quotation {{ $quotation->quotation_no }}</title>
</head>
<body>
    <h1>Quotation Details</h1>
    <p><strong>Quotation No.:</strong> {{ $quotation->quotation_no }}</p>
    <p><strong>Customer Name:</strong> {{ $quotation->customer->name }}</p>
    <p><strong>Total:</strong> {{ $quotation->total }}</p>
    <h2>Items</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Item</th>
                <th>QTY</th>
                <th>Unit Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quotation->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td>{{ $product->pivot->price }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>