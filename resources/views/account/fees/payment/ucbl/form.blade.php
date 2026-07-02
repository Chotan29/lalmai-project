<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CyberSource Payment</title>
    <link rel="stylesheet" href="{{ asset('assets/plugins/paymentgateway/cybersource/payment.css') }}">
    <script src="{{ asset('js/jquery-1.7.min.js') }}"></script>
    <script src="{{ asset('js/payment_form.js') }}"></script>
</head>
<body>
    {{-- <h2>Secure Payment Checkout</h2> --}}

    {{-- <form id="payment_form" method="POST" action="https://testsecureacceptance.cybersource.com/silent/pay"> --}}
    {{-- <form id="payment_form" method="POST" action="https://testsecureacceptance.cybersource.com/pay">
        @foreach($data as $key => $value)
           {{ $key }}: <input type="text" name="{{ $key }}" value="{{ $value }}"><br>
        @endforeach
        {{-- <input type="hidden" name="override_custom_receipt_page" value="{{ route('ucb.payment.confirmation') }}"> 


        <input type="submit" value="Pay Now" />
    </form> --}}

    {{-- <form id="payment_form" method="POST" action="https://testsecureacceptance.cybersource.com/pay"> --}}
    <form id="payment_form" method="POST" action="{{ env('UCBL_LIVE_MODE') == 'true' ? 'https://secureacceptance.cybersource.com/pay' : 'https://testsecureacceptance.cybersource.com/pay' }}">
    @foreach($data as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach

    <!-- Custom fields to restore state -->
    <input type="hidden" name="custom_student_reg_no" value="{{ $student->reg_no }}">
    <input type="hidden" name="custom_invoice_id" value="{{ $invoiceId }}">
    <input type="hidden" name="custom_amount" value="{{ $amount }}">
    <input type="hidden" name="custom_user_id" value="{{ auth()->id() }}">
    
    {{-- <input type="submit" value="Pay Now" /> --}}
</form>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('payment_form').submit();
        });
    </script>
</body>

</html>


