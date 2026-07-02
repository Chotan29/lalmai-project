<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }}  List</h4>
        <div class="clearfix">

    <span class="easy-link-menu">
        <a class="btn-primary btn-sm bulk-action-btn" attr-action-type="active"><i class="fa fa-check" aria-hidden="true"></i>&nbsp;{{__('common.active_button')}}</a>
        <a class="btn-warning btn-sm bulk-action-btn" attr-action-type="in-active"><i class="fa fa-remove" aria-hidden="true"></i>&nbsp{{__('common.in_active_button')}}</a>
        <a class="btn-danger btn-sm bulk-action-btn" attr-action-type="delete"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{__('common.delete_button')}}</a>
    </span>

            <span class="pull-right tableTools-container"></span>
        </div>
        {{--<div class="table-header">
            {{ $panel }}  Record list on table. Filter {{ $panel }} using the filter.
        </div>--}}
        <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => 'application.bulk-action', 'id' => 'bulk_action_form']) !!}
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
                        <th>Type</th>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Created By</th>
                        <th>{{ __('common.status')}}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @if (isset($data['application']) && $data['application']->count() > 0)
                    @php($i=1)
                    @foreach($data['application'] as $application)
                        <tr>
                            <td class="center first-child">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ $application->id }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>
                            <td>{{ $i }}</td>
                            <td>{{ isset($application->application_type_id)?ViewHelper::getApplicationTypeById($application->application_type_id):'' }}</td>
                            <td>
                            {{\Carbon\Carbon::parse($application->date)->format('Y-m-d')}}
{{--                            TO--}}
{{--                                {{\Carbon\Carbon::parse($application->end_date)->format('Y-m-d')}}--}}
                            </td>
                            <td>
                                <a href="{{ route('application.view', ['id' => encrypt($application->id)]) }}" >
                                    {{ $application->subject }}
                                </a>
                            </td>
                            <td> {{$application->created_by_name}} </td>
                            <td>
{{--                                <h4 class="header large lighter blue text-uppercase">--}}
{{--                                    <i class="fa fa-list-alt" aria-hidden="true"></i>&nbsp;--}}
{{--                                    {{ ViewHelper::getApplicationTypeById($application->application_type_id) }} Application ---}}
{{--                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $application->status == 'active'?"btn-info":"btn-warning" }}" >--}}
{{--                                        {{ $application->status == 'active'?"Read / View":"UnSeen" }}--}}
{{--                                    </button>--}}

{{--                                    @if($data['applicationType']->need_approve ==1)--}}
{{--                                        ---}}
{{--                                        <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $application->approve_status == 1?"btn-success":"btn-warning" }}" >--}}
{{--                                            {{ $application->approve_status==1?"Approved":"Pending" }}--}}
{{--                                        </button>--}}
{{--                                    @endif--}}
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn-primary btn-sm dropdown-toggle {{ $application->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $application->status == 'active'?"Active":"In Active" }}
                                        <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('application.active', ['id' => encrypt($application->id)]) }}" title="Active"><i class="fa fa-check" aria-hidden="true"></i></a>
                                        </li>

                                        <li>
                                            <a href="{{ route('application.in-active', ['id' => encrypt($application->id)]) }}" title="In-Active"><i class="fa fa-remove" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @ability('super-admin','application-view')
                                    <a href="{{ route('application.view', ['id' => encrypt($application->id)]) }}" class="btn-primary btn-sm">
                                        <i class="ace-icon fa fa-eye bigger-130" title="View"></i>
                                    </a>
                                    @endability

                                    @ability('super-admin','application-edit')
                                    <a href="{{ route('application.edit', ['id' => encrypt($application->id)]) }}" class="btn-success btn-sm">
                                        <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                    </a>
                                    @endability

                                    @ability('super-admin','application-delete')
                                    <a href="{{ route('application.delete', ['id' => encrypt($application->id)]) }}" class="btn-danger btn-sm bootbox-confirm" >
                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                    </a>
                                    @endability
                                </div>
                            </td>

                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="12">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        {!! Form::close() !!}
    </div>
</div>


