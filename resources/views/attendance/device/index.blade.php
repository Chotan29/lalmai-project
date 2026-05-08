@extends('layouts.master')

@section('css')
    <style>
        :root {
            --primary: #346da5;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --light: #f8fafc;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, .1), 0 2px 4px -1px rgba(0, 0, 0, .06)
        }

        .container-fluid {
            padding: 14px
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px
        }

        .header h1 {
            margin: 0;
            color: var(--dark);
            font-weight: 800;
            font-size: 22px
        }

        .header .sub {
            color: var(--secondary);
            font-size: 13px
        }

        .tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 10px
        }

        .tab {
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 999px;
            padding: 8px 12px;
            font-weight: 700;
            cursor: pointer
        }

        .tab.active {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary)
        }

        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 14px
        }

        .card .card-hd {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border)
        }

        .card .card-bd {
            padding: 12px
        }

        .input,
        .select,
        .textarea {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 10px;
            background: #fff
        }

        .select {
            min-width: 160px
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 10px;
            padding: 8px 12px;
            cursor: pointer;
            border: 1px solid var(--border);
            background: #fff
        }

        .btn:hover {
            background: #f1f5f9
        }

        .btn[disabled] {
            opacity: .6;
            cursor: not-allowed
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff
        }

        .btn-primary:hover {
            background: #264f7c
        }

        .btn-danger {
            background: var(--danger);
            border-color: var(--danger);
            color: #fff
        }

        .badge {
            display: inline-block;
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 12px;
            font-weight: 700;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid var(--border)
        }

        .table {
            width: 100%;
            border-collapse: collapse
        }

        .table th,
        .table td {
            border-bottom: 1px solid var(--border);
            padding: 8px 10px;
            text-align: left;
            font-size: 14px
        }

        .table th {
            background: #f8fafc;
            font-weight: 700;
            color: #0f172a
        }

        .empty {
            padding: 20px;
            text-align: center;
            color: #64748b;
            border: 1px dashed var(--border);
            border-radius: 12px;
            background: #fff
        }

        .kbd {
            display: inline-block;
            border: 1px solid #cbd5e1;
            border-bottom-width: 3px;
            padding: 2px 6px;
            border-radius: 6px;
            background: #f8fafc;
            font-family: ui-monospace, Consolas, Menlo, monospace
        }

        .flex {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap
        }

        .mt-8 {
            margin-top: 8px
        }

        .mb-8 {
            margin-bottom: 8px
        }

        .mr-8 {
            margin-right: 8px
        }

        .w-100 {
            width: 100%
        }

        .grid {
            display: grid;
            gap: 10px
        }

        .grid-2 {
            grid-template-columns: 1fr 1fr
        }

        @media (max-width:900px) {
            .grid-2 {
                grid-template-columns: 1fr
            }
        }

        .small {
            font-size: 12px;
            color: #64748b
        }

        .muted {
            color: #64748b
        }

        ul.chklist {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0
        }

        ul.chklist li {
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 8px 10px;
            display: flex;
            gap: 8px;
            align-items: center;
            background: #fff
        }

        pre.gray {
            background: #0b1220;
            color: #cbd5e1;
            border-radius: 10px;
            padding: 10px;
            overflow: auto
        }

        .progress-wrap {
            display: none;
            align-items: center;
            gap: 10px;
            margin-top: 8px
        }

        .progress {
            position: relative;
            width: 100%;
            height: 10px;
            background: #eef2ff;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid var(--border)
        }

        .progress-bar {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #6ea8fe, #346da5);
            transition: width .25s ease
        }

        .progress-text {
            min-width: 90px;
            text-align: right;
            font-size: 12px;
            color: #334155
        }
    </style>
@endsection

@section('content')
    @php $today = now()->toDateString(); @endphp
    <div class="container-fluid">
        <div class="header">
            <div>
                <h1>Biometric Devices — Integration</h1>
                <div class="sub">Manage device sync without changing your manual/scan pages. Today:
                    <b>{{ $today }}</b>
                </div>
            </div>
            <div class="flex">
                <a class="btn" href="{{ route('attendance.live.students') }}"><i class="fas fa-user-graduate"></i>
                    Students</a>
                <a class="btn" href="{{ route('attendance.live.staff') }}"><i class="fas fa-users"></i> Staff</a>
                <a class="btn" href="{{ route('attendance.scan') }}"><i class="fas fa-qrcode"></i> Scanner</a>
            </div>
        </div>

        <div class="tabs" id="tabs">
            <div class="tab active" data-tab="devices"><i class="fas fa-microchip"></i> Devices</div>
            <div class="tab" data-tab="push"><i class="fas fa-user-plus"></i> Push Person</div>
            <div class="tab" data-tab="batch"><i class="fas fa-layer-group"></i> Batch Allocations</div>
            <div class="tab" data-tab="batchstatus"><i class="fas fa-people-arrows"></i> Batch Update</div>
            <div class="tab" data-tab="sync"><i class="fas fa-sync"></i> Sync Logs</div>
        </div>

        {{-- DEVICES --}}
        <div class="card tabpane" id="tab-devices">
            <div class="card-hd">
                <div><b>Devices</b> <span class="small">(from server)</span></div>
                <div class="flex">
                    <button class="btn" id="btn-refresh-devices"><i class="fas fa-sync"></i> Refresh</button>
                </div>
            </div>
            <div class="card-bd">
                <div id="devices-wrap">
                    <div class="empty">Click <b>Refresh</b> to load devices.</div>
                </div>
            </div>
        </div>

        {{-- PUSH PERSON --}}
        <div class="card tabpane" id="tab-push" style="display:none">
            <div class="card-hd">
                <div><b>Push Person</b> <span class="small">(create/update on server & optional allocate)</span></div>
            </div>
            <div class="card-bd">
                <div class="grid grid-2">
                    <div>
                        <div class="mb-8">
                            <label class="mr-8">Type</label>
                            <label class="mr-8"><input type="radio" name="pp-type" value="student" checked>
                                Student</label>
                            <label><input type="radio" name="pp-type" value="staff"> Staff</label>
                        </div>

                        <div class="flex mb-8">
                            <input type="text" id="pp-search" class="input w-100"
                                placeholder="Search by name / reg no (local)">
                            <button class="btn" id="pp-btn-search"><i class="fas fa-search"></i> Search</button>
                        </div>

                        <div id="pp-results" class="empty">Search to list people…</div>
                    </div>

                    <div>
                        <div class="mb-8"><b>Selected</b> <span class="small muted">(from left)</span></div>
                        <div class="grid">
                            <input type="hidden" id="pp-id">
                            <input type="hidden" id="pp-type-hidden" value="student">
                            <div class="flex">
                                <div class="badge">Name</div>
                                <div id="pp-name" class="muted">—</div>
                            </div>
                            <div class="flex">
                                <div class="badge">Reg No</div>
                                <div id="pp-code" class="muted">—</div>
                            </div>

                            <div class="flex">
                                <label class="mr-8"><input type="checkbox" id="pp-use-rfid" checked> Use Reg No as
                                    RFID</label>
                                <input type="text" id="pp-rfid" class="input" placeholder="(optional RFID)">
                            </div>

                            <div class="mt-8"><b>Allocate to devices</b> <span class="small muted">(optional)</span></div>
                            <div class="small muted">Select from loaded devices (see <b>Devices</b> tab).</div>
                            <ul id="pp-devices" class="chklist mt-8"></ul>

                            <div class="flex mt-8">
                                <label><input type="checkbox" id="pp-allocate" checked> Allocate after push</label>
                            </div>

                            <div class="flex mt-8">
                                <button class="btn-primary" id="pp-submit"><i class="fas fa-paper-plane"></i> Push (+
                                    allocate)</button>
                                <button class="btn" id="pp-clear"><i class="fas fa-eraser"></i> Clear</button>
                            </div>

                            <div id="pp-log" class="small muted mt-8"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BATCH ALLOCATIONS (manual) --}}
        <div class="card tabpane" id="tab-batch" style="display:none">
            <div class="card-hd">
                <div><b>Batch Allocations</b> <span class="small">(server-side)</span></div>
            </div>
            <div class="card-bd">
                <div class="grid grid-2">
                    <div>
                        <div class="mb-8">
                            <label class="mr-8">Action</label>
                            <label class="mr-8"><input type="radio" name="ba-action" value="allocate" checked>
                                Allocate</label>
                            <label><input type="radio" name="ba-action" value="revoke"> Revoke</label>
                        </div>

                        <div class="mb-8">
                            <label><b>Person identifiers</b> <span class="small muted">(CSV — use
                                    <code>reg_no</code>)</span></label>
                            <textarea id="ba-persons" class="textarea w-100" rows="4" placeholder="EX: 2020-IT-001, 2020-IT-002, STF-7"></textarea>
                        </div>

                        <div>
                            <label><b>Device IDs</b> <span class="small muted">(CSV)</span></label>
                            <input id="ba-devices" class="input w-100" placeholder="EX: 50065, 50066">
                        </div>

                        <div class="flex mt-8">
                            <button class="btn-primary" id="ba-submit"><i class="fas fa-share-square"></i> Submit
                                Batch</button>
                            <button class="btn" id="ba-clear"><i class="fas fa-eraser"></i> Clear</button>
                        </div>
                    </div>
                    <div>
                        <div class="mb-8"><b>Tips</b></div>
                        <ul class="small">
                            <li>Identifiers should match what was pushed to the server (normally your <b>reg_no</b>).</li>
                            <li>Device IDs are the numeric/short IDs shown under the <b>Devices</b> tab.</li>
                        </ul>
                        <div class="mt-8">
                            <b>Last Result</b>
                            <pre id="ba-result" class="gray small">No batch run yet.</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BATCH UPDATE BY STATUS (QUEUED) --}}
        <div class="card tabpane" id="tab-batchstatus" style="display:none">
            <div class="card-hd">
                <div><b>Batch Update</b> <span class="small">(Active → create & allocate · Inactive → revoke-all)</span>
                </div>
            </div>
            <div class="card-bd">
                <div class="grid grid-2">
                    <div>
                        <div class="small"><b>Who</b></div>
                        <div class="flex">
                            <label><input type="radio" name="bu-type" value="student" checked> Students</label>
                            <label><input type="radio" name="bu-type" value="staff"> Staff</label>
                            <label><input type="radio" name="bu-type" value="both"> Both</label>
                        </div>

                        <div class="small" style="margin-top:8px"><b>Options</b></div>
                        <div class="flex">
                            <label><input type="checkbox" id="bu-photos"> Include photos</label>
                            <label><input type="checkbox" id="bu-rfid"> Use Reg No as RFID</label>
                        </div>

                        <div class="small" style="margin-top:8px"><b>Allocate to devices</b> (checked devices)</div>
                        <ul id="bu-devices" class="flex" style="flex-wrap:wrap"></ul>

                        <div class="flex" style="margin-top:10px">
                            <button class="btn-primary" id="bu-run"><i class="fas fa-people-arrows"></i> Run Batch
                                Update</button>
                        </div>

                        <div class="progress-wrap" id="bu-progress-wrap">
                            <div class="progress">
                                <div class="progress-bar" id="bu-progress-bar"></div>
                            </div>
                            <div class="progress-text" id="bu-progress-text">0%</div>
                        </div>
                    </div>

                    <div>
                        <div class="small"><b>Result</b></div>
                        <pre id="bu-result" class="small"
                            style="background:#0b1220;color:#cbd5e1;border-radius:10px;padding:10px;min-height:160px">No run yet.</pre>
                    </div>
                </div>
            </div>
        </div>

        {{-- SYNC --}}
        <div class="card tabpane" id="tab-sync" style="display:none">
            <div class="card-hd">
                <div><b>Sync Logs</b> <span class="small">(pull punches → merge into <code>attendances</code>)</span>
                </div>
                <div class="flex">
                    <button class="btn" id="btn-sync-status"><i class="fas fa-info-circle"></i> Status</button>
                </div>
            </div>
            <div class="card-bd">
                <div class="grid grid-2">
                    <div>
                        <div class="mb-8"><b>Manual Run</b> <span class="small muted">leave blank = use cursor</span>
                        </div>
                        <div class="grid">
                            <div class="flex">
                                <div style="min-width:100px">Start</div>
                                <input id="sync-start" class="input" placeholder="YYYY-MM-DD HH:mm:ss">
                            </div>
                            <div class="flex">
                                <div style="min-width:100px">End</div>
                                <input id="sync-end" class="input" placeholder="YYYY-MM-DD HH:mm:ss">
                            </div>
                            <div class="flex mt-8">
                                <button class="btn-primary" id="btn-sync-run"><i class="fas fa-sync"></i> Sync
                                    Now</button>
                                <button class="btn" id="btn-sync-clear"><i class="fas fa-eraser"></i> Clear</button>
                            </div>

                            <div class="progress-wrap" id="sync-progress-wrap">
                                <div class="progress">
                                    <div class="progress-bar" id="sync-progress-bar"></div>
                                </div>
                                <div class="progress-text" id="sync-progress-text">0%</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="mb-8"><b>Info</b></div>
                        <div id="sync-info" class="small muted">No sync yet.</div>
                        <div class="mt-8">
                            <b>Last Response</b>
                            <pre id="sync-result" class="gray small">—</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function() {
            "use strict";

            /* ROUTES */
            var R_DEVICES = @json(route('attendance.tipsoi.devices.index'));
            var R_PUSH = @json(route('attendance.tipsoi.sdk.push-person'));
            var R_BATCH = @json(route('attendance.tipsoi.allocations.batch'));
            var R_SYNC_STATUS = @json(route('attendance.tipsoi.status'));
            // NEW:
            var R_RUNS_STORE = @json(route('attendance.tipsoi.runs.store'));
            var R_RUNS_SHOW = @json(route('attendance.tipsoi.runs.show', ['run' => ':id']));
            // Legacy kept:
            var R_LIVE_LIST = @json(route('attendance.live.list'));

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            /* ---------- Tabs ---------- */
            $('.tab').on('click', function() {
                $('.tab').removeClass('active');
                $(this).addClass('active');
                var t = $(this).data('tab');
                $('.tabpane').hide();
                $('#tab-' + t).show();
            });

            /* ---------- Simple progress helpers ---------- */
            function startProgress(prefix) {
                var wrap = $('#' + prefix + '-progress-wrap');
                var bar = $('#' + prefix + '-progress-bar');
                var txt = $('#' + prefix + '-progress-text');
                wrap.show();
                bar.css('width', '0%');
                txt.text('0%');
                var p = 0;
                var timer = setInterval(function() {
                    if (p < 90) {
                        p += Math.max(1, Math.round((90 - p) / 10));
                        bar.css('width', p + '%');
                        txt.text(p + '%');
                    }
                }, 200);
                return {
                    timer,
                    bar,
                    txt,
                    wrap
                };
            }

            function endProgress(ok, ctx, finalText) {
                if (!ctx) return;
                clearInterval(ctx.timer);
                ctx.bar.css('width', '100%');
                ctx.txt.text(finalText || '100%');
                setTimeout(function() {
                    ctx.wrap.hide();
                }, ok ? 800 : 1500);
            }

            function failProgress(ctx) {
                if (!ctx) return;
                clearInterval(ctx.timer);
                ctx.bar.css('width', '100%');
                ctx.txt.text('Failed');
                setTimeout(function() {
                    ctx.wrap.hide();
                }, 2000);
            }

            /* ---------- Devices ---------- */
            var DEVICES_CACHE = [];

            function renderDevices(list) {
                if (!list || !list.length) {
                    $('#devices-wrap').html('<div class="empty">No devices returned.</div>');
                    $('#pp-devices').html('');
                    $('#bu-devices').html('<li class="small">Load devices in Devices tab first.</li>');
                    return;
                }
                var rows = '<table class="table"><thead><tr>' +
                    '<th>ID</th><th>Name</th><th>Status</th><th>Last Seen</th><th>Actions</th></tr></thead><tbody>';
                list.forEach(function(d) {
                    rows += '<tr>' +
                        '<td>' + (d.identifier || d.id || '-') + '</td>' +
                        '<td>' + (d.name || '-') + '</td>' +
                        '<td>' + (d.status || d.state || '-') + '</td>' +
                        '<td>' + (d.last_seen || d.last_seen_at || '-') + '</td>' +
                        '<td><span class="badge">Server</span></td>' +
                        '</tr>';
                });
                rows += '</tbody></table>';
                $('#devices-wrap').html(rows);

                // Device checklists
                var opts = list.map(function(d) {
                    var id = (d.identifier || d.id || '').toString();
                    if (!id) return '';
                    return '<li><label><input type="checkbox" class="pp-dev" value="' + id + '"> ' + id + (d
                        .name ? (' · ' + d.name) : '') + '</label></li>';
                }).join('');
                $('#pp-devices').html(opts);

                var bu = list.map(function(d) {
                    var id = (d.identifier || d.id || '').toString();
                    if (!id) return '';
                    return '<li><label><input type="checkbox" class="bu-dev" value="' + id + '"> ' + id + (d
                        .name ? (' · ' + d.name) : '') + '</label></li>';
                }).join('');
                $('#bu-devices').html(bu);
            }

            function loadDevices() {
                $('#btn-refresh-devices').prop('disabled', true);
                $.get(R_DEVICES).always(function() {
                    $('#btn-refresh-devices').prop('disabled', false);
                }).done(function(res) {
                    var list = [];
                    if (res && res.data) {
                        list = Array.isArray(res.data) ? res.data : (res.data.data || []);
                    } else if (Array.isArray(res)) {
                        list = res;
                    }
                    DEVICES_CACHE = list;
                    renderDevices(list);
                }).fail(function() {
                    $('#devices-wrap').html('<div class="empty">Failed to load devices.</div>');
                    $('#pp-devices').html('');
                    $('#bu-devices').html('<li class="small">Failed to load devices.</li>');
                });
            }

            /* ---------- Push Person (unchanged) ---------- */
            var pick = {
                type: 'student',
                id: null,
                name: '',
                code: ''
            };
            $('input[name="pp-type"]').on('change', function() {
                pick.type = this.value;
                $('#pp-type-hidden').val(pick.type);
                $('#pp-results').html('<div class="empty">Search to list ' + pick.type + '…</div>');
                clearPick();
            });

            function clearPick() {
                pick.id = null;
                pick.name = '';
                pick.code = '';
                $('#pp-id').val('');
                $('#pp-name').text('—');
                $('#pp-code').text('—');
                $('#pp-rfid').val('');
                $('#pp-log').text('Cleared.');
                $('#pp-devices input[type="checkbox"]').prop('checked', false);
                $('#pp-allocate').prop('checked', true);
            }
            $('#pp-clear').on('click', clearPick);

            $('#pp-btn-search').on('click', doSearch);
            $('#pp-search').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    doSearch();
                }
            });

            function doSearch() {
                var q = $('#pp-search').val().trim();
                if (!q) return;
                var params = {
                    type: pick.type,
                    q: q,
                    per_page: 20,
                    page: 1,
                    date: @json($today)
                };
                $.get(R_LIVE_LIST, params).done(function(res) {
                    var list = (res && res.data) ? res.data : [];
                    if (!list.length) {
                        $('#pp-results').html('<div class="empty">No matches.</div>');
                        return;
                    }
                    var html =
                        '<table class="table"><thead><tr><th></th><th>Name</th><th>Reg/Code</th><th>Status</th></tr></thead><tbody>';
                    list.forEach(function(it) {
                        html += '<tr>' +
                            '<td><button class="btn btn-sm btn-pick" data-id="' + it.pid +
                            '" data-name="' + (it.name || '') + '" data-code="' + (it.code || '') +
                            '">Pick</button></td>' +
                            '<td>' + (it.name || '-') + '</td>' +
                            '<td>' + (it.code || '-') + '</td>' +
                            '<td>' + (it.last_status_code || '-') + '</td>' +
                            '</tr>';
                    });
                    html += '</tbody></table>';
                    $('#pp-results').html(html);
                }).fail(function() {
                    $('#pp-results').html('<div class="empty">Search failed.</div>');
                });
            }
            $(document).on('click', '.btn-pick', function() {
                pick.id = parseInt($(this).data('id'), 10);
                pick.name = $(this).data('name') || '';
                pick.code = $(this).data('code') || '';
                $('#pp-id').val(pick.id);
                $('#pp-name').text(pick.name || ('#' + pick.id));
                $('#pp-code').text(pick.code || '—');
                $('#pp-type-hidden').val(pick.type);
                if ($('#pp-use-rfid').is(':checked') && pick.code) $('#pp-rfid').val(pick.code);
                else $('#pp-rfid').val('');
                $('#pp-log').text('Selected ' + pick.type + ' #' + pick.id);
            });
            $('#pp-use-rfid').on('change', function() {
                if (this.checked && pick.code) $('#pp-rfid').val(pick.code);
                if (!this.checked) $('#pp-rfid').val('');
            });
            $('#pp-submit').on('click', function() {
                if (!pick.id) {
                    Swal.fire('Select person', 'Please pick a ' + pick.type + ' from search results first.',
                        'info');
                    return;
                }
                var rfid = $('#pp-rfid').val().trim();
                var allocate = $('#pp-allocate').is(':checked');
                var devs = [];
                $('#pp-devices input.pp-dev:checked').each(function() {
                    devs.push($(this).val());
                });

                var payload = {
                    type: pick.type,
                    id: pick.id
                };
                if (rfid) payload.rfid = rfid;
                if (allocate) {
                    payload.allocate = 1;
                    if (devs.length) payload.device_identifier = devs;
                }

                $('#pp-submit').prop('disabled', true);
                $.post(R_PUSH, payload).always(function() {
                        $('#pp-submit').prop('disabled', false);
                    })
                    .done(function(res) {
                        var allocTxt = '';
                        if (res && res.alloc_stats) allocTxt = ' · Allocated ' + (res.alloc_stats.ok || 0) +
                            '/' + (res.alloc_stats.total || 0);
                        $('#pp-log').html(
                            '<span class="badge" style="background:#ecfdf5;color:#065f46;border-color:#a7f3d0">OK</span> Pushed' +
                            allocTxt + '.');
                        Swal.fire('Pushed', 'Person was pushed to server' + (allocate ? ' & allocated.' :
                            '.'), 'success');
                    }).fail(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON
                            .message : 'Failed';
                        $('#pp-log').html(
                            '<span class="badge" style="background:#fef2f2;color:#991b1b;border-color:#fecaca">ERR</span> ' +
                            msg);
                        Swal.fire('Push failed', msg, 'error');
                    });
            });

            /* ---------- Batch Allocations (manual) ---------- */
            function csvToArr(s) {
                return (s || '').split(',').map(function(v) {
                    return v.trim();
                }).filter(Boolean);
            }
            $('#ba-clear').on('click', function() {
                $('#ba-persons').val('');
                $('#ba-devices').val('');
                $('#ba-result').text('Cleared.');
            });
            $('#ba-submit').on('click', function() {
                var action = $('input[name="ba-action"]:checked').val();
                var persons = csvToArr($('#ba-persons').val());
                var devices = csvToArr($('#ba-devices').val());
                if (!persons.length || !devices.length) {
                    Swal.fire('Missing data', 'Provide person identifiers and device IDs (CSV).', 'info');
                    return;
                }
                $('#ba-submit').prop('disabled', true);
                $.post(R_BATCH, {
                        action,
                        person_identifiers: persons,
                        device_ids: devices
                    })
                    .always(function() {
                        $('#ba-submit').prop('disabled', false);
                    })
                    .done(function(res) {
                        $('#ba-result').text(JSON.stringify(res, null, 2));
                        Swal.fire('Batch sent', 'Server accepted the batch request.', 'success');
                    }).fail(function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON
                            .message : 'Failed';
                        $('#ba-result').text('ERROR: ' + msg);
                        Swal.fire('Batch failed', msg, 'error');
                    });
            });

            /* ---------- Batch Update (QUEUED + POLL) ---------- */
            function renderBatchDevices(list) {
                if (!list || !list.length) {
                    $('#bu-devices').html('<li class="small">Load devices in Devices tab first.</li>');
                    return;
                }
                var opts = [];
                list.forEach(function(d) {
                    var id = (d.identifier || d.id || '').toString();
                    if (!id) return;
                    opts.push('<li><label><input type="checkbox" class="bu-dev" value="' + id + '"> ' + id + (d
                        .name ? (' · ' + d.name) : '') + '</label></li>');
                });
                $('#bu-devices').html(opts.join(''));
            }

            $('#bu-run').on('click', function() {
                var who = $('input[name="bu-type"]:checked').val(); // student|staff|both
                var photos = $('#bu-photos').is(':checked') ? 1 : 0;
                var rfid = $('#bu-rfid').is(':checked') ? 1 : 0;
                var devs = [];
                $('#bu-devices input.bu-dev:checked').each(function() {
                    devs.push($(this).val());
                });
                if (!devs.length) {
                    Swal.fire('Select devices', 'Please select at least one device for allocation.', 'info');
                    return;
                }

                $('#bu-run').prop('disabled', true);
                $('#bu-result').text('Queuing…');
                var prog = startProgress('bu');

                $.post(R_RUNS_STORE, {
                    run_type: 'batch_update',
                    who: who,
                    device_identifier: devs,
                    with_photos: photos,
                    use_rfid: rfid
                }).done(function(resp) {
                    if (!resp || !resp.id) {
                        failProgress(prog);
                        $('#bu-run').prop('disabled', false);
                        $('#bu-result').text('ERROR: No run id returned.');
                        Swal.fire('Batch failed', 'No run id returned.', 'error');
                        return;
                    }
                    var runId = resp.id;
                    var pollMs = 1500;

                    function poll() {
                        $.get(R_RUNS_SHOW.replace(':id', runId)).done(function(r) {
                            $('#bu-result').text(JSON.stringify(r, null, 2));
                            if (r.status === 'failed') {
                                failProgress(prog);
                                $('#bu-run').prop('disabled', false);
                                Swal.fire('Batch failed', r.error || 'Unknown error', 'error');
                                return;
                            }
                            var pct = Math.max(0, Math.min(100, r.percent || 0));
                            prog.bar.css('width', pct + '%');
                            prog.txt.text(pct + '%');

                            if (r.status === 'finished') {
                                endProgress(true, prog, '100%');
                                $('#bu-run').prop('disabled', false);
                                var ok = (r.result && r.result.allocated && r.result.allocated
                                    .ok) ? r.result.allocated.ok : 0;
                                var tot = (r.result && r.result.allocated && r.result.allocated
                                    .total) ? r.result.allocated.total : 0;
                                Swal.fire('Batch queued', 'Allocated ' + ok + '/' + tot +
                                    ' · Revoked ' + (r.result?.revoked || 0), 'success');
                            } else {
                                setTimeout(poll, pollMs);
                            }
                        }).fail(function() {
                            setTimeout(poll, pollMs);
                        });
                    }
                    poll();
                }).fail(function(xhr) {
                    failProgress(prog);
                    $('#bu-run').prop('disabled', false);
                    var m = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message :
                        'Failed';
                    $('#bu-result').text('ERROR: ' + m);
                    Swal.fire('Batch failed', m, 'error');
                });
            });

            /* ---------- Sync (QUEUED + POLL) ---------- */
            function statusNow() {
                $('#btn-sync-status').prop('disabled', true);
                $.get(R_SYNC_STATUS).always(function() {
                    $('#btn-sync-status').prop('disabled', false);
                }).done(function(res) {
                    var t = 'Cursor: <b>' + (res.cursor || 'null') + '</b> · Now: ' + (res.now || '-');
                    $('#sync-info').html(t);
                }).fail(function() {
                    $('#sync-info').text('Failed to get status.');
                });
            }
            $('#btn-sync-status').on('click', statusNow);

            $('#btn-sync-clear').on('click', function() {
                $('#sync-start').val('');
                $('#sync-end').val('');
                $('#sync-result').text('—');
            });

            $('#btn-sync-run').on('click', function() {
                var s = $('#sync-start').val().trim();
                var e = $('#sync-end').val().trim();

                $('#btn-sync-run').prop('disabled', true);
                $('#sync-result').text('Queuing sync…');
                var prog = startProgress('sync');

                $.post(R_RUNS_STORE, {
                        type: 'sync_logs',
                        start: s || null,
                        end: e || null
                    })
                    .done(function(resp) {
                        if (!resp || !resp.id) {
                            failProgress(prog);
                            $('#btn-sync-run').prop('disabled', false);
                            $('#sync-result').text('ERROR: No run id returned.');
                            Swal.fire('Sync failed', 'No run id returned.', 'error');
                            return;
                        }
                        var runId = resp.id;

                        var pollMs = 1500;

                        function poll() {
                            $.get(R_RUNS_SHOW.replace(':id', runId)).done(function(r) {
                                $('#sync-result').text(JSON.stringify(r, null, 2));

                                if (r.status === 'failed') {
                                    failProgress(prog);
                                    $('#btn-sync-run').prop('disabled', false);
                                    Swal.fire('Sync failed', r.error || 'Unknown error', 'error');
                                    return;
                                }
                                var pct = Math.max(0, Math.min(100, r.percent || 0));
                                prog.bar.css('width', pct + '%');
                                prog.txt.text(pct + '%');

                                if (r.status === 'finished') {
                                    endProgress(true, prog, '100%');
                                    $('#btn-sync-run').prop('disabled', false);

                                    var R = r.result || {};
                                    var merged = ((R.created || 0) + (R.updated || 0));
                                    Swal.fire('Synced', r.result.synced + ' log(s) merged.', 'success');

                                    statusNow();
                                } else {
                                    setTimeout(poll, pollMs);
                                }
                            }).fail(function() {
                                setTimeout(poll, pollMs);
                            });
                        }
                        poll();
                    }).fail(function(xhr) {
                        failProgress(prog);
                        $('#btn-sync-run').prop('disabled', false);
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON
                            .message : 'Failed';
                        $('#sync-result').text('ERROR: ' + msg);
                        Swal.fire('Sync failed', msg, 'error');
                    });
            });

            $('#sync-start,#sync-end').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    $('#btn-sync-run').click();
                }
            });

            /* init */
            loadDevices();
            statusNow();

        })();
    </script>
@endsection
