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

    /* Payment Gateway rows.

       This was a four-column grid sized for a wallet full of gateways. The college has one
       switched on, so it drew a single card in the first column and three empty columns beside
       it - which is what the office saw and asked about. A list of rows reads properly whether
       there is one gateway or six, and matches the confirm step the admission form already
       shows a new student. */
    .payment-gateways-grid {
        display: block;
        padding: 0 22px 4px;
    }

    .payment-gateway-card {
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e3e8ef;
        padding: 14px 16px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
        display: flex;
        flex-direction: row;
        align-items: center;
        text-align: left;
        position: relative;
    }

    .payment-gateway-card:hover,
    .payment-gateway-card:focus {
        background: #fff;
        border-color: #1f5aa6;
        box-shadow: 0 2px 10px rgba(31, 90, 166, .12);
    }

    .payment-gateway-logo {
        width: 62px;
        height: 34px;
        object-fit: contain;
        margin-right: 14px;
        flex: 0 0 auto;
    }

    .payment-gateway-text {
        flex: 1 1 auto;
        min-width: 0;
    }

    .payment-gateway-name {
        font-weight: 600;
        color: #1f2e44;
        margin: 0 0 2px;
        font-size: 15px;
    }

    .payment-gateway-desc {
        font-size: 12px;
        color: #6c757d;
        margin: 0;
    }

    .payment-gateway-card .payment-gateway-go {
        color: #2e9f6f;
        font-size: 18px;
        margin-left: 10px;
        flex: 0 0 auto;
    }

    /* The amount, said once and said plainly - the old modal never showed what was about to be
       charged, so the student had to trust the page behind it. */
    .payment-modal__amount {
        text-align: center;
        padding: 20px 22px 6px;
    }

    .payment-modal__amount-label {
        font-size: 13px;
        color: #6c757d;
    }

    .payment-modal__amount-value {
        font-size: 30px;
        font-weight: 700;
        color: #1f5aa6;
        line-height: 1.2;
    }

    .payment-modal__note {
        font-size: 12px;
        color: #6c757d;
        text-align: center;
        padding: 6px 22px 0;
    }

    .payment-modal__cancel {
        display: block;
        width: 100%;
        background: none;
        border: none;
        color: #6c757d;
        font-size: 13px;
        padding: 14px 0 20px;
        cursor: pointer;
    }

    .payment-modal__cancel:hover { color: #1f2e44; }

    /* Hidden forms container */
    .hidden-forms {
        display: none;
    }

    /* Responsive adjustments */
    @media (max-width: 480px) {
        .payment-modal__container {
            width: 95%;
            margin: 2.5vh auto;
        }

        .payment-modal__amount-value { font-size: 26px; }
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
    /* 900px was room for a four-column grid that no longer exists. A confirm step wants to be
       narrow: the same width the admission form's payment step uses. */
    max-width: 460px;
    width: 92%;
    max-height: 90vh;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    overflow-y: auto;
    z-index: 99999;
}

.payment-modal__header {
    padding: 18px 22px;
    border-bottom: 1px solid #eef0f3;
}

.payment-modal__title {
    font-size: 1.05rem;
    font-weight: 600;
    color: #1f2e44;
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
            <h3 class="payment-modal__title">
                <i class="fa fa-lock" style="color:#1f5aa6; margin-right:8px;"></i>Confirm Payment
            </h3>
            <button class="payment-modal__close">&times;</button>
        </div>

        {{-- What is about to be charged.

             Taken from the same balance the Pay Now card shows, and only drawn when it can be
             worked out - this block is shared by the student panel, the guardian panel and the
             fee print, and a modal that invents an amount would be worse than one that omits
             it. --}}
        @php($payableAmount = isset($payableAmount)
                ? $payableAmount
                : (isset($data['student']->balance) ? max((float) $data['student']->balance, 0) : null))

        @if($payableAmount !== null && $payableAmount > 0)
            <div class="payment-modal__amount">
                <div class="payment-modal__amount-label">Amount Payable</div>
                <div class="payment-modal__amount-value">&#2547;{{ number_format($payableAmount, 2) }}</div>
            </div>
        @endif

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
                    <div class="payment-gateway-text">
                        <h4 class="payment-gateway-name">Credit/Debit Card</h4>
                        <p class="payment-gateway-desc">Secure card payments via Stripe</p>
                    </div>
                    <i class="fa fa-check-circle payment-gateway-go"></i>
                </div>
            @endif

            <!-- PayPal -->
            @if(isset($manageSettingStatus['Paypal']) && $manageSettingStatus['Paypal'] == 'active')
                <div class="payment-gateway-card" data-pay-form="paypal-form">
                    <img src="{{ asset('assets/images/paymenticon/paypal.png') }}" alt="PayPal" class="payment-gateway-logo">
                    <div class="payment-gateway-text">
                        <h4 class="payment-gateway-name">PayPal</h4>
                        <p class="payment-gateway-desc">Pay with your PayPal account</p>
                    </div>
                    <i class="fa fa-check-circle payment-gateway-go"></i>
                </div>
            @endif

            <!-- Instamojo -->
            @if(isset($manageSettingStatus['Instamojo']) && $manageSettingStatus['Instamojo'] == 'active')
                <div class="payment-gateway-card" data-pay-form="instamojo-form">
                    <img src="{{ asset('assets/images/paymenticon/instamojo.png') }}" alt="Instamojo" class="payment-gateway-logo">
                    <div class="payment-gateway-text">
                        <h4 class="payment-gateway-name">Instamojo</h4>
                        <p class="payment-gateway-desc">Easy payments with Instamojo</p>
                    </div>
                    <i class="fa fa-check-circle payment-gateway-go"></i>
                </div>
            @endif

            <!-- PayUMoney -->
            @if(isset($manageSettingStatus['PayUMoney']) && $manageSettingStatus['PayUMoney'] == 'active')
                <div class="payment-gateway-card" data-pay-form="payumoney-form">
                    <img src="{{ asset('assets/images/paymenticon/payumoney.png') }}" alt="PayUMoney" class="payment-gateway-logo">
                    <div class="payment-gateway-text">
                        <h4 class="payment-gateway-name">PayUMoney</h4>
                        <p class="payment-gateway-desc">Secure payments with PayUMoney</p>
                    </div>
                    <i class="fa fa-check-circle payment-gateway-go"></i>
                </div>
            @endif

            <!-- RazorPay -->
            @if(isset($manageSettingStatus['RozorPay']) && $manageSettingStatus['RozorPay'] == 'active')
                <div class="payment-gateway-card" data-pay-form="rozorpay-form">
                    <img src="{{ asset('assets/images/paymenticon/rozorpay.png') }}" alt="RazorPay" class="payment-gateway-logo">
                    <div class="payment-gateway-text">
                        <h4 class="payment-gateway-name">RazorPay</h4>
                        <p class="payment-gateway-desc">Fast and secure RazorPay</p>
                    </div>
                    <i class="fa fa-check-circle payment-gateway-go"></i>
                </div>
            @endif

            <!-- PayStack -->
            @if(isset($manageSettingStatus['PayStack']) && $manageSettingStatus['PayStack'] == 'active')
                <div class="payment-gateway-card" data-pay-form="paystack-form">
                    <img src="{{ asset('assets/images/paymenticon/paystack.png') }}" alt="PayStack" class="payment-gateway-logo">
                    <div class="payment-gateway-text">
                        <h4 class="payment-gateway-name">PayStack</h4>
                        <p class="payment-gateway-desc">African payment solution</p>
                    </div>
                    <i class="fa fa-check-circle payment-gateway-go"></i>
                </div>
            @endif

            <!-- SSLCommerz -->
            @if($sslCommerzStatus == 'active')
                <div class="payment-gateway-card" data-pay-form="sslcommerz-form">
                    <img src="{{ asset('assets/images/paymenticon/sslcommerz.png') }}" alt="SSLCommerz" class="payment-gateway-logo">
                    <div class="payment-gateway-text">
                        <h4 class="payment-gateway-name">SSLCommerz</h4>
                        <p class="payment-gateway-desc">Bangladeshi payment gateway</p>
                    </div>
                    <i class="fa fa-check-circle payment-gateway-go"></i>
                </div>
            @endif

            <!-- UCB -->
            @if(isset($manageSettingStatus['UCB']) && $manageSettingStatus['UCB'] == 'active')
                <div class="payment-gateway-card" data-pay-form="ucb-form">
                    <img src="{{ asset('assets/images/paymenticon/ucb.png') }}" alt="UCB" class="payment-gateway-logo">
                    <div class="payment-gateway-text">
                        <h4 class="payment-gateway-name">UCB</h4>
                        <p class="payment-gateway-desc">CyberSource payment solution</p>
                    </div>
                    <i class="fa fa-check-circle payment-gateway-go"></i>
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
                    <div class="payment-gateway-text">
                        <h4 class="payment-gateway-name">Upay</h4>
                        <p class="payment-gateway-desc">Mobile payment solution</p>
                    </div>
                    <i class="fa fa-check-circle payment-gateway-go"></i>
                </div>
            @endif
        </div>

        <div class="payment-modal__note">
            <i class="fa fa-shield" aria-hidden="true"></i>
            You will be redirected to the secure gateway.
            Please do not close or refresh the page during payment.
        </div>

        <button type="button" class="payment-modal__cancel">Cancel</button>
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

    /* The Cancel line under the gateways, the same one the admission form offers. */
    const cancelButton = document.querySelector('.payment-modal__cancel');
    if (cancelButton) {
        cancelButton.addEventListener('click', closeModal);
    }

    /* Escape closes it. A payment box with no way out but the mouse is a box people abandon
       the page from, and an abandoned page is a pending payment row. */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && paymentModal.style.display === 'block') {
            closeModal();
        }
    });
    
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