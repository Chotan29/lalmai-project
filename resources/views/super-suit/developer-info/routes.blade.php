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
                    <div class="col-xs-12 ">
                        @include($view_path.'.includes.buttons')
                        @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')
                    </div><!-- /.col -->
                    <div class="row">
                        <div class="col-xs-12">
                            <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
                            <!-- div.table-responsive -->
                            <!-- div.table-responsive -->
                            <div class="table-responsive">
                                <div class="clearfix table-head-menu">
                                    <span class="pull-right tableTools-container"></span>
                                </div>
                                <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>S.N.</th>
                                            <th>HTTP Method</th>
                                            <th>Route</th>
                                            <th>Name</th>
                                            <th>Corresponding Action</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                    @if($data['route-info'] && $data['route-info']->count()>0)
                                        @php($i=1)
                                        @foreach($data['route-info'] as $value)
                                        <tr>
                                            <td>{{$i}} </td>
                                            <td>{{$value->methods()[0]}} </td>
                                            <td>{{$value->uri()}} </td>
                                            <td>{{$value->getName()}} </td>
                                            <td>{{$value->getActionName()}} </td>
                                        </tr>
                                        @php($i++)
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                                {{--{!! Form::close() !!}--}}

                            </div>
                        </div>
                    </div>
                </div><!-- /.row -->

            </div>
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
    @endsection


@section('js')
    @include('includes.scripts.jquery_validation_scripts')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')
    @endsection