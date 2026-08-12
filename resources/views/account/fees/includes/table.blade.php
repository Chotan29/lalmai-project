<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        <div class="clearfix hidden-print">
            {{-- Print carries the filter across unchanged and adds print=1, so the sheet that
                 comes out is the list on screen - every row of it, not just this page. --}}
            <a class="btn btn-primary btn-sm pull-left"
               href="{{ $data['url'] }}?{{ http_build_query(array_merge(request()->except('page', 'print'), ['print' => 1])) }}"
               target="_blank">
                <i class="fa fa-print" aria-hidden="true"></i>&nbsp;Print Report
            </a>
            <span class="pull-right tableTools-container"></span>
        </div>
        {{--<div class="table-header">
            {{ $panel }}  Record list on table. Filter {{ $panel }} using the filter.
        </div>--}}
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="dynamic-table-1" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <td class="text-center" colspan="16">{!! $data['feesCollection']->appends($data['filter_query'])->links() !!}</td>
                        </tr>
                        <tr>
                            <th >S.N.</th>
                            <th>Reg. Num.</th>
                            <th>Name</th>
                            <th>{{__('form_fields.student.fields.semester')}}</th>
                            <th>Head</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Fi.</th>
                            <th>Dis.</th>
                            <th>Method</th>
                            <th>RefNo</th>
                            <th>BankRef</th>
                            <th>Status</th>
                            <th>Note</th>
                            <th>User</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if (isset($data['feesCollection']) && $data['feesCollection']->count() > 0)
                        @php($i=1)
                        @foreach($data['feesCollection'] as $feesCollection)
                            <tr>
                                <td>{{ $i }}</td>
                                <td> <a href="{{ route('student.view', ['id' => encrypt($feesCollection->students_id)]) }}">{{ $feesCollection->reg_no }}</a></td>
                                <td> <a href="{{ route('student.view', ['id' => encrypt($feesCollection->students_id)]) }}">{{ $feesCollection->first_name.' '.$feesCollection->middle_name.' '. $feesCollection->last_name }}</a></td>
                                <td> {{  ViewHelper::getSemesterTitle( $feesCollection->semester ) }}</td>
                                <td>{{ ViewHelper::getFeeHeadById($feesCollection->fee_head) }}</td>
                                <td>{{ \Carbon\Carbon::parse($feesCollection->date)->format('Y-m-d')}} </td>
                                <td class="text-right">{{ $feesCollection->paid_amount }}</td>
                                <td class="text-right">{{ $feesCollection->fine }}</td>
                                <td class="text-right">{{ $feesCollection->discount }}</td>
                                <td>{{ $feesCollection->payment_method }}
                                    @if($feesCollection->payment_method =='Bank')
                                        @if(isset($feesCollection->verified_at))
                                            <span class="badge badge-success"><i class="ace-icon fa fa-certificate"></i></span>
                                        @else
                                            <span class="badge badge-warning"><i class="ace-icon fa fa-certificate"></i></span>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $feesCollection->ref_no }}</td>
                                <td>{{ $feesCollection->external_ref_no }}</td>
                                
                                <td>
                                    <button  class="btn-primary btn-sm dropdown-toggle {{ $feesCollection->fc_status == 1?"btn-success":"btn-danger" }}" >
                                                {{ $feesCollection->fc_status == 1?"Success":"Cancel" }}
                                            </button>

                                </td>
                                <td class="small">{{ $feesCollection->note }}</td>
                                <td> {{  ViewHelper::getUserNameId( $feesCollection->created_by ) }}</td>
                                <td>
                                    <div class="btn-primary btn-sm action-buttons">
                                        <a class="white" href="{{ route('account.fees.collection.view', ['id' => encrypt($feesCollection->students_id)]) }}">
                                            <i class="ace-icon fa fa-calculator bigger-130"></i>&nbsp;
                                        </a>
                                    </div>
                                </td>
                            @php($i++)
                        @endforeach
                    @else
                        <tr>
                            <td colspan="16">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                        </tr>
                    @endif
                    </tbody>
                    <tfoot>
                        {{-- The whole filter, not this page of it. Adding up the rows on screen
                             showed the sum of twenty-five receipts for a filter matching three
                             hundred, and looked like a real answer. Counted in the database over
                             the same query the list is drawn from. --}}
                        <tr style="font-size: 14px; background: orangered;color: white;">
                            <td colspan="6" class="text-right">Total ({{ number_format($data['totals']['row_count']) }} receipts)</td>
                            <td class="text-right">{{ number_format($data['totals']['paid_amount'], 2) }}</td>
                            <td class="text-right">{{ number_format($data['totals']['fine'], 2) }}</td>
                            <td class="text-right">{{ number_format($data['totals']['discount'], 2) }}</td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                        </tr>
                    </tfoot>
                </table>
        </div>
        {!! Form::close() !!}
    </div>
</div>


