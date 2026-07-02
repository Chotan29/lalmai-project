@extends('layouts.master')

@section('css')

@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Detail
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    @include('application.includes.buttons')
                    <div class="col-xs-12 ">
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <tr>
                                                <td colspan="2">
                                                    <h4 class="header large lighter blue text-uppercase">
                                                        <i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;
                                                        {{ ViewHelper::getApplicationTypeById($data['application']->application_type_id) }} Application -
                                                        <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $data['application']->status == 'active'?"btn-info":"btn-warning" }}" >
                                                            {{ $data['application']->status == 'active'?"Read / View":"UnSeen" }}
                                                        </button>

                                                        @if($data['application_type']->need_approve==1)
                                                            -
                                                            <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $data['application']->approve_status == 1?"btn-success":"btn-warning" }}" >
                                                                {{ $data['application']->approve_status==1?"Approved":"Pending" }}
                                                            </button>
                                                        @endif
                                                    </h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-uppercase bold-text">Application Type</td>
                                                <td>
                                                    {{ ViewHelper::getApplicationTypeById($data['application']->application_type_id) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-uppercase bold-text">Start Date</td>
                                                <td>{{ $data['application']->date }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-uppercase bold-text">End Date</td>
                                                <td>{{ $data['application']->end_date }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-uppercase bold-text">Subject</td>
                                                <td>{{ $data['application']->subject }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-uppercase bold-text">Message</td>
                                                <td>{{ $data['application']->message }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-uppercase bold-text">AttachFile</td>
                                                <td>
                                                    @if ($data['application']->file)
                                                        <a href="{{ asset('application'.DIRECTORY_SEPARATOR.$data['application']->file) }}" target="_blank">
                                                            <i class="ace-icon fa fa-download bigger-120"></i> &nbsp;{{ $data['application']->title }} Attached File
                                                        </a>
                                                    @else
                                                        <p>No File.</p>
                                                    @endif

                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-uppercase bold-text">Status</td>
                                                <td>
                                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $data['application']->status == 'active'?"btn-info":"btn-warning" }}" >
                                                        {{ $data['application']->status == 'active'?"Read / View":"UnSeen" }}
                                                    </button>
                                                </td>
                                            </tr>
                                            @if($data['application_type']->need_approve==1)
                                                <tr>
                                                    <td class="text-uppercase bold-text">Approve Status</td>
                                                    <td>
                                                        @if($data['application']->approve_status==0)
                                                            <a href="{{ route('application.approve', ['id' => encrypt($data['application']->id)]) }}" class="btn-primary btn-sm dropdown-toggle btn-primary" title="Active"><i class="fa fa-check" aria-hidden="true"></i> Approve</a>
                                                        @endif
                                                        @if($data['application']->approve_status==1)
                                                                Approved
                                                                <a href="{{ route('application.un-approve', ['id' => encrypt($data['application']->id)]) }}" class="btn-primary btn-sm dropdown-toggle btn-danger" title="In-Active"><i class="fa fa-remove" aria-hidden="true"></i> Un Approve</a>

                                                        @endif
                                                    </td>
                                                </tr>

                                            @endif
                                            <tr>
                                                <td>Created by</td>
                                                <td>{{ $data['application']->created_by }}</td>
                                            </tr>
                                            <tr>
                                                <td>Last updated by</td>
                                                <td>{{ $data['application']->updated_by_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-uppercase bold-text">Created On</td>
                                                <td>{{ \Carbon\Carbon::parse($data['application']->created_at)->format('M d, Y')}} </td>
                                            </tr>
                                            <tr>
                                                <td class="text-uppercase bold-text">Update On</td>
                                                <td>{{ \Carbon\Carbon::parse($data['application']->updated_at)->format('M d, Y')}} </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                            </div><!-- /.row -->

                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    <!-- inline scripts related to this page -->
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    @include('includes.scripts.datepicker_script')

@endsection