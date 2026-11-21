{{-- Copy of rngcarrental's reservations/pdf/invoice-footer.blade.php. Keep in step. --}}
<style>
    * {
        /* font-kerning: normal;
        text-rendering: optimizeLegibility; */
        font-family: Arial, sans-serif;
        font-size: 10pt;
        line-height: 1.5;
    }

    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .bold { font-weight: bold; }

    .mt-8mm { margin-top: 8mm; }
    .mt-5mm { margin-top: 5mm; }
    .mt-10mm { margin-top: 10mm; }
    .mt-15mm { margin-top: 15mm; }

    .signature-line {
        display: block;
        width: 60mm;
        border-bottom: 0.25mm solid #000;
        margin-top: 14mm;
        margin-bottom: 2mm;
    }
</style>

<div>
    <div style="font-size: smaller;">
        Notes:<br>
        1. All cheques should be crossed and made payable to {{ $company->name ?? 'RENT N GO CAR RENTAL SDN BHD' }}<br>
        2. All payment by online transfer shall be made to {{ $company->bank_name ?? 'PUBLIC BANK' }} Account No: {{ $company->account_number ?? '3236992909' }}<br>
        3. Goods sold are neither returnable nor refundable. <br>
    </div>

    <!-- SIGNATURES -->
    <div style="padding: 5px;">
        <table class="footer-table mt-15mm">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <img src="{{ $chop }}" height="80"><br>
                    _________________________________<br>
                    <strong>Authorised Signature</strong>
                </td>
            </tr>
        </table>
    </div>
</div>

