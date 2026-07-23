@extends('layouts.master')

@section('css')
    <style>
        @page { size: A4; margin: 12mm 10mm; }

        body {
            font-family: 'Arial', Helvetica, sans-serif;
            font-size: 11pt; line-height: 1.4; color: #1a1a1a; background: #fff;
            padding: 0; margin: 0;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }

        .reg-sheet {
            width: 210mm; min-height: 297mm; margin: 0 auto; padding: 0;
            box-sizing: border-box; background: #fff;
        }
        .reg-doc { border: 1px solid #d8d8d8; border-radius: 4px; overflow: hidden; }

        .reg-topbar { height: 6px; background: #0f5132; }

        .reg-head { display: flex; align-items: center; gap: 5mm; padding: 4mm 6mm 3mm; }
        .reg-monogram { width: 20mm; height: 20mm; object-fit: contain; }
        .reg-monogram-fallback {
            width: 20mm; height: 20mm; border-radius: 50%; border: 2px solid #0f5132;
            display: flex; align-items: center; justify-content: center; color: #0f5132; font-size: 20pt;
        }
        .reg-inst { flex: 1; text-align: center; }
        .reg-inst .gov { font-size: 8.5pt; letter-spacing: .3px; color: #555; }
        .reg-inst .name { font-size: 18pt; font-weight: bold; color: #0f5132; margin: 1mm 0; }
        .reg-inst .addr { font-size: 8.5pt; color: #444; }
        .reg-photo-box { text-align: center; }
        .reg-photo { width: 26mm; height: 31mm; object-fit: cover; border: 1px solid #bbb; background: #f4f4f4; }
        .reg-sign-img { width: 30mm; height: 12mm; object-fit: contain; margin-top: 1mm; }
        .reg-photo-cap { font-size: 7.5pt; color: #888; margin-top: .5mm; }

        .reg-title { text-align: center; padding: 1mm 0 3mm; }
        .reg-title span {
            display: inline-block; background: #0f5132; color: #fff; font-size: 12pt; font-weight: bold;
            letter-spacing: 1px; padding: 1.4mm 8mm; border-radius: 2mm;
        }

        .reg-strip { display: flex; margin: 0 6mm; border: 1px solid #d5d5d5; border-radius: 2mm; overflow: hidden; font-size: 9.5pt; }
        .reg-strip > div { flex: 1; padding: 2mm 3mm; border-right: 1px solid #e2e2e2; }
        .reg-strip > div:last-child { border-right: 0; }
        .reg-strip .lb { color: #777; }

        .reg-section { padding: 3mm 6mm 1mm; }
        .reg-sec-head { display: flex; align-items: center; gap: 2mm; margin-bottom: 2mm; }
        .reg-sec-head .bar { width: 1.2mm; height: 4.2mm; background: #0f5132; display: inline-block; }
        .reg-sec-head .txt { font-size: 10.5pt; font-weight: bold; color: #0f5132; letter-spacing: .3px; }

        .reg-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.6mm 8mm; font-size: 9.5pt; }
        .reg-grid .item { border-bottom: 1px dotted #ccc; padding: 1mm 0; }
        .reg-grid .item.full { grid-column: 1 / -1; }
        .reg-grid .lb { color: #777; }
        .reg-grid .hl { background: #f2f8f4; }
        .reg-grid .hl .lb, .reg-grid .hl b { color: #0f5132; }

        .reg-table { width: 100%; border-collapse: collapse; font-size: 9pt; }
        .reg-table th { background: #eef3ef; color: #0f5132; border: 1px solid #cfd8d2; padding: 1.6mm; }
        .reg-table td { border: 1px solid #dcdcdc; padding: 1.4mm; }
        .reg-badge-opt { background: #e6f0ff; color: #185fa5; padding: .3mm 2.4mm; border-radius: 3mm; font-size: 8.5pt; }

        .reg-declaration { padding: 3mm 6mm 1mm; }
        .reg-declaration .dt { font-size: 10pt; font-weight: bold; text-align: center; color: #0f5132; margin-bottom: 1mm; }
        .reg-declaration p { font-size: 9pt; color: #333; text-align: justify; line-height: 1.5; margin: 0; }

        .reg-pay { margin: 4mm 6mm 0; border: 1px solid #cfd8d2; border-radius: 2mm; overflow: hidden; }
        .reg-pay .ph { background: #eef3ef; color: #0f5132; font-weight: bold; text-align: center; padding: 1.6mm; font-size: 9.5pt; }
        .reg-pay .pb { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2mm 8mm; padding: 2mm 4mm; font-size: 9.5pt; }
        .reg-pay .lb { color: #777; }

        .reg-signs { display: flex; justify-content: space-between; padding: 10mm 8mm 5mm; font-size: 9.5pt; }
        .reg-signs .col { text-align: center; }
        .reg-signs .line { border-top: 1px solid #555; width: 55mm; padding-top: 1mm; }

        .reg-annex { padding: 1mm 6mm 3mm; font-size: 9pt; }
        .reg-annex .at { font-weight: bold; color: #0f5132; margin-bottom: 1mm; }
        .reg-annex .ai { margin-left: 4mm; }

        .reg-footer { background: #0f5132; color: #dfeee6; font-size: 8pt; text-align: center; padding: 1.6mm; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; background: #fff; }
            .reg-sheet { padding: 0; }
        }
    </style>
@endsection

@section('content')
    @if(session()->has('message_success'))
        <div class="alert alert-success alert-dismissible fade show no-print" role="alert" style="margin-bottom:15px;">
            <strong><i class="fa fa-check-circle"></i> Success!</strong> {{ session()->get('message_success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if(session()->has('message_danger'))
        <div class="alert alert-danger alert-dismissible fade show no-print" role="alert" style="margin-bottom:15px;">
            <strong><i class="fa fa-exclamation-circle"></i> Error!</strong> {{ session()->get('message_danger') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="no-print text-center" style="margin-bottom:10px;">
        <button class="btn btn-primary" onclick="window.print()"><i class="fa fa-print"></i> Print Registration Form</button>
        @if(isset($data['onlinePayment']) && $data['onlinePayment'])
            <a href="{{ route('print-out.fees.online-payment-receipt', ['id' => encrypt($data['onlinePayment']->id)]) }}" target="_blank" class="btn btn-info">
                <i class="fa fa-file-pdf-o"></i> Print Payment Receipt
            </a>
        @endif
    </div>

    @php
        $s = $data['student'];
        $fullName = trim(preg_replace('/\s+/', ' ', $s->first_name.' '.$s->middle_name.' '.$s->last_name));
        $fatherName = trim(preg_replace('/\s+/', ' ', $s->father_first_name.' '.$s->father_middle_name.' '.$s->father_last_name));
        $motherName = trim(preg_replace('/\s+/', ' ', $s->mother_first_name.' '.$s->mother_middle_name.' '.$s->mother_last_name));
        $guardianName = trim(preg_replace('/\s+/', ' ', $s->guardian_first_name.' '.$s->guardian_middle_name.' '.$s->guardian_last_name));
        $session = ViewHelper::getStudentBatchId($s->batch);
        $program = ViewHelper::getFacultyTitle($s->faculty);
        $semesterTitle = ViewHelper::getSemesterTitle($s->semester);
        $appliedSubjects = isset($data['appliedSubjects']) ? $data['appliedSubjects'] : collect();
    @endphp

    <div class="reg-sheet">
        <div class="reg-doc">
            <div class="reg-topbar"></div>

            <div class="reg-head">
                @if(isset($generalSetting->logo) && $generalSetting->logo)
                    <img class="reg-monogram" src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" alt="Logo">
                @else
                    <div class="reg-monogram-fallback"><i class="fa fa-university"></i></div>
                @endif
                <div class="reg-inst">
                    <div class="gov">Government of the People's Republic of Bangladesh</div>
                    <div class="name">{{ isset($generalSetting->institute) ? $generalSetting->institute : 'Lalmai Govt. College' }}</div>
                    <div class="addr">
                        {{ isset($generalSetting->address) ? $generalSetting->address : 'Cumilla Sadar South, Cumilla' }}
                        @if(isset($generalSetting->phone) && $generalSetting->phone) &middot; Mob: {{ $generalSetting->phone }} @endif
                        @if(isset($generalSetting->email) && $generalSetting->email) &middot; {{ $generalSetting->email }} @endif
                    </div>
                </div>
                <div class="reg-photo-box">
                    @if($s->student_image != '')
                        <img class="reg-photo" src="{{ asset('images'.DIRECTORY_SEPARATOR.$folder_name.DIRECTORY_SEPARATOR.$s->student_image) }}" alt="Photo">
                    @else
                        <img class="reg-photo" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" alt="Photo">
                    @endif
                    @if($s->student_signature != '')
                        <img class="reg-sign-img" src="{{ asset('images'.DIRECTORY_SEPARATOR.$folder_name.DIRECTORY_SEPARATOR.$s->student_signature) }}" alt="Signature">
                        <div class="reg-photo-cap">Signature</div>
                    @endif
                </div>
            </div>

            <div class="reg-title"><span>STUDENT REGISTRATION FORM</span></div>

            <div class="reg-strip">
                <div><span class="lb">Registration No</span><br><b>{{ $s->reg_no }}</b></div>
                <div><span class="lb">Session</span><br><b>{{ $session ?: 'N/A' }}</b></div>
                <div><span class="lb">Program</span><br><b>{{ $program }}{{ $semesterTitle ? ' - '.$semesterTitle : '' }}</b></div>
                <div><span class="lb">Date</span><br><b>{{ $s->reg_date ? \Carbon\Carbon::parse($s->reg_date)->format('d-m-Y') : '' }}</b></div>
            </div>

            <div class="reg-section">
                <div class="reg-sec-head"><span class="bar"></span><span class="txt">Personal Information</span></div>
                <div class="reg-grid">
                    <div class="item"><span class="lb">Full Name</span> &nbsp; <b>{{ $fullName }}</b></div>
                    <div class="item"><span class="lb">Gender</span> &nbsp; <b>{{ $s->gender }}</b></div>
                    <div class="item"><span class="lb">Date of Birth</span> &nbsp; <b>{{ $s->date_of_birth ? \Carbon\Carbon::parse($s->date_of_birth)->format('d-m-Y') : '' }}</b></div>
                    <div class="item"><span class="lb">Blood Group</span> &nbsp; <b>{{ $s->blood_group }}</b></div>
                    <div class="item"><span class="lb">Religion</span> &nbsp; <b>{{ $s->religion }}</b></div>
                    <div class="item"><span class="lb">National ID</span> &nbsp; <b>{{ $s->national_id_1 }}</b></div>
                    <div class="item"><span class="lb">Student Mobile</span> &nbsp; <b>{{ $s->mobile_1 }}</b></div>
                    <div class="item"><span class="lb">Email</span> &nbsp; <b>{{ $s->email }}</b></div>
                </div>
            </div>

            @if($appliedSubjects->count() > 0)
                <div class="reg-section">
                    <div class="reg-sec-head"><span class="bar"></span><span class="txt">Subjects Taken</span></div>
                    <table class="reg-table">
                        <thead>
                            <tr>
                                <th style="width:10mm; text-align:center;">SL</th>
                                <th style="width:20mm; text-align:center;">Code</th>
                                <th style="text-align:left;">Subject</th>
                                <th style="width:28mm; text-align:center;">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appliedSubjects as $i => $subject)
                                @php
                                    $subId = isset($subject->subjects_id) ? $subject->subjects_id : (isset($subject->subject_id) ? $subject->subject_id : null);
                                    $subModel = $subId ? \App\Models\Subject::find($subId) : null;
                                    $subCode = $subModel ? $subModel->code : '';
                                    $subTitle = $subModel ? $subModel->title : ViewHelper::getSubjectById($subId);
                                    $subType = $subModel ? (string) $subModel->sub_type : '';
                                    $isOpt = stripos($subType, 'optional') !== false || stripos((string) $subTitle, 'optional') !== false;
                                @endphp
                                <tr>
                                    <td style="text-align:center;">{{ $i + 1 }}</td>
                                    <td style="text-align:center;">{{ $subCode }}</td>
                                    <td>{{ $subTitle }}</td>
                                    <td style="text-align:center;">
                                        @if($isOpt)<span class="reg-badge-opt">Optional</span>@else{{ $subType ?: 'Compulsory' }}@endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="reg-section">
                <div class="reg-sec-head"><span class="bar"></span><span class="txt">Parents &amp; Guardian</span></div>
                <div class="reg-grid">
                    <div class="item"><span class="lb">Father's Name</span> &nbsp; <b>{{ $fatherName }}</b></div>
                    <div class="item"><span class="lb">Father's Mobile</span> &nbsp; <b>{{ $s->father_mobile_1 }}</b></div>
                    <div class="item"><span class="lb">Mother's Name</span> &nbsp; <b>{{ $motherName }}</b></div>
                    <div class="item"><span class="lb">Mother's Mobile</span> &nbsp; <b>{{ $s->mother_mobile_1 }}</b></div>
                    <div class="item"><span class="lb">Guardian Name</span> &nbsp; <b>{{ $guardianName ?: $fatherName }}</b></div>
                    <div class="item hl"><span class="lb"><i class="fa fa-phone"></i> Guardian Mobile</span> &nbsp; <b>{{ $s->guardian_mobile_1 ?: $s->father_mobile_1 }}</b></div>
                    <div class="item"><span class="lb">Relation</span> &nbsp; <b>{{ $s->guardian_relation }}</b></div>
                    <div class="item"><span class="lb">Guardian Email</span> &nbsp; <b>{{ $s->guardian_email }}</b></div>
                </div>
            </div>

            <div class="reg-section">
                <div class="reg-sec-head"><span class="bar"></span><span class="txt">Address</span></div>
                <div class="reg-grid">
                    <div class="item full"><span class="lb">Permanent Address</span> &nbsp; <b>{{ $s->address }}{{ isset($s->postal_code) && $s->postal_code ? ', '.$s->postal_code : '' }}</b></div>
                    <div class="item full"><span class="lb">Present Address</span> &nbsp; <b>{{ $s->temp_address }}{{ isset($s->temp_postal_code) && $s->temp_postal_code ? ', '.$s->temp_postal_code : '' }}</b></div>
                </div>
            </div>

            @if(isset($data['academicInfos']) && $data['academicInfos']->count() > 0)
                <div class="reg-section">
                    <div class="reg-sec-head"><span class="bar"></span><span class="txt">Educational Qualifications</span></div>
                    <table class="reg-table">
                        <thead>
                            <tr>
                                <th style="text-align:left;">Exam</th>
                                <th style="text-align:left;">Board / Institution</th>
                                <th style="width:16mm; text-align:center;">Year</th>
                                <th style="width:22mm; text-align:center;">Subjects</th>
                                <th style="width:16mm; text-align:center;">GPA</th>
                                <th style="width:14mm; text-align:center;">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['academicInfos'] as $academicInfo)
                                <tr>
                                    <td>{{ $academicInfo->board }}</td>
                                    <td>{{ $academicInfo->institution }}</td>
                                    <td style="text-align:center;">{{ $academicInfo->pass_year }}</td>
                                    <td style="text-align:center;">{{ $academicInfo->major_subjects }}</td>
                                    <td style="text-align:center;">{{ $academicInfo->grade_point }}</td>
                                    <td style="text-align:center;">{{ $academicInfo->grade_letter }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if(isset($data['onlinePayment']) && $data['onlinePayment'])
                <div class="reg-pay">
                    <div class="ph">Payment Receipt</div>
                    <div class="pb">
                        <div><span class="lb">Payment Date</span> &nbsp; <b>{{ \Carbon\Carbon::parse($data['onlinePayment']->date)->format('d-m-Y H:i') }}</b></div>
                        <div><span class="lb">Payment Mode</span> &nbsp; <b>{{ $data['onlinePayment']->payment_gateway }}</b></div>
                        <div><span class="lb">Transaction Ref</span> &nbsp; <b>{{ $data['onlinePayment']->ref_no }}</b></div>
                        <div><span class="lb">Amount Paid</span> &nbsp; <b>&#2547;{{ number_format($data['onlinePayment']->amount, 2) }}</b></div>
                        <div><span class="lb">Status</span> &nbsp; <b style="color:#0f5132;">{{ strtoupper($data['onlinePayment']->payment_status) }}</b></div>
                    </div>
                </div>
            @endif

            <div class="reg-declaration">
                <div class="dt">Declaration</div>
                <p>I declare that I have gone through the rules of the College and University and I have met the eligibility criteria for admission. The information given in this form is true and correct to the best of my knowledge. My application is liable to be cancelled if it is found to contain incorrect / false information. I further declare that I shall abide by the rules and regulations of the college.</p>
            </div>

            <div class="reg-signs">
                <div class="col"><div class="line">Signature of Parent / Guardian</div></div>
                <div class="col"><div class="line">Signature of Student</div></div>
            </div>

            @if(isset($data['annexure']) && $data['annexure']->count() > 0)
                <div class="reg-annex">
                    <div class="at">Documents Attached:</div>
                    @foreach($data['annexure'] as $annexure)
                        <div class="ai">&#10003; {{ ViewHelper::getAnnextureById($annexure->annexures_id) }}</div>
                    @endforeach
                </div>
            @endif

            <div class="reg-footer">{{ isset($generalSetting->institute) ? $generalSetting->institute : 'Lalmai Govt. College' }} &middot; Generated on {{ \Carbon\Carbon::now()->format('d-m-Y') }} &middot; This is a system-generated registration form</div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            @if(request()->has('autoprint'))
                window.print();
            @endif
        });
    </script>
@endsection
