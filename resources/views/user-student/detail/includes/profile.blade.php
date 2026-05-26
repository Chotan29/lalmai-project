<div class="row">
    <div class="col-md-4">
        <!-- Basic Information -->
        <div class="info-card animate__animated animate__fadeInUp">
            <h5><i class="bi bi-person-badge me-2"></i> Basic Information</h5>
            @foreach([
                'Full Name' => trim($data['student']->first_name . ' ' . $data['student']->middle_name . ' ' . $data['student']->last_name),
                'Date of Birth' => $data['student']->date_of_birth ? \Carbon\Carbon::parse($data['student']->date_of_birth)->format('d M, Y') : 'N/A',
                'Gender' => $data['student']->gender ?: 'N/A',
                'Blood Group' => $data['student']->blood_group ?: 'N/A',
                'Religion' => $data['student']->religion ?: 'N/A',
                'Nationality' => $data['student']->nationality ?: 'N/A',
            ] as $label => $value)
                <div class="info-item">
                    <div class="info-label">{{ $label }}</div>
                    <div class="info-value">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <!-- Contact Information -->
        <div class="info-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <h5><i class="bi bi-telephone me-2"></i> Contact Information</h5>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $data['student']->email ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Mobile</div>
                <div class="info-value">
                    @if ($data['student']->mobile_1)
                        <a href="tel:{{ $data['student']->mobile_1 }}" class="text-decoration-none">{{ $data['student']->mobile_1 }}</a>
                        @if ($data['student']->mobile_2)
                            , <a href="tel:{{ $data['student']->mobile_2 }}" class="text-decoration-none">{{ $data['student']->mobile_2 }}</a>
                        @endif
                    @else
                        N/A
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Permanent Address -->
        <div class="info-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <h5><i class="bi bi-house-door me-2"></i> Permanent Address</h5>
            @foreach([
                'Address' => $data['student']->address ?? 'N/A',
                'State' => $data['student']->state ?? 'N/A',
                'Country' => $data['student']->country ?? 'N/A',
            ] as $label => $value)
                <div class="info-item">
                    <div class="info-label">{{ $label }}</div>
                    <div class="info-value">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <!-- Temporary Address -->
        <div class="info-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <h5><i class="bi bi-house me-2"></i> Temporary Address</h5>
            @foreach([
                'Address' => $data['student']->temp_address ?? 'N/A',
                'State' => $data['student']->temp_state ?? 'N/A',
                'Country' => $data['student']->temp_country ?? 'N/A',
            ] as $label => $value)
                <div class="info-item">
                    <div class="info-label">{{ $label }}</div>
                    <div class="info-value">{{ $value }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-md-4">
        <!-- Academic Info -->
        <div class="info-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <h5><i class="bi bi-mortarboard me-2"></i> Academic Information</h5>
            @foreach([
                'Faculty' => ViewHelper::getFacultyTitle($data['student']->faculty) ?: 'N/A',
                'Semester' => ViewHelper::getSemesterTitle($data['student']->semester) ?: 'N/A',
                'Batch' => ViewHelper::getStudentBatchId($data['student']->batch) ?: 'Unknown',
                'Reg. Date' => $data['student']->reg_date ? \Carbon\Carbon::parse($data['student']->reg_date)->format('d M, Y') : 'N/A',
            ] as $label => $value)
                <div class="info-item">
                    <div class="info-label">{{ $label }}</div>
                    <div class="info-value">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        @if ($data['student']->student_signature)
            <div class="info-card text-center animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
                <h5><i class="bi bi-pen me-2"></i> Signature</h5>
                <img src="{{ asset('images/studentProfile/' . $data['student']->student_signature) }}" 
                     alt="Signature" 
                     class="img-fluid"
                     style="max-height: 80px; border: 1px solid #eee; border-radius: 4px; padding: 5px;">
            </div>
        @endif
    </div>
</div>

<!-- Family Info -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="info-card animate__animated animate__fadeIn">
            <h5><i class="bi bi-people me-2"></i> Family Information</h5>
            <div class="row">
                @foreach(['Father', 'Mother', 'Guardian'] as $role)
                    @php $prefix = strtolower($role); @endphp
                    <div class="col-md-4">
                        <h6 class="font-weight-bold text-primary mb-3">{{ $role }}</h6>
                        <div class="info-item">
                            <div class="info-label">Name</div>
                            <div class="info-value">
                                {{ $data['student']->{"{$prefix}_first_name"} . ' ' . $data['student']->{"{$prefix}_middle_name"} . ' ' . $data['student']->{"{$prefix}_last_name"} }}
                            </div>
                        </div>
                        @if($role === 'Guardian')
                            <div class="info-item">
                                <div class="info-label">Relation</div>
                                <div class="info-value">{{ $data['student']->guardian_relation ?? 'N/A' }}</div>
                            </div>
                        @else
                            <div class="info-item">
                                <div class="info-label">Occupation</div>
                                <div class="info-value">{{ $data['student']->{"{$prefix}_occupation"} ?? 'N/A' }}</div>
                            </div>
                        @endif
                        <div class="info-item">
                            <div class="info-label">Mobile</div>
                            <div class="info-value">
                                @if ($data['student']->{"{$prefix}_mobile_1"})
                                    <a href="tel:{{ $data['student']->{"{$prefix}_mobile_1"} }}" class="text-decoration-none">
                                        {{ $data['student']->{"{$prefix}_mobile_1"} }}
                                    </a>
                                    @if ($data['student']->{"{$prefix}_mobile_2"})
                                        , <a href="tel:{{ $data['student']->{"{$prefix}_mobile_2"} }}" class="text-decoration-none">
                                            {{ $data['student']->{"{$prefix}_mobile_2"} }}
                                        </a>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Family Photos -->
@if ($data['student']->father_image || $data['student']->mother_image || $data['student']->guardian_image)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="info-card animate__animated animate__fadeIn">
                <h5><i class="bi bi-images me-2"></i> Family Photos</h5>
                <div class="row text-center">
                    @foreach(['father', 'mother', 'guardian'] as $role)
                        @if ($data['student']->{$role . '_image'})
                            <div class="col-md-4 mb-3">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ asset('images/parents/' . $data['student']->{$role . '_image'}) }}"
                                         class="family-photo mb-2"
                                         alt="{{ ucfirst($role) }}">
                                    <h6 class="font-weight-bold">{{ ucfirst($role) }}</h6>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif