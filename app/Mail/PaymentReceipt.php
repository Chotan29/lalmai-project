<?php

namespace App\Mail;

use App\Models\OnlinePayment;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $student;
    public $institutionName;

    /** College letterhead for the email body - the same thing generatePDF() puts on the pdf. */
    public $generalSetting;

    public function __construct(OnlinePayment $payment, Student $student)
    {
        $this->payment = $payment;
        $this->student = $student;
        $this->institutionName = config('app.name');

        /*
         * build() has always asked for $this->generalSetting and nothing ever set it, so every
         * receipt died on "Undefined property" - caught and logged in sendPaymentReceipt(), which
         * is why payments looked fine while not one receipt was ever delivered.
         *
         * The college's own settings row is used where there is one, and the config values
         * otherwise, so an installation without that table still sends a sensible receipt.
         */
        $this->generalSetting = $this->settings();
    }

    protected function settings()
    {
        try {
            $row = \App\Models\GeneralSetting::first();
            if ($row) { return $row; }
        } catch (\Throwable $e) {
            /* fall through to the config values below */
        }

        return (object) [
            'logo'      => config('app.logo'),
            'institute' => config('app.name'),
            'address'   => config('app.address'),
            'phone'     => config('app.phone'),
            'email'     => config('app.email'),
            'website'   => config('app.website'),
        ];
    }

    // In app/Mail/PaymentReceipt.php
    public function build()
    {
        return $this->subject($this->institutionName . ' - Payment Receipt #' . $this->payment->invoice_id)
                    ->view('emails.payment-receipt') // Make sure this view exists
                    ->with([
                        'payment' => $this->payment,
                        'student' => $this->student,
                        'generalSetting' => $this->generalSetting
                    ]);
    }

    protected function generatePDF()
    {
        $pdf = \PDF::loadView('print.student-fee.online-payment-receipt', [
            'data' => [
                'payment' => $this->payment,
                'student' => $this->student,
            ],
            'generalSetting' => (object)[
                'logo' => config('app.logo'),
                'institute' => config('app.name'),
                'address' => config('app.address'),
                'phone' => config('app.phone'),
                'email' => config('app.email'),
                'website' => config('app.website'),
            ]
        ]);
        
        return $pdf->output();
    }
}