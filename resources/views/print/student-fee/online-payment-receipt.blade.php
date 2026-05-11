<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Payment Receipt - {{ $data['payment']->invoice_id }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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

        /* Institution Header Styles */
        .institution-header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #333;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 5px;
        }

        .institution-logo {
            max-height: 40px;
            max-width: 100%;
            object-fit: contain;
        }

        .institution-slogan {
            font-size: 10px;
            color: #666;
            font-style: italic;
            margin: 2px 0;
        }

        .institution-name {
            font-weight: bold;
            font-size: 14px;
            margin: 3px 0;
            color: #2a6496;
            text-transform: uppercase;
        }

        .institution-contact {
    font-size: 9px;
    color: #555;
    margin: 3px 0;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}

.contact-item {
    display: inline-flex;
    align-items: center;
    margin: 0 5px 3px 0;
    white-space: nowrap;
}

.contact-item i {
    margin-right: 3px;
    color: #2a6496; /* Matching institution name color */
    font-size: 10px;
    width: 14px;
    text-align: center;
}

        /* Status Indicators */
        .verification-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 5px;
        }

        .status-verified {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }



        /* Receipt Content Styles */
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

        th,
        td {
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

        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
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

            .institution-name {
                font-size: 14pt;
            }

            .institution-contact {
                font-size: 9pt;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container no-break">
        <!-- Watermark -->
        <div class="watermark">{{ strtoupper($data['payment']->payment_status) }}</div>
        <!-- Watermark -->
        <div class="watermark">
            @if ($data['payment']->status == 1)
                VERIFIED
            @else
                PENDING
            @endif
        </div>

        <!-- Institution Header -->
        <div class="institution-header no-break">
            @if (isset($generalSetting->logo))
                <div class="logo-container">
                    <img class="institution-logo"
                        src="{{ asset('images' . DIRECTORY_SEPARATOR . 'setting' . DIRECTORY_SEPARATOR . 'general' . DIRECTORY_SEPARATOR . $generalSetting->logo) }}"
                        alt="{{ isset($generalSetting->institute) ? $generalSetting->institute : 'Institution Logo' }}">
                </div>
            @endif

            @if (isset($generalSetting->salogan) && $generalSetting->salogan)
                <div class="institution-slogan">{{ $generalSetting->salogan }}</div>
            @endif

            <div class="institution-name">
                {{ isset($generalSetting->institute) ? $generalSetting->institute : 'Institution Name' }}
            </div>

            <div class="institution-contact">
                @if (isset($generalSetting->address) && $generalSetting->address)
                    <span class="contact-item">
                        <i class="ace-icon fa fa-building"></i> {{ $generalSetting->address }}
                    </span>
                @endif

                @if (isset($generalSetting->phone) && $generalSetting->phone)
                    <span class="contact-item">
                        <i class="ace-icon fa fa-phone-square"></i> {{ $generalSetting->phone }}
                    </span>
                    @if (isset($generalSetting->mobile) && $generalSetting->mobile)
                        <span class="contact-item">
                            <i class="ace-icon fa fa-mobile-alt"></i> {{ $generalSetting->mobile }}
                        </span>
                    @endif
                @endif

                @if (isset($generalSetting->email) && $generalSetting->email)
                    <span class="contact-item">
                        <i class="fa fa-envelope-open"></i> {{ $generalSetting->email }}
                    </span>
                @endif

                @if (isset($generalSetting->website) && $generalSetting->website)
                    <span class="contact-item">
                        <i class="fa fa-link"></i> {{ $generalSetting->website }}
                    </span>
                @endif

                @if (isset($generalSetting->fax) && $generalSetting->fax)
                    <span class="contact-item">
                        <i class="fa fa-fax"></i> {{ $generalSetting->fax }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Receipt Header -->
        <div class="header">
            <div style="flex: 2;">
                <p style="font-size: 13px; margin: 3px 0; font-weight: bold;">ONLINE PAYMENT RECEIPT</p>
            </div>
            <div style="flex: 2; text-align: right;">
                <p style="margin: 2px 0;"><strong>Receipt #</strong> {{ $data['payment']->invoice_id }}</p>
                <p style="margin: 2px 0;"><strong>Date:</strong>
                    {{ \Carbon\Carbon::parse($data['payment']->date)->format('d M Y') }}</p>
                <div style="margin-top: 5px;">
                    <span class="status-badge status-{{ strtolower($data['payment']->payment_status) }}">
                        {{ strtoupper($data['payment']->payment_status) }}
                    </span>
                    <span
                        class="verification-status status-{{ $data['payment']->status == 1 ? 'verified' : 'pending' }}">
                        {{ $data['payment']->status == 1 ? 'VERIFIED' : 'PENDING VERIFICATION' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="payment-details no-break">
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Student Name</div>
                    <div>{{ $data['student']->first_name }} {{ $data['student']->middle_name }}
                        {{ $data['student']->last_name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Registration No.</div>
                    <div>{{ $data['student']->reg_no }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div>{{ $data['student']->email }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Mobile</div>
                    <div>{{ $data['student']->mobile_1 }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Payment Method</div>
                    <div>{{ $data['payment']->payment_gateway }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Transaction ID</div>
                    <div>{{ $data['payment']->ref_no }}</div>
                </div>
            </div>
        </div>
        <!-- Payment Status Notice -->
        <div style="background-color: #f8f9fa; padding: 8px; border-radius: 3px; margin-bottom: 10px; font-size: 11px;"
            class="no-break">
            <h4 style="margin: 0 0 5px 0; font-size: 12px;">PAYMENT STATUS</h4>
            @if ($data['payment']->status == 1)
                <p style="margin: 3px 0; color: #155724;">
                    <i class="fa fa-check-circle"></i> This payment has been <strong>verified</strong> and
                    processed successfully.
                </p>
            @else
                <p style="margin: 3px 0; color: #856404;">
                    <i class="fa fa-clock"></i> This payment is <strong>pending verification</strong>. Please
                    allow 24-48 hours for processing.
                </p>
            @endif
        </div>

        <!-- Amount Breakdown -->
        <table class="no-break">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount ({{ config('app.currency', '-') }})</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Online Payment via {{ $data['payment']->payment_gateway }}</td>
                    <td style="text-align: right;">{{ number_format($data['payment']->amount, 2) }}</td>
                </tr>
                @if ($data['payment']->note)
                    <tr>
                        <td colspan="2"><strong>Note:</strong> {{ $data['payment']->note }}</td>
                    </tr>
                @endif
                <tr style="background-color: #f0f7ff;">
                    <td style="font-weight: bold;">TOTAL PAID</td>
                    <td style="text-align: right; font-weight: bold; color: #1a73e8;">
                        {{ number_format($data['payment']->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Gateway Info -->
        <div style="background-color: #f8f9fa; padding: 8px; border-radius: 3px; margin-bottom: 10px; font-size: 11px;"
            class="no-break">
            <h4 style="margin: 0 0 5px 0; font-size: 12px;">PAYMENT DETAILS</h4>
            <p style="margin: 3px 0;"><strong>Gateway:</strong> {{ $data['payment']->payment_gateway }}</p>
            <p style="margin: 3px 0;"><strong>Reference No:</strong> {{ $data['payment']->ref_no }}</p>
            <p style="margin: 3px 0;"><strong>Date:</strong>
                {{ \Carbon\Carbon::parse($data['payment']->date)->format('d M Y H:i') }}</p>
            @if ($data['payment']->ref_text)
                @php $refData = json_decode($data['payment']->ref_text, true); @endphp
                @if (isset($refData['transaction_id']))
                    <p style="margin: 3px 0;"><strong>Gateway TXN ID:</strong> {{ $refData['transaction_id'] }}</p>
                @endif
            @endif
        </div>

        <!-- Footer -->
        <div class="footer no-break">
            <p>This is an official receipt. Please keep it for your records.</p>
            <p>Thank you for your payment!</p>
            <p style="margin-top: 8px; font-style: italic;">Printed on:
                {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
            @if (isset($generalSetting->website) && $generalSetting->website)
                <p>Visit us at: {{ $generalSetting->website }}</p>
            @endif
        </div>

        <!-- Print Registration Form Link -->
        <div style="margin-top: 15px; text-align: center; border-top: 1px solid #ddd; padding-top: 10px;">
            <p style="margin: 0; font-size: 10px; color: #666;">
                <a href="{{ route('online-registration.print', encrypt($data['student']->id)) }}" 
                   style="color: #2a6496; text-decoration: none; font-weight: bold;">
                   👉 Click here to print your Student Registration Form
                </a>
            </p>
        </div>
    </div>

    <!-- Browser Actions (Hidden on Print) -->
    <div style="margin-top: 20px; text-align: center; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; display: none; print-hidden;" id="browserActions">
        <h5 style="margin-top: 0;">Next Steps:</h5>
        <a href="{{ route('online-registration.print', encrypt($data['student']->id)) }}" 
           class="btn btn-primary" 
           style="display: inline-block; padding: 8px 15px; background: #2a6496; color: white; text-decoration: none; border-radius: 4px; margin: 5px;">
           📄 Print Student Registration Form
        </a>
        <a href="{{ url('/online-registration') }}" 
           class="btn btn-secondary" 
           style="display: inline-block; padding: 8px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin: 5px;">
           🏠 Back to Home
        </a>
    </div>

    <style>
        @media print {
            #browserActions {
                display: none !important;
            }
        }
    </style>

    <script>
        // Show browser actions if NOT printing
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('browserActions').style.display = 'block';
        });

        // Auto-print when the page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
</body>

</html>
