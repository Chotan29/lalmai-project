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
                            List
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                        @include('mcq.includes.buttons')
                        @include($view_path.'.includes.buttons')
                        @include('includes.flash_messages')
                    <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')
                            <div class="action-buttons align-right">
                                <a href="{{ route($base_route.'.edit', ['id' => encrypt($data['row']->id)]) }}" class="btn-primary btn-sm btn-success">
                                    <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i> Edit
                                </a>
                            </div>
                        <div class="table-responsive">
                            <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th width="20%">Column</th>
                                    <th>Value</th>
                                </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>Subject</td>
                                        <td>{{ViewHelper::getSubjectCodeById($data['row']->subjects_id).' - '.ViewHelper::getSubjectById($data['row']->subjects_id)}}</td>
                                    </tr>

                                    <tr>
                                        <td>Group</td>
                                        <td class="text-capitalize">{{ ViewHelper::getQuestionGroupById($data['row']->mcq_question_groups_id) }}</td>
                                    </tr>

                                    <tr>
                                        <td>Level</td>
                                        <td class="text-capitalize">{{ ViewHelper::getQuestionLevelById($data['row']->mcq_question_levels_id) }}</td>
                                    </tr>

                                    <tr>
                                        <td>Question</td>
                                        <td>{!! $data['row']->question !!}</td>
                                    </tr>

                                    <tr>
                                        <td>Image</td>
                                        <td>
                                            @if ($data['row']->image)
                                                <img src="{{ asset('images/'.$folder_name.'/'.$data['row']->image) }}" height="100px">
                                            @else
                                                <p>No image</p>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Explanation</td>
                                        <td>{!! $data['row']->explanation !!}</td>
                                    </tr>

                                    <tr>
                                        <td>Hints</td>
                                        <td>{!! $data['row']->hints !!}</td>
                                    </tr>

                                    <tr>
                                        <td>Mark</td>
                                        <td>{!! $data['row']->mark !!}</td>
                                    </tr>

                                    <tr>
                                        <td>Type</td>
                                        <td>{!! $data['row']->type !!}</td>
                                    </tr>

                                    <tr>
                                        <td>Create at</td>
                                        <td>{{ $data['row']->created_at }}</td>
                                    </tr>

                                    <tr>
                                        <td>Last updated at</td>
                                        <td>{{ $data['row']->updated_at }}</td>
                                    </tr>

                                    <tr>
                                        <td>Created by</td>
                                        <td>{{ $data['row']->created_by_name }}</td>
                                    </tr>

                                    <tr>
                                        <td>Last updated by</td>
                                        <td>{{ $data['row']->updated_by_name }}</td>
                                    </tr>

                                    <tr>
                                        <td>Status</td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="dropdown" class="btn btn-minier dropdown-toggle {{ $data['row']->status == 'active'?"btn-info":"btn-warning" }}" >
                                                    {{ $data['row']->status == 'active'?"Active":"In Active" }}
                                                    <i class="ace-icon fa fa-caret-down"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route($base_route.'.active', ['id' => encrypt($data['row']->id)]) }}" title="Active"><i class="ace-icon fa fa-check"></i> Active</a>
                                                    </li>

                                                    <li>
                                                        <a href="{{ route($base_route.'.in-active', ['id' => encrypt($data['row']->id)]) }}" title="In-Active"><i class="ace-icon fa fa-remove"></i> InActive</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Options</td>
                                        <td>
                                            @if(isset($data['row']->options))
                                                <div>
                                                    <table class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <th>SN</th>
                                                            <th>Option</th>
                                                            <th>Image</th>
                                                            <th>Answer</th>
                                                            <th>{{ __('common.status')}}</th>
                                                            <th></th>
                                                        </tr>
                                                        @php($i=1)
                                                        @foreach($data['row']->options as $option)
                                                            <tr>
                                                                <td>{{$i}}</td>
                                                                <td>
                                                                    {{$option->option}}
                                                                </td>
                                                                <td>
                                                                    @if ($option->image)
                                                                        <img src="{{ asset('images/'.$folder_name.'/'.$option->image) }}" height="100px">
                                                                    @else
                                                                        <p>No image</p>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($option->answer_status)
                                                                        <i class="ace-icon fa fa-check bigger-200"></i>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $option->status == 'active'?"btn-info":"btn-warning" }}" >
                                                                            {{ $option->status == 'active'?"Active":"In Active" }}
                                                                            <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                                                        </button>

                                                                        <ul class="dropdown-menu">
                                                                            <li>
                                                                                <a href="{{ route($base_route.'.option.active', ['id' => encrypt($option->id)]) }}" title="Active"><i class="fa fa-check btn-primary" aria-hidden="true"></i> Active</a>
                                                                            </li>

                                                                            <li>
                                                                                <a href="{{ route($base_route.'.option.in-active', ['id' => encrypt($option->id)]) }}" title="In-Active"><i class="fa fa-remove btn-warning" aria-hidden="true"></i> In-Active</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="action-buttons">
                                                                        <a href="{{ route($base_route.'.option.delete', ['id' => encrypt($option->id)]) }}" class="btn-danger btn-sm bootbox-confirm" title="Delete">
                                                                            <i class="fa fa-trash bigger-130" title="Delete"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @php($i++)
                                                        @endforeach
                                                    </table>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div><!-- /.table-responsive -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
        </div><!-- /.page-content -->
    </div>
    </div><!-- /.main-content -->
@endsection

@section('js')
    <!-- inline scripts related to this page -->
    @include('includes.scripts.delete_confirm')
    @include('includes.scripts.bulkaction_confirm')
    @include('includes.scripts.dataTable_scripts')

@endsection