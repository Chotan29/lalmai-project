<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" id="dynamic-table">
        <thead>
            <tr>
                <th>{{ __('common.s_n') }}</th>
                <th>Main Fee Head</th>
                <th>Session</th>
                <th>Sub Heads</th>
                <th>Total</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if (isset($data['fee_head_groups']) && $data['fee_head_groups']->count() > 0)
                @php($i = 1)
                @foreach($data['fee_head_groups'] as $group)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>
                            {{ $group->title }}
                            @if($group->is_locked)
                                <span class="label label-warning" title="Fees have been collected against this">
                                    <i class="fa fa-lock" aria-hidden="true"></i>&nbsp;Locked
                                </span>
                            @endif
                        </td>
                        <td>{{ $group->session }}</td>
                        <td>
                            {{ $group->items->count() }}
                            @php($departmentTotal = $group->items->filter(function ($item) {
                                return $item->feeHead && $item->feeHead->collected_by == 'department';
                            })->sum('amount'))
                            @if($departmentTotal > 0)
                                <span class="label label-info" title="Collected by the college, owed to the departments">
                                    Dept {{ number_format($departmentTotal, 2) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            {{ number_format($group->total_amount, 2) }}
                            @if(!$group->isBalanced())
                                <span class="label label-danger" title="Sub heads do not add up to the total">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    {{ number_format($group->itemsTotal(), 2) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($group->status == 'active')
                                <span class="label label-success">Active</span>
                            @else
                                <span class="label label-default">In-Active</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="btn-group">
                                @ability('super-admin', 'fees-fee-head-group-edit')
                                    <a class="btn btn-xs btn-info" title="Edit"
                                       href="{{ route($base_route.'.edit', ['id' => encrypt($group->id)]) }}">
                                        <i class="ace-icon fa fa-pencil bigger-120"></i>
                                    </a>

                                    <a class="btn btn-xs btn-primary" title="Copy for a new session"
                                       href="{{ route($base_route.'.duplicate', ['id' => encrypt($group->id)]) }}">
                                        <i class="ace-icon fa fa-copy bigger-120"></i>
                                    </a>

                                    @if($group->status == 'active')
                                        <a class="btn btn-xs btn-warning" title="In-Active"
                                           href="{{ route($base_route.'.in-active', ['id' => encrypt($group->id)]) }}">
                                            <i class="ace-icon fa fa-times bigger-120"></i>
                                        </a>
                                    @else
                                        <a class="btn btn-xs btn-success" title="Active"
                                           href="{{ route($base_route.'.active', ['id' => encrypt($group->id)]) }}">
                                            <i class="ace-icon fa fa-check bigger-120"></i>
                                        </a>
                                    @endif
                                @endability
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
            {{-- No colspan placeholder row here on purpose. DataTables walks every cell in the
                 body and throws on a row that is narrower than the header; that exception stops
                 jQuery running the ready callbacks queued after it, which silently killed the
                 Add Sub Head button and the running total on this page. An empty tbody lets
                 DataTables print its own "no data" line instead. --}}
        </tbody>
    </table>
</div>
