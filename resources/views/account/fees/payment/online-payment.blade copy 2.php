 @if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif


@php($manageSettingStatus = collect(array_pluck($paymentGatewayStatus,'status','identity')))
    @php($manageSetting = array_pluck($paymentGatewayStatus,'config','identity'))
    {{--Stripe--}}
    {{--
    @if(isset($manageSettingStatus['Paypal']) && $manageSettingStatus['Paypal'] == 'active')
        @php($stripe = json_decode($manageSetting['Paypal'],true))
        @include('account.fees.payment.paypal')
    @endif
    --}}

    @if(isset($manageSettingStatus['Stripe']) && $manageSettingStatus['Stripe'] == 'active')
        @php($stripe = json_decode($manageSetting['Stripe'],true))
        @include('account.fees.payment.stripe')
    @endif

    @if(isset($manageSettingStatus['Instamojo']) && $manageSettingStatus['Instamojo'] == 'active')
        @php($instamojo  = json_decode($manageSetting['Instamojo'],true))
        @include('account.fees.payment.instamojo')
    @endif

    @if(isset($manageSettingStatus['PayUMoney']) && $manageSettingStatus['PayUMoney'] == 'active')
        @php($payumoney = json_decode($manageSetting['PayUMoney'],true))
        @include('account.fees.payment.payumoney.payumoney')
    @endif

    @if(isset($manageSettingStatus['RozorPay']) && $manageSettingStatus['RozorPay'] == 'active')
        @php($stripe = json_decode($manageSetting['RozorPay'],true))
        @include('account.fees.payment.rozorpay')
    @endif

    @if(isset($manageSettingStatus['PayStack']) && $manageSettingStatus['PayStack'] == 'active')
        @php($paystack = json_decode($manageSetting['PayStack'],true))
        @include('account.fees.payment.paystack')
    @endif
   
    @if(isset($manageSettingStatus['SSLCommerz']) && $manageSettingStatus['SSLCommerz'] == 'active')
        @php($paystack = json_decode($manageSetting['SSLCommerz'],true))
        @include('account.fees.payment.sslcommerz.payment')
    @endif

    @if(isset($manageSettingStatus['UCB']) && $manageSettingStatus['UCB'] == 'active')
        @php($paystack = json_decode($manageSetting['UCB'],true))
        @include('account.fees.payment.cybersource.payment')
    @endif

    @if(isset($manageSettingStatus['Upay']) && $manageSettingStatus['Upay'] == 'active')
        @php($paystack = json_decode($manageSetting['Upay'],true))
        @include('account.fees.payment.upay.payment')
    @endif

    {{--@include('account.fees.payment.stripe')
    @include('account.fees.payment.rozorpay.rozorpay')--}}
    {{--
    @include('account.fees.payment.pesapal.pesapal')
    @include('account.fees.payment.khalti')--}}
    @permission('fees-online-payment-pay')

    @endability

    {{-- @if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

<style>
    /* Payment button styles */
    .payment-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        background: #4a6cf7;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px rgba(74, 108, 247, 0.1);
    }

    .payment-button:hover {
        background: #3a5ce4;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(74, 108, 247, 0.15);
    }

    /* Modal styles */
    .payment-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
        display: none;
    }

    .payment-modal__overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }

    .payment-modal__container {
        position: relative;
        max-width: 800px;
        width: 90%;
        max-height: 90vh;
        margin: 5vh auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        overflow-y: auto;
    }

    .payment-modal__header {
        padding: 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .payment-modal__close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
    }

    /* Make original payment gateways look good in modal */
    .payment-gateways-container {
        padding: 20px;
    }

    .btn:hover{
        background-color: none;
    }
</style>

<!-- Pay Now Button -->
<button class="payment-button" id="openPaymentModal">
    <span>💳</span>
    <span>Pay Now</span>
</button>

<!-- Payment Modal -->
<div class="payment-modal">
    <div class="payment-modal__overlay"></div>
    <div class="payment-modal__container">
        <div class="payment-modal__header">
            <h3>Select Payment Method</h3>
            <button class="payment-modal__close">&times;</button>
        </div>
        
        <div class="payment-gateways-container">
            @php($manageSettingStatus = collect(array_pluck($paymentGatewayStatus,'status','identity')))
            @php($manageSetting = array_pluck($paymentGatewayStatus,'config','identity'))
            
            <!-- Your original payment gateway content -->
            @if(isset($manageSettingStatus['Stripe']) && $manageSettingStatus['Stripe'] == 'active')
                @php($stripe = json_decode($manageSetting['Stripe'],true))
                @include('account.fees.payment.stripe')
            @endif

            @if(isset($manageSettingStatus['Instamojo']) && $manageSettingStatus['Instamojo'] == 'active')
                @php($instamojo  = json_decode($manageSetting['Instamojo'],true))
                @include('account.fees.payment.instamojo')
            @endif

            @if(isset($manageSettingStatus['PayUMoney']) && $manageSettingStatus['PayUMoney'] == 'active')
                @php($payumoney = json_decode($manageSetting['PayUMoney'],true))
                @include('account.fees.payment.payumoney.payumoney')
            @endif

            @if(isset($manageSettingStatus['RozorPay']) && $manageSettingStatus['RozorPay'] == 'active')
                @php($stripe = json_decode($manageSetting['RozorPay'],true))
                @include('account.fees.payment.rozorpay')
            @endif

            @if(isset($manageSettingStatus['PayStack']) && $manageSettingStatus['PayStack'] == 'active')
                @php($paystack = json_decode($manageSetting['PayStack'],true))
                @include('account.fees.payment.paystack')
            @endif
           
            @if(isset($manageSettingStatus['SSLCommerz']) && $manageSettingStatus['SSLCommerz'] == 'active')
                @php($paystack = json_decode($manageSetting['SSLCommerz'],true))
                @include('account.fees.payment.sslcommerz.payment')
            @endif

            @if(isset($manageSettingStatus['UCB']) && $manageSettingStatus['UCB'] == 'active')
                @php($paystack = json_decode($manageSetting['UCB'],true))
                @include('account.fees.payment.cybersource.payment')
            @endif

            @if(isset($manageSettingStatus['Upay']) && $manageSettingStatus['Upay'] == 'active')
                @php($paystack = json_decode($manageSetting['Upay'],true))
                @include('account.fees.payment.upay.payment')
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentButton = document.getElementById('openPaymentModal');
    const paymentModal = document.querySelector('.payment-modal');
    const closeButton = document.querySelector('.payment-modal__close');

    // Open modal
    paymentButton.addEventListener('click', function() {
        paymentModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    });

    // Close modal
    function closeModal() {
        paymentModal.style.display = 'none';
        document.body.style.overflow = '';
    }

    closeButton.addEventListener('click', closeModal);
    
    // Close when clicking outside
    paymentModal.addEventListener('click', function(e) {
        if (e.target === paymentModal) {
            closeModal();
        }
    });
});
</script>

@permission('fees-online-payment-pay')
@endability --}}

@if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif

<style>
    /* Payment button styles */
    .payment-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        background: #4a6cf7;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(74, 108, 247, 0.1);
    }

    .payment-button:hover {
        background: #3a5ce4;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(74, 108, 247, 0.2);
    }

    /* Modal styles */
    .payment-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1000;
        display: none;
    }

    .payment-modal__overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }

    .payment-modal__container {
        position: relative;
        max-width: 900px;
        width: 90%;
        max-height: 90vh;
        margin: 5vh auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        overflow-y: auto;
    }

    .payment-modal__header {
        padding: 24px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .payment-modal__title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
    }

    .payment-modal__close {
        background: none;
        border: none;
        font-size: 1.8rem;
        cursor: pointer;
        color: #666;
        transition: all 0.2s;
    }

    .payment-modal__close:hover {
        color: #333;
        transform: rotate(90deg);
    }

    /* Payment Gateway Cards */
    .payment-gateways-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        padding: 24px;
    }

    .payment-gateway-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .payment-gateway-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #4a6cf7, #3a5ce4);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .payment-gateway-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(74, 108, 247, 0.15);
        border-color: rgba(74, 108, 247, 0.3);
    }

    .payment-gateway-card:hover::before {
        opacity: 1;
    }

    .payment-gateway-logo {
        width: 80px;
        height: 50px;
        object-fit: contain;
        margin-bottom: 15px;
        transition: transform 0.3s ease;
    }

    .payment-gateway-card:hover .payment-gateway-logo {
        transform: scale(1.1);
    }

    .payment-gateway-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .payment-gateway-desc {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 15px;
    }

    .payment-form-container {
        padding: 0 24px 24px;
    }

    .back-to-options {
        margin-top: 20px;
        background: #f5f7ff;
        color: #4a6cf7;
        border: 1px solid #d6e0ff;
    }

    .back-to-options:hover {
        background: #e6ebff;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .payment-gateways-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .payment-gateways-grid {
            grid-template-columns: 1fr;
        }
        
        .payment-modal__container {
            width: 95%;
            margin: 2.5vh auto;
        }
    }
</style>

<!-- Pay Now Button -->
<button class="payment-button" id="openPaymentModal">
    <span>💳</span>
    <span>Pay Now</span>
</button>

<!-- Payment Modal -->
<div class="payment-modal">
    <div class="payment-modal__overlay"></div>
    <div class="payment-modal__container">
        <div class="payment-modal__header">
            <h3 class="payment-modal__title">Select Payment Method</h3>
            <button class="payment-modal__close">&times;</button>
        </div>
        
        <div class="payment-gateways-grid">
            @php($manageSettingStatus = collect(array_pluck($paymentGatewayStatus,'status','identity')))
            @php($manageSetting = array_pluck($paymentGatewayStatus,'config','identity'))
            
            <!-- Stripe -->
            @if(isset($manageSettingStatus['Stripe']) && $manageSettingStatus['Stripe'] == 'active')
                <div class="payment-gateway-card" onclick="showPaymentForm('stripe')">
                    <img src="{{ asset('assets/images/paymenticon/stripe.png') }}" alt="Stripe" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">Credit/Debit Card</h4>
                    <p class="payment-gateway-desc">Secure card payments via Stripe</p>
                </div>
            @endif

            <!-- PayPal -->
            @if(isset($manageSettingStatus['Paypal']) && $manageSettingStatus['Paypal'] == 'active')
                <div class="payment-gateway-card" onclick="showPaymentForm('paypal')">
                    <img src="{{ asset('assets/images/paymenticon/paypal.png') }}" alt="PayPal" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">PayPal</h4>
                    <p class="payment-gateway-desc">Pay with your PayPal account</p>
                </div>
            @endif

            <!-- Instamojo -->
            @if(isset($manageSettingStatus['Instamojo']) && $manageSettingStatus['Instamojo'] == 'active')
                <div class="payment-gateway-card" onclick="showPaymentForm('instamojo')">
                    <img src="{{ asset('assets/images/paymenticon/instamojo.png') }}" alt="Instamojo" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">Instamojo</h4>
                    <p class="payment-gateway-desc">Easy payments with Instamojo</p>
                </div>
            @endif

            <!-- PayUMoney -->
            @if(isset($manageSettingStatus['PayUMoney']) && $manageSettingStatus['PayUMoney'] == 'active')
                <div class="payment-gateway-card" onclick="showPaymentForm('payumoney')">
                    <img src="{{ asset('assets/images/paymenticon/payumoney.png') }}" alt="PayUMoney" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">PayUMoney</h4>
                    <p class="payment-gateway-desc">Secure payments with PayUMoney</p>
                </div>
            @endif

            <!-- RazorPay -->
            @if(isset($manageSettingStatus['RozorPay']) && $manageSettingStatus['RozorPay'] == 'active')
                <div class="payment-gateway-card" onclick="showPaymentForm('rozorpay')">
                    <img src="{{ asset('assets/images/paymenticon/rozorpay.png') }}" alt="RazorPay" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">RazorPay</h4>
                    <p class="payment-gateway-desc">Fast and secure RazorPay</p>
                </div>
            @endif

            <!-- PayStack -->
            @if(isset($manageSettingStatus['PayStack']) && $manageSettingStatus['PayStack'] == 'active')
                <div class="payment-gateway-card" onclick="showPaymentForm('paystack')">
                    <img src="{{ asset('assets/images/paymenticon/paystack.png') }}" alt="PayStack" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">PayStack</h4>
                    <p class="payment-gateway-desc">African payment solution</p>
                </div>
            @endif

            <!-- SSLCommerz -->
            @if(isset($manageSettingStatus['SSLCommerz']) && $manageSettingStatus['SSLCommerz'] == 'active')
                <div class="payment-gateway-card" onclick="showPaymentForm('sslcommerz')">
                    <img src="{{ asset('assets/images/paymenticon/sslcommerz.png') }}" alt="SSLCommerz" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">SSLCommerz</h4>
                    <p class="payment-gateway-desc">Bangladeshi payment gateway</p>
                </div>
            @endif

            <!-- UCB -->
            @if(isset($manageSettingStatus['UCB']) && $manageSettingStatus['UCB'] == 'active')
                <div class="payment-gateway-card" onclick="showPaymentForm('ucb')">
                    <img src="{{ asset('assets/images/paymenticon/ucb.png') }}" alt="UCB" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">UCB</h4>
                    <p class="payment-gateway-desc">CyberSource payment solution</p>
                </div>
            @endif

            <!-- Upay -->
            @if(isset($manageSettingStatus['Upay']) && $manageSettingStatus['Upay'] == 'active')
                <div class="payment-gateway-card" onclick="showPaymentForm('upay')">
                    <img src="{{ asset('assets/images/paymenticon/upay.png') }}" alt="Upay" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">Upay</h4>
                    <p class="payment-gateway-desc">Mobile payment solution</p>
                </div>
            @endif
        </div>

        <!-- Payment Form Container -->
        <div class="payment-form-container" id="paymentFormContainer" style="display: none;">
            <!-- Payment forms will be loaded here dynamically -->
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentButton = document.getElementById('openPaymentModal');
        const paymentModal = document.querySelector('.payment-modal');
        const closeButton = document.querySelector('.payment-modal__close');

        // Open modal
        paymentButton.addEventListener('click', function() {
            paymentModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });

        // Close modal
        function closeModal() {
            paymentModal.style.display = 'none';
            document.body.style.overflow = '';
            backToGateways();
        }

        closeButton.addEventListener('click', closeModal);
        
        // Close when clicking outside
        paymentModal.addEventListener('click', function(e) {
            if (e.target === paymentModal) {
                closeModal();
            }
        });
    });



    function backToGateways() {
        document.querySelector('.payment-gateways-grid').style.display = 'grid';
        document.getElementById('paymentFormContainer').style.display = 'none';
        document.getElementById('paymentFormContainer').innerHTML = '';
    }
</script>

@permission('fees-online-payment-pay')
@endability