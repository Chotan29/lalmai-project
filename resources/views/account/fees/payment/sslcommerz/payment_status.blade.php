<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Status - SSLCommerz</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --success: #28a745;
            --pending: #ffc107;
            --failed: #dc3545;
            --primary: #4a6cf7;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: var(--dark);
        }
        .payment-status-container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }
        .payment-status-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 40px;
            text-align: center;
        }
        .status-icon {
            margin: 0 auto 25px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }
        .status-icon.success { background-color: rgba(40, 167, 69, 0.1); color: var(--success); }
        .status-icon.pending { background-color: rgba(255, 193, 7, 0.1); color: var(--pending); }
        .status-icon.failed, .status-icon.error, .status-icon.cancelled {
            background-color: rgba(220, 53, 69, 0.1); color: var(--failed);
        }
        .status-title {
            font-size: 2rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .status-title.success { color: var(--success); }
        .status-title.pending { color: var(--pending); }
        .status-title.failed, .status-title.error, .status-title.cancelled { color: var(--failed); }

        .status-message {
            font-size: 1.1rem;
            color: var(--gray);
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .transaction-details {
            background: var(--light);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            width: 140px;
        }

        .detail-value {
            color: var(--dark);
            flex: 1;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-badge.success { background-color: rgba(40, 167, 69, 0.1); color: var(--success); }
        .status-badge.pending { background-color: rgba(255, 193, 7, 0.1); color: var(--pending); }
        .status-badge.failed, .status-badge.error, .status-badge.cancelled {
            background-color: rgba(220, 53, 69, 0.1); color: var(--failed);
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #3a5ce4;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 108, 247, 0.2);
        }

        .btn-secondary {
            background-color: white;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-secondary:hover {
            background-color: #f5f7ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 108, 247, 0.1);
        }

        @media (max-width: 576px) {
            .payment-status-card { padding: 25px; }
            .status-title { font-size: 1.5rem; }
            .action-buttons { flex-direction: column; }
            .btn { width: 100%; justify-content: center; }
            .detail-row { flex-direction: column; }
            .detail-label { width: 100%; margin-bottom: 5px; }
        }
    </style>
</head>
<body>
    <div class="payment-status-container">
        <div class="payment-status-card">
            @if ($status === 'success' || $status === 'completed')
                <div class="status-icon success"><i class="fas fa-check-circle"></i></div>
                <h1 class="status-title success">Payment Successful!</h1>
                <p class="status-message">Thank you for your payment. Your transaction has been completed successfully.</p>
            @elseif ($status === 'pending')
                <div class="status-icon pending"><i class="fas fa-clock"></i></div>
                <h1 class="status-title pending">Payment Pending</h1>
                <p class="status-message">Your payment is being processed. We'll notify you once it's completed.</p>
            @elseif ($status === 'cancelled')
                <div class="status-icon cancelled"><i class="fas fa-times-circle"></i></div>
                <h1 class="status-title cancelled">Payment Cancelled</h1>
                <p class="status-message">You cancelled the payment process. No amount was deducted from your account.</p>
            @else
                <div class="status-icon failed"><i class="fas fa-exclamation-circle"></i></div>
                <h1 class="status-title failed">Payment Failed</h1>
                <p class="status-message">{{ $message ?? 'We could not process your payment. Please try again or contact support.' }}</p>
            @endif

            <div class="transaction-details">
                <div class="detail-row">
                    <span class="detail-label">Transaction ID:</span>
                    <span class="detail-value">{{ $transactionId ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value status-badge {{ $status }}">{{ ucfirst($status) }}</span>
                </div>
                @if(isset($payment->amount))
                    <div class="detail-row">
                        <span class="detail-label">Amount:</span>
                        <span class="detail-value">{{ number_format($payment->amount, 2) }}</span>
                    </div>
                @endif
                @if(isset($dueAmount))
                    <div class="detail-row">
                        <span class="detail-label">Current Due:</span>
                        <span class="detail-value">{{ number_format((float) $dueAmount, 2) }}</span>
                    </div>
                @endif
                @if(!empty($message))
                    <div class="detail-row">
                        <span class="detail-label">Message:</span>
                        <span class="detail-value">{{ $message }}</span>
                    </div>
                @endif
                @if(!empty($payment->payment_gateway))
                    <div class="detail-row">
                        <span class="detail-label">Payment Method:</span>
                        <span class="detail-value">{{ $payment->payment_gateway }}</span>
                    </div>
                @endif
            </div>

            <div class="action-buttons">
                <a href="{{ route('user-student.fees') }}" class="btn btn-primary">
                    <i class="fas fa-home"></i> Go to Dashboard
                </a>
                @if ($status === 'success' || $status === 'completed')
                    <a href="{{ route('print-out.fees.online-payment-receipt', ['id' => encrypt($payment->id)]) }}" target="_blank" class="btn btn-secondary">
                        <i class="fas fa-receipt"></i> View Receipt
                    </a>
                @endif
                {{-- @if ($status === 'failed' || $status === 'cancelled')
                    <a href="{{ route('account.fees.pay-with-sslcommerz.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Try Again
                    </a>
                @endif --}}
            </div>

            @if ($status === 'success' || $status === 'completed')
                <div style="margin-top: 30px; font-size: 0.9em; color: var(--gray);">
                    <p>A receipt has been emailed to you. For queries, contact our support team.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
