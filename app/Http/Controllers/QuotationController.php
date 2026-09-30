<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    public function downloadQuotation(Request $request)
    {
        $data = [
            'quotationNumber' => 'RNG-' . strtoupper(uniqid()),
            'date' => now()->format('d M Y'),
            'customer' => (object) [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+46 70 123 4567',
                'communication_channel' => 'WhatsApp / SMS'
            ],
            'car' => (object) [
                'name' => 'Jeep Renegade',
                'type' => 'Standard',
                'insurance_package' => 'Basic CDW',
                'deposit_amount' => '150',
                'seats' => '4',
                'luggage' => '2',
                'fuel' => 'Petrol',
                'drive' => 'Auto'
            ],
            'schedule' => (object) [
                'pickup_date' => 'SUN, 5 MAR • 10:30 AM',
                'pickup_loc' => 'Kalmar Airport (Terminal Pick-up Counter)',
                'dropoff_date' => 'MON, 13 MAR • 10:30 AM',
                'dropoff_loc' => 'Kalmar Airport (Terminal Drop-off Return)',
            ],
            'pricing' => [
                'hire_days' => 8,
                'hire_total' => 2147.20,
                'taxes_fees' => 100.00,
                'total' => 2247.20
            ],
            'extras_label' => 'Winter Tyres & Equipment Package'
        ];

        $pdf = Pdf::loadView('cars.quotation', $data);

        return $pdf->download('RNG-Car-Rental-Quotation-' . $data['quotationNumber'] . '.pdf');
    }
}
