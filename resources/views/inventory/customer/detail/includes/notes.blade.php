<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;Notes List</h4>

        <!-- div.table-responsive -->
        <div class="table-responsive">
            {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}

            <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                <thead>
                <tr>
                    <th width="5%">S.N.</th>
                    <th>Subject</th>
                    <th>Note Description</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @if (isset($data['note']) && $data['note']->count() > 0)
                    @php($i=1)
                    @foreach($data['note'] as $note)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $note->subject }}</td>
                            <td>{{ $note->note }}</td>
                            <td>
                                <div class="action-buttons">

                                    <a href="{{ route('inventory.customer.note.edit', ['id' => encrypt($note->id)]) }}" class="btn-primary btn-sm btn-success">
                                        <i class="ace-icon fa fa-pencil bigger-130" title="Edit"></i>
                                    </a>

                                    <a href="{{ route('inventory.customer.note.delete', ['id' => encrypt($note->id)]) }}" class="btn-primary btn-sm btn-danger bootbox-confirm" >
                                        <i class="fa fa-trash bigger-130" title="Delete"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="align-left" >No data found.</td>
                    </tr>
                @endif
                </tbody>
            </table>
            {!! Form::close() !!}
        </div>
    </div>
</div>