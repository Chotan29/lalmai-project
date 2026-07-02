@extends('layouts.master')

@section('css')
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                @include('layouts.includes.template_setting')
                <div class="page-header">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            View
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    @include('certificate.includes.buttons')

                    @include('includes.flash_messages')
                    <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')
                        <table id="" class="table table-striped table-bordered table-hover">
                            <tbody>
                            @if (isset($data['student']) && $data['student']->count() > 0)
                                @php($i=1)
                                @foreach($data['student'] as $student)
                                    <tr>
                                        <td colspan="2" class="text-right hidden-print">

{{--                                            <a href="{{ route($base_route.'.print', ['id' => encrypt($student->id)]) }}" class="btn-primary btn-sm" target="_blank">--}}
{{--                                                <i class="ace-icon fa fa-print bigger-130"></i> Print Certificate--}}
{{--                                            </a>--}}
                                            <a href="#" onclick="window.print();" class="btn-primary btn-sm" target="_blank">
                                                <i class="ace-icon fa fa-print bigger-130"></i> Print View Report
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{__('form_fields.student.fields.faculty')}}</th>
                                        <td> {{  ViewHelper::getFacultyTitle( $student->faculty ) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{__('form_fields.student.fields.semester')}}</th>
                                        <td> {{  ViewHelper::getSemesterTitle( $student->semester ) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('form_fields.student.fields.reg_no') }}</th>
                                        <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}" target="_blank">{{ $student->reg_no }}</a></td>
                                    </tr>
                                    <tr>
                                        <th>{{__('form_fields.student.fields.name_of_student')}}</th>
                                        <td><a href="{{ route('student.view', ['id' => encrypt($student->id)]) }}" target="_blank"> {{ $student->first_name.' '.$student->middle_name.' '. $student->last_name }}</a></td>
                                    </tr>
                                    <tr>
                                        <th>TRC.NO.</th>
                                        <td>{{$student->trc_num}}</td>
                                    </tr>
                                    <tr>
                                        <th>Issue Date</th>
                                        <td>{{ \Carbon\Carbon::parse($student->date_of_issue)->format('d-M-Y')}} </td>
                                    </tr>
                                    <tr>
                                        <th>Year</th>
                                        <td>{{$student->year}}</td>
                                    </tr>
                                    <tr>
                                        <th>Duration</th>
                                        <td>{{$student->duration}}</td>
                                    </tr>
                                    <tr>
                                        <th>Credit Required</th>
                                        <td>{{$student->credit_required}}</td>
                                    </tr>

                                    <tr>
                                        <th>GPA</th>
                                        <td>{{$student->gpa}}</td>
                                    </tr>

                                    <tr>
                                        <th>Verification Code</th>
                                        <td>{{$student->verification_code}}</td>
                                    </tr>

                                    <tr>
                                        <th>Mark Sheet Serial No.</th>
                                        <td>{{$student->mark_sheet_sn}}</td>
                                    </tr>

                                    <tr>
                                        <th>Provisional Certificate Number</th>
                                        <td>{{$student->provisional_certificate_num}}</td>
                                    </tr>

                                    <tr>
                                        <th>REF TEXT</th>
                                        <td>
                                            @if($student->ref_text)
                                                @php($refText = json_decode($student->ref_text))
                                                <table class="table table-striped table-bordered table-hover">
                                                    @foreach($refText as $key => $value)
                                                        @if($key == 'created_by')
                                                            <tr>
                                                                <td class="text-uppercase" style="font-weight: 600">{{str_replace('_',' ',$key)}}</td>
                                                                <td>{{ViewHelper::getUserNameId($value)}}</td>
                                                            </tr>
                                                        @else
                                                            <tr>
                                                                <td class="text-uppercase" style="font-weight: 600">{{str_replace('_',' ',$key)}}</td>
                                                                <td>{{$value}}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </table>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>History</th>
                                        <td>
                                            @php($history = $student->certificateHistory->where('certificate','character')->where('certificate_id',$student->certificate_id))
                                            @if (isset($history) && $history->count() > 0)
                                                @foreach($history as $key => $hist)
                                                    <div class="table-header text-capitalize">
                                                        {{ $hist->history_type }}-{{ $hist->created_at }}
                                                    </div>
                                                    @if($hist->ref_text)
                                                        @php($refText = json_decode($hist->ref_text))
                                                        <table class="table table-striped table-bordered table-hover">
                                                            @foreach($refText as $key => $value)
                                                                @if($key == 'created_by')
                                                                    <tr>
                                                                        <td class="text-uppercase" style="font-weight: 600">{{str_replace('_',' ',$key)}}</td>
                                                                        <td>{{ViewHelper::getUserNameId($value)}}</td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td class="text-uppercase" style="font-weight: 600">{{str_replace('_',' ',$key)}}</td>
                                                                        <td>{{$value}}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        </table>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div>
        </div><!-- /.page-content -->
    </div>
    </div><!-- /.main-content -->
@endsection


@section('js')


@endsection