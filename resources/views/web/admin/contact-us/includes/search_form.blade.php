
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
                <div class="form-group">
                    {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('name', null, ['class' => 'form-control border-form', 'placeholder' => '']) !!}

                    </div>

                    {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('address', null, ['class' => 'form-control border-form', 'placeholder' => '']) !!}

                    </div>
                </div>
                <div class="space-4"></div>
                <div class="form-group">
                    {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('email', null, ['class' => 'form-control border-form', 'placeholder' => '']) !!}

                    </div>
                    {!! Form::label('phone', 'Phone', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('phone', null, ['class' => 'form-control border-form', 'placeholder' => '']) !!}

                    </div>
                </div>
                <div class="space-4"></div>
                <div class="form-group">
                    {!! Form::label('message', 'Message', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::textarea('message', null, ["placeholder" => "Message", "class" => "form-control border-form","rows"=>"2"]) !!}
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    {!! Form::label('create_date', 'Created', ['class' => 'col-sm-2 control-label']) !!}
                    <div class=" col-sm-4">
                        <div class="input-group ">
                            {!! Form::text('create_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                            <span class="input-group-addon">
                                    <i class="fa fa-exchange"></i>
                                </span>
                            {!! Form::text('create_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                        </div>
                    </div>

                    {!! Form::label('update_date', 'Updated', ['class' => 'col-sm-2 control-label']) !!}
                    <div class=" col-sm-4">
                        <div class="input-group ">
                            {!! Form::text('update_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                            <span class="input-group-addon">
                                    <i class="fa fa-exchange"></i>
                                </span>
                            {!! Form::text('update_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="status"> Status </label>
                    <div class="col-sm-10">
                        <div class="control-group">
                            <div class="radio">
                                <label>
                                    {!! Form::radio('view_status', 'active', false, ['class' => 'ace']) !!}
                                    <span class="lbl"> Read</span>
                                </label>
                                <label>
                                    {!! Form::radio('view_status', 'in-active', false, ['class' => 'ace']) !!}
                                    <span class="lbl"> UnRead</span>
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
{{--
<tr>
    <th class="center">&nbsp;</th>

                <span class="form-inline">
                    {!! Form::text('created_date_start', request('created-start-date'), ['class' => 'input-medium date-picker', 'placeholder' => 'From']) !!}
                    &nbsp; To &nbsp;
                    {!! Form::text('created_date_end', request('created-end-date'), ['class' => 'input-medium date-picker', 'placeholder' => 'To']) !!}

                </span>

    {!! Form::select('status', ['all' => 'All', '0' => 'Un-Read', '1' => 'Read'], request('status'), ['class' => 'form-control border-form']) !!}

        <button class="btn btn-xs btn-info" id="filter-btn">
            <i class="icon-filter bigger-120">&nbsp;Filter</i>
        </button>
</tr>--}}
