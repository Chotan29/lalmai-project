
@php($manageSettingStatus = collect(array_pluck($paymentGatewayStatus,'status','identity')))
    @php($manageSetting = array_pluck($paymentGatewayStatus,'config','identity'))
    @php($sslCommerzStatus = $manageSettingStatus['SSLCommerz'] ?? $manageSettingStatus['sslcommerz'] ?? null)
    @php($sslCommerzConfig = $manageSetting['SSLCommerz'] ?? $manageSetting['sslcommerz'] ?? null)
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
   
    @if($sslCommerzStatus == 'active' && $sslCommerzConfig)
        @php($paystack = json_decode($sslCommerzConfig,true))
        @include('account.fees.payment.sslcommerz.payment')
    @endif

    @if(isset($manageSettingStatus['UCB']) && $manageSettingStatus['UCB'] == 'active')
        @php($paystack = json_decode($manageSetting['UCB'],true))
        {{-- @include('account.fees.payment.cybersource.index') --}}
        {{-- @include('account.fees.payment.ucbl.payment') --}}
        @include('account.fees.payment.ucbl.payment')
    @endif

    @if(isset($manageSettingStatus['Upay']) && $manageSettingStatus['Upay'] == 'active')
        @php($paystack = json_decode($manageSetting['Upay'],true))
        @include('account.fees.payment.upay.payment')
    @endif
