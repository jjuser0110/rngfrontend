<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $quotationNumber }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            background: #fff;
        }
        .header-table, .details-table, .item-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo-title {
            font-size: 22px;
            font-weight: bold;
            color: #111;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            background-color: #f39c12;
            color: white;
            padding: 4px 8px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 3px;
        }
        .section-title {
            background: #f8f9fa;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            color: #555;
            border-left: 4px solid #e9993e;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.items th, table.items td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }
        table.items th {
            background-color: #f8f9fa;
            color: #333;
        }
        .total-section {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .total-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .total-section td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .grand-total {
            font-weight: bold;
            font-size: 16px;
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <table class="header-table">
        <tr>
            <td>
                <div class="logo-title">RNG CAR RENTAL</div>
                <div style="font-size: 12px; color: #666;">Official Vehicle Quotation</div>
            </td>
            <td class="text-right">
                <span class="badge">Official Document</span>
                <p style="margin: 5px 0 0 0; font-size: 13px;"><strong>Quotation #:</strong> {{ $quotationNumber }}</p>
                <p style="margin: 2px 0 0 0; font-size: 13px;"><strong>Date Issued:</strong> {{ $date }}</p>
            </td>
        </tr>
    </table>

    <div class="section-title">Customer & Rental Details</div>
    <table class="details-table" style="font-size: 13px;">
        <tr>
            <td style="width: 50%; padding-right: 15px;">
                <p style="margin: 0 0 5px 0;"><strong>Customer Name:</strong> {{ $customer->name }}</p>
                <p style="margin: 0 0 5px 0;"><strong>Email:</strong> {{ $customer->email }}</p>
                <p style="margin: 0 0 5px 0;"><strong>Phone:</strong> {{ $customer->phone }}</p>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <p style="margin: 0 0 5px 0;"><strong>Pick-up:</strong> {{ $schedule->pickup_date }}<br><span style="color: #666; font-size: 11px;">{{ $schedule->pickup_loc }}</span></p>
                <p style="margin: 5px 0 0 0;"><strong>Drop-off:</strong> {{ $schedule->dropoff_date }}<br><span style="color: #666; font-size: 11px;">{{ $schedule->dropoff_loc }}</span></p>
            </td>
        </tr>
    </table>

    <div class="section-title">Vehicle Choice & Package Summary</div>
    <table class="items">
        <thead>
            <tr>
                <th>Description / Item</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $car->name }}</strong> (or similar) - {{ $pricing['hire_days'] }} Days Hire<br>
                    <small style="color: #666;">Specs: {{ $car->seats }} Seats, {{ $car->luggage }} Bags, {{ $car->fuel }}, {{ $car->drive }} | Insurance: {{ $car->insurance_package }}</small>
                </td>
                <td class="text-right">${{ number_format($pricing['hire_total'], 2) }}</td>
            </tr>
            <tr>
                <td>
                    <strong>Selected Extras & Protections</strong><br>
                    <small style="color: #666;">{{ $extras_label }} + Collision Damage Waiver (CDW)</small>
                </td>
                <td class="text-right">${{ number_format($pricing['taxes_fees'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">${{ number_format($pricing['total'], 2) }}</td>
            </tr>
            <tr>
                <td>Security Bond:</td>
                <td class="text-right">${{ $car->deposit_amount }} <small>(Refundable)</small></td>
            </tr>
            <tr class="grand-total">
                <td>Estimated Total:</td>
                <td class="text-right" style="color: #e9993e;">${{ number_format($pricing['total'], 2) }}</td>
            </tr>
        </table>
    </div>
    <div style="clear: both;"></div>

    <div class="footer">
        <p style="margin: 0 0 5px 0;"><strong>Terms & Conditions:</strong> Free cancellation up to 48 hours before pick-up time.</p>
        <p style="margin: 0;">Thank you for choosing RNG Car Rental! For support, contact support@rngcarrental.com</p>
    </div>
</div>

</body>
</html>
