<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        <div class="clearfix">

            <span class="pull-right tableTools-container"></span>
        </div>
        {{--<div class="table-header">
            {{ $panel }}  Record list on table. Filter {{ $panel }} using the filter.
        </div>--}}
    <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th class="center">
                        <label>
                            <input type="checkbox" class="ace" />
                            <span class="lbl"></span>
                        </label>
                    </th>
                    <th>{{ __('common.s_n')}}</th>
                    <th>Detail</th>
                    <th>Visible For</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['rows']) && $data['rows']->count() > 0)
                    @php($i=1)
                    {{--{!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}--}}
                    @foreach($data['rows'] as $row)
                        <tr>
                            <td class="center">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ encrypt($row->id) }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>
                                {{$i}}
                            </td>

                            <td>
                                <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">

                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <a href="#{{$row->id}}" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                                                <i class="ace-icon fa fa-arrow-down"></i>
                                                &nbsp;
                                                {{ $row->title }}
                                                <hr class="hr-2">
                                                <span class="label label-sm label-primary">
                                                    Publish Date: {{ \Carbon\Carbon::parse($row->publish_date)->format('Y-m-d')}}
                                                </span>
                                                <span class="label label-sm label-primary">
                                                    End Date: {{ \Carbon\Carbon::parse($row->end_date)->format('Y-m-d')}}
                                                </span>
                                                <span class="label label-sm label-primary">
                                                    Created On - {{ date('jS M, Y', strtotime($row->created_at)) }}
                                                </span>
                                                <span class="label label-sm label-success">
                                                    Last Updated - {{ date('jS M, Y', strtotime($row->updated_at)) }}
                                                </span>
                                            </a>
                                        </div>

                                        <div class="panel-collapse collapse" id="{{$row->id}}" style="height: 0px;">
                                            <div class="panel-body">

                                                {!! $row->message !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(isset($row->display_group))
                                    @php($groups = explode(',',$row->display_group))
                                    @foreach($groups as $group)
                                        {{\App\Facades\WebsiteViewHelperFacade::getRoleNameId($group)}}
                                        <hr class="hr-2">
                                    @endforeach
                                @endif
                            </td>

                            <td>
                                <div class="btn-group">
                                    <a class="btn-primary btn-sm" href="{{ route($base_route.'.edit', ['id' => $row->id]) }}"><i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;Edit</a>
                                    <a class="btn-danger btn-sm bootbox-confirm" href="{{ route($base_route.'.delete', ['id' => $row->id]) }}"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;Delete</a>
                                </div>
                            </td>

                        </tr>
                    @php($i++)

                    @endforeach

                    {{--{!! Form::close() !!}--}}

                    <tr>
                        <td colspan="7">{!! $data['rows']->appends($data['filter_query'])->links() !!}</td>
                    </tr>

                @else
                    <tr><td colspan="7">No data fount.</td></tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
