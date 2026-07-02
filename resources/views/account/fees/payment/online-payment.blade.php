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

    /* Hidden forms container */
    .hidden-forms {
        display: none;
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

    /* Payment Modal Styles - Ensure these are at the end of your CSS */
.payment-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999; /* Very high z-index to ensure it's above everything */
    display: none;
}

.payment-modal__overlay {
    position: fixed; /* Changed from absolute to fixed */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 99998;
}

.payment-modal__container {
    position: fixed; /* Changed from relative to fixed */
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    overflow-y: auto;
    z-index: 99999;
}
</style>

{{-- <!-- Pay Now Button -->
<button class="payment-button" id="openPaymentModal" data-open-payment-modal="1" type="button">
    <span>💳</span>
    <span>Pay Now</span>
</button> --}}

<!-- Payment Modal -->
<div class="payment-modal">
    <div class="payment-modal__overlay"></div>
    <div class="payment-modal__container">
        <div class="payment-modal__header">
            <h3 class="payment-modal__title">Select Payment Method</h3>
            <button class="payment-modal__close">&times;</button>
        </div>
        
        <div class="payment-gateways-grid">
            <!-- Hidden forms container (for direct submission) -->
            <div class="hidden-forms">
                <!-- Upay Form -->
                {{-- <form action="{{ route('account.fees.pay-with-upay.pay') }}" id="upay-form" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-block upay-btn mt-4"></button>
                </form> --}}
                @include('account.fees.payment.online-payment-forms')
            
            </div>
            
            @php($manageSettingStatus = collect(array_pluck($paymentGatewayStatus,'status','identity')))
            @php($manageSetting = array_pluck($paymentGatewayStatus,'config','identity'))
            @php($sslCommerzStatus = $manageSettingStatus['SSLCommerz'] ?? $manageSettingStatus['sslcommerz'] ?? null)
            
            <!-- Stripe -->
            @if(isset($manageSettingStatus['Stripe']) && $manageSettingStatus['Stripe'] == 'active')
                <div class="payment-gateway-card" data-pay-form="stripe-form">
                    <img src="{{ asset('assets/images/paymenticon/stripe.png') }}" alt="Stripe" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">Credit/Debit Card</h4>
                    <p class="payment-gateway-desc">Secure card payments via Stripe</p>
                </div>
            @endif

            <!-- PayPal -->
            @if(isset($manageSettingStatus['Paypal']) && $manageSettingStatus['Paypal'] == 'active')
                <div class="payment-gateway-card" data-pay-form="paypal-form">
                    <img src="{{ asset('assets/images/paymenticon/paypal.png') }}" alt="PayPal" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">PayPal</h4>
                    <p class="payment-gateway-desc">Pay with your PayPal account</p>
                </div>
            @endif

            <!-- Instamojo -->
            @if(isset($manageSettingStatus['Instamojo']) && $manageSettingStatus['Instamojo'] == 'active')
                <div class="payment-gateway-card" data-pay-form="instamojo-form">
                    <img src="{{ asset('assets/images/paymenticon/instamojo.png') }}" alt="Instamojo" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">Instamojo</h4>
                    <p class="payment-gateway-desc">Easy payments with Instamojo</p>
                </div>
            @endif

            <!-- PayUMoney -->
            @if(isset($manageSettingStatus['PayUMoney']) && $manageSettingStatus['PayUMoney'] == 'active')
                <div class="payment-gateway-card" data-pay-form="payumoney-form">
                    <img src="{{ asset('assets/images/paymenticon/payumoney.png') }}" alt="PayUMoney" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">PayUMoney</h4>
                    <p class="payment-gateway-desc">Secure payments with PayUMoney</p>
                </div>
            @endif

            <!-- RazorPay -->
            @if(isset($manageSettingStatus['RozorPay']) && $manageSettingStatus['RozorPay'] == 'active')
                <div class="payment-gateway-card" data-pay-form="rozorpay-form">
                    <img src="{{ asset('assets/images/paymenticon/rozorpay.png') }}" alt="RazorPay" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">RazorPay</h4>
                    <p class="payment-gateway-desc">Fast and secure RazorPay</p>
                </div>
            @endif

            <!-- PayStack -->
            @if(isset($manageSettingStatus['PayStack']) && $manageSettingStatus['PayStack'] == 'active')
                <div class="payment-gateway-card" data-pay-form="paystack-form">
                    <img src="{{ asset('assets/images/paymenticon/paystack.png') }}" alt="PayStack" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">PayStack</h4>
                    <p class="payment-gateway-desc">African payment solution</p>
                </div>
            @endif

            <!-- SSLCommerz -->
            @if($sslCommerzStatus == 'active')
                <div class="payment-gateway-card" data-pay-form="sslcommerz-form">
                    <img src="{{ asset('assets/images/paymenticon/sslcommerz.png') }}" alt="SSLCommerz" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">SSLCommerz</h4>
                    <p class="payment-gateway-desc">Bangladeshi payment gateway</p>
                </div>
            @endif

            <!-- UCB -->
            @if(isset($manageSettingStatus['UCB']) && $manageSettingStatus['UCB'] == 'active')
                <div class="payment-gateway-card" data-pay-form="ucb-form">
                    <img src="{{ asset('assets/images/paymenticon/ucb.png') }}" alt="UCB" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">UCB</h4>
                    <p class="payment-gateway-desc">CyberSource payment solution</p>
                </div>
                {{-- <a class="payment-gateway-card" href="{{ route('account.fees.pay-with-cybersource.index') }}">
                    <img src="{{ asset('assets/images/paymenticon/sslcommerz.png') }}" alt="SSLCommerz" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">Cyber</h4>
                    <p class="payment-gateway-desc">NEW payment gateway</p>
                </a> --}}
            @endif

            <!-- Upay -->
            @if(isset($manageSettingStatus['Upay']) && $manageSettingStatus['Upay'] == 'active')
                <div class="payment-gateway-card" data-pay-form="upay-form">
                    <img src="{{ asset('assets/images/paymenticon/upay.png') }}" alt="Upay" class="payment-gateway-logo">
                    <h4 class="payment-gateway-name">Upay</h4>
                    <p class="payment-gateway-desc">Mobile payment solution</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function() {
    const paymentButtons = document.querySelectorAll('[data-open-payment-modal], #openPaymentModal');
    const paymentModal = document.querySelector('.payment-modal');
    const closeButton = document.querySelector('.payment-modal__close');
    const modalContainer = document.querySelector('.payment-modal__container');

    if (!paymentModal || !modalContainer) {
        return;
    }

    function openModal() {
        paymentModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    // Open modal
    paymentButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openModal();
        });
    });

    function submitPaymentGatewayForm(formId) {
        const form = document.getElementById(formId);
        if (!form) {
            return false;
        }

        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.submit();
        }

        return true;
    }

    document.querySelectorAll('.payment-gateway-card[data-pay-form]').forEach(function(card) {
        card.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            submitPaymentGatewayForm(card.getAttribute('data-pay-form'));
        });
    });

    // Expose helpers for custom buttons/cards.
    window.openPaymentGatewayModal = openModal;
    window.submitPaymentGatewayForm = submitPaymentGatewayForm;

    // Close modal
    function closeModal() {
        paymentModal.style.display = 'none';
        document.body.style.overflow = '';
    }

    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }
    
    // Close when clicking outside
    paymentModal.addEventListener('click', function(e) {
        if (e.target === paymentModal || !modalContainer.contains(e.target)) {
            closeModal();
        }
    });
});
</script>

@permission('fees-online-payment-pay')
@endability