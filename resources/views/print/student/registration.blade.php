@extends('layouts.master')

@section('css')
    <style>
        /* A4 Page Settings */
        @page {
            size: A4;
            margin: 15mm 10mm;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 0;
            margin: 0;
        }
        
        /* Main Container */
        .print-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 5mm;
            box-sizing: border-box;
        }
        
        /* Header Section */
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 5mm;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3mm;
        }
        
        .logo {
            width: 25mm;
            height: 25mm;
            object-fit: contain;
            margin-right: 5mm;
        }
        
        .institution-info {
            flex-grow: 1;
            text-align: center;
        }
        
        .institution-name {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        
        .institution-address {
            font-size: 9pt;
            margin: 1mm 0;
        }
        
        .form-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin: 3mm 0;
            text-decoration: underline;
        }
        
        /* Student Images */
        .student-images {
            text-align: right;
            width: 40mm;
        }
        
        .student-photo {
            width: 30mm;
            height: 35mm;
            object-fit: cover;
            border: 1px solid #ddd;
            margin-bottom: 2mm;
        }
        
        .student-signature {
            width: 40mm;
            height: 15mm;
            object-fit: contain;
        }
        
        /* Information Tables */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
            font-size: 10pt;
        }
        
        .info-table td {
            padding: 2mm 3mm;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .info-table b {
            font-weight: 600;
        }
        
        /* Academic Table */
        .academic-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3mm 0;
            font-size: 9pt;
        }
        
        .academic-table th, 
        .academic-table td {
            border: 1px solid #ddd;
            padding: 2mm;
            text-align: center;
        }
        
        .academic-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        /* Declaration Section */
        .declaration {
            margin: 5mm 0;
            font-size: 10pt;
            text-align: justify;
        }
        
        .declaration-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 2mm;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 8mm;
        }
        
        .signature-table {
            width: 100%;
            margin-top: 5mm;
        }
        
        .signature-table td {
            padding: 2mm;
            vertical-align: bottom;
        }
        
        /* Annexures */
        .annexures {
            margin-top: 5mm;
            font-size: 10pt;
        }
        
        .annexures-title {
            font-weight: bold;
            margin-bottom: 2mm;
        }
        
        .annexure-item {
            margin-left: 5mm;
        }
        
        /* Utility Classes */
        .text-center {
            text-align: center;
        }
        
        .mb-2 {
            margin-bottom: 2mm;
        }
        
        /* Print Specific */
        @media print {
            .no-print {
                display: none;
            }
            
            .print-container {
                padding: 0;
            }
            
            body {
                padding: 0;
                margin: 0;
                background: #fff;
            }
        }
    </style>
@endsection

@section('content')
    <div class="no-print text-center" style="margin-bottom: 10px;">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fa fa-print"></i> Print Form
        </button>
    </div>

    <div class="print-container">
        <!-- Header Section -->
        <div class="header">
            @if(isset($generalSetting->logo))
                <img class="logo" src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" alt="Institution Logo">
            @endif
            
            <div class="institution-info">
                <div class="institution-name">{{ isset($generalSetting->institute) ? $generalSetting->institute : 'INSTITUTE NAME' }}</div>
                <div class="institution-address">
                    {{ isset($generalSetting->address) ? $generalSetting->address : '' }}<br>
                    {{ isset($generalSetting->phone) ? 'Phone: '.$generalSetting->phone : '' }} | 
                    {{ isset($generalSetting->email) ? 'Email: '.$generalSetting->email : '' }}
                </div>
            </div>
            
            <div class="student-images">
                @if($data['student']->student_image != '')
                    <img class="student-photo" src="{{ asset('images'.DIRECTORY_SEPARATOR.$folder_name.DIRECTORY_SEPARATOR.$data['student']->student_image) }}" alt="Student Photo">
                @else
                    <img class="student-photo" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" alt="Student Photo">
                @endif
                
                @if($data['student']->student_signature != '')
                    <img class="student-signature" src="{{ asset('images'.DIRECTORY_SEPARATOR.$folder_name.DIRECTORY_SEPARATOR.$data['student']->student_signature) }}" alt="Student Signature">
                @endif
            </div>
        </div>
        
        <!-- Form Title -->
        <div class="form-title">STUDENT REGISTRATION FORM</div>
        
        <!-- Registration Info -->
        <table class="info-table">
            <tr>
                <td width="50%"><b>Registration No:</b> {{ $data['student']->reg_no }}</td>
                <td width="50%"><b>Date:</b> {{ \Carbon\Carbon::parse($data['student']->reg_date)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td colspan="2"><b>Program:</b> {{ ViewHelper::getFacultyTitle($data['student']->faculty) }} - {{ ViewHelper::getSemesterTitle($data['student']->semester) }}</td>
            </tr>
        </table>
        
        <!-- Personal Information -->
        <table class="info-table">
            <tr>
                <td width="50%"><b>Full Name:</b> {{ $data['student']->first_name.' '.$data['student']->middle_name.' '.$data['student']->last_name }}</td>
                <td width="50%"><b>Subjects:</b> 
                    @if(isset($data['appliedSubjects']) && $data['appliedSubjects']->count() > 0)
                        @foreach($data['appliedSubjects'] as $subject)
                            {{ ViewHelper::getSubjectById($subject->subject_id) }}@if(!$loop->last), @endif
                        @endforeach
                    @endif
                </td>
            </tr>
            <tr>
                <td><b>Gender:</b> {{ $data['student']->gender }}</td>
                <td><b>Date of Birth:</b> {{ \Carbon\Carbon::parse($data['student']->date_of_birth)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td><b>National ID:</b> {{ $data['student']->national_id_1 }}</td>
                <td><b>PAN:</b> {{ $data['student']->national_id_2 }}</td>
            </tr>
            <tr>
                <td><b>Mobile:</b> {{ $data['student']->mobile_1 }}</td>
                <td><b>Email:</b> {{ $data['student']->email }}</td>
            </tr>
            <tr>
                <td><b>Religion:</b> {{ $data['student']->religion }}</td>
                <td><b>Caste:</b> {{ $data['student']->caste }}</td>
            </tr>
        </table>
        
        <!-- Academic Information -->
        <table class="info-table">
            <tr>
                <td><b>URN:</b> {{ $data['student']->university_reg }}</td>
                <td><b>Enrollment No:</b> {{ $data['student']->university_enrollment_no }}</td>
            </tr>
            <tr>
                <td><b>Special Category:</b> {{ $data['student']->special_category }}</td>
                <td><b>Domicile State:</b> {{ $data['student']->state }}</td>
            </tr>
        </table>
        
        <!-- Family Information -->
        <table class="info-table">
            <tr>
                <td width="50%"><b>Father's Name:</b> {{ $data['student']->father_first_name.' '.$data['student']->father_middle_name.' '.$data['student']->father_last_name }}</td>
                <td width="50%"><b>Mother's Name:</b> {{ $data['student']->mother_first_name.' '.$data['student']->mother_middle_name.' '.$data['student']->mother_last_name }}</td>
            </tr>
            <tr>
                <td><b>Guardian Name:</b> {{ $data['student']->guardian_first_name.' '.$data['student']->guardian_middle_name.' '.$data['student']->guardian_last_name }}</td>
                <td><b>Guardian Mobile:</b> {{ $data['student']->guardian_mobile_1 }}</td>
            </tr>
        </table>
        
        <!-- Address Information -->
        <table class="info-table">
            <tr>
                <td colspan="2"><b>Permanent Address:</b> {{ $data['student']->address }}{{ isset($data['student']->postal_code) ? ', '.$data['student']->postal_code : '' }}</td>
            </tr>
            <tr>
                <td colspan="2"><b>Temporary Address:</b> {{ $data['student']->temp_address }}{{ isset($data['student']->temp_postal_code) ? ', '.$data['student']->temp_postal_code : '' }}</td>
            </tr>
        </table>
        
        <!-- Academic Qualifications -->
        @if (isset($data['academicInfos']) && $data['academicInfos']->count() > 0)
            <div class="text-center mb-2" style="font-weight: bold; font-size: 11pt;">
                EDUCATIONAL QUALIFICATIONS
            </div>
            
            <table class="academic-table">
                <thead>
                    <tr>
                        <th width="20%">Exam</th>
                        <th width="25%">Board/University</th>
                        <th width="10%">Year</th>
                        <th width="25%">Subjects</th>
                        <th width="10%">Grade Point</th>
                        <th width="10%">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['academicInfos'] as $academicInfo)
                        <tr>
                            <td>{{ $academicInfo->board }}</td>
                            <td>{{ $academicInfo->institution }}</td>
                            <td>{{ $academicInfo->pass_year }}</td>
                            <td>{{ $academicInfo->major_subjects }}</td>
                            <td>{{ $academicInfo->grade_point }}</td>
                            <td>{{ $academicInfo->grade_letter }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        
        <!-- Declaration -->
        <div class="declaration">
            <div class="declaration-title">DECLARATION</div>
            <p>
                I declare that I have gone through the rules of College and University and I have met the eligibility criteria for admission. The information given in this form is true and correct to the best of my knowledge. No disciplinary action or court case or use of unfair means in exams has been reported against me. My application is liable to get cancelled if my application is found to contain incorrect / false information. I, further declare that I shall abide by the rules & regulations of the college.
            </p>
        </div>
        
        <!-- Signatures -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td width="50%"><b>Date:</b> _________________________</td>
                    <td width="50%"></td>
                </tr>
                <tr>
                    <td><b>Signature of Parent/Guardian:</b> _________________________</td>
                    <td><b>Signature of Student:</b> _________________________</td>
                </tr>
            </table>
        </div>
        
        <!-- Annexures -->
        @if(isset($data['annexure']) && $data['annexure']->count() > 0)
            <div class="annexures">
                <div class="annexures-title">DOCUMENTS ATTACHED:</div>
                @foreach($data['annexure'] as $annexure)
                    <div class="annexure-item">✓ {{ ViewHelper::getAnnextureById($annexure->annexures_id) }}</div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Automatically trigger print dialog when needed
            @if(request()->has('autoprint'))
                window.print();
            @endif
        });
    </script>
@endsection