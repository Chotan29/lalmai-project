<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Paid but did not get your form? | Lalmai Govt. College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background:#f4f7fb; font-family: Arial, Helvetica, sans-serif; }
        .wrap { max-width: 640px; margin: 40px auto; padding: 0 15px; }
        .card-box { background:#fff; border:1px solid #e3e8ef; border-radius:12px; padding:28px; }
        .head { text-align:center; margin-bottom:22px; }
        .head .ic { width:60px; height:60px; border-radius:50%; background:#eef4fd; color:#1f5aa6;
                    display:flex; align-items:center; justify-content:center; font-size:26px; margin:0 auto 12px; }
        .head h4 { font-weight:700; color:#1f2e44; margin:0; }
        .head p { color:#6c757d; font-size:14px; margin-top:8px; }
        .note { background:#f8fafc; border:1px solid #e6ebf2; border-radius:8px; padding:12px 14px;
                font-size:12.5px; color:#5c6470; margin-top:16px; }
        .result { display:none; margin-top:18px; border-radius:8px; padding:16px; font-size:14px; }
        .result.ok { background:#e8f6ed; border:1px solid #b6dfc6; color:#14603a; }
        .result.bad { background:#fdecea; border:1px solid #f3c2bd; color:#8f2018; }
        .result a.btn { margin-top:10px; margin-right:8px; }
        .sep { text-align:center; color:#98a1ad; font-size:12px; margin:14px 0; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card-box">
        <div class="head">
            <div class="ic"><i class="fa fa-file-invoice"></i></div>
            <h4>Paid but did not get your form?</h4>
            <p>
                If your payment was successful but the registration form or receipt did not appear,
                enter your details below. We will check with SSLCommerz and complete your registration.
                <b>Do not pay again.</b>
            </p>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">E-mail used on the application form</label>
            <input type="email" id="email" class="form-control form-control-lg" placeholder="your@email.com">
        </div>

        <div class="sep">— or —</div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Transaction ID</label>
            <input type="text" id="tranId" class="form-control" placeholder="REG-xxxxxxxx-xxxxxxxxxx">
            <div class="form-text">You can find this in the payment SMS or e-mail from SSLCommerz.</div>
        </div>

        <div class="d-grid">
            <button class="btn btn-primary btn-lg" id="goBtn" type="button">
                <i class="fa fa-magnifying-glass me-1"></i> Check &amp; Complete My Registration
            </button>
        </div>

        <div class="result" id="result"></div>

        <div class="note">
            <b>Nothing is charged here.</b> This page only checks a payment you already made.
            Your registration is completed only when SSLCommerz confirms the payment.
            If it still does not work, contact the college office with your transaction ID.
        </div>

        <div style="text-align:center; margin-top:18px;">
            <a href="{{ route('online-registration.registration') }}" style="font-size:13px;">&larr; Back to registration form</a>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    function show(cls, html) {
        $('#result').removeClass('ok bad').addClass(cls).html(html).show();
    }

    $('#goBtn').click(function () {
        var email = $.trim($('#email').val());
        var tranId = $.trim($('#tranId').val());

        if (!email && !tranId) {
            show('bad', 'Please enter your e-mail or transaction ID.');
            return;
        }

        var $btn = $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Checking with SSLCommerz…');
        show('ok', 'Please wait, checking your payment…');

        $.post('{{ route('registration-self-recovery.recover') }}', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            email: email,
            tran_id: tranId
        }, function (res) {
            $btn.prop('disabled', false).html('<i class="fa fa-magnifying-glass me-1"></i> Check &amp; Complete My Registration');

            if (res.error) { show('bad', res.message); return; }

            var html = '<b>' + res.message + '</b>';
            if (res.form_url) {
                html += '<br><a class="btn btn-success btn-sm" target="_blank" href="' + res.form_url + '">'
                     + '<i class="fa fa-print"></i> Print Registration Form</a>';
            }
            if (res.receipt_url) {
                html += '<a class="btn btn-outline-success btn-sm" target="_blank" href="' + res.receipt_url + '">'
                     + '<i class="fa fa-receipt"></i> Print Payment Receipt</a>';
            }
            show('ok', html);
        }).fail(function () {
            $btn.prop('disabled', false).html('<i class="fa fa-magnifying-glass me-1"></i> Check &amp; Complete My Registration');
            show('bad', 'Something went wrong. Please try again in a moment.');
        });
    });

    $('#email, #tranId').keypress(function (e) { if (e.which === 13) { $('#goBtn').click(); } });
</script>
</body>
</html>
