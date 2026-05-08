@extends('layouts.master')

@section('css')
    <style>
        /* Modern Styles */
        :root {
            --primary: #346da5;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --success: #10b981;
            --dark: #1e293b;
            --light: #f8fafc;
            --border: #e2e8f0;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        /*
        body {
            background-color: #f1f5f9;
            color: #334155;
            padding: 20px;
        } */

        /* .container-fluid {
            max-width: 1600px;
            margin: 0 auto;
        } */

        /* Header styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding: 0 12px;
        }

        .header-title h1 {
            font-weight: 700;
            font-size: 24px;
            color: var(--dark);
            margin-bottom: 6px;
        }

        .header-title p {
            color: var(--secondary);
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* Print dropdown */
        .print-dropdown .btn {
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .print-dropdown .btn:hover {
            background: #e0e7ff;
        }

        .print-dropdown .dropdown-menu {
            border-radius: 8px;
            border: 1px solid var(--border);
            box-shadow: var(--card-shadow);
            padding: 8px 0;
            min-width: 240px;
        }

        .print-dropdown .dropdown-item {
            padding: 8px 16px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 4px;
            margin: 0 4px;
            transition: var(--transition);
        }

        .print-dropdown .dropdown-item:hover {
            background: var(--primary-light);
            color: var(--primary);
        }

        .print-dropdown .dropdown-divider {
            margin: 6px 0;
        }

        .print-dropdown .dropdown-item.disabled {
            color: #94a3b8;
            cursor: not-allowed;
            background: transparent;
        }

        /* Add button */
        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        /* Breadcrumbs */
        .breadcrumb-container {
            background: white;
            border-radius: 12px;
            padding: 14px 20px;
            margin-bottom: 20px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border);
        }

        .breadcrumb-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
        }

        .breadcrumb-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .breadcrumb-link:hover {
            color: #4338ca;
            text-decoration: underline;
        }

        .breadcrumb-divider {
            color: #cbd5e1;
            font-size: 14px;
        }

        /* Hierarchy container */
            .hierarchy-container {
                display: flex;
                gap: 16px;
                overflow-x: auto;
                padding: 10px 0;
                margin-bottom: 24px;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f1f5f9;
            }

            .hierarchy-container::-webkit-scrollbar {
                height: 8px;
            }

            .hierarchy-container::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 4px;
            }

            .hierarchy-container::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            .hierarchy-level {
                min-width: 200px;
                background: white;
                border-radius: 12px;
                box-shadow: var(--card-shadow);
                flex-shrink: 0;
                border: 1px solid var(--border);
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
                gap: 8px;
                margin-top: 5px !important;
            }

            .level-items {
                padding: 10px;
                max-height: 420px;
                overflow-y: auto;
            }

            .level-item {
                padding: 4px 10px;
                border-radius: 10px;
                background: #f8fafc;
                transition: var(--transition);
                cursor: pointer;
                margin-bottom: 5px;
                display: flex;
                align-items: center;
                gap: 10px;
                border: 1px solid transparent;
            }

            .level-item:hover {
                background: #f1f5f9;
                transform: translateY(-2px);
                border-color: var(--border);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            .level-item.active {
                background: var(--primary);
                color: white;
                border-color: var(--primary);
            }

            .level-item.active .muted {
                color: rgba(255, 255, 255, 0.8);
            }

            .level-item .muted {
                color: #64748b;
                font-size: 13px;
            }

        /* Routines container */
        .routines-container {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 20px;
            border: 1px solid var(--border);
            margin-bottom: 24px;
        }

        .routine-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 16px;
            overflow: hidden;
            transition: var(--transition);
        }

        .routine-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .routine-card-header {
            padding: 14px 18px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .routine-card-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .routine-item {
            padding: 16px;
            border-radius: 8px;
            background: #f8fafc;
            border-left: 4px solid var(--primary);
            transition: var(--transition);
            margin: 12px;
        }

        .badge-time {
            background: var(--primary-light);
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
        }

        .small-muted {
            color: #64748b;
            font-size: 13px;
        }

        .icon {
            width: 20px;
            text-align: center;
            color: var(--primary);
        }

        /* Loading indicator */
        .loading-indicator {
            display: none;
            text-align: center;
            padding: 30px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .header-actions {
                width: 100%;
                justify-content: space-between;
            }

            .hierarchy-level {
                min-width: 260px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <!-- Header section -->
        <div class="header">
            <div class="header-title">
                <h1>Class Routine Management</h1>
                <p>Navigate Department → Faculty → Semester → Batch → (Subject optional)</p>
            </div>

            <div class="header-actions">
                <!-- Print dropdown -->
                <div class="print-dropdown dropdown me-2">
                    <button class="btn dropdown-toggle" type="button" id="printMenuBtn" data-toggle="dropdown"
                        {{-- BS4 --}} data-bs-toggle="dropdown" {{-- BS5 --}} aria-haspopup="true"
                        aria-expanded="false">
                        <i class="fas fa-print"></i> Print Routine
                    </button>

                    @php
                        $deptId = request('department_id');
                        $facId = request('faculty_id');
                        $semId = request('semester_id');
                        $batchId = request('batch_id');
                        $subId = request('subject_id');

                        $hrefDept = $deptId ? url('routine/print/department/' . $deptId) : '#';
                        $hrefFac = $deptId && $facId ? url('routine/print/faculty/' . $deptId . '/' . $facId) : '#';
                        $hrefSem =
                            $deptId && $facId && $semId
                                ? url('routine/print/semester/' . $deptId . '/' . $facId . '/' . $semId)
                                : '#';
                        $hrefBatch = $deptId && $batchId ? url('routine/print/batch/' . $deptId . '/' . $batchId) : '#';
                        $hrefSubAll =
                            $deptId && $facId && $semId && $subId
                                ? url('routine/print/subject/' . $deptId . '/' . $facId . '/' . $semId . '/' . $subId)
                                : '#';
                        $hrefSubBat =
                            $deptId && $facId && $semId && $subId && $batchId
                                ? url(
                                    'routine/print/subject/' .
                                        $deptId .
                                        '/' .
                                        $facId .
                                        '/' .
                                        $semId .
                                        '/' .
                                        $subId .
                                        '/batch/' .
                                        $batchId,
                                )
                                : '#';
                    @endphp

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-right" aria-labelledby="printMenuBtn"
                        style="z-index:1060">
                        <li><a class="dropdown-item {{ $deptId ? '' : 'disabled' }}" id="print-dept" target="_blank"
                                href="{{ $hrefDept }}"><i class="fas fa-building"></i> Department</a></li>
                        <li><a class="dropdown-item {{ $deptId && $facId ? '' : 'disabled' }}" id="print-fac"
                                target="_blank" href="{{ $hrefFac }}"><i class="fas fa-graduation-cap"></i>
                                Faculty/Program</a></li>
                        <li><a class="dropdown-item {{ $deptId && $facId && $semId ? '' : 'disabled' }}" id="print-sem"
                                target="_blank" href="{{ $hrefSem }}"><i class="fas fa-book"></i> Semester</a></li>
                        <li><a class="dropdown-item {{ $deptId && $batchId ? '' : 'disabled' }}" id="print-batch"
                                target="_blank" href="{{ $hrefBatch }}"><i class="fas fa-users"></i> Batch</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item {{ $deptId && $facId && $semId && $subId ? '' : 'disabled' }}"
                                id="print-subject-all" target="_blank" href="{{ $hrefSubAll }}"><i
                                    class="fas fa-book-open"></i> Subject (All Batches)</a></li>
                        <li><a class="dropdown-item {{ $deptId && $facId && $semId && $subId && $batchId ? '' : 'disabled' }}"
                                id="print-subject-batch" target="_blank" href="{{ $hrefSubBat }}"><i
                                    class="fas fa-book-open"></i> Subject (This Batch)</a></li>
                    </ul>
                </div>

                <a href="{{ route('routine.index') }}" class="btn btn-primary">
                    <i class="fas fa-list"></i> Routine List
                </a>
                <a href="{{ route('routine.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Routine
                </a>
                <a href="{{ route('routine.manage') }}" class="btn btn-primary">
                    <i class="fas fa-pencil"></i> Manage Routine
                </a>
                <a href="{{ route('routine.import.form') }}" class="btn btn-primary">
                <i class="fa fa-file-import"></i> Import
                </a>
                <a href="{{ route('routine.export.form') }}" class="btn btn-primary">
                <i class="fa fa-file-export"></i> Export
                </a>
            </div>
        </div>

        <!-- Loading indicator -->
        <div class="loading-indicator">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <div class="mt-2">Loading data...</div>
        </div>

        <!-- Breadcrumbs -->
        <div id="breadcrumb-container">
            @if (isset($current_department_head) ||
                    isset($current_department) ||
                    isset($current_faculty) ||
                    isset($current_semester) ||
                    isset($current_batch) ||
                    isset($current_subject))
                @include('academic.class-routine.includes.breadcrumbs')
            @endif
        </div>

         @include('includes.flash_messages')

        <!-- Hierarchy -->
        <div class="hierarchy-container" id="hierarchy-container">
            @include('academic.class-routine.includes.hierarchy')
        </div>
{{-- {{ $routines }} --}}
        <!-- Routines -->
        <div id="routines-container">
            @if (isset($routines))            
                @include('academic.class-routine.includes.routines')
            @endif
        </div>
    </div>
@endsection

@section('js')

    <script>
        (function($) {
            // ---- Helpers ----
            function getParamsFromUrl() {
                const p = new URLSearchParams(window.location.search),
                    o = {};
                for (const [k, v] of p.entries()) o[k] = v;
                return o;
            }

            function setUrl(params) {
                const url = new URL(window.location.href);
                const s = new URLSearchParams();
                Object.keys(params).forEach(k => {
                    if (params[k]) s.set(k, params[k]);
                });
                const next = url.pathname + (s.toString() ? ('?' + s.toString()) : '');
                history.pushState(params, '', next);
            }

            function showLoading() {
                $('.loading-indicator').show();
                $('#hierarchy-container,#breadcrumb-container,#routines-container').css('opacity', .5);
            }

            function hideLoading() {
                $('.loading-indicator').hide();
                $('#hierarchy-container,#breadcrumb-container,#routines-container').css('opacity', 1);
            }

            // Build/enable print links (server paths, no client-side route() hacking)
            function updatePrintMenu(params) {
                const $dept = $('#print-dept'),
                    $fac = $('#print-fac'),
                    $sem = $('#print-sem'),
                    $batch = $('#print-batch'),
                    $subAll = $('#print-subject-all'),
                    $subBt = $('#print-subject-batch');
                [$dept, $fac, $sem, $batch, $subAll, $subBt].forEach($a => {
                    $a.addClass('disabled').attr('href', '#');
                });

                const d = params.department_id,
                    f = params.faculty_id,
                    s = params.semester_id,
                    b = params.batch_id,
                    j = params.subject_id;

                if (d) $dept.removeClass('disabled').attr('href', "{{ url('routine/print/department') }}/" + d);
                if (d && f) $fac.removeClass('disabled').attr('href', "{{ url('routine/print/faculty') }}/" + d + "/" +
                    f);
                if (d && f && s) $sem.removeClass('disabled').attr('href', "{{ url('routine/print/semester') }}/" + d +
                    "/" + f + "/" + s);
                if (d && b) $batch.removeClass('disabled').attr('href', "{{ url('routine/print/batch') }}/" + d + "/" +
                    b);
                if (d && f && s && j) $subAll.removeClass('disabled').attr('href',
                    "{{ url('routine/print/subject') }}/" + d + "/" + f + "/" + s + "/" + j);
                if (d && f && s && j && b) $subBt.removeClass('disabled').attr('href',
                    "{{ url('routine/print/subject') }}/" + d + "/" + f + "/" + s + "/" + j + "/batch/" + b);
            }

            // AJAX loader (controller returns JSON with partial HTML)
            function loadHierarchy(params) {
                showLoading();
                $.ajax({
                    url: "{{ route('routine.index') }}",
                    data: params,
                    type: 'GET',
                    success: function(resp) {
                        if (typeof resp === 'object') {
                            if (resp.hierarchy) $('#hierarchy-container').html(resp.hierarchy);
                            if (resp.breadcrumbs) $('#breadcrumb-container').html(resp.breadcrumbs);
                            $('#routines-container').html(resp.routines || '');
                        } else {
                            // Fallback full HTML (replace, not append → avoids “double”)
                            const $r = $(resp);
                            $('#hierarchy-container').html($r.find('#hierarchy-container').html());
                            $('#breadcrumb-container').html($r.find('#breadcrumb-container').html());
                            $('#routines-container').html($r.find('#routines-container').html() || '');
                        }
                        hideLoading();
                        updatePrintMenu(params);
                    },
                    error: function() {
                        hideLoading();
                        // Hard redirect fallback
                        window.location.search = new URLSearchParams(params).toString();
                    }
                });
            }

            // ---- Events ----
            // hierarchy click (delegated so it still works after AJAX)
            $(document).on('click', '.level-item', function(e) {
                e.preventDefault();
                const level = $(this).data('level'),
                    id = $(this).data('id');
                const p = getParamsFromUrl(),
                    next = {
                        ...p
                    };

                if (level === 'department_head') {
                    next.department_head_id = id;
                    delete next.department_id;
                    delete next.faculty_id;
                    delete next.semester_id;
                    delete next.batch_id;
                    delete next.subject_id;
                } else if (level === 'department') {
                    next.department_id = id;
                    delete next.faculty_id;
                    delete next.semester_id;
                    delete next.batch_id;
                    delete next.subject_id;
                } else if (level === 'faculty') {
                    next.faculty_id = id;
                    delete next.semester_id;
                    delete next.batch_id;
                    delete next.subject_id;
                } else if (level === 'semester') {
                    next.semester_id = id;
                    delete next.batch_id;
                    delete next.subject_id;
                } else if (level === 'batch') {
                    next.batch_id = id;
                } else if (level === 'subject') {
                    next.subject_id = id;
                }

                setUrl(next);
                loadHierarchy(next);
            });

            // breadcrumbs via AJAX
            $(document).on('click', '.breadcrumb-link', function(e) {
                e.preventDefault();
                const url = new URL($(this).attr('href'), window.location.origin);
                const params = Object.fromEntries(url.searchParams.entries());
                setUrl(params);
                loadHierarchy(params);
            });

            // back/forward
            window.addEventListener('popstate', e => {
                const s = e.state || {};
                loadHierarchy(s);
                updatePrintMenu(s);
            });

            // Init: only normalize with AJAX when there are query params
            $(function() {
                const params = getParamsFromUrl();
                updatePrintMenu(params);
                if (window.location.search) { // deep-link → replace sections once (prevents “double”)
                    loadHierarchy(params);
                }
            });
        })(jQuery);
    </script>
@endsection
