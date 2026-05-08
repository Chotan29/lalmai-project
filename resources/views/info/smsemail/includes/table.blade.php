<div class="row">
    <div class="col-xs-12">
    @include('includes.data_table_header')
    <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th class="center">
                        <label class="pos-rel">
                            <input type="checkbox" class="ace" />
                            <span class="lbl"></span>
                        </label>
                    </th>
                    <th>{{ __('common.s_n')}}</th>
                    <th>Message Detail</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['rows']) && $data['rows']->count() > 0)
                    @php($i = 1)
                    @foreach($data['rows'] as $row)
                        <tr>
                            <td class="center" width="3%">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ $row->id }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td width="4%">{{ $i }}</td>
                            <td>
                                <table class="table table-striped table-bordered table-hover">
                                    <tr>
                                        <th width="20%">
                                            DATE & TIME
                                        </th>
                                        <td>
                                            {{ \Carbon\Carbon::parse($row->created_at)->format('D m, Y H:i:s') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">
                                            SUBJECT
                                        </th>
                                        <td>
                                            {{ $row->subject }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">
                                            MESSAGE
                                        </th>
                                        <td>
                                            {!! $row->message !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Group & Counting</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            @if(isset($row->group) && $row->group != '')
                                                @php($groups = explode(',',$row->group))
                                                @php($i=1)
                                                <table class="table table-striped table-bordered table-hover">
                                                    <tr>
                                                        <th colspan="2">Group</th>
                                                    </tr>
                                                    @foreach($groups as $group)
                                                        <tr>
                                                            <td>{{$i}}</td>
                                                            <td>
                                                                {{$group}}
                                                            </td>
                                                        </tr>
                                                        @php($i++)
                                                    @endforeach
                                                </table>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->sms == 1 && $row->email == 1)
                                                <div class="label label-info arrowed-right arrowed-in">
                                                    SMS
                                                </div>
                                                <hr class="hr-2">
                                                <div class="label label-primary arrowed-right arrowed-in">
                                                    Email
                                                </div>
                                            @elseif($row->sms == 1)
                                                @if(isset($row->ref) && $row->ref != 0)
                                                    @php($refs = explode(',',$row->ref))
                                                    @php($i=1)
                                                    <table class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <th colspan="2">SMS - {{count($refs)}}</th>
                                                        </tr>
                                                        @foreach($refs as $ref)
                                                            <tr>
                                                                <td>{{$i}}</td>
                                                                <td>
                                                                    {{$ref}}
                                                                </td>
                                                            </tr>
                                                            @php($i++)
                                                        @endforeach
                                                    </table>
                                                @endif
                                            @elseif($row->email == 1)
                                                @if(isset($row->ref) && $row->ref != '')
                                                    @php($refs = explode(',',$row->ref))
                                                    @php($i=1)
                                                    <table class="table table-striped table-bordered table-hover">
                                                        <tr>
                                                            <th colspan="2">E-mail - {{count($refs)}}</th>
                                                        </tr>
                                                        @foreach($refs as $ref)
                                                            <tr>
                                                                <td>{{$i}}</td>
                                                                <td>
                                                                    {{$ref}}
                                                                </td>
                                                            </tr>
                                                            @php($i++)
                                                        @endforeach
                                                    </table>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th width="20%">
                                            Created By
                                        </th>
                                        <td>
                                            {!! $row->created_by_name !!}
                                        </td>
                                    </tr>
                                </table>
                            </td>


                            <td>
                                <div class="hidden-sm hidden-xs action-buttons">
                                    <a href="{{ route($base_route.'.delete', ['id' => $row->id]) }}"  class="btn-danger btn-sm bootbox-confirm">
                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                    </a>
                                </div>
                                <div class="hidden-md hidden-lg">
                                    <div class="inline pos-rel">
                                        <button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
                                            <i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
                                            <li>
                                                <a href="{{ route($base_route.'.delete', ['id' => $row->id]) }}" class="tooltip-error bootbox-confirm" data-rel="tooltip" title="Delete">
                                                    <span class="red ">
                                                        <i class="fa fa-trash bigger-120"></i>
                                                    </span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                    {!! Form::close() !!}

                @else
                    <tr><td colspan="7">No data found.</td></tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>