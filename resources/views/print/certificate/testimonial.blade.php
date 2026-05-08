@extends('layouts.master')

@section('css')
    @include('print.certificate.common.css')
    <style>
        @if($data['certificate_template']->background_status == 1)
            @if(@isset($data['certificate_template']->background_image))
                .subpage{
                background-image: url({{ asset('images/certificateBackground/'.$data['certificate_template']->background_image) }}) !important;
                background-repeat: round !important;
                background-size: cover !important;
            }
            @endif
        @endif

        {{$data['certificate_template']->custom_css}}
    </style>
@endsection

@section('content')
    @include('print.certificate.common.print-button')
    @if($data['student'] && $data['student']->count() > 0)
        @foreach($data['student'] as $student)
            <div class="book">
                <div class="page">
                    <div class="subpage">
                        <div class="main-content">
                            <div class="main-content-inner">
                                <div class="page-content">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <!-- PAGE CONTENT BEGINS -->
                                            <div class="row">
                                                <div class="col-sm-12 col-md-12 col-print-12 ">
                                                    <div class="widget-box transparent">
                                                        <div class="institute-detail">
                                                            <div class="hidden-print">
                                                                @include('print.includes.print-head')
                                                            </div>
                                                        </div>
                                                        <div  style="padding: 0px 50px;">
                                                            <table>
                                                                <tr>
                                                                    <td>
                                                                        <div class="text-left">
                                                                            <div class="space-10"></div>
                                                                            <strong> Ref.No. : {{ $student->ref_num}}</strong >
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="text-right">
                                                                            <div class="space-10"></div>
                                                                            <strong> Issue Date : {{ \Carbon\Carbon::parse($student->date_of_issue)->format('d-m-Y') }}</strong >
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <div class="space-16"></div>
                                                            <div class="space-16"></div>
                                                            <div class="space-16"></div>

                                                            <div class="row align-center text-center">
                                                                <h3 class="no-margin no-margin-top text-uppercase" style="font-family: 'Roboto';font-size: 20px">
                                                                    {{--                                                                Holtwood+One+SC|Fugaz+One|Lobster|Merienda|Righteous|Black+Ops+One|Gilda+Display--}}
                                                                    <strong>{{$student->certificate}} </strong>
                                                                </h3>
                                                                <div class="space-10"></div>
                                                                <div class="space-10"></div>
                                                                <div class="space-10"></div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12 col-xs-12 col-sm-12 col-print-12" >
                                                                    <div class="text-justify">
                                                                        {!! $student->certificate_template !!}
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="space-10"></div>
                                                </div><!-- /.col -->
                                            </div><!-- /.row -->
                                        </div><!-- /.page-content -->
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.main-content -->
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection

@section('js')
    <!-- inline scripts related to this page -->
   @include('includes.scripts.print_script')
@endsection