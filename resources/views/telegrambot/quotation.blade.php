@php
    use Carbon\Carbon;
@endphp

<b>📄 Quotation Notification</b>

<b>Quotation #:</b> {{ $quotation->quotation_no ?? 'Pending' }}
<b>Date:</b> {{ Carbon::parse($quotation->quotation_date)->format('M d, Y') }}
<b>Customer:</b> {{ $quotation->customer->name }}

@if($quotation->exchange_rate)
<b>Exchange Rate:</b> 1 USD = {{ number_format($quotation->exchange_rate, 2) }} KHR
@endif

<b>Products:</b>
@foreach($quotation->products as $product)
- {{ $product->name }} ({{ $product->pivot->quantity }} × ${{ number_format($product->pivot->price, 2) }})
@endforeach

<b>Total:</b> ${{ number_format($quotation->total_usd, 2) }} USD / {{ number_format($quotation->total, 2) }} KHR

<b>Terms:</b> {{ $quotation->terms ?? 'Standard' }}

Please review the attached quotation PDF and let us know if you have any questions.

Thank you for your business and we look forward to serving you again!
