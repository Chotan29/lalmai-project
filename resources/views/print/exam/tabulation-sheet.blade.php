@extends('layouts.master')

@php
    /* Cell budget decides how tight the sheet has to be printed. */
    $cellCount = $data['tabulation_cell_count'] ?? (count($data['subject_columns']) * 2 + 4);
    $density = $cellCount > 55 ? 'is-tight' : ($cellCount > 35 ? 'is-wide' : '');
    $paper = $data['paper'] ?? 'legal';
@endphp

@section('css')
    @include('print.exam.includes.tabulation-style')
    <style id="paper-style" media="print">@page { size: {{ $paper }} landscape; margin: 6mm; }</style>
@endsection

@section('content')
    <div class="main-content">
        <div class="col-sm-12 align-right no-print" style="margin-bottom:10px;">
            <span class="paper-picker">
                Paper:
                <button type="button" class="btn btn-default btn-sm paper-btn" data-paper="a4">A4</button>
                <button type="button" class="btn btn-default btn-sm paper-btn active" data-paper="legal">Legal</button>
                <button type="button" class="btn btn-default btn-sm paper-btn" data-paper="a3">A3</button>
            </span>
            {!! Form::open(['route' => 'print-out.exam.mark-sheet', 'style' => 'display:inline-block;', 'id' => 'tab-export-form']) !!}
                @foreach($data['request_inputs'] as $name => $value)
                    @if(is_array($value))
                        @foreach($value as $v)
                            <input type="hidden" name="{{ $name }}[]" value="{{ $v }}"/>
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}"/>
                    @endif
                @endforeach
                <input type="hidden" name="paper" id="tab-paper-field" value="{{ $paper }}"/>

                {{-- Releasing the tabulation to the students is its own switch: publish_status
                     governs the grade sheet, the routine and the admit card, and the office
                     hands these two out on different days. The button re-posts this same
                     sheet, so it redraws with the new state instead of throwing the user back
                     to the filter form. --}}
                @ability('super-admin', 'exam-result-publish')
                    <span class="tab-publish-box">
                        @if(!empty($data['tabulation_published']))
                            <span class="label label-success" style="font-size:12px;">
                                <i class="ace-icon fa fa-check"></i> Published to students
                            </span>
                            <button type="submit" name="tabulation_publish" value="0" class="btn btn-warning btn-sm tab-publish-btn"
                                    data-confirm-title="Confirm Un-Publish"
                                    data-confirm-text="Hide this tabulation sheet from the students again?"
                                    data-confirm-ok="Un-Publish">
                                <i class="ace-icon fa fa-eye-slash"></i> Tabulation Un-Publish
                            </button>
                        @else
                            <span class="label label-default" style="font-size:12px;">Not published</span>
                            <button type="submit" name="tabulation_publish" value="1" class="btn btn-info btn-sm tab-publish-btn"
                                    data-confirm-title="Confirm Tabulation Publish"
                                    data-confirm-text="Every student of this class will then see their own row in their own panel. No SMS is sent - that belongs to Result Publish."
                                    data-confirm-ok="Publish">
                                <i class="ace-icon fa fa-bullhorn"></i> Tabulation Sheet Publish
                            </button>
                        @endif
                    </span>

                    {{-- Releasing to the public website is a further step again: anyone on the
                         internet can then look up a roll. Its own button, its own flag. --}}
                    <span class="tab-publish-box">
                        @if(!empty($data['tabulation_public']))
                            <span class="label label-success" style="font-size:12px;">
                                <i class="ace-icon fa fa-globe"></i> Live on website
                            </span>
                            <button type="submit" name="tabulation_public" value="0" class="btn btn-danger btn-sm tab-publish-btn"
                                    data-confirm-title="Withdraw from the website"
                                    data-confirm-text="The public result page will stop finding this exam, and the teachers' sheet link will be cancelled. You can publish again later, but a new link will be issued."
                                    data-confirm-ok="Withdraw">
                                <i class="ace-icon fa fa-globe"></i> Remove from Website
                            </button>
                        @else
                            <button type="submit" name="tabulation_public" value="1" class="btn btn-success btn-sm tab-publish-btn"
                                    data-confirm-title="Publish this result on the website"
                                    data-confirm-text="This whole sheet will be listed on lalmaigc.edu.bd under this department, open for anyone to read - every roll, name, subject mark and fail on it. Students will also be able to look up their own row by roll and date of birth. Remove from Website takes it all down again."
                                    data-confirm-ok="Publish to Website">
                                <i class="ace-icon fa fa-globe"></i> Publish to Website
                            </button>
                        @endif
                    </span>
                @endability

                {{-- Two versions of the same sheet: the whole class for the office's record, and
                     the ones who passed for the notice board. A toggle rather than a setting,
                     because it is a choice made per print, not once. --}}
                @php($passedOnly = !empty($data['result_summary']['passed_only']))
                <input type="hidden" name="passed_only" id="tab-passed-field" value="{{ $passedOnly ? 1 : 0 }}"/>
                <button type="submit" name="passed_only" value="{{ $passedOnly ? 0 : 1 }}"
                        class="btn btn-sm {{ $passedOnly ? 'btn-warning' : 'btn-purple' }}">
                    <i class="ace-icon fa {{ $passedOnly ? 'fa-users' : 'fa-check-circle' }}"></i>
                    {{ $passedOnly ? 'Show All Students' : 'Passed Students Only' }}
                </button>

                <a href="#" class="btn btn-primary btn-sm" onclick="window.print(); return false;">
                    <i class="ace-icon fa fa-print"></i> Print
                </a>
                <button type="submit" name="output" value="pdf" class="btn btn-danger btn-sm">
                    <i class="ace-icon fa fa-file-pdf-o"></i> PDF
                </button>
                <button type="submit" name="output" value="excel" class="btn btn-success btn-sm">
                    <i class="ace-icon fa fa-file-excel-o"></i> Excel
                </button>
            {!! Form::close() !!}
        </div>
        @ability('super-admin', 'exam-result-publish')
            @if(!empty($data['tabulation_public']) && !empty($data['tabulation_public_token']))
                {{-- Already listed on the public result page under this department; the direct
                     link is here so it can be put in a notice or sent on WhatsApp. --}}
                <div class="col-sm-12 no-print" style="margin-bottom:10px;">
                    <div class="alert alert-success" style="margin-bottom:0;">
                        <strong><i class="ace-icon fa fa-globe"></i> This sheet is live on the website</strong>
                        <div class="input-group" style="margin-top:6px;">
                            <input type="text" class="form-control" id="tab-public-link" readonly
                                   value="{{ route('public-result.sheet', ['token' => $data['tabulation_public_token']]) }}">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="tab-copy-link">Copy</button>
                            </span>
                        </div>
                        <small>
                            It is already listed under this department on
                            <a href="{{ route('public-result') }}" target="_blank">{{ route('public-result') }}</a>,
                            so nobody needs this link to find it - it is here for notices and messages.
                            Remove from Website cancels it.
                        </small>
                    </div>
                </div>
            @endif
        @endability
        <div class="main-content-inner">
            <div class="page-content">
                <div class="tab-sheet-wrapper {{ $density }}">
                    <div class="tab-sheet-scroll">
                        @include('print.exam.includes.tabulation-table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('includes.scripts.print_script')
    <script>
        /* Publish is a one-way door for the students, so it asks first - through the app's
           own SweetAlert, like the Result Publish button on the Exam Dashboard, rather than
           a native confirm() box. */
        $('.tab-publish-btn').on('click', function (e) {
            var $btn = $(this);
            if ($btn.data('confirmed')) { return true; }
            e.preventDefault();

            if (typeof Swal === 'undefined') {
                if (window.confirm($btn.data('confirm-text'))) {
                    $btn.data('confirmed', true).click();
                }
                return false;
            }

            Swal.fire({
                title: $btn.data('confirm-title'),
                text: $btn.data('confirm-text'),
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: $btn.data('confirm-ok'),
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then(function (r) {
                if (r.value || r.isConfirmed) {
                    $btn.data('confirmed', true).click();
                }
            });
            return false;
        });

        $('#tab-copy-link').on('click', function () {
            var box = document.getElementById('tab-public-link');
            box.select();
            box.setSelectionRange(0, 99999);
            try { document.execCommand('copy'); $(this).text('Copied'); } catch (e) { /* select-and-copy by hand */ }
        });

        /* Paper choice drives both the browser print dialog and the PDF download. */
        $('.paper-btn').on('click', function () {
            var paper = $(this).data('paper');
            $('.paper-btn').removeClass('active');
            $(this).addClass('active');
            $('#tab-paper-field').val(paper);
            $('#paper-style').html('@page { size: ' + paper + ' landscape; margin: 6mm; }');
        });
    </script>
@endsection
