{{-- resources/views/account/fees/dashboard/index.blade.php --}}
@extends('layouts.master')

@section('css')
    <style>
        :root {
            --primary: #346da5;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --success: #10b981;
            --dark: #1e293b;
            --light: #f8fafc;
            --border: #e2e8f0;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, .1), 0 2px 4px -1px rgba(0, 0, 0, .06);
            --transition: all .3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif
        }

        .container-fluid {
            background: var(--primary-light);
            min-height: 100vh;
        }

        /* allow scroll when browser fullscreen */
        .container-fluid:fullscreen {
            overflow-y: auto;
        }

        .container-fluid:-webkit-full-screen {
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding: 0 12px
        }

        .header-title h1 {
            font-weight: 700;
            font-size: 24px;
            color: var(--dark);
            margin-bottom: 6px
        }

        .header-title p {
            color: var(--secondary);
            font-size: 14px
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center
        }

        .breadcrumb-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            margin: 10px 0 6px
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px
        }

        .breadcrumb-link {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 5px 10px;
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px
        }

        .breadcrumb-link:hover {
            background: var(--primary-light)
        }

        .breadcrumb-divider {
            color: #cbd5e1
        }

        .chip {
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer
        }

        .btn-soft {
            border: 1px dashed var(--primary);
            background: var(--primary-light);
            color: var(--primary)
        }

        .btn-soft:hover {
            filter: brightness(.97)
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border: none
        }

        .btn-primary:hover {
            filter: brightness(.95)
        }

        .btn-live {
            border: 1px solid var(--border);
            background: #fff
        }

        .btn-live.active {
            background: green !important;
            border-color: #a7f3d0;
            color: #065f46
        }

        .section {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 18px;
            margin-bottom: 16px
        }

        .section-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px
        }

        .section-head .icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary)
        }

        .section-head .title {
            font-weight: 700;
            color: var(--dark)
        }

        .muted {
            color: #64748b
        }

        .hierarchy-block.collapsed {
            display: none
        }

        .hierarchy-container {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding: 10px 0;
            margin-bottom: 12px
        }

        .hierarchy-level {
            min-width: 220px;
            background: #fff;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            flex-shrink: 0;
            border: 1px solid var(--border)
        }

        .level-title {
            color: var(--dark);
            font-weight: 600;
            padding: 10px;
            border-bottom: 1px solid var(--border);
            background: #f8fafc;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .level-items {
            padding: 10px;
            max-height: 420px;
            overflow-y: auto
        }

        .level-item {
            padding: 6px 10px;
            border-radius: 10px;
            background: #f8fafc;
            transition: var(--transition);
            cursor: pointer;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid transparent
        }

        .level-item:hover {
            background: #f1f5f9;
            transform: translateY(-1px);
            border-color: var(--border)
        }

        .level-item.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary)
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-bottom: 6px
        }

        .datebox {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .datebox input {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 10px
        }

        .quick {
            display: flex;
            gap: 8px
        }

        .kpis {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px
        }

        .kpi {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px;
            box-shadow: var(--card-shadow)
        }

        .kpi .h {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 6px
        }

        .kpi .v {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a
        }

        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 12px
        }

        .chart-grid-2 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 12px
        }

        .chart-grid-3 {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 12px
        }

        .chart-box {
            position: relative;
            height: 300px
        }

        .chart-box.sm {
            height: 240px
        }

        .chart-box>canvas {
            width: 100% !important;
            height: 100% !important
        }

        .tbl {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px
        }

        .tbl thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .02em;
            color: #475569;
            text-align: left;
            padding: 8px 10px
        }

        .tbl tbody tr {
            background: #f8fafc;
            border: 1px solid var(--border)
        }

        .tbl tbody tr td {
            padding: 8px 10px;
            border-top: 1px solid var(--border)
        }

        .tbl tbody tr:first-child td {
            border-top: none
        }

        .badge {
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            border: 1px solid var(--border)
        }

        .badge.ok {
            color: #10b981;
            border-color: #a7f3d0;
            background: #ecfdf5
        }

        @media(max-width:1200px) {
            .kpis {
                grid-template-columns: repeat(2, 1fr)
            }

            .chart-grid,
            .chart-grid-2,
            .chart-grid-3 {
                grid-template-columns: 1fr
            }
        }

        /* Full Page mode (CSS layout) */
        body.fullpage .container-fluid {
            padding: 0 !important
        }

        body.fullpage .header {
            margin: 0 0 12px 0;
            padding: 10px 12px
        }

        body.fullpage .section {
            border-radius: 0
        }

        body.fullpage .chart-box {
            height: 420px
        }

        body.fullpage .chart-box.sm {
            height: 320px
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4" data-currency="{{ $currency }}">
        <div class="header">
            <div class="header-title">
                <h1>Fees Dashboard</h1>
                <p>Filter by hierarchy and date to analyze collections and online payments.</p>
            </div>
            <div class="header-actions">
                <span class="chip" id="now-tz">--:--</span>
                <button class="btn btn-soft" id="btn-hierarchy-toggle" title="Show/Hide Hierarchy">
                    <i class="fas fa-eye"></i> Show Hierarchy
                </button>
                <button class="btn btn-soft" id="btn-fullscreen" title="Toggle Fullscreen">
                    <i class="fas fa-expand"></i> Fullscreen
                </button>
                <button class="btn btn-soft" id="btn-fullpage" title="Full Page View">
                    <i class="fas fa-expand-arrows-alt"></i> Full Page
                </button>
                <button class="btn btn-soft" id="btn-reset" title="Reset filters">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>

        <div id="fee-breadcrumbs" style="display:none;">
            <nav class="breadcrumb-wrap"></nav>
        </div>

        <div class="section">
            <div class="filters">
                <div class="datebox">
                    <label class="muted">From</label>
                    <input type="date" id="from_date" value="{{ $from_date }}">
                </div>
                <div class="datebox">
                    <label class="muted">To</label>
                    <input type="date" id="to_date" value="{{ $to_date }}">
                </div>

                <div class="quick">
                    <button class="btn btn-soft" data-q="today"><i class="far fa-calendar"></i> Today</button>
                    <button class="btn btn-soft" data-q="month"><i class="far fa-calendar-alt"></i> This Month</button>
                    <button class="btn btn-soft" data-q="30"><i class="fas fa-history"></i> Last 30 Days</button>
                    <button class="btn btn-soft" data-q="year"><i class="far fa-calendar-check"></i> This Year</button>
                </div>

                <button class="btn btn-primary" id="apply-filters"><i class="fas fa-sync"></i> Apply</button>
                <button class="btn btn-live" id="toggle-live" title="Auto-refresh every 15s">
                    <i class="fas fa-broadcast-tower"></i> Live: OFF
                </button>
            </div>

            <div id="hierarchy-block" class="hierarchy-block collapsed">
                <div class="section-head" style="margin-top:6px">
                    <div class="icon"><i class="fas fa-sitemap"></i></div>
                    <div>
                        <div class="title">Hierarchy</div>
                        <div class="muted">Department Head → Department → Faculty → Semester → Batch</div>
                    </div>
                </div>

                <div class="hierarchy-container">
                    <div class="hierarchy-level">
                        <div class="level-title"><i class="fas fa-user-tie"></i> Department Heads</div>
                        <div class="level-items" id="heads">
                            @foreach ($department_heads as $id => $name)
                                <div class="level-item" data-level="department_head" data-id="{{ $id }}">
                                    <span>{{ $name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="hierarchy-level" id="lv-departments" style="display:none;">
                        <div class="level-title"><i class="fas fa-university"></i> Departments</div>
                        <div class="level-items" id="departments"></div>
                    </div>

                    <div class="hierarchy-level" id="lv-faculties" style="display:none;">
                        <div class="level-title"><i class="fas fa-graduation-cap"></i> Faculties</div>
                        <div class="level-items" id="faculties"></div>
                    </div>

                    <div class="hierarchy-level" id="lv-semesters" style="display:none;">
                        <div class="level-title"><i class="fas fa-layer-group"></i> Semesters</div>
                        <div class="level-items" id="semesters"></div>
                    </div>

                    <div class="hierarchy-level" id="lv-batches" style="display:none;">
                        <div class="level-title"><i class="fas fa-users"></i> Batches</div>
                        <div class="level-items" id="batches"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="kpis">
                <div class="kpi">
                    <div class="h">Collected (<span class="cur">{{ $currency }}</span>)</div>
                    <div class="v" id="c-collected">0</div>
                </div>
                <div class="kpi">
                    <div class="h">Receipts</div>
                    <div class="v" id="c-receipts">0</div>
                </div>
                <div class="kpi">
                    <div class="h">Students Paid</div>
                    <div class="v" id="c-students">0</div>
                </div>
                <div class="kpi">
                    <div class="h">Avg Receipt</div>
                    <div class="v" id="c-avg">0</div>
                </div>
                <div class="kpi">
                    <div class="h">Online (Paid)</div>
                    <div class="v" id="c-online-paid">0</div>
                </div>
                <div class="kpi">
                    <div class="h">Online (Pending)</div>
                    <div class="v" id="c-online-pending">0</div>
                </div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="section">
                <div class="section-head">
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <div class="title">Collections by Day</div>
                </div>
                <div class="chart-box"><canvas id="chart-byday"></canvas></div>
            </div>
            <div class="section">
                <div class="section-head">
                    <div class="icon"><i class="fas fa-lines-leaning"></i></div>
                    <div class="title">Cumulative Collections</div>
                </div>
                <div class="chart-box sm"><canvas id="chart-cumulative"></canvas></div>
            </div>
            <div class="section">
                <div class="section-head">
                    <div class="icon"><i class="fas fa-money-check-alt"></i></div>
                    <div class="title">Payment Methods</div>
                </div>
                <div class="chart-box sm"><canvas id="chart-pm"></canvas></div>
            </div>
        </div>

        <div class="chart-grid-2">
            <div class="section">
                <div class="section-head">
                    <div class="icon"><i class="fas fa-chart-area"></i></div>
                    <div class="title">Payment Methods by Day</div>
                </div>
                <div class="chart-box"><canvas id="chart-pmbyday"></canvas></div>
            </div>
            <div class="section">
                <div class="section-head">
                    <div class="icon"><i class="fas fa-toggle-on"></i></div>
                    <div class="title">Online vs Offline by Day</div>
                </div>
                <div class="chart-box"><canvas id="chart-modesplit"></canvas></div>
            </div>
        </div>

        <div class="chart-grid-2">
            <div class="section">
                <div class="section-head">
                    <div class="icon"><i class="fas fa-signal"></i></div>
                    <div class="title">Online Payments Timeline</div>
                </div>
                <div class="chart-box"><canvas id="chart-op-timeline"></canvas></div>
            </div>
            <div class="section">
                <div class="section-head">
                    <div class="icon"><i class="fas fa-signal"></i></div>
                    <div class="title">Online Payments Status</div>
                </div>
                <div class="chart-box sm"><canvas id="chart-op"></canvas></div>
            </div>
        </div>

        <div class="chart-grid-2">
            <div class="section">
                <div class="section-head">
                    <div class="icon"><i class="fas fa-layer-group"></i></div>
                    <div class="title">Top Fee Heads (Bar)</div>
                </div>
                <div class="chart-box"><canvas id="chart-heads"></canvas></div>
            </div>
            <div class="section">
                <div class="section-head">
                    <div class="icon"><i class="fas fa-pie-chart"></i></div>
                    <div class="title">Heads Share (Donut)</div>
                </div>
                <div class="chart-box sm"><canvas id="chart-headshare"></canvas></div>
            </div>
        </div>

        <div class="section">
            <div class="section-head">
                <div class="icon"><i class="fas fa-table"></i></div>
                <div class="title">Top Fee Heads (Table)</div>
            </div>
            <div class="table-responsive">
                <table class="tbl" id="topheads-table">
                    <thead>
                        <tr>
                            <th>Head</th>
                            <th>Total ({{ $currency }})</th>
                            <th>Receipts</th>
                            <th>Avg</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="section">
            <div class="section-head">
                <div class="icon"><i class="fas fa-receipt"></i></div>
                <div class="title">Recent Receipts</div>
            </div>
            <div class="table-responsive">
                <table class="tbl" id="recent-table">
                    <thead>
                        <tr>
                            <th>Reg No</th>
                            <th>Student Name</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Paid Amount ({{ $currency }})</th>
                            <th>Discount</th>
                            <th>Fine</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function($) {

            const ENDPOINTS = {
                departments: {
                    url: "{{ route('get-departments') }}",
                    param: "department_head_id",
                    cache: {}
                },
                faculties: {
                    url: "{{ route('get-faculties') }}",
                    param: "department_id",
                    cache: {}
                },
                semesters: {
                    url: "{{ route('get-semesters') }}",
                    param: "faculty_id",
                    cache: {}
                },
                batches: {
                    url: "{{ route('get-batches') }}",
                    param: "semester_id",
                    cache: {}
                },
                summary: {
                    url: "{{ route('fees.summary.chart') }}"
                }
            };

            const currency = (document.querySelector('.container-fluid[data-currency]')?.dataset?.currency || '')
                .toUpperCase();

            const state = {
                department_head_id: {!! json_encode($init_filters['department_head_id'] ?? null) !!},
                department_id: {!! json_encode($init_filters['department_id'] ?? null) !!},
                faculty_id: {!! json_encode($init_filters['faculty_id'] ?? null) !!},
                semester_id: {!! json_encode($init_filters['semester_id'] ?? null) !!},
                student_batch_id: {!! json_encode($init_filters['student_batch_id'] ?? null) !!},
                from_date: "{{ $from_date }}",
                to_date: "{{ $to_date }}"
            };

            function setNowTZ() {
                const d = new Date();
                const fmt = d.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Local';
                const el = document.getElementById('now-tz');
                if (el) el.textContent = fmt + ' · ' + tz;
            }

            function showLevel(id) {
                const el = document.getElementById(id);
                if (el) el.style.display = 'block';
            }

            function hideLevel(id) {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            }

            function fetchList(key, parentId) {
                const ep = ENDPOINTS[key];
                if (!ep) return $.Deferred().resolve({}).promise();
                const cacheKey = String(parentId ?? '');
                if (ep.cache[cacheKey]) return $.Deferred().resolve(ep.cache[cacheKey]).promise();
                const data = {};
                data[ep.param] = parentId;
                return $.get(ep.url, data).then(function(resp) {
                    ep.cache[cacheKey] = (resp || {});
                    return ep.cache[cacheKey];
                });
            }

            function renderList(containerId, level, items, activeId) {
                const $c = $('#' + containerId).empty();
                if (items && Object.keys(items).length) {
                    $.each(items, function(id, text) {
                        const html =
                            `<div class="level-item ${String(id)===String(activeId)?'active':''}" data-level="${level}" data-id="${id}">${text}</div>`;
                        $c.append(html);
                    });
                } else {
                    $c.append('<div class="muted">No items</div>');
                }
            }

            function moneyFmt(n) {
                n = Number(n || 0);
                return n.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function axisMoney(v) {
                return currency + ' ' + moneyFmt(v);
            }

            function syncUrl() {
                const p = new URLSearchParams();
                if (state.from_date) p.set('from_date', state.from_date);
                if (state.to_date) p.set('to_date', state.to_date);
                if (state.department_head_id) p.set('department_head_id', state.department_head_id);
                if (state.department_id) p.set('department_id', state.department_id);
                if (state.faculty_id) p.set('faculty_id', state.faculty_id);
                if (state.semester_id) p.set('semester_id', state.semester_id);
                if (state.student_batch_id) p.set('student_batch_id', state.student_batch_id);
                const newUrl = location.pathname + (p.toString() ? ('?' + p.toString()) : '');
                window.history.replaceState({}, '', newUrl);
            }

            // colors
            function palette(n, a = .9) {
                const out = [];
                for (let i = 0; i < n; i++) {
                    const h = Math.round((360 / n) * i) % 360;
                    out.push(`hsl(${h} 70% 55% / ${a})`);
                }
                return out;
            }

            function solidize(colors) {
                return colors.map(c => c.replace(/\/\s*0\.?\d+\)/, '/ 1)'));
            }

            function applyPieColors(chart) {
                const n = (chart.data.labels || []).length;
                const cols = palette(n, .9);
                if (chart.data.datasets[0]) {
                    chart.data.datasets[0].backgroundColor = cols;
                    chart.data.datasets[0].borderColor = solidize(cols);
                    chart.data.datasets[0].borderWidth = 1;
                }
            }

            function applyBarColors(chart) {
                const n = (chart.data.labels || []).length;
                const cols = palette(n, .85);
                if (chart.data.datasets[0]) {
                    chart.data.datasets[0].backgroundColor = cols;
                    chart.data.datasets[0].borderColor = solidize(cols);
                    chart.data.datasets[0].borderWidth = 1;
                }
            }

            // charts
            let chByDay, chCumulative, chHeads, chHeadShare, chPM, chPMByDay, chModeSplit, chOP, chOpTimeline;
            let liveTimer = null,
                liveOn = false;

            function initCharts() {
                // SAFE base—no references to other chart vars here
                const base = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const val = Array.isArray(ctx.raw) ? ctx.raw[0] : ctx.raw;
                                    if (ctx.chart && ctx.chart.data && ctx.chart.data.isMoney) return axisMoney(
                                        val);
                                    return String(val);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(v) {
                                    if (this.chart && this.chart.data && this.chart.data.isMoney)
                                    return axisMoney(v);
                                    return v;
                                }
                            }
                        }
                    }
                };

                chByDay = new Chart(document.getElementById('chart-byday'), {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: currency,
                            data: [],
                            tension: .3,
                            fill: true
                        }]
                    },
                    options: base
                });
                chByDay.data.isMoney = true;

                chCumulative = new Chart(document.getElementById('chart-cumulative'), {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Cumulative',
                            data: [],
                            tension: .25,
                            fill: false
                        }]
                    },
                    options: base
                });
                chCumulative.data.isMoney = true;

                // Heads (horizontal bar) — category scale on Y, show all labels
                chHeads = new Chart(document.getElementById('chart-heads'), {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: currency,
                            data: [],
                            maxBarThickness: 28
                        }]
                    },
                    options: {
                        ...base,
                        indexAxis: 'y',
                        scales: {
                            y: {
                                type: 'category',
                                ticks: {
                                    autoSkip: false
                                }
                            },
                            x: {
                                ticks: {
                                    callback: function(v) {
                                        if (this.chart && this.chart.data && this.chart.data.isMoney)
                                        return axisMoney(v);
                                        return v;
                                    }
                                }
                            }
                        }
                    }
                });
                chHeads.data.isMoney = true;

                chHeadShare = new Chart(document.getElementById('chart-headshare'), {
                    type: 'doughnut',
                    data: {
                        labels: [],
                        datasets: [{
                            data: []
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        }
                    }
                });

                chPM = new Chart(document.getElementById('chart-pm'), {
                    type: 'doughnut',
                    data: {
                        labels: [],
                        datasets: [{
                            data: []
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        }
                    }
                });

                chPMByDay = new Chart(document.getElementById('chart-pmbyday'), {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: []
                    },
                    options: {
                        ...base,
                        plugins: {
                            ...base.plugins,
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        }
                    }
                });
                chPMByDay.data.isMoney = true;

                chModeSplit = new Chart(document.getElementById('chart-modesplit'), {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Online',
                            data: [],
                            stack: 'mode'
                        }, {
                            label: 'Offline',
                            data: [],
                            stack: 'mode'
                        }]
                    },
                    options: {
                        ...base,
                        plugins: {
                            ...base.plugins,
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        },
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                ticks: base.scales.y.ticks
                            }
                        }
                    }
                });
                chModeSplit.data.isMoney = true;

                chOpTimeline = new Chart(document.getElementById('chart-op-timeline'), {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Txn Count',
                            data: [],
                            tension: .3,
                            fill: true
                        }]
                    },
                    options: {
                        ...base,
                        plugins: {
                            ...base.plugins,
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                chOP = new Chart(document.getElementById('chart-op'), {
                    type: 'doughnut',
                    data: {
                        labels: [],
                        datasets: [{
                            data: []
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            function renderTopHeadsTable(rows) {
                const $tb = $('#topheads-table tbody').empty();
                if (!rows || !rows.length) {
                    $tb.append('<tr><td colspan="4" class="muted" style="padding:10px">No data</td></tr>');
                    return;
                }
                rows.forEach(r => {
                    $tb.append(
                        `<tr>
                    <td>${r.label||'—'}</td>
                    <td>${moneyFmt(r.sum||0)}</td>
                    <td>${r.count||0}</td>
                    <td>${moneyFmt(r.avg||0)}</td>
                </tr>`
                    );
                });
            }

            function renderRecentTable(rows) {
                const $tb = $('#recent-table tbody').empty();
                if (!rows || !rows.length) {
                    $tb.append(
                        '<tr><td colspan="8" class="muted" style="padding:10px">No receipts in this period.</td></tr>'
                        );
                    return;
                }
                rows.forEach(rc => {
                    $tb.append(
                        `<tr>
                    <td>${rc.reg_no || '—'}</td>
                    <td>${rc.student || '—'}</td>
                    <td>${rc.date || ''}</td>
                    <td>${rc.payment_method || ''}</td>
                    <td>${moneyFmt(rc.amount || 0)}</td>
                    <td>${moneyFmt(rc.discount || 0)}</td>
                    <td>${moneyFmt(rc.fine || 0)}</td>
                    <td>${rc.note ? $('<div>').text(rc.note).html() : ''}</td>
                </tr>`
                    );
                });
            }

            function refreshSummary() {
                syncUrl();
                $.get(ENDPOINTS.summary.url, {
                        ...state
                    })
                    .done(function(resp) {
                        // KPIs
                        $('#c-collected').text(moneyFmt(resp?.counters?.collected || 0));
                        $('#c-receipts').text(resp?.counters?.receipts ?? 0);
                        $('#c-students').text(resp?.counters?.students_paid ?? 0);
                        $('#c-avg').text(moneyFmt(resp?.counters?.avg_receipt || 0));
                        $('#c-online-paid').text(moneyFmt(resp?.counters?.online_paid || 0));
                        $('#c-online-pending').text(moneyFmt(resp?.counters?.online_pending || 0));

                        // Collections
                        chByDay.data.labels = resp?.collectionsByDay?.labels || [];
                        chByDay.data.datasets[0].data = resp?.collectionsByDay?.data || [];
                        chByDay.update();

                        chCumulative.data.labels = resp?.cumulativeByDay?.labels || [];
                        chCumulative.data.datasets[0].data = resp?.cumulativeByDay?.data || [];
                        chCumulative.update();

                        // Top heads (Bar + Donut + Table)
                        const th = resp?.topHeads || [];
                        chHeads.data.labels = th.map(x => x.label);
                        chHeads.data.datasets[0].data = th.map(x => x.value);
                        applyBarColors(chHeads);
                        chHeads.update();

                        chHeadShare.data.labels = resp?.headShare?.labels || [];
                        chHeadShare.data.datasets[0].data = resp?.headShare?.data || [];
                        applyPieColors(chHeadShare);
                        chHeadShare.update();

                        renderTopHeadsTable(resp?.topHeadsTable || []);

                        // PMs
                        chPM.data.labels = resp?.paymentMethods?.labels || [];
                        chPM.data.datasets[0].data = resp?.paymentMethods?.data || [];
                        // safety net: merge duplicate labels
                        (function() {
                            const labels = chPM.data.labels || [];
                            const data = chPM.data.datasets[0].data || [];
                            const agg = {};
                            labels.forEach((l, i) => {
                                agg[l] = (agg[l] || 0) + (Number(data[i]) || 0);
                            });
                            chPM.data.labels = Object.keys(agg);
                            chPM.data.datasets[0].data = Object.values(agg);
                        })();
                        applyPieColors(chPM);
                        chPM.update();

                        chPMByDay.data.labels = resp?.methodsByDay?.labels || [];
                        chPMByDay.data.datasets = (resp?.methodsByDay?.datasets || []).map(function(ds, idx) {
                            const cols = palette((resp?.methodsByDay?.datasets || []).length, .9);
                            return {
                                ...ds,
                                tension: .25,
                                fill: false,
                                borderColor: cols[idx],
                                backgroundColor: cols[idx]
                            };
                        });
                        chPMByDay.update();

                        const msCols = palette(2, .9);
                        chModeSplit.data.labels = resp?.modeSplitByDay?.labels || [];
                        chModeSplit.data.datasets[0].data = resp?.modeSplitByDay?.online || [];
                        chModeSplit.data.datasets[1].data = resp?.modeSplitByDay?.offline || [];
                        chModeSplit.data.datasets[0].backgroundColor = msCols[0];
                        chModeSplit.data.datasets[1].backgroundColor = msCols[1];
                        chModeSplit.update();

                        // Online
                        chOpTimeline.data.labels = resp?.onlineTimeline?.labels || [];
                        chOpTimeline.data.datasets[0].data = resp?.onlineTimeline?.data || [];
                        chOpTimeline.update();

                        chOP.data.labels = resp?.onlineStatus?.labels || [];
                        chOP.data.datasets[0].data = resp?.onlineStatus?.data || [];
                        applyPieColors(chOP);
                        chOP.update();

                        // Recent table
                        renderRecentTable(resp?.recentReceipts || []);
                    })
                    .fail(function(xhr) {
                        console.error('Summary load failed', xhr?.responseText || xhr?.statusText);
                    });
            }

            // hierarchy clicks (unchanged)
            $(document).on('click', '.level-item', async function() {
                const level = $(this).data('level'),
                    id = String($(this).data('id'));
                $(this).siblings().removeClass('active');
                $(this).addClass('active');

                const label = $.trim($(this).text());
                if (level === 'department_head') {
                    state.department_head_id = id;
                    state.department_id = state.faculty_id = state.semester_id = state.student_batch_id =
                        null;
                }
                if (level === 'department') {
                    state.department_id = id;
                    state.faculty_id = state.semester_id = state.student_batch_id = null;
                }
                if (level === 'faculty') {
                    state.faculty_id = id;
                    state.semester_id = state.student_batch_id = null;
                }
                if (level === 'semester') {
                    state.semester_id = id;
                    state.student_batch_id = null;
                }
                if (level === 'batch') {
                    state.student_batch_id = id;
                }

                if (level === 'department_head') {
                    const deps = await fetchList('departments', id);
                    NAME_CACHE.department = deps;
                    renderList('departments', 'department', deps, state.department_id);
                    showLevel('lv-departments');
                    hideLevel('lv-faculties');
                    hideLevel('lv-semesters');
                    hideLevel('lv-batches');
                }
                if (level === 'department') {
                    const fac = await fetchList('faculties', id);
                    NAME_CACHE.faculty = fac;
                    renderList('faculties', 'faculty', fac, state.faculty_id);
                    showLevel('lv-faculties');
                    hideLevel('lv-semesters');
                    hideLevel('lv-batches');
                }
                if (level === 'faculty') {
                    const sem = await fetchList('semesters', id);
                    NAME_CACHE.semester = sem;
                    renderList('semesters', 'semester', sem, state.semester_id);
                    showLevel('lv-semesters');
                    hideLevel('lv-batches');
                }
                if (level === 'semester') {
                    const batches = await fetchList('batches', id);
                    NAME_CACHE.batch = batches;
                    renderList('batches', 'batch', batches, state.student_batch_id);
                    showLevel('lv-batches');
                }

                if (level === 'department_head') NAME_CACHE.department_head[id] = label;
                if (level === 'department') NAME_CACHE.department[id] = label;
                if (level === 'faculty') NAME_CACHE.faculty[id] = label;
                if (level === 'semester') NAME_CACHE.semester[id] = label;
                if (level === 'batch') NAME_CACHE.batch[id] = label;

                renderBreadcrumbs();
                refreshSummary();
            });

            function setQuick(which) {
                which = String(which).toLowerCase();
                const t = new Date(),
                    pad = v => String(v).padStart(2, '0');
                const to = `${t.getFullYear()}-${pad(t.getMonth()+1)}-${pad(t.getDate())}`;
                let from = to;

                if (which === 'month' || which === 'thismonth') {
                    const d = new Date(t.getFullYear(), t.getMonth(), 1);
                    from = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
                } else if (['30', 'last30', '30d'].includes(which)) {
                    const d2 = new Date(t.getTime() - 29 * 86400000);
                    from = `${d2.getFullYear()}-${pad(d2.getMonth()+1)}-${pad(d2.getDate())}`;
                } else if (which === 'year' || which === 'thisyear') {
                    const d3 = new Date(t.getFullYear(), 0, 1);
                    from = `${d3.getFullYear()}-${pad(d3.getMonth()+1)}-${pad(d3.getDate())}`;
                } else if (which === 'today') {
                    from = to;
                }

                $('#from_date').val(from);
                $('#to_date').val(to);
            }

            $(document).on('click', '.quick .btn', function(e) {
                e.preventDefault();
                setQuick($(this).attr('data-q'));
                $('#apply-filters').trigger('click');
            });

            function sanitizeDatesAndApply() {
                let from = $('#from_date').val(),
                    to = $('#to_date').val();
                if (!from || !to) return;
                if (new Date(from) > new Date(to)) {
                    const tmp = from;
                    from = to;
                    to = tmp;
                    $('#from_date').val(from);
                    $('#to_date').val(to);
                }
                state.from_date = from;
                state.to_date = to;
                refreshSummary();
            }

            function debounce(fn, ms) {
                let t;
                return function() {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, arguments), ms);
                };
            }
            const debouncedApplyDates = debounce(sanitizeDatesAndApply, 300);

            $('#apply-filters').on('click', function(e) {
                e.preventDefault();
                state.from_date = $('#from_date').val();
                state.to_date = $('#to_date').val();
                sanitizeDatesAndApply();
            });
            $('#from_date, #to_date').on('change input', function() {
                debouncedApplyDates();
            });

            const NAME_CACHE = {
                department_head: {},
                department: {},
                faculty: {},
                semester: {},
                batch: {}
            };
            const LABELS = {
                department_head: null,
                department: null,
                faculty: null,
                semester: null,
                batch: null
            };

            function cacheHeadsFromDom() {
                $('#heads .level-item').each(function() {
                    NAME_CACHE.department_head[String($(this).data('id'))] = $.trim($(this).text());
                });
            }

            function refreshLabelsFromState() {
                LABELS.department_head = state.department_head_id ? NAME_CACHE.department_head[String(state
                    .department_head_id)] : null;
                LABELS.department = state.department_id ? NAME_CACHE.department[String(state.department_id)] : null;
                LABELS.faculty = state.faculty_id ? NAME_CACHE.faculty[String(state.faculty_id)] : null;
                LABELS.semester = state.semester_id ? NAME_CACHE.semester[String(state.semester_id)] : null;
                LABELS.batch = state.student_batch_id ? NAME_CACHE.batch[String(state.student_batch_id)] : null;
            }

            function renderBreadcrumbs() {
                refreshLabelsFromState();
                const $wrap = $('#fee-breadcrumbs'),
                    $nav = $wrap.find('.breadcrumb-wrap').empty();
                const crumbs = [];
                if (state.department_head_id && LABELS.department_head) crumbs.push({
                    level: 'department_head',
                    label: LABELS.department_head
                });
                if (state.department_id && LABELS.department) crumbs.push({
                    level: 'department',
                    label: LABELS.department
                });
                if (state.faculty_id && LABELS.faculty) crumbs.push({
                    level: 'faculty',
                    label: LABELS.faculty
                });
                if (state.semester_id && LABELS.semester) crumbs.push({
                    level: 'semester',
                    label: LABELS.semester
                });
                if (state.student_batch_id && LABELS.batch) crumbs.push({
                    level: 'batch',
                    label: LABELS.batch
                });

                if (!crumbs.length) {
                    $wrap.hide();
                    return;
                }
                $wrap.show();
                $nav.append(
                    `<span class="breadcrumb-item"><a href="#" class="breadcrumb-link" data-level="root"><i class="fas fa-home"></i> All</a></span>`
                    );
                crumbs.forEach(c => {
                    $nav.append(`<span class="breadcrumb-divider">›</span>`);
                    $nav.append(
                        `<span class="breadcrumb-item"><a href="#" class="breadcrumb-link" data-level="${c.level}">${c.label}</a></span>`
                        );
                });
            }

            $(document).on('click', '.breadcrumb-link', async function(e) {
                e.preventDefault();
                const lvl = $(this).data('level');
                if (lvl === 'root') {
                    state.department_head_id = state.department_id = state.faculty_id = state.semester_id =
                        state.student_batch_id = null;
                } else if (lvl === 'department_head') {
                    state.department_id = state.faculty_id = state.semester_id = state.student_batch_id =
                        null;
                } else if (lvl === 'department') {
                    state.faculty_id = state.semester_id = state.student_batch_id = null;
                } else if (lvl === 'faculty') {
                    state.semester_id = state.student_batch_id = null;
                } else if (lvl === 'semester') {
                    state.student_batch_id = null;
                }
                await rebuildHierarchyFromState();
                refreshSummary();
                renderBreadcrumbs();
            });

            async function rebuildHierarchyFromState() {
                $('.level-items .level-item').removeClass('active');
                if (!state.department_head_id) {
                    $('#lv-departments,#lv-faculties,#lv-semesters,#lv-batches').hide();
                    return;
                }
                $('#heads .level-item[data-id="' + state.department_head_id + '"]').addClass('active');

                const deps = await fetchList('departments', state.department_head_id);
                NAME_CACHE.department = deps;
                renderList('departments', 'department', deps, state.department_id);
                showLevel('lv-departments');

                if (!state.department_id) {
                    hideLevel('lv-faculties');
                    hideLevel('lv-semesters');
                    hideLevel('lv-batches');
                    return;
                }

                const fac = await fetchList('faculties', state.department_id);
                NAME_CACHE.faculty = fac;
                renderList('faculties', 'faculty', fac, state.faculty_id);
                showLevel('lv-faculties');

                if (!state.faculty_id) {
                    hideLevel('lv-semesters');
                    hideLevel('lv-batches');
                    return;
                }

                const sem = await fetchList('semesters', state.faculty_id);
                NAME_CACHE.semester = sem;
                renderList('semesters', 'semester', sem, state.semester_id);
                showLevel('lv-semesters');

                if (!state.semester_id) {
                    hideLevel('lv-batches');
                    return;
                }

                const batches = await fetchList('batches', state.semester_id);
                NAME_CACHE.batch = batches;
                renderList('batches', 'batch', batches, state.student_batch_id);
                showLevel('lv-batches');
            }

            const HIER_KEY = 'fees.hierarchy.visible';

            function isHierarchyVisible() {
                return localStorage.getItem(HIER_KEY) === '1';
            }

            function setHierarchyVisible(on) {
                localStorage.setItem(HIER_KEY, on ? '1' : '0');
                updateHierarchyToggleUI();
            }

            function updateHierarchyToggleUI() {
                const on = isHierarchyVisible();
                const $b = $('#hierarchy-block');
                const $bt = $('#btn-hierarchy-toggle');
                $b.toggleClass('collapsed', !on);
                if ($bt.length) {
                    $bt.html(
                        `<i class="fas ${on?'fa-eye-slash':'fa-eye'}"></i> ${on?'Hide Hierarchy':'Show Hierarchy'}`);
                }
                setTimeout(() => {
                    try {
                        window.dispatchEvent(new Event('resize'));
                    } catch (e) {}
                }, 60);
            }
            $(document).on('click', '#btn-hierarchy-toggle', function(e) {
                e.preventDefault();
                setHierarchyVisible(!isHierarchyVisible());
            });

            const FP_KEY = 'fees.fullpage';

            function isFullPage() {
                return localStorage.getItem(FP_KEY) === '1';
            }

            function setFullPage(on) {
                localStorage.setItem(FP_KEY, on ? '1' : '0');
                updateFullPageUI();
            }

            function updateFullPageUI() {
                const on = isFullPage();
                document.body.classList.toggle('fullpage', on);
                const $btn = $('#btn-fullpage');
                if ($btn.length) {
                    $btn.html(
                        `<i class="fas ${on?'fa-compress-arrows-alt':'fa-expand-arrows-alt'}"></i> ${on?'Exit Full Page':'Full Page'}`
                        );
                }
                setTimeout(() => {
                    try {
                        window.dispatchEvent(new Event('resize'));
                    } catch (e) {}
                }, 60);
            }
            $(document).on('click', '#btn-fullpage', function(e) {
                e.preventDefault();
                setFullPage(!isFullPage());
            });

            function isFullscreen() {
                return !!(document.fullscreenElement || document.webkitFullscreenElement || document
                    .msFullscreenElement);
            }

            function reqFs(el) {
                if (el.requestFullscreen) return el.requestFullscreen();
                if (el.webkitRequestFullscreen) return el.webkitRequestFullscreen();
                if (el.msRequestFullscreen) return el.msRequestFullscreen();
            }

            function exitFs() {
                if (document.exitFullscreen) return document.exitFullscreen();
                if (document.webkitExitFullscreen) return document.webkitExitFullscreen();
                if (document.msExitFullscreen) return document.msExitFullscreen();
            }

            function updateFsBtn() {
                const on = isFullscreen();
                const btn = document.getElementById('btn-fullscreen');
                if (btn) {
                    btn.innerHTML =
                        `<i class="fas ${on?'fa-compress':'fa-expand'}"></i> ${on?'Exit Fullscreen':'Fullscreen'}`;
                }
                setTimeout(function() {
                    [chByDay, chCumulative, chHeads, chHeadShare, chPM, chPMByDay, chModeSplit, chOP,
                        chOpTimeline
                    ].forEach(c => {
                        if (c && typeof c.resize === 'function') c.resize();
                    });
                    window.dispatchEvent(new Event('resize'));
                }, 80);
            }

            function toggleFullscreen(e) {
                if (e) e.preventDefault();
                const target = document.querySelector('.container-fluid') || document.documentElement;
                if (isFullscreen()) exitFs();
                else reqFs(target);
            }
            $(document).on('click', '#btn-fullscreen', toggleFullscreen);
            document.addEventListener('fullscreenchange', updateFsBtn);
            document.addEventListener('webkitfullscreenchange', updateFsBtn);
            document.addEventListener('msfullscreenchange', updateFsBtn);

            async function bootFromUrl() {
                cacheHeadsFromDom();
                if (state.department_head_id) {
                    $('#heads .level-item[data-id="' + state.department_head_id + '"]').addClass('active');

                    const deps = await fetchList('departments', state.department_head_id);
                    NAME_CACHE.department = deps;
                    renderList('departments', 'department', deps, state.department_id);
                    if (state.department_id) showLevel('lv-departments');

                    if (state.department_id) {
                        const fac = await fetchList('faculties', state.department_id);
                        NAME_CACHE.faculty = fac;
                        renderList('faculties', 'faculty', fac, state.faculty_id);
                        showLevel('lv-faculties');
                    }
                    if (state.faculty_id) {
                        const sem = await fetchList('semesters', state.faculty_id);
                        NAME_CACHE.semester = sem;
                        renderList('semesters', 'semester', sem, state.semester_id);
                        showLevel('lv-semesters');
                    }
                    if (state.semester_id) {
                        const batches = await fetchList('batches', state.semester_id);
                        NAME_CACHE.batch = batches;
                        renderList('batches', 'batch', batches, state.student_batch_id);
                        showLevel('lv-batches');
                    }
                }
                renderBreadcrumbs();
                refreshSummary();
            }

            $('#btn-reset').on('click', function(e) {
                e.preventDefault();
                const base = location.origin + location.pathname;
                window.location.href = base;
            });

            $('#toggle-live').on('click', function() {
                liveOn = !liveOn;
                $(this).toggleClass('active', liveOn)
                    .html(
                        `<i class="${liveOn?'fa fa-spinner fa-spin':'fas fa-broadcast-tower'}"></i> Live: ${liveOn?'ON':'OFF'}`
                        );
                if (liveOn) {
                    refreshSummary();
                    liveTimer = setInterval(refreshSummary, 15000);
                } else {
                    if (liveTimer) {
                        clearInterval(liveTimer);
                        liveTimer = null;
                    }
                }
            });

            $(function() {
                setNowTZ();
                setInterval(setNowTZ, 1000);
                initCharts();
                updateFullPageUI();
                updateHierarchyToggleUI();
                bootFromUrl();
            });

        })(jQuery);
    </script>
@endsection
