<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title">
                <i class="bi bi-file-earmark"></i>
                <h5 class="mb-0">Documents</h5>
            </div>
            @ability('super-admin', 'student-add-document')
            <a href="{{ route('student.add-document', ['id' => $data['student']->id]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle mr-1"></i> Upload Document
            </a>
            @endability
        </div>
        
        @if (isset($data['document']) && $data['document']->count() > 0)
            <div class="row">
                @foreach($data['document'] as $document)
                <div class="col-md-4">
                    <div class="document-card animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.1 }}s">
                        <div class="d-flex align-items-start">
                            <div class="document-icon">
                                @php
                                    $fileExtension = pathinfo($document->file, PATHINFO_EXTENSION);
                                    $icon = 'bi-file-earmark';
                                    if(in_array($fileExtension, ['pdf'])) $icon = 'bi-file-earmark-pdf';
                                    elseif(in_array($fileExtension, ['doc', 'docx'])) $icon = 'bi-file-earmark-word';
                                    elseif(in_array($fileExtension, ['xls', 'xlsx'])) $icon = 'bi-file-earmark-excel';
                                    elseif(in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) $icon = 'bi-file-earmark-image';
                                @endphp
                                <i class="bi {{ $icon }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $document->title }}</h6>
                                <p class="text-muted small mb-2">{{ $document->description }}</p>
                                <div class="d-flex flex-wrap">
                                    <a href="{{ asset('documents/student/'.ViewHelper::getStudentById($document->member_id).'/'.$document->file) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary mr-2 mb-2"
                                       data-toggle="tooltip" title="Download">
                                        <i class="bi bi-download mr-1"></i> Download
                                    </a>
                                    <a href="{{ asset('documents/student/'.ViewHelper::getStudentById($document->member_id).'/'.$document->file) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-secondary mr-2 mb-2"
                                       data-toggle="tooltip" title="Preview">
                                        <i class="bi bi-eye mr-1"></i> Preview
                                    </a>
                                    @ability('super-admin', 'student-delete-document')
                                    <a href="{{ route('student.delete-document', ['id' => $document->id]) }}" 
                                       class="btn btn-sm btn-outline-danger mb-2 bootbox-confirm"
                                       data-toggle="tooltip" title="Delete">
                                        <i class="bi bi-trash mr-1"></i> Delete
                                    </a>
                                    @endability
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info animate__animated animate__fadeIn">
                <i class="bi bi-info-circle mr-2"></i> No documents found.
            </div>
        @endif
    </div>
</div>