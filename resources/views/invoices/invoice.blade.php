<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $reservation->invoice_no }}</title>

    {{-- Same styles as rngcarrental's reservations/pdf/invoice.blade.php --}}
    <style>
        @page { margin: 12mm 12mm; }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
        }

        .box {
            position: relative;
            padding: 15px;
        }

        .box .corner {
            position: absolute;
            width: 40px;
            height: 25px;
            border: 1.5px solid black;
        }

        .box .tl { top: 0; left: 0; border-right: none; border-bottom: none; }
        .box .tr { top: 0; right: 0; border-left: none; border-bottom: none; }
        .box .bl { bottom: 0; left: 0; border-right: none; border-top: none; }
        .box .br { bottom: 0; right: 0; border-left: none; border-top: none; }

        .items-wrapper {
            min-height: 80mm;
            display: block;
        }

        .header-table,
        .info-table,
        .items-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th,
        .items-table td {
            vertical-align: top;
        }

        .items-table th {
            text-align: center;
            border-top: 0.25mm solid #000;
            border-bottom: 0.25mm solid #000;
        }

        .blank-row td {
            height: 6mm;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }

        .mt-8mm { margin-top: 8mm; }
        .mt-5mm { margin-top: 5mm; }
        .mt-10mm { margin-top: 10mm; }
        .mt-15mm { margin-top: 15mm; }
    </style>
</head>

<body>
    {{-- rngcarrental prints the footer at the page bottom (wkhtmltopdf); here it follows the totals --}}
    @include('invoices.body', ['reservation' => $reservation, 'customer' => $reservation->customer, 'logo' => $logo, 'company' => $company])

    <div class="mt-10mm">
        @include('invoices.footer', ['reservation' => $reservation, 'chop' => $chop, 'company' => $company])
    </div>
</body>
</html>
