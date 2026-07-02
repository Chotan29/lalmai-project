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
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                   {{-- <tr>
                        <td class="text-center" colspan="7">{!! $data['staff']->appends($data['filter_query'])->links() !!}</td>
                    </tr>--}}
                    <tr>
                        <th>{{ __('common.s_n')}}</th>
                        <th>{{ __('form_fields.student.fields.reg_no') }}</th>
                        <th>Staff Name</th>
                        <th>Designation</th>
                        <th>Book Taken</th>
                        <th>Eligible</th>
                        <th>{{ __('common.status')}}</th>
                    </tr>
                </thead>
                <tbody>
                @if (isset($data['staff']) && $data['staff']->count() > 0)
                    @php($i=1)
                    @foreach($data['staff'] as $staff)
                        <tr>
                            <td>{{ $i }}</td>
                            <td><a href="{{ route($base_route.'.view', ['id' => $staff->member_id]) }}">{{ $staff->reg_no }}</a></td>
                            <td><a href="{{ route($base_route.'.view', ['id' => $staff->member_id]) }}"> {{ $staff->first_name.' '.$staff->middle_name.' '. $staff->last_name }}</a></td>
                            <td>{{ ViewHelper::getDesignationId($staff->designation) }}</td>
                            <td> {{ $staff->taken }}</td>
                            <td> {{ $staff->eligible }}</td>
                            <td>
                                <div class="btn-group">
                                    <span data-toggle="dropdown" class="btn-primary btn-sm {{ $staff->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $staff->status == 'active'?"Active":"In Active" }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="11">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>