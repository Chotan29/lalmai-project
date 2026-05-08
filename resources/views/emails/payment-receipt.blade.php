<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $data['payment']->invoice_id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #2a6496;
            padding: 20px;
            text-align: center;
            color: white;
        }
        .logo {
            max-height: 50px;
            margin-bottom: 10px;
        }
        .institution-name {
            font-size: 18px;
            font-weight: 600;
            margin: 5px 0;
        }
        .receipt-title {
            font-size: 22px;
            font-weight: 600;
            margin: 20px 0;
            color: #2a6496;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .status-banner {
            background-color: #f0f7ff;
            border-left: 4px solid #1a73e8;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .status-verified {
            background-color: #d4edda;
            border-left-color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            border-left-color: #856404;
        }
        .detail-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .detail-item {
            margin-bottom: 8px;
        }
        .detail-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
            margin-bottom: 3px;
        }
        .detail-value {
            font-weight: 500;
        }
        .amount-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .amount-table th, .amount-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .amount-table th {
            background-color: #f8f9fa;
            font-weight: 500;
        }
        .total-row {
            font-weight: 600;
            background-color: #f0f7ff;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2a6496;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            margin: 10px 0;
        }
        .contact-item {
            display: inline-flex;
            align-items: center;
            margin: 0 10px 5px 0;
            font-size: 12px;
        }
        .contact-item i {
            margin-right: 5px;
            color: #2a6496;
        }
        @media (max-width: 600px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with Institution Info -->
        <div class="header">
            @if(isset($generalSetting->logo))
                <img src="{{ asset('images/setting/general/'.$generalSetting->logo) }}" alt="Logo" class="logo">
            @endif
            <div class="institution-name">{{ $generalSetting->institute ?? 'Institution Name' }}</div>
            <div style="font-size: 12px; opacity: 0.9;">Payment Receipt</div>
        </div>

        <div class="content">
            <div class="receipt-title">Payment Receipt Confirmation</div>
            
            <p>Dear {{ $data['student']->first_name }},</p>
            
            <p>Thank you for your payment. Here's your transaction confirmation:</p>
            
            <!-- Payment Status Banner -->
            <div class="status-banner status-{{ $data['payment']->status == 1 ? 'verified' : 'pending' }}">
                <i class="fas {{ $data['payment']->status == 1 ? 'fa-check-circle' : 'fa-clock' }}"></i>
                @if($data['payment']->status == 1)
                    Payment Verified • {{ \Carbon\Carbon::parse($data['payment']->date)->format('M d, Y h:i A') }}
                @else
                    Payment Pending Verification • Please allow 24-48 hours for processing
                @endif
            </div>
            
            <!-- Transaction Summary Card -->
            <div class="detail-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <div>
                        <div class="detail-label">Receipt Number</div>
                        <div style="font-size: 16px; font-weight: 600;">{{ $data['payment']->invoice_id }}</div>
                    </div>
                    <div style="text-align: right;">
                        <div class="detail-label">Date</div>
                        <div>{{ \Carbon\Carbon::parse($data['payment']->date)->format('M d, Y') }}</div>
                    </div>
                </div>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Student Name</div>
                        <div class="detail-value">{{ $data['student']->first_name }} {{ $data['student']->last_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Registration No.</div>
                        <div class="detail-value">{{ $data['student']->reg_no }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Payment Method</div>
                        <div class="detail-value">{{ $data['payment']->payment_gateway }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Transaction ID</div>
                        <div class="detail-value">{{ $data['payment']->ref_no }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Amount Breakdown -->
            <table class="amount-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Payment via {{ $data['payment']->payment_gateway }}</td>
                        <td style="text-align: right;">{{ config('app.currency', 'USD') }} {{ number_format($data['payment']->amount, 2) }}</td>
                    </tr>
                    @if($data['payment']->note)
                    <tr>
                        <td colspan="2"><strong>Note:</strong> {{ $data['payment']->note }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Total Paid</td>
                        <td style="text-align: right;">{{ config('app.currency', 'USD') }} {{ number_format($data['payment']->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Action Buttons -->
            <div style="text-align: center; margin: 25px 0;">
                <a href="#" class="btn">View Payment History</a>
                <p style="font-size: 12px; margin-top: 10px; color: #666;">
                    <i class="fas fa-paperclip"></i> A printable receipt is attached to this email
                </p>
            </div>
            
            <!-- Additional Payment Details -->
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="margin-top: 0; font-size: 14px; color: #444;">Payment Details</h4>
                <div style="font-size: 13px;">
                    <p><strong>Gateway:</strong> {{ $data['payment']->payment_gateway }}</p>
                    <p><strong>Reference:</strong> {{ $data['payment']->ref_no }}</p>
                    <p><strong>Processed:</strong> {{ \Carbon\Carbon::parse($data['payment']->date)->format('M d, Y h:i A') }}</p>
                    @if($data['payment']->ref_text)
                        @php $refData = json_decode($data['payment']->ref_text, true); @endphp
                        @if(isset($refData['transaction_id']))
                        <p><strong>Gateway TXN ID:</strong> {{ $refData['transaction_id'] }}</p>
                        @endif
                    @endif
                </div>
            </div>
            
            <p>If you have any questions about this payment, please contact our support team.</p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div style="margin-bottom: 10px;">
                @if(isset($generalSetting->email))
                    <span class="contact-item"><i class="fas fa-envelope"></i> {{ $generalSetting->email }}</span>
                @endif
                @if(isset($generalSetting->phone))
                    <span class="contact-item"><i class="fas fa-phone"></i> {{ $generalSetting->phone }}</span>
                @endif
                @if(isset($generalSetting->website))
                    <span class="contact-item"><i class="fas fa-globe"></i> {{ $generalSetting->website }}</span>
                @endif
            </div>
            <div>© {{ date('Y') }} {{ $generalSetting->institute ?? 'Institution Name' }}. All rights reserved.</div>
            <div style="font-size: 11px; color: #999; margin-top: 5px;">
                This is an official receipt. Please do not reply to this automated message.
            </div>
        </div>
    </div>
</body>
</html>