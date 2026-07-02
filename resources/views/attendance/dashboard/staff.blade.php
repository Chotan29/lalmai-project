@extends('layouts.master')

@section('css')
<style>
    :root {
        --primary: #346da5;
        --primary-light: #eef2ff;
        --secondary: #64748b;
        --success: #10b981;
        --success-dark: #059669;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --dark: #1e293b;
        --light: #f8fafc;
        --border: #e2e8f0;
        --card-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
    }
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
    .container-fluid{background:var(--primary-light);min-height:100vh;padding:20px;}
    .container-fluid:fullscreen,.container-fluid:-webkit-full-screen,.container-fluid:-ms-fullscreen{width:100vw;height:100vh;overflow-y:auto !important;-webkit-overflow-scrolling:touch;}
    .container-fluid.is-fs{width:100vw;height:100vh;overflow-y:auto !important;-webkit-overflow-scrolling:touch;}
    .container-fluid.is-fs .header{position:sticky;top:0;z-index:110;background:var(--primary-light);padding-top:10px;padding-bottom:10px;box-shadow:0 2px 6px rgba(0,0,0,.06);margin-bottom:8px;}
    .container-fluid.is-fs .section--filters{position:sticky;top:var(--fs-header-h, 64px);z-index:105;background:var(--primary-light);margin-top:0;margin-bottom:12px;box-shadow:0 2px 6px rgba(0,0,0,.06);}
    .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;padding:0 12px}
    .header-title h1{font-weight:700;font-size:24px;color:var(--dark);margin-bottom:6px}
    .header-title p{color:var(--secondary);font-size:14px}
    .header-actions{display:flex;gap:12px;align-items:center}
    .chip{background:var(--primary-light);color:var(--primary);border:1px solid var(--border);border-radius:999px;padding:6px 10px;font-size:12px}
    .btn{display:inline-flex;align-items:center;gap:8px;border-radius:8px;padding:8px 12px;cursor:pointer;text-decoration:none;font-size:14px;transition:all 0.2s;}
    .btn-soft{border:1px dashed var(--primary);background:var(--primary-light);color:var(--primary)}
    .btn-soft:hover{filter:brightness(.97);transform:translateY(-1px);}
    .btn-primary{background:var(--primary);color:#fff;border:none}
    .btn-primary:hover{filter:brightness(.95);transform:translateY(-1px);}
    .btn-live{border:1px solid var(--border);background:#fff;color:var(--dark)}
    .btn-live.active{background:var(--success)!important;color:#fff;border-color:var(--success-dark)!important}
    .filters{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:6px;padding:15px;background:white;border-radius:12px;box-shadow:var(--card-shadow);}
    .datebox{display:flex;align-items:center;gap:8px}
    .datebox input,.datebox select{border:1px solid var(--border);border-radius:8px;padding:8px 10px;background:#fff;min-width:120px;}
    .datebox label{font-weight:500;font-size:13px;}
    .kpis{display:grid;grid-template-columns:repeat(6,1fr);gap:10px;margin-bottom:20px;}
    .kpi{background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px;box-shadow:var(--card-shadow);text-align:center;transition:transform 0.2s;}
    .kpi:hover{transform:translateY(-3px);}
    .kpi-present{border-bottom:4px solid var(--success);}
    .kpi-absent{border-bottom:4px solid var(--danger);}
    .kpi-late{border-bottom:4px solid var(--warning);}
    .kpi-leave{border-bottom:4px solid var(--info);}
    .kpi-holiday{border-bottom:4px solid var(--secondary);}
    .kpi-total{border-bottom:4px solid var(--primary);}
    .kpi .h{font-size:12px;color:#64748b;margin-bottom:6px;font-weight:500;}
    .kpi .v{font-size:24px;font-weight:800;color:#0f172a}
    .section{background:#fff;border:1px solid var(--border);border-radius:12px;box-shadow:var(--card-shadow);padding:18px;margin-bottom:16px}
    .section-head{display:flex;align-items:center;gap:10px;margin-bottom:12px}
    .section-head .icon{width:38px;height:38px;border-radius:10px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;color:var(--primary)}
    .section-head .title{font-weight:700;color:var(--dark);font-size:18px;}
    .muted{color:#64748b}
    .chart-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:12px;margin-bottom:16px;}
    .chart-box{position:relative;height:300px;background:#fafafa;border-radius:8px;padding:10px;}
    .chart-box.sm{height:240px}
    .chart-box>canvas{width:100% !important;height:100% !important}
    .loading-overlay{position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.8);display:flex;align-items:center;justify-content:center;z-index:10;border-radius:8px;}
    .loading-spinner{width:40px;height:40px;border:4px solid #f3f3f3;border-top:4px solid var(--primary);border-radius:50%;animation:spin 1s linear infinite;}
    @keyframes spin{0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}
    .stats-card{display:flex;align-items:center;padding:15px;background:#f8fafc;border-radius:8px;margin-bottom:10px;}
    .stats-icon{width:40px;height:40px;border-radius:8px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;color:var(--primary);margin-right:12px;font-size:18px;}
    .stats-content{flex:1;}
    .stats-title{font-size:13px;color:#64748b;margin-bottom:4px;}
    .stats-value{font-size:18px;font-weight:700;color:#0f172a;}
    .quick-filters{display:flex;gap:8px;flex-wrap:wrap;}
    .quick-filter-btn{padding:6px 12px;border-radius:20px;background:white;border:1px solid var(--border);font-size:13px;cursor:pointer;transition:all 0.2s;}
    .quick-filter-btn:hover{background:var(--primary-light);border-color:var(--primary);}
    .quick-filter-btn.active{background:var(--primary);color:white;border-color:var(--primary);}
    @media print {
        body, html { background: #fff !important; }
        .container-fluid { background:#fff !important; padding: 10mm; width: 210mm; min-height: 297mm; }
        .btn, .quick-filters, #toggle-live, #btn-fullscreen { display:none !important; }
        .chip { border: none !important; background:#fff !important; color:#000 !important; }
        .section { page-break-inside: avoid; box-shadow: none; border-color: #ddd; margin-bottom: 10mm; }
        .kpis { grid-template-columns: repeat(3,1fr); gap: 5mm; }
        .kpi { break-inside: avoid; padding: 5mm; border-width: 0.5mm; }
        .kpi .h { font-size: 10pt; }
        .kpi .v { font-size: 16pt; }
        .chart-grid { grid-template-columns: 1fr; gap: 5mm; }
        .chart-box { height: 80mm !important; padding: 2mm; background: #fff; }
        .chart-box.sm { height: 60mm !important; }
        .chart-box>canvas { width: 100% !important; height: 100% !important; }
        .section-head .title { font-size: 14pt; }
        .stats-card { page-break-inside: avoid; padding: 3mm; }
        .stats-title { font-size: 10pt; }
        .stats-value { font-size: 12pt; }
        .loading-overlay { display: none !important; }
        @page { size: A4; margin: 10mm; }
    }
    @media(max-width:1200px){
        .kpis{grid-template-columns:repeat(3,1fr)}
        .chart-grid{grid-template-columns:1fr}
    }
    @media(max-width:768px){
        .kpis{grid-template-columns:repeat(2,1fr)}
        .filters{flex-direction:column;align-items:flex-start;}
        .header{flex-direction:column;align-items:flex-start;gap:15px;}
        .header-actions{width:100%;justify-content:space-between;}
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="header">
        <div class="header-title">
            <h1>Staff Attendance Dashboard</h1>
            <p>Monitor and analyze staff attendance patterns across designations and age groups</p>
        </div>
        <div class="header-actions">
            <span class="chip" id="now-tz">--:--</span>
            <a href="{{ $students_index_route }}" class="btn btn-soft"><i class="fas fa-user-graduate"></i> Students</a>
            <a href="{{ $staff_index_route }}" class="btn btn-primary"><i class="fas fa-users"></i> Staff</a>
            <button class="btn btn-soft" id="btn-fullscreen"><i class="fas fa-expand"></i> Fullscreen</button>
            <button class="btn btn-soft" id="btn-print"><i class="fas fa-print"></i> Print</button>
        </div>
    </div>

    <div class="section section--filters">
        <div class="filters">
            <div class="datebox">
                <label class="muted">From</label>
                <input type="date" id="from_date" value="{{ $from_date }}">
            </div>
            <div class="datebox">
                <label class="muted">To</label>
                <input type="date" id="to_date" value="{{ $to_date }}">
            </div>
            <div class="quick-filters">
                <button class="quick-filter-btn" data-days="0">Today</button>
                <button class="quick-filter-btn" data-days="7">Last 7 Days</button>
                <button class="quick-filter-btn active" data-days="30">Last 30 Days</button>
                <button class="quick-filter-btn" data-days="90">Last 90 Days</button>
                <button class="quick-filter-btn" data-days="180">Last 6 Months</button>
            </div>
            <button class="btn btn-primary" id="apply-filters"><i class="fas fa-sync"></i> Apply</button>
            <button class="btn btn-live" id="toggle-live" title="Auto-refresh every 15s">
                <i class="fas fa-broadcast-tower"></i> Live: OFF
            </button>
        </div>
    </div>

    <div class="kpis">
        <div class="kpi kpi-present">
            <div class="h">Present</div>
            <div class="v" id="k-present">0</div>
        </div>
        <div class="kpi kpi-absent">
            <div class="h">Absent</div>
            <div class="v" id="k-absent">0</div>
        </div>
        <div class="kpi kpi-late">
            <div class="h">Late</div>
            <div class="v" id="k-late">0</div>
        </div>
        <div class="kpi kpi-leave">
            <div class="h">Leave</div>
            <div class="v" id="k-leave">0</div>
        </div>
        <div class="kpi kpi-holiday">
            <div class="h">Half-Leave</div>
            <div class="v" id="k-holiday">0</div>
        </div>
        <div class="kpi kpi-total">
            <div class="h">Total Records</div>
            <div class="v" id="k-total">0</div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="section">
            <div class="section-head">
                <div class="icon"><i class="fas fa-chart-line"></i></div>
                <div class="title">Attendance Trend (Present by Day)</div>
            </div>
            <div class="chart-box">
                <div class="loading-overlay" id="trend-loading">
                    <div class="loading-spinner"></div>
                </div>
                <canvas id="ch-trend"></canvas>
            </div>
        </div>
        <div class="section">
            <div class="section-head">
                <div class="icon"><i class="fas fa-chart-pie"></i></div>
                <div class="title">Attendance Distribution</div>
            </div>
            <div class="chart-box sm">
                <div class="loading-overlay" id="status-loading">
                    <div class="loading-spinner"></div>
                </div>
                <canvas id="ch-status"></canvas>
            </div>
        </div>
        <div class="section">
            <div class="section-head">
                <div class="icon"><i class="fas fa-venus-mars"></i></div>
                <div class="title">Gender Distribution</div>
            </div>
            <div class="chart-box sm">
                <div class="loading-overlay" id="gender-loading">
                    <div class="loading-spinner"></div>
                </div>
                <canvas id="ch-gender"></canvas>
            </div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="section">
            <div class="section-head">
                <div class="icon"><i class="fas fa-id-badge"></i></div>
                <div class="title">Attendance by Designation</div>
            </div>
            <div class="chart-box">
                <div class="loading-overlay" id="desig-loading">
                    <div class="loading-spinner"></div>
                </div>
                <canvas id="ch-desig"></canvas>
            </div>
        </div>
        <div class="section">
            <div class="section-head">
                <div class="icon"><i class="fas fa-cake-candles"></i></div>
                <div class="title">Attendance by Age Group</div>
            </div>
            <div class="chart-box">
                <div class="loading-overlay" id="age-loading">
                    <div class="loading-spinner"></div>
                </div>
                <canvas id="ch-age"></canvas>
            </div>
        </div>
        <div class="section">
        <div class="section-head">
            <div class="icon"><i class="fas fa-chart-bar"></i></div>
            <div class="title">Quick Statistics</div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stats-content">
                    <div class="stats-title">Average Daily Attendance</div>
                    <div class="stats-value" id="avg-daily">0</div>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-tachometer-alt"></i></div>
                <div class="stats-content">
                    <div class="stats-title">Attendance Rate</div>
                    <div class="stats-value" id="attendance-rate">0%</div>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-id-badge"></i></div>
                <div class="stats-content">
                    <div class="stats-title">Top Designation</div>
                    <div class="stats-value" id="top-desig">--</div>
                </div>
            </div>
            <div class="stats-card">
                <div class="stats-icon"><i class="fas fa-cake-candles"></i></div>
                <div class="stats-content">
                    <div class="stats-title">Top Age Group</div>
                    <div class="stats-value" id="top-age">--</div>
                </div>
            </div>
        </div>
    </div>
    </div>

    
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
(function($){
    const ENDPOINT = "{{ route('attendance.dashboard.staff.summary') }}";

    let chTrend, chStatus, chGender, chDesig, chAge;
    let live = false, timer = null;

    function allCharts(){
        return [chTrend, chStatus, chGender, chDesig, chAge].filter(Boolean);
    }

    function initCharts(){
        const baseOptions = { 
            responsive: true, 
            maintainAspectRatio: false,
            devicePixelRatio: window.matchMedia("print").matches ? 4 : 2,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20, usePointStyle: true, pointStyle: 'circle', font: { size: 12 } }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    padding: 10,
                    cornerRadius: 3,
                    displayColors: true
                }
            }
        };

        chTrend = new Chart(document.getElementById('ch-trend'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Present',
                    data: [],
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(52, 109, 165, 0.1)',
                    borderColor: 'rgba(52, 109, 165, 1)',
                    pointBackgroundColor: 'rgba(52, 109, 165, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(52, 109, 165, 1)'
                }]
            },
            options: {
                ...baseOptions,
                scales: {
                    y: { 
                        beginAtZero: true, 
                        title: { display: true, text: 'Number of Staff', font: { size: 14 } },
                        ticks: { font: { size: 12 } }
                    },
                    x: { 
                        title: { display: true, text: 'Date', font: { size: 14 } },
                        ticks: { font: { size: 12 }, maxRotation: 45, minRotation: 45 }
                    }
                }
            }
        });

        chStatus = new Chart(document.getElementById('ch-status'), {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(100, 116, 139, 0.8)',
                        'rgba(156, 163, 175, 0.8)'
                    ],
                    borderColor: [
                        'rgb(16, 185, 129)',
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                        'rgb(100, 116, 139)',
                        'rgb(156, 163, 175)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                ...baseOptions,
                cutout: '70%',
                plugins: {
                    ...baseOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: function(c){
                                const label = c.label || '';
                                const value = c.raw || 0;
                                const total = c.dataset.data.reduce((a,b)=>a+b,0);
                                const pct = total ? Math.round((value/total)*100) : 0;
                                return `${label}: ${value} (${pct}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        formatter: (value, ctx) => {
                            const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
                            return total ? Math.round((value/total)*100) + '%' : '';
                        },
                        color: '#fff',
                        font: { weight: 'bold', size: 10 },
                        padding: 4
                    }
                }
            }
        });

        chGender = new Chart(document.getElementById('ch-gender'), {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: ['rgba(59,130,246,0.8)', 'rgba(236,72,153,0.8)', 'rgba(156,163,175,0.8)'],
                    borderColor: ['rgb(59,130,246)', 'rgb(236,72,153)', 'rgb(156,163,175)'],
                    borderWidth: 1
                }]
            },
            options: { 
                ...baseOptions, 
                cutout: '70%',
                plugins: {
                    ...baseOptions.plugins,
                    datalabels: {
                        display: true,
                        formatter: (value, ctx) => {
                            const total = ctx.dataset.data.reduce((a,b)=>a+b,0);
                            return total ? Math.round((value/total)*100) + '%' : '';
                        },
                        color: '#fff',
                        font: { weight: 'bold', size: 10 },
                        padding: 4
                    }
                }
            }
        });

        const barOpts = {
            ...baseOptions,
            indexAxis: 'y',
            scales: { 
                x: { 
                    beginAtZero: true, 
                    title: { display: true, text: 'Number of Staff', font: { size: 14 } },
                    ticks: { font: { size: 12 } }
                },
                y: {
                    ticks: { font: { size: 12 } }
                }
            }
        };

        chDesig = new Chart(document.getElementById('ch-desig'), { 
            type: 'bar', 
            data: { labels: [], datasets: [{ label: 'Present', data: [], backgroundColor: 'rgba(52,109,165,0.7)', borderColor: 'rgba(52,109,165,1)', borderWidth: 1 }] }, 
            options: barOpts 
        });

        chAge = new Chart(document.getElementById('ch-age'), { 
            type: 'bar', 
            data: { labels: [], datasets: [{ label: 'Present', data: [], backgroundColor: 'rgba(52,109,165,0.7)', borderColor: 'rgba(52,109,165,1)', borderWidth: 1 }] }, 
            options: barOpts 
        });
    }

    function showLoading(){ $('.loading-overlay').show(); }
    function hideLoading(){ $('.loading-overlay').hide(); }

    function setChartData(chart, labels = [], data = []){
        chart.data.labels = Array.isArray(labels) ? labels : [];
        const ds = chart.data.datasets && chart.data.datasets[0] ? chart.data.datasets[0] : null;
        if (ds) ds.data = Array.isArray(data) ? data : [];
        chart.update();
    }

    function refresh(){
        showLoading();
        const p = { 
            from_date: $('#from_date').val(), 
            to_date: $('#to_date').val(),
            _: Date.now()
        };
        
        $.get(ENDPOINT, p).done(function(resp){
            if (resp.error) console.error(resp.error);

            $('#k-present').text(resp?.kpi?.present ?? 0);
            $('#k-absent').text(resp?.kpi?.absent ?? 0);
            $('#k-late').text(resp?.kpi?.late ?? 0);
            $('#k-leave').text(resp?.kpi?.leave ?? 0);
            $('#k-holiday').text(resp?.kpi?.holiday ?? 0);
            $('#k-total').text(resp?.kpi?.total ?? 0);

            setChartData(chTrend, resp?.byDay?.labels, resp?.byDay?.data);
            setChartData(chStatus, resp?.statusPie?.labels, resp?.statusPie?.data);
            setChartData(chGender, resp?.genderPie?.labels, resp?.genderPie?.data);
            setChartData(chDesig, resp?.designationWise?.labels, resp?.designationWise?.data);
            setChartData(chAge, resp?.ageWise?.labels, resp?.ageWise?.data);

            $('#top-desig').text((resp?.designationWise?.labels||[])[0] ?? '--');
            $('#top-age').text((resp?.ageWise?.labels||[])[0] ?? '--');

            const total = Number(resp?.kpi?.total || 0);
            const present = Number(resp?.kpi?.present || 0);
            const days = daysBetween(p.from_date, p.to_date);
            const avgDaily = days > 0 ? Math.round(present / days) : 0;
            $('#avg-daily').text(avgDaily);
            const attendanceRate = total > 0 ? Math.round((present / total) * 100) : 0;
            $('#attendance-rate').text(attendanceRate + '%');

            hideLoading();
        }).fail(function(xhr){
            console.error('staff summary failed', xhr?.responseText || xhr?.statusText);
            hideLoading();
        });
    }

    function daysBetween(date1, date2){
        const d1 = new Date(date1), d2 = new Date(date2);
        const diff = Math.abs(d2 - d1);
        return Math.ceil(diff / (1000*60*60*24)) + 1;
    }

    function setQuickDateRange(days){
        const today = new Date();
        const toDate = today.toISOString().split('T')[0];
        const fromDate = new Date(); fromDate.setDate(today.getDate() - days);
        const fromStr = fromDate.toISOString().split('T')[0];
        $('#from_date').val(fromStr);
        $('#to_date').val(toDate);
    }

    function setNowTZ(){
        const d = new Date();
        const fmt = d.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit',second:'2-digit'});
        const tz = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Local';
        $('#now-tz').text(fmt+' · '+tz);
    }

    function setChartHiDPI(scale){
        allCharts().forEach(c=>{
            const canvas = c.canvas;
            const rect = canvas.getBoundingClientRect();
            c.$origSize = {w: rect.width, h: rect.height};
            c.resize(rect.width*scale, rect.height*scale);
            c.update('none');
        });
    }

    function resetChartSize(){
        allCharts().forEach(c=>{
            const s = c.$origSize;
            if (s){ c.resize(s.w, s.h); }
            setTimeout(()=> c.resize(), 0);
        });
    }

    function isFullscreen(){ 
        return !!(document.fullscreenElement||document.webkitFullscreenElement||document.msFullscreenElement);
    }

    function setFsHeaderHeight(){
        if (!isFullscreen()) { $('.container-fluid').css('--fs-header-h',''); return; }
        const hh = $('.header').outerHeight() || 64;
        $('.container-fluid').css('--fs-header-h', hh + 'px');
    }

    function updateFsBtn(){
        const on = isFullscreen();
        $('#btn-fullscreen').html(`<i class="fas ${on?'fa-compress':'fa-expand'}"></i> ${on?'Exit Fullscreen':'Fullscreen'}`);
        $('.container-fluid').toggleClass('is-fs', on);
        setFsHeaderHeight();
        setTimeout(()=>{ allCharts().forEach(c=>c && c.resize && c.resize()); }, 80);
    }

    function reqFs(el){ 
        if(el.requestFullscreen) return el.requestFullscreen(); 
        if(el.webkitRequestFullscreen) return el.webkitRequestFullscreen(); 
        if(el.msRequestFullscreen) return el.msRequestFullscreen(); 
    }

    function exitFs(){ 
        if(document.exitFullscreen) return document.exitFullscreen(); 
        if(document.webkitExitFullscreen) return document.webkitExitFullscreen(); 
        if(document.msExitFullscreen) return document.msExitFullscreen(); 
    }

    $(function(){
        $.ajaxSetup({ cache: false });
        initCharts();
        refresh();
        setNowTZ();
        setInterval(setNowTZ, 1000);

        $('.quick-filter-btn').on('click', function(e){
            e.preventDefault();
            $('.quick-filter-btn').removeClass('active');
            $(this).addClass('active');
            const days = parseInt($(this).data('days'));
            setQuickDateRange(days);
            refresh();
        });

        $('#apply-filters').on('click', function(e){
            e.preventDefault();
            refresh();
        });

        let debounceTimer = null;
        $('#from_date, #to_date').on('change input', function(){
            $('.quick-filter-btn').removeClass('active');
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(refresh, 150);
        });

        $('#toggle-live').on('click', function(){
            live = !live;
            $(this).toggleClass('active', live)
                .html(`<i class="${live?'fa fa-spinner fa-spin':'fas fa-broadcast-tower'}"></i> Live: ${live?'ON':'OFF'}`);
            if (live){ refresh(); timer = setInterval(refresh, 15000); }
            else { if (timer){ clearInterval(timer); timer = null; } }
        });

        $('#btn-fullscreen').on('click', function(e){
            e.preventDefault();
            const target = document.querySelector('.container-fluid') || document.documentElement;
            if(isFullscreen()) exitFs(); else reqFs(target);
        });
        document.addEventListener('fullscreenchange', updateFsBtn);
        document.addEventListener('webkitfullscreenchange', updateFsBtn);
        document.addEventListener('msfullscreenchange', updateFsBtn);
        window.addEventListener('resize', function(){ if(isFullscreen()) setFsHeaderHeight(); });

        $('#btn-print').on('click', function(e){
            e.preventDefault();
            setChartHiDPI(4);
            setTimeout(()=> window.print(), 50);
        });
        window.addEventListener('beforeprint', ()=> setChartHiDPI(4));
        window.addEventListener('afterprint', ()=> resetChartSize());
        const mq = window.matchMedia && window.matchMedia('print');
        if (mq && mq.addEventListener){
            mq.addEventListener('change', e => { if (e.matches) setChartHiDPI(4); else resetChartSize(); });
        }
    });
})(jQuery);
</script>
@endsection