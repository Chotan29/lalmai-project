{{-- resources/views/account/fees/payment/upay/payment.blade.php
@extends('layouts.master') {{-- Adjust your layout as needed --}}
{{-- 
@section('content')
    <div class="container">
        <h1>Pay College Fee with Upay</h1>

        @if (session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        <form action="{{ route('account.fees.pay-with-upay.initiate') }}" method="POST">
            @csrf
            <p>You are about to pay your total unpaid due.</p>
            {{-- You might display the amount here from your controller --}}
{{-- <p>Amount: {{ $amount ?? 'N/A' }}</p> 
            <button type="submit" class="btn btn-primary">Proceed to Upay</button>
        </form>
    </div>
@endsection --}}
{{-- 
<style>
    .upay-btn {
            background-image: linear-gradient(to right, #28a745 0%, #218838 100%); /* Green gradient for SSLCommerz */
            color: white;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 1.1em;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: none;
        }
    .upay-btn {
        background: url('{{ asset('assets/images/paymenticon/upay.png') }}') no-repeat left center !important;
        background-size: 100px;
        height:200px;
        padding-left: 30px;
        width=250px;
    }
</style> --}}

<form action="{{ route('fees.upay.pay') }}" method="POST" id="upay-form">
    @csrf
    {{-- <p>You are about to pay your current unpaid installment.</p> --}}
    {{-- You might display the amount here from your controller --}}
    {{-- <p>Amount: {{ $amount ?? 'N/A' }}</p> --}}
    {{-- <button type="submit" class="btn btn-primary">Proceed to Upay</button> --}}
    <button type="submit" class="btn btn-block upay-btn mt-4"></button>
</form>


