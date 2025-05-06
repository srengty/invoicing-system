<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation</title>
</head>
<body>
    <h1>Quotation #{{ $quotation->quotation_no }}</h1>
    <p>Customer: {{ $quotation->customer->name }}</p>
    <p>Date: {{ $quotation->quotation_date }}</p>
    <p>Total: {{ $quotation->total }} KHR</p>
    <p>Thank you for your business.</p>
</body>
</html>
