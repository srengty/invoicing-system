<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->quotation_no }}</title>
</head>
<body>
    <h1>Quotation #{{ $quotation->quotation_no }}</h1>
    <p>Customer: {{ $quotation->customer->name }}</p>
    <p>Date:     {{ $quotation->quotation_date }}</p>
    <p>Total:    {{ number_format($quotation->total) }} KHR</p>
    <p>Thank you for your business.</p>

    <!-- Buttons to trigger your new sendViaTelegram route -->
    <form action="{{ route('quotations.sendTelegram', $quotation->id) }}" method="POST" style="margin-top:2em">
        @csrf

        <!-- Send to channel -->
        <button type="submit" name="target" value="channel">
            📤 Send PDF to Telegram Channel (@finances_itc)
        </button>

        <!-- Send to customer -->
        <button type="submit" name="target" value="phone">
            🤳 Send PDF to Customer ({{ $quotation->customer->phone_number }})
        </button>
    </form>

    @if(session('success'))
        <p style="color:green; margin-top:1em">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p style="color:red; margin-top:1em">{{ session('error') }}</p>
    @endif
</body>
</html>
