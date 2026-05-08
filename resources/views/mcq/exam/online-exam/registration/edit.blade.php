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
                        @include($view_path.'.registration.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Edit  Customer
                        </small>
                    </h1>
                </div><!-- /.page-header -->
                <div class="row">
                    <div class="col-xs-12 ">
                        <!-- PAGE CONTENT BEGINS -->
                        @include($view_path.'.includes.buttons')
                        @include('includes.flash_messages')
                        @include('includes.validation_error_messages')
                        <div class="action-buttons align-right">
                            <a href="{{ route($base_route.'.view', ['id' => encrypt($data['row']->id)]) }}" class="btn-primary btn-sm btn-success">
                                <i class="ace-icon fa fa-eye bigger-130" title="View"></i> View
                            </a>
                        </div>
                        {!! Form::model($data['row'], ['route' => [$base_route.'.update', encrypt($data['row']->id)], 'method' => 'POST', 'class' => 'form-horizontal',
                   'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                        {!! Form::hidden('id', encrypt($data['row']->id)) !!}
                        {{--{!! Form::hidden('guardians_id', $data['row']->guardians_id) !!}--}}
                        @include($view_path.'.registration.includes.form')
                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn btn-info update-question" type="submit">
                                    <i class="icon-ok bigger-110"></i>
                                    Update
                                </button>
                            </div>
                        </div>
                        <div class="hr hr-24"></div>
                        {!! Form::close() !!}
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


@endsection

@section('js')
    <!-- page specific plugin scripts -->
    <script>
        //Helper function that gets a count of all the rows <TR> in a table body <TBODY>
        $.fn.rowCount = function() {
            return $('tr', $(this).find('tbody')).length;
        };

        $(document).ready(function ($) {

            $('#load-option-html').click(function () {
                optionLoad();
            });


            $('select[name="subjects_id"]').select2({
                placeholder: 'Search Sub/Course...',
                ajax: {
                    url: '{{ route('subject-name-autocomplete') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: data
                        };

                    },
                    cache: true
                }

            });


            //validation
            $(".update-question").click(function () {
                var flag = false;
                var subjects_id = $('select[name="subjects_id"]').val();
                var mcq_question_groups_id = $('select[name="mcq_question_groups_id"]').val();
                var mcq_question_levels_id = $('select[name="mcq_question_levels_id"]').val();
                var question = $('textarea[name="question"]').val();
                var mark = $('input[name="mark"]').val();
                var type = $('select[name="type"]').val();


                if (subjects_id > 0 && subjects_id !=='') {

                }else{
                    toastr.warning("Please,Find and Choose Subject", "Info:");
                    flag = true;
                    return false;
                }

                if (mcq_question_groups_id > 0 && mcq_question_groups_id !=='') {

                }else{
                    toastr.warning("Please,Question group required.", "Info:");
                    flag = true;
                    return false;
                }

                if (mcq_question_levels_id > 0 && mcq_question_levels_id !=='') {

                }else{
                    toastr.warning("Please,Question level required.", "Info:");
                    flag = true;
                    return false;
                }


                if (question.length > 15) {

                }else{
                    toastr.warning("Please, Question is require or need to increase question length min 15 character.", "Info:");
                    flag = true;
                    return false;
                }



                if (mark =='' || mark == 0) {
                    toastr.warning("Please, Mark is required.", "Info:");
                    flag = true;
                    return false;
                }

                if (type == '') {
                    toastr.warning("Please,Answer type required.", "Info:");
                    flag = true;
                    return false;
                }


                $option = $("input[name='option_title[]']")
                    .map(function(){return $(this).val();}).get();


                if ($option.length <= 0) {
                    toastr.warning("Please, Add At Least One Options.", "Info:");
                    return false;
                }

                $answerExist = false;
                $answer = $('input[name="answer_status[]"]')
                    .map(function(){
                        if($(this).val() ==1){
                            return $answerExist = true;
                        }
                    }).get();

                if ($answerExist) {

                }else{
                    toastr.warning("Please, Mark Minimum One Answer in Options.", "Info:");
                    return false;
                }

                if(flag){
                    toastr.warning("Something is Wrong, Please Check", "Info:");
                    $('#validation-form').submit(function(){
                        return false;
                    });
                }
            });
        });



        function optionLoad(){
            var type = $('select[name="type"]').val();
            var count = $('#option-table').rowCount()+1;
            //console.log(count);

            if(type ==''){
                toastr.warning('Please, Select answer type.','Warning')
                return false;
            }

            $.ajax({
                type: 'POST',
                url: '{{ route('mcq.question.option-html') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: type,
                    number:count
                },
                success: function (response) {
                    var data = $.parseJSON(response);

                    if (data.error) {
                        // toastr(data.message, "warning");
                    } else {

                        $('#option_wrapper').append(data.html);
                        //toastr(data.message, "success");
                    }
                }
            });
        }

        function appendOption(){
            $('#option_wrapper').empty();
            optionLoad();
        }

    </script>
    <script>
        $('.radio-maker').on('click' , function(){
            $(this).closest("table").find('input[name="answer_status[]"]').val('0');
            $(this).closest("tr").find('input[name="answer_status[]"]').val('1');
        });

        $('.check-maker').on('click' , function(){

            if($(this).closest('tr').find('input:checkbox').is(':checked'))
                $(this).closest("tr").find('input[name="answer_status[]"]').val('1');
            else
                $(this).closest("tr").find('input[name="answer_status[]"]').val('0');

        });
    </script>

    @include('includes.scripts.summarnote')
    @include('includes.scripts.jquery_validation_scripts')
@endsection


