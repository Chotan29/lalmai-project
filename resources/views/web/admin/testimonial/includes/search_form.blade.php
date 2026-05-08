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
                <div class="clearfix">
                    <div class="form-group">
                        {!! Form::label('name', 'Full Name', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('name', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

                            @include('includes.form_fields_validation_message', ['name' => 'Name'])
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('comment', 'Testimonial', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::textarea('comment', null, ["placeholder" => "Comment", "class" => "form-control border-form form-control","rows"=>'3']) !!}

                            @include('includes.form_fields_validation_message', ['name' => 'comment'])
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('email', 'Email', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('email', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'email'])
                        </div>
                        {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            <!-- input field attributes-->
                            {!! Form::text('address', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}

                            @include('includes.form_fields_validation_message', ['name' => 'address'])
                        </div>
                    </div>

                    <div class="space-4"></div>
                    <!-- label-->
                    <div class="form-group">
                        {!! Form::label('office', 'Office', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('office', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'office'])
                        </div>

                        {!! Form::label('position', 'Designation', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('position', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'position'])
                        </div>
                    </div>

                    <div class="space-4"></div>
                    <!-- label-->
                    <div class="form-group">
                        {!! Form::label('link', 'Link', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('link', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'link'])
                        </div>
                    </div>

                    <div class="space-4"></div>
                    <div class="form-group">
                        {!! Form::label('create_date', 'Created', ['class' => 'col-sm-2 control-label']) !!}
                        <div class=" col-sm-3">
                            <div class="input-group ">
                                {!! Form::text('create_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                <span class="input-group-addon">
                                    <i class="fa fa-exchange"></i>
                                </span>
                                {!! Form::text('create_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                            </div>
                        </div>

                        {!! Form::label('update_date', 'Updated', ['class' => 'col-sm-1 control-label']) !!}
                        <div class=" col-sm-3">
                            <div class="input-group ">
                                {!! Form::text('update_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                <span class="input-group-addon">
                                    <i class="fa fa-exchange"></i>
                                </span>
                                {!! Form::text('update_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                            </div>
                        </div>

                        <label class="col-sm-1 control-label" for="status"> Status </label>
                        <div class="col-sm-2">
                            <div class="control-group">
                                <div class="radio">
                                    <label>
                                        {!! Form::radio('status', 'active', false, ['class' => 'ace']) !!}
                                        <span class="lbl"> Active</span>
                                    </label>
                                    <label>
                                        {!! Form::radio('status', 'in-active', false, ['class' => 'ace']) !!}
                                        <span class="lbl"> Inactive</span>
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