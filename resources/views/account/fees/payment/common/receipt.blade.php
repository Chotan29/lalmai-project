<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Payment Receipt - {{ $payment->invoice_id }}</title>
    <style>
        @page {
            size: A5;
            margin: 5mm;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.4;
            color: #333;
            font-size: 12px;
            background: white;
            margin: 0;
            padding: 0;
        }
        
        .receipt-container {
            width: 148mm;
            height: 210mm;
            margin: 0 auto;
            padding: 5mm;
            position: relative;
            box-sizing: border-box;
        }
        
        .watermark {
            opacity: 0.1;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 3rem;
            font-weight: bold;
            color: #ccc;
            z-index: -1;
            white-space: nowrap;
        }
        
        /* Institution Header Styles for Receipt */
        .institution-header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #333;
        }
        
        .institution-logo {
            max-height: 40px;
            margin-bottom: 5px;
        }
        
        .institution-name {
            font-weight: bold;
            font-size: 14px;
            margin: 3px 0;
            text-transform: uppercase;
        }
        
        .institution-contact {
            font-size: 10px;
            color: #555;
            margin: 3px 0;
        }
        
        .contact-item {
            display: inline-block;
            margin: 0 5px;
        }
        
        /* Rest of the receipt styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            padding-bottom: 5px;
            page-break-after: avoid;
        }
        
        .payment-details {
            margin-bottom: 10px;
        }
        
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }
        
        .detail-item {
            margin-bottom: 3px;
        }
        
        .detail-label {
            font-weight: bold;
            font-size: 0.9em;
            color: #555;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 11px;
            page-break-inside: avoid;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #555;
            page-break-before: avoid;
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.8em;
            font-weight: bold;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        @media print {
            body {
                zoom: 0.9;
            }
            .receipt-container {
                padding: 0;
            }
            .institution-header {
                border-bottom: 1px solid #000;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container no-break">
        <!-- Watermark -->
        <div class="watermark">{{ strtoupper($transaction['status']) }}</div>

        <!-- Institution Header -->
        <div class="institution-header no-break">
            @if(isset($generalSetting->logo))
                <img class="institution-logo" 
                     src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" 
                     alt="{{ isset($generalSetting->institute) ? $generalSetting->institute : 'Institution Logo' }}">
            @endif
            
            <div class="institution-name">
                {{ isset($generalSetting->institute) ? $generalSetting->institute : 'Institution Name' }}
            </div>
            
            @if(isset($generalSetting->salogan) && $generalSetting->salogan)
                <div class="institution-slogan" style="font-size: 10px; font-style: italic; margin: 2px 0;">
                    {{ $generalSetting->salogan }}
                </div>
            @endif
            
            <div class="institution-contact">
                @if(isset($generalSetting->address) && $generalSetting->address)
                    <span class="contact-item">{{ $generalSetting->address }}</span>
                @endif
                
                @if(isset($generalSetting->phone) && $generalSetting->phone)
                    <span class="contact-item">Tel: {{ $generalSetting->phone }}</span>
                @endif
                
                @if(isset($generalSetting->email) && $generalSetting->email)
                    <span class="contact-item">Email: {{ $generalSetting->email }}</span>
                @endif
            </div>
        </div>

        <!-- Header -->
        <div class="header">
            <div style="flex: 2;">
                <p style="font-size: 13px; margin: 3px 0; font-weight: bold;">ONLINE PAYMENT RECEIPT</p>
            </div>
            <div style="flex: 1; text-align: right;">
                <p style="margin: 2px 0;"><strong>Receipt #</strong> {{ $payment->invoice_id }}</p>
                <p style="margin: 2px 0;"><strong>Date:</strong> {{ \Carbon\Carbon::parse($payment->date)->format('d M Y') }}</p>
                <span class="status-badge status-{{ strtolower($transaction['status']) }}" style="margin-top: 5px; display: inline-block;">
                    {{ strtoupper($transaction['status']) }}
                </span>
            </div>
        </div>

        <!-- Student Information -->
        <div class="payment-details no-break">
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Student Name</div>
                    <div>{{ $student->user->name ?? 'N/A' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Registration No.</div>
                    <div>{{ $student->reg_no }}</div>
                </div>
                @if(isset($student->batch))
                <div class="detail-item">
                    <div class="detail-label">Batch</div>
                    <div>{{ $student->batch->batch_name ?? 'N/A' }}</div>
                </div>
                @endif
                @if(isset($student->faculty))
                <div class="detail-item">
                    <div class="detail-label">Faculty</div>
                    <div>{{ $student->faculty->faculty_name ?? 'N/A' }}</div>
                </div>
                @endif
                <div class="detail-item">
                    <div class="detail-label">Payment Method</div>
                    <div>{{ $payment->payment_gateway }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Transaction ID</div>
                    <div>{{ $payment->ref_no }}</div>
                </div>
            </div>
        </div>

        <!-- Amount Breakdown -->
        <table class="no-break">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount ({{ config('app.currency', 'USD') }})</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Online Payment</td>
                    <td style="text-align: right;">{{ number_format($payment->amount, 2) }}</td>
                </tr>
                @if(isset($payment->note) && $payment->note)
                <tr>
                    <td colspan="2"><strong>Note:</strong> {{ $payment->note }}</td>
                </tr>
                @endif
                <tr style="background-color: #f0f7ff;">
                    <td style="font-weight: bold;">TOTAL PAID</td>
                    <td style="text-align: right; font-weight: bold; color: #1a73e8;">{{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Gateway Info -->
        <div style="background-color: #f8f9fa; padding: 8px; border-radius: 3px; margin-bottom: 10px; font-size: 11px;" class="no-break">
            <h4 style="margin: 0 0 5px 0; font-size: 12px;">PAYMENT INFORMATION</h4>
            <p style="margin: 3px 0;">Processed via {{ $payment->payment_gateway }}</p>
            <p style="margin: 3px 0;">Transaction Ref: {{ $payment->ref_no }}</p>
            @if(isset($payment->ref_text))
                @php $refData = json_decode($payment->ref_text, true); @endphp
                @if(isset($refData['transaction_id']))
                <p style="margin: 3px 0;">Gateway ID: {{ $refData['transaction_id'] }}</p>
                @endif
            @endif
        </div>

        <!-- Footer -->
        <div class="footer no-break">
            <p>This is an official receipt. Please keep it for your records.</p>
            <p>Thank you for your payment!</p>
            <p style="margin-top: 8px; font-style: italic;">Printed on: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
        </div>
    </div>

    <script>
        // Auto-print when the page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>