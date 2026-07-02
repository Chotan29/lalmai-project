@extends('layouts.master')

@section('css')
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/alert-settings.css') }}"> --}}
    <style>
        /* Alert System Styles */
        .alert-settings-container {
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .alert-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .alert-type-toggle {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .alert-toggle-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .template-editor {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }

        .template-variables {
            background: #f5f5f5;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
        }

        .variable-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 3px 8px;
            border-radius: 4px;
            margin: 0 5px 5px 0;
            font-family: monospace;
            cursor: pointer;
            transition: all 0.2s;
        }

        .variable-badge:hover {
            background: #bbdefb;
        }

        .template-preview {
            border: 1px dashed #ccc;
            padding: 15px;
            margin-top: 15px;
            background: #fff;
            border-radius: 4px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .alert-type-toggle {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .form-horizontal .control-label {
                text-align: left;
                margin-bottom: 5px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')

                <div class="page-header">
                    <h1>
                        <i class="fa fa-bell"></i> Alert Settings
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Manage System Alerts
                        </small>
                    </h1>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        @include('setting.includes.buttons')
                        @include('includes.flash_messages')

                        <div class="alert-settings-container">
                            <div class="alert-header">
                                <h3 class="blue">
                                    <i class="fa fa-cog"></i> Alert Configuration
                                </h3>
                                <p class="text-muted">Manage how alerts are delivered for different system events</p>
                            </div>

                            @include('setting.alert.includes.table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('includes.scripts.dataTable_scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable with better UX
            // $('#dynamic-table').DataTable({
            //     "pageLength": 25,
            //     "responsive": true,
            //     "dom": '<"top"f>rt<"bottom"lip><"clear">',
            //     "language": {
            //         "search": "_INPUT_",
            //         "searchPlaceholder": "Search alerts..."
            //     }
            // });

            // Smooth scroll for event links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $($(this).attr('href')).offset().top - 20
                }, 500);
            });
        });
    </script>
@endsection
