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
        .rec-badge.checking { background:#e9f1fb; color:#1a4f8a; }
        .rec-badge.done { background:#eef0ff; color:#3c34a0; }
        .rec-ref { font-family: monospace; font-size:11px; color:#777; }
        .rec-row-msg { font-size:11.5px; margin-top:4px; }
        .rec-toolbar { display:flex; flex-wrap:wrap; gap:8px; align-items:center; margin-bottom:12px; }
        .rec-counts span { margin-right:14px; font-size:12.5px; }
        tr.is-paid { background:#f6fbf8; }
        tr.is-unpaid { opacity:.62; }
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
                            Paid but no registration form / receipt
                        </small>
                    </h1>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        @include('includes.flash_messages')

                        <div class="rec-note">
                            <b>What this page does:</b> a student may pay at SSLCommerz and still not get the
                            registration form or receipt. Press <b>Check All with SSLCommerz</b> below: every
                            unfinished application is verified against the gateway, and the ones that were really
                            paid can be completed with one click.
                            <br>Cancelled or unpaid attempts are removed automatically, so only real payments stay here.
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
                                                Only needed for a payment that is not in the list below.
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
                                    Unfinished Applications
                                    <span class="badge badge-warning">{{ count($data['rows']) }}</span>
                                </h4>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main">
                                    @if(count($data['rows']) > 0)
                                        <div class="rec-toolbar">
                                            <button class="btn btn-primary btn-sm" type="button" id="checkAllBtn">
                                                <i class="ace-icon fa fa-refresh"></i> Check All with SSLCommerz
                                            </button>
                                            <button class="btn btn-success btn-sm" type="button" id="completePaidBtn" disabled>
                                                <i class="ace-icon fa fa-check-circle"></i> Complete All Paid
                                            </button>
                                            <label style="margin:0 0 0 6px; font-weight:normal; font-size:12.5px;">
                                                <input type="checkbox" id="onlyPaid"> show paid only
                                            </label>
                                            <button class="btn btn-white btn-sm" type="button" id="cleanupBtn"
                                                    style="margin-left:auto;" title="Remove every cancelled / unpaid attempt (paid ones are always kept)">
                                                <i class="ace-icon fa fa-trash-o"></i> Remove cancelled &amp; unpaid
                                            </button>
                                        </div>

                                        <div class="rec-counts" id="recCounts" style="display:none;">
                                            <span>Paid: <b id="cPaid">0</b></span>
                                            <span>Not paid: <b id="cUnpaid">0</b></span>
                                            <span>Already completed: <b id="cDone">0</b></span>
                                            <span>Checked: <b id="cChecked">0</b> / {{ count($data['rows']) }}</span>
                                        </div>

                                        <div class="rec-result" id="bulkResult"></div>

                                        <div class="table-responsive" style="margin-top:10px;">
                                            <table class="table table-bordered table-striped rec-table" id="recTable">
                                                <thead>
                                                    <tr>
                                                        <th style="width:40px;">#</th>
                                                        <th>Student</th>
                                                        <th style="width:90px;">Amount</th>
                                                        <th style="width:120px;">Started</th>
                                                        <th style="width:80px;">Keeps</th>
                                                        <th style="width:120px;">Payment</th>
                                                        <th style="width:210px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($data['rows'] as $i => $row)
                                                    @php $v = $row->verified; @endphp
                                                    <tr data-ref="{{ $row->ref }}"
                                                        class="{{ $v ? (!empty($v['paid']) ? 'is-paid' : 'is-unpaid') : '' }}">
                                                        <td>{{ $i + 1 }}</td>
                                                        <td>
                                                            <b>{{ $row->name }}</b>
                                                            @if($row->student_type)
                                                                <span class="rec-badge unknown">{{ ucfirst($row->student_type) }}</span>
                                                            @endif
                                                            <br>
                                                            <span style="font-size:12px;color:#777;">
                                                                {{ $row->email }}{{ $row->mobile ? ' · '.$row->mobile : '' }}
                                                            </span><br>
                                                            <span class="rec-ref">{{ $row->ref }}</span>
                                                        </td>
                                                        <td>&#2547;{{ number_format((float) $row->amount, 0) }}</td>
                                                        <td style="font-size:12px;">{{ \Carbon\Carbon::parse($row->initiated_at)->format('d M Y') }}<br>
                                                            <span style="color:#999;">{{ \Carbon\Carbon::parse($row->initiated_at)->format('h:i A') }}</span>
                                                        </td>
                                                        <td style="font-size:12px;">
                                                            @if($row->days_left > 0)
                                                                {{ $row->days_left }} day{{ $row->days_left > 1 ? 's' : '' }}
                                                            @else
                                                                <span style="color:#a3271f;">expired</span>
                                                            @endif
                                                        </td>
                                                        <td class="pay-cell">
                                                            @if($v)
                                                                @if(!empty($v['already_done']))
                                                                    <span class="rec-badge done">COMPLETED</span>
                                                                @elseif(!empty($v['paid']))
                                                                    <span class="rec-badge valid">PAID</span>
                                                                @else
                                                                    <span class="rec-badge invalid">{{ $v['status'] ?? 'NOT PAID' }}</span>
                                                                @endif
                                                            @else
                                                                <span class="rec-badge unknown">not checked</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-xs btn-info row-check" type="button">Check</button>
                                                            <button class="btn btn-xs btn-success row-complete" type="button">Complete</button>
                                                            <div class="rec-row-msg"></div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-success" style="margin-bottom:0;">
                                            <i class="ace-icon fa fa-check"></i>
                                            No unfinished application is waiting on the server.
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
    var AUTO_URL     = '{{ route('registration-payment-recovery.auto-verify') }}';
    var COMPLETE_URL = '{{ route('registration-payment-recovery.complete') }}';
    var CLEANUP_URL  = '{{ route('registration-payment-recovery.cleanup') }}';
    var TOKEN        = '{{ csrf_token() }}';

    function show(el, cls, html) {
        el.removeClass('ok bad warn').addClass(cls).html(html).show();
    }

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

    function verify(tranId, ref, resultEl) {
        if (!tranId && !ref) { show(resultEl, 'bad', 'Enter a transaction ID first.'); return; }
        show(resultEl, 'warn', 'Checking with SSLCommerz…');
        $.post(VERIFY_URL, { _token: TOKEN, tran_id: tranId, ref: ref }, function (res) {
            if (res.error) { show(resultEl, 'bad', res.message); return; }
            var d = res.data;
            show(resultEl, (d.gateway && d.gateway.valid) ? 'ok' : 'bad', renderInfo(d));
        }).fail(function () { show(resultEl, 'bad', 'Verification request failed. Please try again.'); });
    }

    function complete(tranId, ref, resultEl) {
        if (!tranId && !ref) { show(resultEl, 'bad', 'Transaction reference missing.'); return; }
        show(resultEl, 'warn', 'Verifying payment and creating registration…');
        $.post(COMPLETE_URL, { _token: TOKEN, tran_id: tranId, ref: ref }, function (res) {
            if (res.error) { show(resultEl, res.manual_required ? 'warn' : 'bad', res.message); return; }
            var html = res.message;
            if (res.receipt_url) { html += '<br><a href="' + res.receipt_url + '" target="_blank">Open receipt</a>'; }
            if (res.form_url) { html += ' &nbsp;|&nbsp; <a href="' + res.form_url + '" target="_blank">Open registration form</a>'; }
            show(resultEl, 'ok', html);
        }).fail(function () { show(resultEl, 'bad', 'Completion request failed. Please try again.'); });
    }

    /* ---------- single transaction box ---------- */
    $('#verifyBtn').click(function () { verify($.trim($('#tranIdInput').val()), '', $('#verifyResult')); });
    $('#completeBtn').click(function () { complete($.trim($('#tranIdInput').val()), '', $('#verifyResult')); });
    $('#tranIdInput').keypress(function (e) { if (e.which === 13) { e.preventDefault(); $('#verifyBtn').click(); } });
    $(document).on('click', '.inline-complete', function () {
        complete($(this).data('tran'), $(this).data('ref'), $('#verifyResult'));
    });

    /* ---------- list: auto verification ---------- */
    function setPayCell($tr, data) {
        var html;
        if (data.already_done) { html = '<span class="rec-badge done">COMPLETED</span>'; }
        else if (data.paid)    { html = '<span class="rec-badge valid">PAID</span>'; }
        else                   { html = '<span class="rec-badge invalid">' + (data.status || 'NOT PAID') + '</span>'; }

        if (data.paid && data.amount) { html += '<div style="font-size:11px;color:#777;">&#2547;' + data.amount + '</div>'; }
        $tr.find('.pay-cell').html(html);
        $tr.removeClass('is-paid is-unpaid').addClass(data.paid ? 'is-paid' : 'is-unpaid');
        $tr.data('paid', data.paid ? 1 : 0).data('done', data.already_done ? 1 : 0);

        if (data.already_done && data.receipt_url) {
            $tr.find('.rec-row-msg').html('<a href="' + data.receipt_url + '" target="_blank">Open receipt</a>');
        }
    }

    function updateCounts() {
        var paid = 0, unpaid = 0, done = 0, checked = 0;
        $('#recTable tbody tr').each(function () {
            var $t = $(this);
            if ($t.data('paid') === undefined) { return; }
            checked++;
            if ($t.data('done')) { done++; }
            else if ($t.data('paid')) { paid++; }
            else { unpaid++; }
        });
        $('#cPaid').text(paid); $('#cUnpaid').text(unpaid);
        $('#cDone').text(done); $('#cChecked').text(checked);
        $('#recCounts').show();
        $('#completePaidBtn').prop('disabled', paid === 0);
    }

    function checkRow($tr, force, done) {
        $tr.find('.pay-cell').html('<span class="rec-badge checking">checking…</span>');
        $.post(AUTO_URL, { _token: TOKEN, ref: $tr.data('ref'), force: force ? 1 : 0 }, function (res) {
            if (!res.error) { setPayCell($tr, res.data); }
            else { $tr.find('.pay-cell').html('<span class="rec-badge unknown">error</span>'); }
            updateCounts();
            if (done) { done(); }
        }).fail(function () {
            $tr.find('.pay-cell').html('<span class="rec-badge unknown">error</span>');
            if (done) { done(); }
        });
    }

    $('.row-check').click(function () { checkRow($(this).closest('tr'), true); });

    $('.row-complete').click(function () {
        var $tr = $(this).closest('tr');
        if (!confirm('Complete registration for ' + $tr.find('b').first().text() + '?')) { return; }
        var $msg = $tr.find('.rec-row-msg');
        $msg.html('<span style="color:#1a4f8a;">Working…</span>');
        $.post(COMPLETE_URL, { _token: TOKEN, ref: $tr.data('ref') }, function (res) {
            if (res.error) { $msg.html('<span style="color:#a3271f;">' + res.message + '</span>'); return; }
            var html = '<span style="color:#1c7a43;">Done.</span>';
            if (res.receipt_url) { html += ' <a href="' + res.receipt_url + '" target="_blank">Receipt</a>'; }
            if (res.form_url) { html += ' | <a href="' + res.form_url + '" target="_blank">Form</a>'; }
            $msg.html(html);
            $tr.find('.pay-cell').html('<span class="rec-badge done">COMPLETED</span>');
            $tr.data('done', 1); updateCounts();
        }).fail(function () { $msg.html('<span style="color:#a3271f;">Request failed.</span>'); });
    });

    /* Check every row, one at a time so the gateway is not flooded. */
    $('#checkAllBtn').click(function () {
        var $rows = $('#recTable tbody tr'), i = 0;
        var $btn = $(this).prop('disabled', true);
        show($('#bulkResult'), 'warn', 'Checking ' + $rows.length + ' application(s) with SSLCommerz…');

        (function next() {
            if (i >= $rows.length) {
                $btn.prop('disabled', false);
                show($('#bulkResult'), 'ok', 'Check finished. Rows marked <b>PAID</b> can be completed.');
                return;
            }
            checkRow($rows.eq(i++), false, next);
        })();
    });

    /* Complete every row that the gateway confirmed as paid. */
    $('#completePaidBtn').click(function () {
        var $rows = $('#recTable tbody tr').filter(function () {
            return $(this).data('paid') === 1 && $(this).data('done') !== 1;
        });
        if (!$rows.length) { return; }
        if (!confirm('Complete registration for ' + $rows.length + ' paid application(s)?')) { return; }

        var i = 0, okCount = 0, failCount = 0;
        var $btn = $(this).prop('disabled', true);
        show($('#bulkResult'), 'warn', 'Completing ' + $rows.length + ' registration(s)…');

        (function next() {
            if (i >= $rows.length) {
                $btn.prop('disabled', false);
                show($('#bulkResult'), okCount ? 'ok' : 'bad',
                    'Completed: <b>' + okCount + '</b>, failed: <b>' + failCount + '</b>. Reload the page to refresh the list.');
                return;
            }
            var $tr = $rows.eq(i++);
            var $msg = $tr.find('.rec-row-msg');
            $msg.html('<span style="color:#1a4f8a;">Working…</span>');
            $.post(COMPLETE_URL, { _token: TOKEN, ref: $tr.data('ref') }, function (res) {
                if (res.error) { failCount++; $msg.html('<span style="color:#a3271f;">' + res.message + '</span>'); }
                else {
                    okCount++;
                    var html = '<span style="color:#1c7a43;">Done.</span>';
                    if (res.receipt_url) { html += ' <a href="' + res.receipt_url + '" target="_blank">Receipt</a>'; }
                    if (res.form_url) { html += ' | <a href="' + res.form_url + '" target="_blank">Form</a>'; }
                    $msg.html(html);
                    $tr.find('.pay-cell').html('<span class="rec-badge done">COMPLETED</span>');
                    $tr.data('done', 1);
                }
                updateCounts(); next();
            }).fail(function () { failCount++; $msg.html('<span style="color:#a3271f;">Request failed.</span>'); next(); });
        })();
    });

    $('#onlyPaid').change(function () {
        var on = $(this).is(':checked');
        $('#recTable tbody tr').each(function () {
            var $t = $(this);
            $t.toggle(!on || $t.data('paid') === 1);
        });
    });

    $('#cleanupBtn').click(function () {
        if (!confirm('Remove every cancelled / unpaid attempt? Paid applications are always kept, and attempts started in the last 30 minutes are left alone.')) { return; }
        var $btn = $(this).prop('disabled', true);
        show($('#bulkResult'), 'warn', 'Cleaning up…');
        $.post(CLEANUP_URL, { _token: TOKEN }, function (res) {
            $btn.prop('disabled', false);
            show($('#bulkResult'), res.error ? 'bad' : 'ok', res.message);
        }).fail(function () { $btn.prop('disabled', false); show($('#bulkResult'), 'bad', 'Cleanup failed.'); });
    });

    updateCounts();
</script>
@endsection
