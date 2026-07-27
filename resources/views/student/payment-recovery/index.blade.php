@extends('layouts.master')

@section('css')
    <style>
        .rec-note { background:#fff8e5; border:1px solid #f0d9a0; border-radius:4px; padding:12px 15px; margin-bottom:18px; font-size:13px; }
        .rec-note b { color:#8a6100; }
        .rec-result { margin-top:12px; padding:12px 15px; border-radius:4px; font-size:13px; display:none; }
        .rec-result.ok { background:#e8f6ed; border:1px solid #b6dfc6; }
        .rec-result.bad { background:#fdecea; border:1px solid #f3c2bd; }
        .rec-result.warn { background:#fff8e5; border:1px solid #f0d9a0; }
        .rec-table td, .rec-table th { vertical-align: middle !important; font-size:13px; }
        .rec-badge { padding:2px 8px; border-radius:10px; font-size:11px; font-weight:bold; }
        .rec-badge.valid { background:#e8f6ed; color:#1c7a43; }
        .rec-badge.invalid { background:#fdecea; color:#a3271f; }
        .rec-badge.unknown { background:#eee; color:#666; }
        .rec-ref { font-family: monospace; font-size:11px; color:#777; }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        Registration Payment Recovery
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Complete a paid but unfinished registration
                        </small>
                    </h1>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        @include('includes.flash_messages')

                        <div class="rec-note">
                            <b>What this page does:</b> if a student paid at SSLCommerz but the registration form
                            and receipt never appeared, the payment can be verified here and the registration
                            completed. A registration is created <b>only</b> when SSLCommerz itself confirms the
                            payment is VALID, and never twice for the same transaction.
                        </div>

                        {{-- Lookup by transaction id --}}
                        <div class="widget-box">
                            <div class="widget-header"><h4 class="widget-title">Check a Transaction</h4></div>
                            <div class="widget-body">
                                <div class="widget-main">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <input type="text" id="tranIdInput" class="form-control"
                                                       placeholder="Enter SSLCommerz Transaction ID (tran_id)">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-info btn-sm" type="button" id="verifyBtn">
                                                        <i class="ace-icon fa fa-search"></i> Verify
                                                    </button>
                                                    <button class="btn btn-success btn-sm" type="button" id="completeBtn">
                                                        <i class="ace-icon fa fa-check"></i> Complete Registration
                                                    </button>
                                                </span>
                                            </div>
                                            <div style="font-size:12px;color:#888;margin-top:6px;">
                                                Find the transaction ID in the SSLCommerz dashboard or in the student's payment SMS/email.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rec-result" id="verifyResult"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Pending payloads --}}
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="widget-title">
                                    Unfinished Registrations on Server
                                    <span class="badge badge-warning">{{ count($data['rows']) }}</span>
                                </h4>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main">
                                    @if(count($data['rows']) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped rec-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:40px;">#</th>
                                                        <th>Student</th>
                                                        <th style="width:90px;">Type</th>
                                                        <th style="width:90px;">Amount</th>
                                                        <th style="width:150px;">Started At</th>
                                                        <th style="width:320px;">Transaction ID &amp; Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($data['rows'] as $i => $row)
                                                    <tr data-ref="{{ $row->ref }}">
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>
                                                            <b>{{ $row->name }}</b><br>
                                                            <span style="font-size:12px;color:#777;">
                                                                {{ $row->email }}{{ $row->mobile ? ' · '.$row->mobile : '' }}
                                                            </span><br>
                                                            <span class="rec-ref">{{ $row->ref }}</span>
                                                        </td>
                                                        <td>{{ ucfirst($row->student_type) }}</td>
                                                        <td>{{ $row->amount }}</td>
                                                        <td>{{ $row->initiated_at }}</td>
                                                        <td>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control input-sm row-tran"
                                                                       placeholder="tran_id from SSLCommerz">
                                                                <span class="input-group-btn">
                                                                    <button class="btn btn-xs btn-info row-verify" type="button">Verify</button>
                                                                    <button class="btn btn-xs btn-success row-complete" type="button">Complete</button>
                                                                </span>
                                                            </div>
                                                            <div class="rec-result row-result"></div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-success" style="margin-bottom:0;">
                                            <i class="ace-icon fa fa-check"></i>
                                            No unfinished registration payments are waiting on the server.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    var VERIFY_URL   = '{{ route('registration-payment-recovery.verify') }}';
    var COMPLETE_URL = '{{ route('registration-payment-recovery.complete') }}';
    var TOKEN        = '{{ csrf_token() }}';

    function badge(g) {
        if (!g || !g.found) return '<span class="rec-badge unknown">NOT FOUND</span>';
        return g.valid
            ? '<span class="rec-badge valid">' + (g.status || 'VALID') + '</span>'
            : '<span class="rec-badge invalid">' + (g.status || 'NOT VALID') + '</span>';
    }

    function renderInfo(d) {
        var g = d.gateway || {};
        var html = 'Gateway status: ' + badge(g);
        if (g.amount) { html += ' &nbsp;·&nbsp; Amount: <b>' + g.amount + ' ' + (g.currency || '') + '</b>'; }
        if (g.tran_date) { html += ' &nbsp;·&nbsp; Date: ' + g.tran_date; }
        if (g.value_a) { html += '<br>Reference: <span class="rec-ref">' + g.value_a + '</span>'; }
        if (g.error) { html += '<br><span style="color:#a3271f;">' + g.error + '</span>'; }

        if (d.already_done) {
            html += '<br><b>Already completed.</b> <a href="' + d.existing_receipt + '" target="_blank">Open receipt</a>';
        } else if (g.valid && d.has_data) {
            html += '<br><b>Ready to recover</b> — registration data found on the server.'
                 + '<br><button type="button" class="btn btn-success btn-sm inline-complete" style="margin-top:8px;"'
                 + ' data-tran="' + (d.tran_id || '') + '" data-ref="' + (g.value_a || '') + '">'
                 + 'Complete Registration Now</button>';
        } else if (g.valid && !d.has_data) {
            html += '<br><b>Payment is valid, but the saved form data is gone.</b> Register this student manually and treat the fee as already paid.';
        }
        return html;
    }

    function show(el, cls, html) {
        el.removeClass('ok bad warn').addClass(cls).html(html).show();
    }

    function verify(tranId, ref, resultEl) {
        if (!tranId && !ref) { show(resultEl, 'bad', 'Enter a transaction ID first.'); return; }
        show(resultEl, 'warn', 'Checking with SSLCommerz…');
        $.post(VERIFY_URL, { _token: TOKEN, tran_id: tranId, ref: ref }, function (res) {
            if (res.error) { show(resultEl, 'bad', res.message); return; }
            var d = res.data;
            var cls = (d.gateway && d.gateway.valid) ? 'ok' : 'bad';
            show(resultEl, cls, renderInfo(d));
        }).fail(function () { show(resultEl, 'bad', 'Verification request failed. Please try again.'); });
    }

    function complete(tranId, ref, resultEl) {
        if (!tranId) { show(resultEl, 'bad', 'Enter the SSLCommerz transaction ID to complete this registration.'); return; }
        if (!confirm('Complete this registration for transaction ' + tranId + '?')) { return; }
        show(resultEl, 'warn', 'Verifying payment and creating registration…');
        $.post(COMPLETE_URL, { _token: TOKEN, tran_id: tranId, ref: ref }, function (res) {
            if (res.error) { show(resultEl, res.manual_required ? 'warn' : 'bad', res.message); return; }
            var html = res.message;
            if (res.receipt_url) { html += '<br><a href="' + res.receipt_url + '" target="_blank">Open receipt</a>'; }
            if (res.form_url) { html += ' &nbsp;|&nbsp; <a href="' + res.form_url + '" target="_blank">Open registration form</a>'; }
            show(resultEl, 'ok', html);
        }).fail(function () { show(resultEl, 'bad', 'Completion request failed. Please try again.'); });
    }

    $('#verifyBtn').click(function () {
        verify($.trim($('#tranIdInput').val()), '', $('#verifyResult'));
    });

    /* Type a transaction id and complete that one registration directly. */
    $('#completeBtn').click(function () {
        complete($.trim($('#tranIdInput').val()), '', $('#verifyResult'));
    });

    $('#tranIdInput').keypress(function (e) {
        if (e.which === 13) { e.preventDefault(); $('#verifyBtn').click(); }
    });

    /* Complete button rendered inside a verify result. */
    $(document).on('click', '.inline-complete', function () {
        complete($(this).data('tran'), $(this).data('ref'), $('#verifyResult'));
    });

    $('.row-verify').click(function () {
        var tr = $(this).closest('tr');
        verify($.trim(tr.find('.row-tran').val()), tr.data('ref'), tr.find('.row-result'));
    });

    $('.row-complete').click(function () {
        var tr = $(this).closest('tr');
        complete($.trim(tr.find('.row-tran').val()), tr.data('ref'), tr.find('.row-result'));
    });
</script>
@endsection
