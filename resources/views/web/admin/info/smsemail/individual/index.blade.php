@extends('web.admin.layouts.master')

@section('css')
    <style>
        .sms-composer-panel {
            border: 1px solid #d8e2ef;
            border-radius: 10px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            padding: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .sms-composer-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
        }

        .sms-composer-input {
            resize: vertical;
            min-height: 220px;
            font-family: Consolas, Menlo, Monaco, 'Courier New', monospace;
            font-size: 15px;
            line-height: 1.5;
        }

        .sms-composer-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-top: 10px;
        }

        .sms-counter {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            background: #eef4fb;
            color: #24405f;
            font-size: 12px;
            font-weight: 600;
        }

        .sms-hint {
            color: #6d7b8a;
            font-size: 12px;
        }
    </style>
@endsection

@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div class="page-header">
                    <h1>
                        @include($view_path.'.includes.breadcrumb-primary')
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            Individual Messaging
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">
                    <div class="col-xs-12 ">
                    @include($view_path.'.includes.buttons')
                    @include('includes.flash_messages')
                        <!-- PAGE CONTENT BEGINS -->
                        <div class="form-horizontal">
                            <div class="hr hr-18 dotted hr-double"></div>
                        </div>
                        {!! Form::open(['route' => $base_route.'.individual.send', 'method' => 'POST', 'class' => 'form-horizontal',
                                'id' => 'individual_message_send_form', "enctype" => "multipart/form-data"]) !!}
                            @include($view_path.'.individual.includes.form')
                        {!! Form::close() !!}
                    </div><!-- /.col -->
                </div><!-- /.row -->

            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->
@endsection


@section('js')
    <!-- inline scripts related to this page -->
    <script type="text/javascript">
        $(document).ready(function () {
            function countSmsParts(text) {
                var length = text.length;
                var isUnicode = /[^\x00-\x7F]/.test(text);
                var singleLimit = isUnicode ? 70 : 160;
                var concatLimit = isUnicode ? 67 : 153;

                if (length <= singleLimit) {
                    return 1;
                }

                return Math.ceil(length / concatLimit);
            }

            function updateSmsComposerStats() {
                var messageBox = document.getElementById('smsmessage');
                if (!messageBox) {
                    return;
                }

                var message = messageBox.value || '';
                var parts = countSmsParts(message);

                $('#count').text(message.length + ' characters');
                $('#smsSegments').text(parts + ' SMS');
            }

            function insertSmsToken(token) {
                var messageBox = document.getElementById('smsmessage');
                if (!messageBox) {
                    return;
                }

                var start = messageBox.selectionStart || 0;
                var end = messageBox.selectionEnd || 0;
                var value = messageBox.value || '';
                messageBox.value = value.substring(0, start) + token + value.substring(end);
                messageBox.focus();
                messageBox.selectionStart = messageBox.selectionEnd = start + token.length;
                updateSmsComposerStats();
            }

            $(document).on('click', '.sms-token-btn', function () {
                insertSmsToken($(this).data('sms-token'));
            });

            $('#sms-clear-message').on('click', function () {
                $('#smsmessage').val('');
                updateSmsComposerStats();
                $('#smsmessage').focus();
            });

            $('#smsmessage').on('input keyup change', function () {
                updateSmsComposerStats();
            });

            /*Send Message */
            $('#individual-message-send-btn').click(function () {
                /*type*/
                $sms = $('#typeSms').is(':checked');
                $email = $('#typeEmail').is(':checked');

                /*Individual*/
                var number = $('input[name="number"]').val();
                var email = $('input[name="email"]').val();
                var subject = $('input[name="subject"]').val();

                var emailMessage = document.getElementById("summernote");
                var emailMessage = (emailMessage.value).length; // This will now contain text of textarea

                var message = document.getElementById("smsmessage");
                var message = (message.value).length; // This will now contain text of textarea

                if($sms || $email){

                    if($sms && number === ''){
                        toastr.info("Please, Fill At Least One Contact Number", "Info:");
                        return false;
                    }

                    if($sms && message < 8){
                        toastr.info("Message is Required With More Than 8 Character. When target is SMS", "Info:");
                        return false;
                    }

                    if($email && email === ''){
                        toastr.info("Please, Select Fill At Lease One Email ID", "Info:");
                        return false;
                    }

                    if($email && subject === ''){
                        toastr.info("Subject is Required. When target is Email", "Info:");
                        return false;
                    }

                    if($email && emailMessage < 12){
                        toastr.info("Message is Required With More Than 12 Character. When target is SMS", "Info:");
                        return false;
                    }

                }else{
                    toastr.info("Please, Select Message Type", "Info:");
                    return false;
                }

            });
            /*Message End*/
            $('.email').css('display', 'none');
            updateSmsComposerStats();

        });


        function messageTypeCondition(f) {
            $sms = $('#typeSms').is(':checked');
            $email = $('#typeEmail').is(':checked');
            if($sms) {
                $('.email').css('display', 'none');
                $('.sms').css('display', 'block');
            }

            if($email) {
                $('.email').css('display', 'block');
                $('.sms').css('display', 'none');
            }


        }
    </script>
    @include('includes.scripts.summarnote')
@endsection