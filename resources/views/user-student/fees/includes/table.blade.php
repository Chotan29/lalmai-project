<div class="col-xs-12">
    <style>
        .fees-table-modern {
            border: 1px solid #dfe7f1;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .fees-table-modern thead.header th {
            background: linear-gradient(180deg, #eaf3fb 0%, #dbeaf7 100%);
            color: #24527a;
            font-weight: 700;
            letter-spacing: 0.2px;
            text-align: center;
            vertical-align: middle;
            border-color: #d3e3f2;
            white-space: nowrap;
        }

        .fees-table-modern tbody td,
        .fees-table-modern tfoot td {
            text-align: center;
            vertical-align: middle;
            border-color: #e6edf5;
        }

        .fees-table-modern tbody tr.warning {
            background: #fffef5;
        }

        .fees-table-modern tbody tr.white-td {
            background: #fcfdff;
        }

        .fees-table-modern tbody tr:hover {
            background: #f4f9ff;
        }

        .fees-table-modern .label,
        .fees-table-modern .badge {
            border-radius: 4px;
        }

        .fees-table-modern tfoot tr {
            font-weight: 700;
        }
    </style>
    <div class="clearfix">
        <span class="pull-right tableTools-container"></span>
    </div>
    <!-- div.table-responsive -->
    <div class="table-responsive">
        <table id="dynamic-table" class="table table-striped table-bordered table-hover fees-table-modern">
            <thead class="header">
                <tr role="row">
                    <th class="sorting_disabled">{{ __('form_fields.student.fields.semester') }}</th>
                    <th class="sorting_disabled">FeeHead</th>
                        <th class="sorting_disabled">DueDate</th>
                        {{-- DueDate-2 and DueDate-3 removed (single installment, single due date) --}}
                        {{-- <th class="sorting_disabled">DueDate-2</th> --}}
                        {{-- <th class="sorting_disabled">DueDate-3</th> --}}
                    <th class="sorting_disabled">Amount </th>
                    <th class="sorting_disabled">Inst</th>
                    <th class="sorting_disabled">Method</th>
                    <th class="sorting_disabled">Date</th>
                    <th class="sorting_disabled">RefNo:</th>
                    <th class="sorting_disabled">Di </th>
                    <th class="sorting_disabled">Fi </th>
                    <th class="sorting_disabled">Paid </th>
                    <th class="sorting_disabled">Balance</th>
                    <th class="sorting_disabled" width="3%">Status</th>
                    <th class="sorting_disabled" width="10%">Remark</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($data['fee_master']) && $data['fee_master']->count() > 0)
                    @foreach ($data['fee_master'] as $feemaster)
                        <tr class="warning font12 odd" role="row" style="font-weight: 600;">

                            <td>{{ ViewHelper::getSemesterById($feemaster->semester) }}</td>
                            <td>{{ ViewHelper::getFeeHeadById($feemaster->fee_head) }}</td>
                            <td>{{ isset($feemaster->fee_due_date) ? \Carbon\Carbon::parse($feemaster->fee_due_date)->format('Y-m-d') : '' }}
                            </td>
                                {{-- fee_due_date2 and fee_due_date3 removed (single due date) --}}
                                {{-- <td>{{ isset($feemaster->fee_due_date2) ? \Carbon\Carbon::parse($feemaster->fee_due_date2)->format('Y-m-d') : '' }}</td> --}}
                                {{-- <td>{{ isset($feemaster->fee_due_date3) ? \Carbon\Carbon::parse($feemaster->fee_due_date3)->format('Y-m-d') : '' }}</td> --}}
                            <td>{{ $feemaster->fee_amount }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $feemaster->feeCollect()->sum('discount') ? $feemaster->feeCollect()->sum('discount') : '-' }}
                            </td>
                            <td>{{ $feemaster->feeCollect()->sum('fine') ? $feemaster->feeCollect()->sum('fine') : '-' }}
                            </td>
                            <td>{{ $feemaster->feeCollect()->sum('paid_amount') ? $feemaster->feeCollect()->sum('paid_amount') : '-' }}
                            </td>
                            <td>
                                @php
                                    $net_balance =
                                        $feemaster->fee_amount -
                                        ($feemaster->feeCollect()->sum('paid_amount') +
                                            $feemaster->feeCollect()->sum('discount')) +
                                        $feemaster->feeCollect()->sum('fine');
                                    $formatted_balance = number_format($net_balance, 2, '.', '');
                                @endphp

                                {{ $formatted_balance != '0.00' ? $formatted_balance : '' }}

                            </td>
                            <td>
                                @if ($net_balance == 0)
                                    <span class="label label-success">Paid</span>
                                @elseif($net_balance < 0)
                                    <span class="label label-warning">Negative</span>
                                @elseif($net_balance < $feemaster->fee_amount)
                                    <span class="label label-warning">Partial</span>
                                @else
                                    <span class="label label-danger">Due</span>
                                @endif
                            </td>
                            <td></td>
                        </tr>
                        @if (isset($data['fee_collection']) && $data['fee_collection']->count() > 0)
                            @php($i = 1)
                            @foreach ($data['fee_collection'] as $fee_collection)
                                @if ($fee_collection->fee_masters_id == $feemaster->id)
                                    <tr class="white-td even" role="row">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                            {{-- two extra tds removed (matched DueDate-2, DueDate-3 columns removed) --}}
                                            {{-- <td></td> --}}
                                            {{-- <td></td> --}}
                                        <td class="align-right"><i class="fa fa-arrow-right"></i></td>
                                        <td>
                                            <!-- <a href="#" data-toggle="popover" class="detail_popover" data-original-title="" title=""> {{ $i . ' of ' . $fee_collection->fee_masters_id }}</a>
                                                    <div class="fee_detail_popover" style="display: none">
                                                        <p class="text text-danger">{{ $fee_collection->note }}</p>
                                                    </div> -->
                                            {{ $fee_collection->installment_number }}
                                        </td>
                                        <td>{{ $fee_collection->payment_method }}</td>
                                        <td> {{ \Carbon\Carbon::parse($fee_collection->date)->format('Y-m-d') }}</td>
                                        <td>
                                            {{ $fee_collection->external_ref_no }}<br>
                                            {{ $fee_collection->ref_no }}
                                        </td>
                                        <td>{{ $fee_collection->discount ? $fee_collection->discount : '-' }}</td>
                                        <td>{{ $fee_collection->fine ? $fee_collection->fine : '-' }}</td>
                                        <td>{{ $fee_collection->paid_amount ? $fee_collection->paid_amount : '-' }}</td>
                                        <td>


                                        </td>
                                        <td>
                                            <!-- {{ $fee_collection->note }} -
                                                    Verified:{{ $fee_collection->verified_at }}
                                                            {{--  {{ ViewHelper::getUserNameId($fee_collection->created_by) }} --}} -->
                                            @if ($fee_collection->status == 1)
                                                {{-- <span class="label label-success">Success</span> --}}
                                                @if ($fee_collection->payment_method == 'Bank')
                                                    @if (isset($fee_collection->verified_at))
                                                        <span class="badge badge-success"><i
                                                                class="ace-icon fa fa-certificate"></i></span>
                                                    @else
                                                        <span class="badge badge-warning"><i
                                                                class="ace-icon fa fa-certificate"></i></span>
                                                    @endif
                                                @endif
                                            @else
                                                {{-- <span class="label label-danger">Cancel</span> --}}
                                                <span class="badge badge-danger"><i
                                                        class="ace-icon fa fa-certificate"></i></span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $fee_collection->note }}

                                        </td>

                                    </tr>
                                    @php($i++)
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr style="font-size: 14px; background: orangered;color: white;">
                    {{-- previously colspan=5 for DueDate-1/2/3 layout --}}
                    {{-- <td colspan="5">Total</td> --}}
                    <td colspan="3">Total</td>
                    <td>{{ $data['student']->fee_amount }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $data['student']->discount ? $data['student']->discount : '-' }}</td>
                    <td>{{ $data['student']->fine ? $data['student']->fine : '-' }}</td>
                    <td>{{ $data['student']->paid_amount ? $data['student']->paid_amount : '-' }}</td>
                    <td>
                        {{ $data['student']->balance ? number_format((float) $data['student']->balance, 2, '.', '') : '-' }}
                    </td>
                    <td>
                        @if ($data['student']->balance == 0)
                            <span class="label label-success">Paid</span>
                        @elseif($data['student']->balance < 0)
                            <span class="label label-warning">Negative</span>
                        @elseif($data['student']->balance < $data['student']->fee_amount)
                            <span class="label label-warning">Partial</span>
                        @else
                            <span class="label label-danger">Due</span>
                        @endif
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
