<div id="accordion" class="filter-form accordion-style1 panel-group hidden-print">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false">
                    <h3 class="header large lighter blue">
                        <i class="bigger-110 ace-icon fa fa-angle-double-right" data-icon-hide="ace-icon fa fa-angle-double-down" data-icon-show="ace-icon fa fa-angle-double-right"></i>
                        Filter {{$panel}}
                        <i class="fa fa-filter" aria-hidden="true"></i>&nbsp;
                    </h3>
                </a>
            </h4>
        </div>

        <div class="panel-collapse collapse" id="collapseOne" aria-expanded="false" style="height: 0px;">
            <div class="panel-body">
                {{--{!! Form::open(['route' => $base_route,'method' => 'GET', 'class' => 'form-horizontal', "enctype" => "multipart/form-data"]) !!}--}}
                <div class="clearfix">
                    <div class="form-group">
                        {!! Form::label('group', 'Group', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('group', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

                            @include('includes.form_fields_validation_message', ['name' => 'group'])
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('subject', 'Subject', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('subject', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

                            @include('includes.form_fields_validation_message', ['name' => 'subject'])
                        </div>
                    </div>


                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('message', 'Message', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::textarea('message', null, ["placeholder" => "", "class" => "form-control border-form","rows"=>'2']) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'message'])
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('date', 'Date', ['class' => 'col-sm-2 control-label']) !!}
                        <div class=" col-sm-3">
                            <div class="input-group ">
                                {!! Form::text('start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                <span class="input-group-addon">
                                    <i class="fa fa-exchange"></i>
                                </span>
                                {!! Form::text('end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                            </div>
                        </div>

                        <label class="col-sm-2 control-label" for="status"> Message Type </label>
                        <div class="col-sm-2">
                            <div class="control-group">
                                <div class="radio">
                                    <label>
                                        {!! Form::radio('type', 'sms', false, ['class' => 'ace']) !!}
                                        <span class="lbl"> SMS</span>
                                    </label>
                                    <label>
                                        {!! Form::radio('type', 'email', false, ['class' => 'ace']) !!}
                                        <span class="lbl"> Email</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-4"></div>
                </div>
                <div class="clearfix form-actions">
                    <div class="align-right">
                        <button class="btn btn-info" type="submit" id="filter-btn">
                            <i class="fa fa-filter bigger-110"></i>
                            Filter
                        </button>
                    </div>
                </div>
                {{--{!! Form::close() !!}--}}

            </div>
        </div>
    </div>
</div>