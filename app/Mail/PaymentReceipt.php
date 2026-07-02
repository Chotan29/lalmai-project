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

    public function __construct(OnlinePayment $payment, Student $student)
    {
        $this->payment = $payment;
        $this->student = $student;
        $this->institutionName = config('app.name');
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