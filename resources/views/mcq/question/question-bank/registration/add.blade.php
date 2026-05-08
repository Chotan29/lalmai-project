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
                            Add
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    @include('mcq.includes.buttons')
                    @include($view_path.'.includes.buttons')
                    <!-- PAGE CONTENT BEGINS -->
                        @include('includes.validation_error_messages')

                        {!! Form::open(['route' => $base_route.'.store', 'method' => 'POST', 'class' => 'form-horizontal',
                        'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}

                        @include($view_path.'.registration.includes.form')

                        <div class="clearfix form-actions">
                            <div class="col-md-12 align-right">
                                <button class="btn" type="reset">
                                    <i class="fa fa-undo bigger-110"></i>
                                    Reset
                                </button>

                                <button class="btn btn-primary add-question" type="submit" value="Save" name="add_question" id="add-question">
                                    <i class="fa fa-save bigger-110"></i>
                                    Save
                                </button>

                                <button class="btn btn-success add-question" type="submit" value="Save" name="add_question_another" id="add-question-another">
                                    <i class="fa fa-save bigger-110"></i>
                                    <i class="fa fa-plus bigger-110"></i>
                                    Save & Add Another
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

            //validation
            $(".add-question").click(function () {
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

        });




        function optionLoad(){
            var type = $('select[name="type"]').val();
            var count = $('#option-table').rowCount()+1;

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

    @include('includes.scripts.summarnote')
    @include('includes.scripts.jquery_validation_scripts')
    @include('includes.scripts.inputMask_script')
    @include('includes.scripts.datepicker_script')
@endsection

