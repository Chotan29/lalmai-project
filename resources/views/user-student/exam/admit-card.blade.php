@extends('user-student.layouts.master')

@section('css')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet">
    <style>
        /* Print-specific styles */
        @media print {
            body {
                font-family: 'Poppins', sans-serif;
                background: #fff !important;
                color: #333;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .admit-card {
                border: 2px solid #000;
                padding: 20px;
                page-break-after: always;
            }
        }
        
        /* Screen styles */
        @media screen {
            .admit-card {
                max-width: 800px;
                margin: 30px auto;
                padding: 30px;
                background: #fff;
                box-shadow: 0 0 25px rgba(0,0,0,0.1);
                border: 1px solid #e0e0e0;
            }
        }
        
        /* Common styles */
        .admit-card {
            position: relative;
        }
        .print-btn {
            position: fixed;
            right: 30px;
            top: 30px;
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            z-index: 1000;
        }
        .print-btn:hover {
            background: #2980b9;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eaeaea;
        }
        .institute-info {
            text-align: center;
            flex-grow: 1;
        }
        .institute-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .department {
            font-size: 18px;
            font-weight: 600;
            color: #3498db;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .address {
            font-size: 14px;
            color: #7f8c8d;
        }
        .card-title {
            text-align: center;
            margin: 25px 0;
        }
        .card-title h2 {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: #e74c3c;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .card-title h3 {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
        }
        .student-photo {
            width: 120px;
            height: 150px;
            border: 2px solid #ddd;
            object-fit: cover;
        }
        .student-info {
            margin: 25px 0;
        }
        .info-row {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #eee;
        }
        .info-label {
            width: 150px;
            font-weight: 600;
            color: #555;
        }
        .info-value {
            flex-grow: 1;
            font-weight: 500;
        }
        .signature-area {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            border-top: 1px solid #333;
            padding-top: 5px;
            text-align: center;
            margin-top: 50px;
        }
        .note {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            font-size: 14px;
            color: #555;
        }
        .logo {
            max-height: 80px;
        }
        .exam-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <button class="print-btn no-print" onclick="window.print()">
        <i class="ace-icon fa fa-print"></i> Print Admit Card
    </button>

    @if($data['student'] && $data['student']->count() > 0)
        @foreach($data['student'] as $student)
            <div class="admit-card">
                <div class="header">
                    @if(isset($generalSetting->logo))
                        <img src="{{ asset('images/setting/general/'.$generalSetting->logo) }}" class="logo" alt="Institute Logo">
                    @endif
                    
                    <div class="institute-info">
                        <div class="institute-name">{{$generalSetting->institute}}</div>
                        <div class="department">Department of Examination</div>
                        <div class="address">
                            {{$generalSetting->address}}, {{$generalSetting->phone}}
                        </div>
                    </div>
                    
                    @if($student->student_image != '')
                        <img class="student-photo" src="{{ asset('images/studentProfile/'.$student->student_image) }}" alt="Student Photo">
                    @else
                        <img class="student-photo" src="{{ asset('assets/images/avatars/profile-pic.jpg') }}" alt="Student Photo">
                    @endif
                </div>

                <div class="card-title">
                    <h2>Admit Card</h2>
                    <h3>{{ ViewHelper::getExamById($data['exam']) }} - {{ ViewHelper::getYearById($data['year']) }}</h3>
                </div>

                <div class="exam-details">
                    {{ ViewHelper::getFacultyTitle($data['faculty']) }} / {{ ViewHelper::getSemesterTitle($data['semester']) }}
                </div>

                <div class="student-info">
                    <div class="info-row">
                        <div class="info-label">Registration No:</div>
                        <div class="info-value"><strong>{{ $student->reg_no }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Student Name:</div>
                        <div class="info-value"><strong>{{ $student->first_name.' '.$student->middle_name.' '.$student->last_name }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Date of Birth:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($student->date_of_birth)->format('d F Y') }}</div>
                    </div>
                </div>

                <div class="signature-area">
                    <div class="signature-box">
                        Student Signature
                    </div>
                    <div class="signature-box">
                        Exam Controller
                    </div>
                </div>

                <div class="note">
                    <strong>Note:</strong> Students must follow all examination rules and regulations. 
                    This admit card must be presented for entry to the examination hall.
                </div>
            </div>
        @endforeach
    @endif
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Format dates nicely
            $('.date-value').each(function() {
                var dateText = $(this).text();
                if(dateText) {
                    var formattedDate = moment(dateText).format('DD MMMM YYYY');
                    $(this).text(formattedDate);
                }
            });
        });
    </script>
@endsection