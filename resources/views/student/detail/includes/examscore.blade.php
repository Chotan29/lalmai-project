<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">

           <?php
                /*
                 *
                 * {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}
                {!! Form::hidden('bulk_action', 'print-certificate', null, ['class' => 'form-control']) !!}
                {!! Form::hidden('chkIds[]', encrypt($data['student']->id), null, ['class' => 'form-control']) !!}
                {!! Form::hidden('certificate-template', '14', null, ['class' => 'form-control']) !!}
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fa fa-print" aria-hidden="true"></i>&nbsp; Print Final Certificate
                </button>
            {!! Form::close() !!}

            *  <a href="{{ route('certificate.transcript.print', ['id' => encrypt($data['student']->id)]) }}" class="btn btn-sm btn-success" target="_blank">
                <i class="fa fa-print" aria-hidden="true"></i>&nbsp; Print Transcript Certificate
            </a>
                {!! Form::open(['route' => 'print-out.exam.mark-sheet', 'id' => 'print-student-marksheet']) !!}
            <div class="clearfix">

{{--                    {!! Form::hidden('year_id', $exam->years_id) !!}--}}
{{--                    {!! Form::hidden('month_id', $exam->months_id) !!}--}}
{{--                    {!! Form::hidden('exams_id', $exam->exams_id) !!}--}}
{{--                    {!! Form::hidden('faculty_id', $exam->faculty_id) !!}--}}
{{--                    {!! Form::hidden('semester_select', $exam->semesters_id) !!}--}}
{{--                    {!! Form::hidden('semester_id', $exam->semesters_id) !!}--}}
                    {!! Form::hidden('chkIds', encrypt($data['student']->id)) !!}
                    {!! Form::hidden('result-type', 'transcript') !!}
                    <label class="pos-rel">
                        {!! Form::radio('print_type',1, true, ['class' => 'ace', "required"]) !!}
                        <span class="lbl"></span> <span class="ace" >Transcript </span>
                    </label>

                    <label class="pos-rel">
                        {!! Form::radio('print_type',2, false, ['class' => 'ace', "required"]) !!}
                        <span class="lbl"></span> <span class="ace" > Semester Wise Final </span>
                    </label>
                          <button type="submit" class="btn btn-sm btn-success">
                            <i class="fa fa-print" aria-hidden="true"></i>&nbsp; Print Final Certificate
                        </button>
                    </div>

                {!! Form::close() !!}
            * */
           ?>
                    <hr>
                @if (isset($data['schedule_exams']) && $data['schedule_exams']->count() > 0)
                    @php($i=1)
                    @foreach($data['schedule_exams'] as $exam)
                                <div id="accordion{{$i}}" class="accordion-style1 panel-group hidden-print">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title ">
                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion{{$i}}" href="#collapseOne-{{$i}}" aria-expanded="false">
                                                    <h4 class="header large lighter blue" style="text-align: left;">
                                                        <i class="bigger-110 ace-icon fa fa-angle-double-right" data-icon-hide="ace-icon fa fa-angle-double-down" data-icon-show="ace-icon fa fa-angle-double-right"></i>
                                                        {{ $i }} - {{ ViewHelper::getYearById($exam->years_id) }} - {{ ViewHelper::getMonthById($exam->months_id) }} - {{ ViewHelper::getFacultyTitle($exam->faculty_id) }} - {{ ViewHelper::getSemesterTitle($exam->semesters_id) }} - {{ ViewHelper::getExamById($exam->exams_id) }}
                                                    </h4>
                                                </a>
                                            </h4>
                                        </div>

                                        <div class="panel-collapse collapse" id="collapseOne-{{$i}}" aria-expanded="false" style="height: 0px;">
                                            <div class="panel-body">
                                                <div class="easy-link-menu align-center">
                                                    {!! Form::open(['route' => 'print-out.exam.routine']) !!}
                                                    {!! Form::hidden('years_id', $exam->years_id) !!}
                                                    {!! Form::hidden('months_id', $exam->months_id) !!}
                                                    {!! Form::hidden('exams_id', $exam->exams_id) !!}
                                                    {!! Form::hidden('target_faculty', $exam->faculty_id) !!}
                                                    {!! Form::hidden('semester_select', $exam->semesters_id) !!}
                                                    <button type="submit" class="">
                                                        <i class="fa fa-print" aria-hidden="true"></i> Print Exam Schedule
                                                    </button>
                                                    {!! Form::close() !!}
                                                    <hr>
                                                    {!! Form::open(['route' => 'print-out.exam.admit-card']) !!}
                                                    {!! Form::hidden('years_id', $exam->years_id) !!}
                                                    {!! Form::hidden('months_id', $exam->months_id) !!}
                                                    {!! Form::hidden('exams_id', $exam->exams_id) !!}
                                                    {!! Form::hidden('target_faculty', $exam->faculty_id) !!}
                                                    {!! Form::hidden('semester_select', $exam->semesters_id) !!}
                                                    {!! Form::hidden('chkIds[]', $data['student']->id) !!}
                                                    <label class="pos-rel">
                                                        {!! Form::radio('print_type',1, true, ['class' => 'ace', "required"]) !!}
                                                        <span class="lbl"></span> <span class="ace" >Admit Card </span>
                                                    </label>

                                                    <label class="pos-rel">
                                                        {!! Form::radio('print_type',2, false, ['class' => 'ace', "required"]) !!}
                                                        <span class="lbl"></span> <span class="ace" >Admit Card With Schedule </span>
                                                    </label>
                                                    <button type="submit" class="">
                                                        <i class="fa fa-user" aria-hidden="true"></i> Print Admit Card
                                                    </button>
                                                    {!! Form::close() !!}


                                                    <hr>
                                                    {!! Form::open(['route' => 'print-out.exam.mark-sheet', 'id' => 'print-student-marksheet']) !!}

                                                    {!! Form::hidden('year_id', $exam->years_id) !!}
                                                    {!! Form::hidden('month_id', $exam->months_id) !!}
                                                    {!! Form::hidden('exams_id', $exam->exams_id) !!}
                                                    {!! Form::hidden('faculty_id', $exam->faculty_id) !!}
                                                    {!! Form::hidden('semester_select', $exam->semesters_id) !!}
                                                    {!! Form::hidden('semester_id', $exam->semesters_id) !!}
                                                    {!! Form::hidden('chkIds[]', $data['student']->id) !!}
                                                    <div class="clearfix">
                                                        <label class="pos-rel">
                                                            <input type="radio" name="result-type" value="grading" id="typeGrading" class="ace" />
                                                            <span class="lbl"></span> Grading
                                                        </label>
                                                        <label class="pos-rel">
                                                            <input type="radio" name="result-type" value="university-grading" id="typeUniversityGrading" checked class="ace" />
                                                            <span class="lbl"></span> UniversityGrading
                                                        </label>
                                                        <label class="pos-rel">
                                                            <input type="radio" name="result-type" value="percentage" id="typePercentage" class="ace" />
                                                            <span class="lbl"></span> Percentage
                                                        </label>
                                                        <label class="pos-rel">
                                                            <input type="radio" name="result-type" value="ledger" id="typeLedger" class="ace" />
                                                            <span class="lbl"></span> Ledger
                                                        </label>
                                                        <button type="submit" class="">
                                                            <i class="fa fa-print" aria-hidden="true"></i>&nbsp; Print Mark Sheets
                                                        </button>
                                                    </div>

                                                    {!! Form::close() !!}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @php($i++)
                    @endforeach
                @endif
        </div>
    </div>
</div>

<?php
/*
  * <div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                        <tr>
                            <th>{{ __('common.s_n')}}</th>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Faculty</th>
                            <th>Semester</th>
                            <th>Exam</th>
                            <th></th>
                        </tr>
                </thead>
                <tbody>
                    @if (isset($data['schedule_exams']) && $data['schedule_exams']->count() > 0)
                        @php($i=1)
                        @foreach($data['schedule_exams'] as $exam)

                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ ViewHelper::getYearById($exam->years_id) }}</td>
                                <td>{{ ViewHelper::getMonthById($exam->months_id) }}</td>
                                <td>{{ ViewHelper::getFacultyTitle($exam->faculty_id) }}</td>
                                <td>{{ ViewHelper::getSemesterTitle($exam->semesters_id) }}</td>
                                <td>{{ ViewHelper::getExamById($exam->exams_id) }}</td>
                                <td class="text-right">
                                    <div id="accordion" class="filter-form accordion-style1 panel-group hidden-print">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne-{{$i}}" aria-expanded="false">
                                                        <h4 class="header large lighter blue">
                                                            <i class="bigger-110 ace-icon fa fa-angle-double-right" data-icon-hide="ace-icon fa fa-angle-double-down" data-icon-show="ace-icon fa fa-angle-double-right"></i>
                                                            PRINT

                                                        </h4>
                                                    </a>
                                                </h4>
                                            </div>

                                            <div class="panel-collapse collapse" id="collapseOne-{{$i}}" aria-expanded="false" style="height: 0px;">
                                                <div class="panel-body">
                                                    <div class="easy-link-menu align-center">
                                                        {!! Form::open(['route' => 'print-out.exam.routine']) !!}
                                                        {!! Form::hidden('years_id', $exam->years_id) !!}
                                                        {!! Form::hidden('months_id', $exam->months_id) !!}
                                                        {!! Form::hidden('exams_id', $exam->exams_id) !!}
                                                        {!! Form::hidden('target_faculty', $exam->faculty_id) !!}
                                                        {!! Form::hidden('semester_select', $exam->semesters_id) !!}
                                                        <button type="submit" class="">
                                                            <i class="fa fa-print" aria-hidden="true"></i> Print Exam Schedule
                                                        </button>
                                                        {!! Form::close() !!}
                                                        <hr>


                                                        {!! Form::open(['route' => 'print-out.exam.admit-card']) !!}
                                                        {!! Form::hidden('years_id', $exam->years_id) !!}
                                                        {!! Form::hidden('months_id', $exam->months_id) !!}
                                                        {!! Form::hidden('exams_id', $exam->exams_id) !!}
                                                        {!! Form::hidden('target_faculty', $exam->faculty_id) !!}
                                                        {!! Form::hidden('semester_select', $exam->semesters_id) !!}
                                                        {!! Form::hidden('chkIds[]', $data['student']->id) !!}
                                                        <label class="pos-rel">
                                                            {!! Form::radio('print_type',1, true, ['class' => 'ace', "required"]) !!}
                                                            <span class="lbl"></span> <span class="ace" >Admit Card </span>
                                                        </label>

                                                        <label class="pos-rel">
                                                            {!! Form::radio('print_type',2, false, ['class' => 'ace', "required"]) !!}
                                                            <span class="lbl"></span> <span class="ace" >Admit Card With Schedule </span>
                                                        </label>
                                                        <hr>
                                                        <button type="submit" class="">
                                                            <i class="fa fa-user" aria-hidden="true"></i> Print Admit Card
                                                        </button>
                                                        {!! Form::close() !!}


                                                        <hr>
                                                        {!! Form::open(['route' => 'print-out.exam.mark-sheet', 'id' => 'print-student-marksheet']) !!}

                                                        {!! Form::hidden('year_id', $exam->years_id) !!}
                                                        {!! Form::hidden('month_id', $exam->months_id) !!}
                                                        {!! Form::hidden('exams_id', $exam->exams_id) !!}
                                                        {!! Form::hidden('faculty_id', $exam->faculty_id) !!}
                                                        {!! Form::hidden('semester_select', $exam->semesters_id) !!}
                                                        {!! Form::hidden('semester_id', $exam->semesters_id) !!}
                                                        {!! Form::hidden('chkIds[]', $data['student']->id) !!}
                                                        <div class="clearfix">
                                                            <label class="pos-rel">
                                                                <input type="radio" name="result-type" value="grading" id="typeGrading" class="ace" />
                                                                <span class="lbl"></span> Grading
                                                            </label>
                                                            <label class="pos-rel">
                                                                <input type="radio" name="result-type" value="university-grading" id="typeUniversityGrading" checked class="ace" />
                                                                <span class="lbl"></span> UniversityGrading
                                                            </label>
                                                            <label class="pos-rel">
                                                                <input type="radio" name="result-type" value="percentage" id="typePercentage" class="ace" />
                                                                <span class="lbl"></span> Percentage
                                                            </label>
                                                            <label class="pos-rel">
                                                                <input type="radio" name="result-type" value="ledger" id="typeLedger" class="ace" />
                                                                <span class="lbl"></span> Ledger
                                                            </label>

                                                            <button type="submit" class="">
                                                                <i class="fa fa-print" aria-hidden="true"></i>&nbsp; Print Mark Sheets
                                                            </button>
                                                        </div>

                                                        {!! Form::close() !!}

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix hidden-print " >
                                    </div>
                                </td>
                            </tr>
                            @php($i++)
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">No {{ $panel }} data found. Please Filter {{ $panel }} to show. </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
  * */

?>
