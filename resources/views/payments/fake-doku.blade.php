{{-- LOCAL TESTING ONLY: stands in for DOKU's payment page (FakeDokuController). Deliberately not the website's layout. --}}
@php
    $parts = $transaction->payload['parts'] ?? [];
    $done = in_array($transaction->status, ['paid', 'expired', 'cancelled', 'review'], true);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>FAKE DOKU payment (local test)</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f2f2f2; color: #222; }
        .banner { background: #b00020; color: #fff; text-align: center; padding: 10px 16px; font-weight: bold; }
        .box { max-width: 480px; margin: 32px auto; background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #666; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px; }
        td { padding: 6px 0; border-bottom: 1px solid #eee; }
        td:last-child { text-align: right; }
        .total td { font-weight: bold; font-size: 16px; border-bottom: 0; }
        button { display: block; width: 100%; padding: 12px; margin-top: 10px; border: 0; border-radius: 6px; font-size: 15px; cursor: pointer; }
        .pay { background: #198754; color: #fff; }
        .alt { background: #e9ecef; color: #222; }
        .note { background: #fff3cd; padding: 10px; border-radius: 6px; font-size: 13px; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="banner">FAKE DOKU · LOCAL TESTING ONLY · NO REAL PAYMENT</div>
    <div class="box">
        <h1>Payment {{ $transaction->reference }}</h1>
        <div class="muted">Booking {{ $reservation->code ?? $reservation->id }} · {{ $reservation->customer->full_name ?? '' }}</div>

        <table>
            @if (($parts['payment'] ?? 0) > 0)
                <tr><td>Car rental</td><td>RM {{ number_format($parts['payment'], 2) }}</td></tr>
            @endif
            @if (($parts['deposit'] ?? 0) > 0)
                <tr><td>Refundable security deposit</td><td>RM {{ number_format($parts['deposit'], 2) }}</td></tr>
            @endif
            <tr class="total"><td>Total</td><td>{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</td></tr>
        </table>

        @if (session('fake_message'))
            <div class="note">{{ session('fake_message') }}</div>
        @endif

        @if ($done)
            <div class="note">This payment is already <strong>{{ $transaction->status }}</strong>.</div>
            <a href="{{ route('payments.doku.result', [$transaction->reservation_id, $transaction]) }}">Back to the website</a>
        @else
            <form method="post" action="{{ route('payments.fake-doku.act', $transaction->reference) }}">
                @csrf
                <button class="pay" name="action" value="pay">Pay (success)</button>
                <button class="alt" name="action" value="pay_delayed">Pay, but DOKU's message is late (like on localhost)</button>
                <button class="alt" name="action" value="fail">Card declined</button>
                <button class="alt" name="action" value="expire">Let the payment page expire</button>
                <button class="alt" name="action" value="cancel">Back to the website without paying</button>
            </form>
        @endif
    </div>
</body>
</html>
