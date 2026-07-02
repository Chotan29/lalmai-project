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
            {{--{!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}--}}
            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <td class="text-center" colspan="3">{!! $data['faculties_sem_assets']->appends($data['filter_query'])->links() !!}</td>
                    </tr>
                    <tr>
                        <th>{{ __('common.s_n')}}</th>
                        <th width="45%">{{__('form_fields.student.fields.faculty')}}</th>
                        <th width="50%">Semester/Sec.</th>
                    </tr>
                </thead>
                <tbody>
                @if (isset($data['faculties_sem_assets']) && $data['faculties_sem_assets']->count() > 0)
                    @php($i=1)
                    @foreach($data['faculties_sem_assets'] as $faculties)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ ViewHelper::getFacultyTitle($faculties->id)}}</td>
                            {{--'semesters_id', 'assets_id', 'quantity', 'status'--}}
                            <td>
                                @if (isset($faculties->semester) && $faculties->semester()->count() > 0)
                                    @foreach($faculties->semester as $semester)
                                        @if (isset($semester->assets) && $semester->assets()->count() > 0)

                                        <div class="label label-info label-large">
                                            {{ViewHelper::getSemesterTitle($semester->id)}}
                                        </div>
                                        <table class="table-responsive">
                                            <tr>
                                                <td>
                                                    <table class="table-responsive">
                                                        <tr>
                                                            <th>Assets</th>
                                                            <th>Qty</th>
                                                            <th></th>
                                                        </tr>
                                                        @foreach($semester->assets as $assets)
                                                        <tr>
                                                            <td>{{ViewHelper::getAssetsById($assets->assets_id)}} </td>
                                                            <td> {{$assets->quantity}}</td>
                                                            <td>
                                                                <div class="action-buttons">
                                                                    <a href="{{ route($base_route.'.delete', ['id' => $assets->id]) }}" class="red bootbox-confirm">
                                                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                            <hr class="hr-4">
                                        @endif
                                    @endforeach

                                @endif
                            </td>

                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="10">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        {{--{!! Form::close() !!}--}}
    </div>
</div>


