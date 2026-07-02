@extends('layouts.master')

@section('css')
    <style>
        .compact-header {
            padding: 8px 15px;
            margin: 10px 0 15px;
        }
        .fee-summary {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .nav-tabs > li > a {
            padding: 10px 15px;
        }
        .tab-content {
            padding: 15px 0;
        }
        .table-responsive {
            margin-bottom: 0;
        }
        @media (max-width: 767px) {
            .compact-header h1 {
                font-size: 20px;
                margin: 5px 0;
            }
            .nav-tabs > li > a {
                padding: 8px 10px;
                font-size: 13px;
            }
        }
    </style>
    <link href="{{ asset('assets/css/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                
                <div class="page-header compact-header">
                    <h1>
                        @include($view_path . '.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Fee Ledger
                        </small>
                    </h1>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        @include('account.includes.buttons')
                        @include('account.fees.includes.buttons')
                        @include('includes.flash_messages')
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-horizontal">
                            @include($view_path . '.collect.includes.profile')
                            @include($view_path . '.collect.includes.fee_master_add_model')
                        </div>
                    </div>
                </div>

                

                <div class="tabbable">
                    <ul class="nav nav-tabs padding-12 hidden-print">
                        <li class="active">
                            <a data-toggle="tab" href="#fees">
                                <i class="green ace-icon fa fa-calculator"></i>
                                Fees
                            </a>
                        </li>

                        <li>
                            <a data-toggle="tab" href="#pay-online">
                                <i class="blue ace-icon fa fa-credit-card"></i>
                                Online Payments
                                @if (isset($data['onlinePayments']) && $data['onlinePayments']->where('status', 'in-active')->count() > 0)
                                <span class="badge badge-warning">{{ $data['onlinePayments']->where('status', 'in-active')->count()}}</span>
                                @endif
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content no-border">
                        <div id="fees" class="tab-pane in active">
                            @include($view_path . '.collect.includes.table')
                        </div>

                        <div id="pay-online" class="tab-pane">
                            @include($view_path . '.collect.includes.online-payment-table')
                        </div>
                    </div>
                </div>

                @include($view_path . '.collect.includes.add_model')
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('includes.scripts.jquery_validation_scripts')
    @include('includes.scripts.inputMask_script')
    @include('account.fees.collection.collect.includes.modal_values_script')
    @include('includes.scripts.alert_confirm')
    @include('includes.scripts.delete_confirm')
    
    <script>
        $(document).ready(function() {
            // Uppercase conversion
            $('.upper').keyup(function() {
                this.value = this.value.toUpperCase();
            });

            // Adjust chosen select width
            $('.chosen-container-single').css('width', '380px');

            // Bulk action handling
            $('.bulk-action-btn').click(function() {
                var $chkIds = $('input[name="chkIds[]"]:checked');
                
                if ($chkIds.length <= 0) {
                    toastr.info("Please select at least one record.", "Info:");
                    return false;
                }

                $('#bulk_action_form').submit();
            });
        });
    </script>

    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.datepicker_script')
@endsection