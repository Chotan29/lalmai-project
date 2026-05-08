    {{-- <style>
    .sslcommerz-btn {
        background: url('{{ asset('assets/images/paymenticon/sslcommerz.png') }}') no-repeat left center !important;
        background-size: 100px;
        height:100px;
        padding-left: 30px;
        width=250px;
    }
</style> --}}
<form action="{{ route('account.fees.sslcommerz.initiate') }}" method="POST" id="sslcommerz-form">
    @csrf
    <input type="hidden" id="reg_no" name="reg_no" value="{{ encrypt($data['student']->reg_no) }}">
    {{-- <input type="hidden" step="0.01" id="amount" name="amount" value="100.00" required>
            <input type="hidden" id="customer_name" name="customer_name" value="SSLCommerz Test Customer" required>
            <input type="hidden" id="customer_email" name="customer_email" value="sslcommerz.test@example.com" required>
            <input type="hidden" id="customer_phone" name="customer_phone" value="01711111111" required>
            <input type="hidden" id="customer_address" name="customer_address" value="123 Test Street" required>
            <input type="hidden" id="customer_city" name="customer_city" value="Dhaka" required>
            <input type="hidden" id="customer_postcode" name="customer_postcode" value="1000" required>
            <input type="hidden" id="customer_country" name="customer_country" value="Bangladesh" required> --}}

    <button type="submit" class="btn btn-block sslcommerz-btn mt-4"></button>
    {{-- Pay Now with SSLCommerz --}}
</form>
