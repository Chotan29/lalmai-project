@extends('layouts.master')

@section('css')
    <style>
        .tab-sheet-wrapper {
            width: 100%;
            margin: 0 auto;
            background: #fff;
            border: 2px solid #333;
            padding: 10px 14px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        .tab-sheet-head { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .tab-sheet-head td { vertical-align: top; padding: 2px 4px; }
        .tab-scale-table { border-collapse: collapse; font-size: 11px; }
        .tab-scale-table th, .tab-scale-table td { border: 1px solid #333; padding: 1px 8px; text-align: center; }
        .tab-title { text-align: center; }
        .tab-title h2 { margin: 0; font-size: 22px; font-weight: bold; }
        .tab-title h4 { margin: 4px 0; font-size: 16px; text-decoration: underline; }
        .tab-meta { font-size: 14px; font-weight: bold; margin-top: 6px; }
        .tab-meta .group-box { border: 1px solid #333; padding: 2px 14px; display: inline-block; }
        /* Many subjects x 2 columns: the sheet only fits when every cell is tight and the
           subject headings use short names. */
        .tab-main-table { width: 100%; border-collapse: collapse; font-size: 11px; table-layout: fixed; }
        .tab-main-table th, .tab-main-table td { border: 1px solid #333; padding: 2px 1px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        .tab-main-table thead th { font-weight: bold; }
        .tab-main-table .text-left { text-align: left; }
        .tab-subject-head { font-size: 11px; white-space: nowrap; }
        .tab-fail { color: #c0392b; font-weight: bold; }
        .tab-subject-legend { margin-top: 8px; font-size: 10px; line-height: 1.6; }
        .tab-subject-legend span { white-space: nowrap; }
        .tab-sheet-wrapper.is-wide .tab-main-table { font-size: 9px; }
        .tab-sheet-wrapper.is-wide .tab-subject-head { font-size: 9px; }
        @media print {
            .no-print { display: none !important; }
            .tab-sheet-wrapper { border: 2px solid #333; }
            body { margin: 0; padding: 0; }
            .page-content { padding: 0 !important; border: none !important; }
            @page { size: A4 landscape; margin: 8mm; }
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="col-sm-12 align-right no-print" style="margin-bottom:10px;">
            {!! Form::open(['route' => 'print-out.exam.mark-sheet', 'style' => 'display:inline-block;']) !!}
                @foreach($data['request_inputs'] as $name => $value)
                    @if(is_array($value))
                        @foreach($value as $v)
                            <input type="hidden" name="{{ $name }}[]" value="{{ $v }}"/>
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}"/>
                    @endif
                @endforeach
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
        <div class="main-content-inner">
            <div class="page-content">
                <div class="tab-sheet-wrapper {{ count($data['subject_columns']) > 10 ? 'is-wide' : '' }}">
                    @include('print.exam.includes.tabulation-table')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('includes.scripts.print_script')
@endsection
