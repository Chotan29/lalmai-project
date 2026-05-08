<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title">
                <i class="bi bi-sticky"></i>
                <h5 class="mb-0">Notes</h5>
            </div>
            @ability('super-admin', 'student-add-note')
            <a href="{{ route('student.add-note', ['id' => $data['student']->id]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle mr-1"></i> Add Note
            </a>
            @endability
        </div>
        
        @if (isset($data['note']) && $data['note']->count() > 0)
            <div class="info-card animate__animated animate__fadeIn">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%">S.N.</th>
                                <th>Subject</th>
                                <th>Note Description</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($i=1)
                            @foreach($data['note'] as $note)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $note->subject }}</td>
                                    <td>{{ $note->note }}</td>
                                    <td>
                                        @ability('super-admin', 'student-delete-note')
                                        <a href="{{ route('student.delete-note', ['id' => $note->id]) }}" 
                                           class="btn btn-sm btn-outline-danger bootbox-confirm"
                                           data-toggle="tooltip" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                        @endability
                                    </td>
                                </tr>
                                @php($i++)
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-info animate__animated animate__fadeIn">
                <i class="bi bi-info-circle mr-2"></i> No notes found.
            </div>
        @endif
    </div>
</div>