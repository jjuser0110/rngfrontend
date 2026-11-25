<?php

namespace App\Support;

use App\Payments\BookingPayment;

/**
 * Details page (online payment on): pay the full amount, or the deposit only. A customer with no deposit gets
 * "Book now, pay later" instead of the deposit only. Used by the summary's choice and the Pay buttons.
 */
class PayOptions
{
    /** ['show' => bool, 'selected' => 'full'|'deposit', 'options' => [value => title, amount, note, button, mobile, icon]] */
    public static function for(array $quote): array
    {
        $fullAmount = $quote['total'] + $quote['security_deposit'];
        $hasDeposit = $quote['security_deposit'] > 0;
        $show = BookingPayment::depositOnlyAllowed() && $quote['total'] > 0;

        $options = [
            'full' => [
                'title'  => 'Pay full amount',
                'amount' => $fullAmount,
                'note'   => $hasDeposit ? 'Rental + refundable deposit.' : 'Rental.',
                'button' => 'Pay RM ' . number_format($fullAmount, 2) . ' & Book',
                'mobile' => 'Pay & Book',
                'icon'   => 'fa-lock',
            ],
            'deposit' => [
                'title'  => $hasDeposit ? 'Pay deposit only' : 'Book now, pay later',
                'amount' => $hasDeposit ? $quote['security_deposit'] : 0,
                'note'   => ($hasDeposit ? 'Rental RM ' . number_format($quote['total'], 2) . ' is paid' : 'Nothing to pay now. Pay')
                    . ' at the counter when you pick up your car, or our staff will contact you about payment.',
                'button' => $hasDeposit ? 'Pay RM ' . number_format($quote['security_deposit'], 2) . ' & Book' : 'Send Booking',
                'mobile' => $hasDeposit ? 'Pay & Book' : 'Send Booking',
                'icon'   => $hasDeposit ? 'fa-lock' : 'fa-paper-plane',
            ],
        ];

        return [
            'show'     => $show,
            'selected' => $show && old('pay_option') === 'deposit' ? 'deposit' : 'full',
            'options'  => $options,
        ];
    }
}
