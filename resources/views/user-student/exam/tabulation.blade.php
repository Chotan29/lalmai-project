{{-- The student's own row of the class tabulation sheet.

     Drawn with the office's own sheet markup (print/exam/includes/tabulation-table) from
     data built by the same grading engine, so what a student reads here is exactly the row
     that appears on the printed sheet - down to the letterhead. Only reachable once the
     office has pressed Tabulation Sheet Publish for this exam. --}}
@extends('user-student.layouts.master')

@php
    /* Cell budget decides how tight the sheet has to be drawn. */
    $cellCount = $data['tabulation_cell_count'] ?? (count($data['subject_columns']) * 2 + 4);
    $density = $cellCount > 55 ? 'is-tight' : ($cellCount > 35 ? 'is-wide' : '');
    $me = $data['student']->first();
@endphp

@section('css')
    @include('print.exam.includes.tabulation-style')
    <style media="print">@page { size: legal landscape; margin: 6mm; }</style>
@endsection

@section('content')
    <div class="main-content">
        <div class="col-sm-12 align-right no-print hidden-print" style="margin-bottom:10px;">
            <a href="#" class="btn btn-primary btn-sm" onclick="window.print(); return false;">
                <i class="ace-icon fa fa-print"></i> Print
            </a>
        </div>
        <div class="main-content-inner">
            <div class="page-content">
                <div class="tab-sheet-wrapper {{ $density }}">
                    @if($me)
                        <div class="tab-one-head">
                            <strong>Your Result</strong>
                            <span class="tab-one-gpa {{ $me->gpa_remark === 'Pass' ? 'is-pass' : 'is-fail' }}">
                                GPA {{ number_format((float) $me->gpa_average, 2) }}
                                &nbsp;|&nbsp; {{ $me->gpa_grade }}
                                &nbsp;|&nbsp; {{ $me->gpa_remark }}
                            </span>
                        </div>
                    @endif

                    <div class="tab-sheet-scroll">
                        @include('print.exam.includes.tabulation-table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
