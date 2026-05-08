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
                    <tr>
                        <td class="text-center" colspan="8">{!! $data['staff']->appends($data['filter_query'])->links() !!}</td>
                    </tr>
                    <tr>
                        <th>{{ __('common.s_n')}}</th>
                        <th>{{ __('form_fields.student.fields.reg_no') }}</th>
                        <th>Staff Name</th>
                        <th>Designation</th>
                        <th>Request On</th>
                        <th>Book</th>
                    </tr>
                </thead>
                <tbody>
                @if (isset($data['staff']) && $data['staff']->count() > 0)
                    @php($i=1)
                    @foreach($data['staff'] as $staff)
                        <tr>
                            <td>{{ $i }}</td>
                            <td><a href="{{ route($base_route.'.staff.view', ['id' => $staff->member_id]) }}">{{ $staff->reg_no }}</a></td>
                            <td><a href="{{ route($base_route.'.staff.view', ['id' => $staff->member_id]) }}"> {{ $staff->first_name.' '.$staff->middle_name.' '. $staff->last_name }}</a></td>
                            <td>{{ ViewHelper::getDesignationId($staff->designation) }}</td>
                            <td> {{ \Carbon\Carbon::parse($staff->requested_date)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route($base_route.'.book.view', ['id' => $staff->book_masters_id]) }}">
                                    {{ ViewHelper::getBookMasterById($staff->book_masters_id) }}
                                </a>
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