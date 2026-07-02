@extends('user-student.layouts.master')

@section('css')
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700|Roboto+Condensed:400,700" rel="stylesheet">
    <style>
        @media print {
            body {
                font-family: 'Poppins', sans-serif;
                color: #333;
                background: #fff !important;
            }
            .print-container {
                padding: 20px;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-after: always;
            }
        }
        @media screen {
            .print-container {
                background: #fff;
                padding: 30px;
                max-width: 800px;
                margin: 0 auto;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 20px;
        }
        .institute-name {
            font-family: 'Roboto Condensed', sans-serif;
            font-weight: 700;
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .department {
            font-weight: 600;
            font-size: 18px;
            color: #3498db;
            margin-bottom: 5px;
        }
        .address {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 15px;
        }
        .exam-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #e74c3c;
            margin: 15px 0;
            text-transform: uppercase;
        }
        .exam-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .exam-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .exam-table th {
            background: #3498db;
            color: white;
            font-weight: 600;
            padding: 10px;
            text-align: center;
        }
        .exam-table td {
            padding: 10px;
            border-bottom: 1px solid #eaeaea;
            text-align: center;
        }
        .exam-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .exam-table tr:hover {
            background-color: #f1f1f1;
        }
        .signature-area {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
            border-top: 1px solid #333;
            padding-top: 5px;
            margin: 0 auto;
        }
        .note {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 30px;
            padding: 10px;
            background: #f8f9fa;
            border-left: 3px solid #3498db;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 10px;
        }
        .print-btn {
            position: fixed;
            right: 20px;
            top: 100px;
            z-index: 1000;
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        .print-btn:hover {
            background: #2980b9;
        }
    </style>
@endsection

@section('content')
    <button class="print-btn no-print" onclick="window.print()">
        <i class="ace-icon fa fa-print"></i> Print Routine
    </button>

    <div class="print-container">
        <div class="header">
            @if(isset($generalSetting->logo))
                <img src="{{ asset('images/setting/general/'.$generalSetting->logo) }}" class="logo" alt="Institute Logo">
            @endif
            <div class="institute-name">{{$generalSetting->institute}}</div>
            <div class="department">Department of Examination</div>
            <div class="address">
                {{$generalSetting->address}}, {{$generalSetting->phone}}
            </div>
            
            <div class="exam-title">
                {{ ViewHelper::getExamById($data['exam']) }} - {{ ViewHelper::getYearById($data['year']) }} Examination Schedule
            </div>
            
            <div class="exam-details">
                <strong>Program:</strong> {{ ViewHelper::getFacultyTitle($data['faculty']) }} / 
                {{ ViewHelper::getSemesterTitle($data['semester']) }}
            </div>
        </div>

        <table class="exam-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Subject</th>
                    <th>FM(T)</th>
                    <th>PM(T)</th>
                    <th>FM(P)</th>
                    <th>PM(P)</th>
                </tr>
            </thead>
            <tbody>
                @if($data['subjects'] && $data['subjects']->count() > 0)
                    @php($i=1)
                    @foreach($data['subjects'] as $subject)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ \Carbon\Carbon::parse($subject->date)->format('d M Y') }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($subject->start_time)->format('h:i A') }} - 
                                {{ \Carbon\Carbon::parse($subject->end_time)->format('h:i A') }}
                            </td>
                            <td>{{ $subject->title }} [{{ $subject->code }}]</td>
                            <td>{{ $subject->full_mark_theory ?: '-' }}</td>
                            <td>{{ $subject->pass_mark_theory ?: '-' }}</td>
                            <td>{{ $subject->full_mark_practical ?: '-' }}</td>
                            <td>{{ $subject->pass_mark_practical ?: '-' }}</td>
                        </tr>
                        @php($i++)
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center">No exam schedule found</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="signature-area">
            <div class="signature-box">
                <div>Exam Controller</div>
            </div>
        </div>

        <div class="note">
            <strong>Note:</strong> This is for informational purposes only. Please contact the Examination Department for any queries.
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Add print button functionality
            $('.print-btn').click(function() {
                window.print();
            });
            
            // Format dates nicely for display
            $('.date-cell').each(function() {
                var dateText = $(this).text();
                if(dateText) {
                    var formattedDate = moment(dateText).format('DD MMM YYYY');
                    $(this).text(formattedDate);
                }
            });
        });
    </script>
@endsection