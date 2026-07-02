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
                        <div class="col-sm-5">
                            {!! Form::text('name', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                        </div>

                        {!! Form::label('position', 'Designation', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            <!-- input field attributes-->
                            {!! Form::text('position', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                        </div>
                    </div>

                    <div class="space-4"></div>
                    <div class="form-group">
                        {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('address', null, ["class" => "form-control border-form"]) !!}
                        </div>
                    </div>
                    <div class="space-4"></div>
                    <div class="form-group">
                        {!! Form::label('tel', 'Telephone', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('tel', null, ["class" => "form-control border-form input-mask-phone"]) !!}
                        </div>

                        {!! Form::label('cell_1', 'Mobile', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('cell_1', null, ["class" => "form-control border-form input-mask-cell"]) !!}
                        </div>
                    </div>
                    <div class="space-4"></div>
                    <div class="form-group">
                        {!! Form::label('cell_2', 'Alternative Contact', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('cell_2', null, ["class" => "form-control border-form input-mask-cell"]) !!}
                        </div>

                        {!! Form::label('email', __('form_fields.student.fields.email'), ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('email', null, ["class" => "form-control border-form"]) !!}
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::textarea('description', null, ["placeholder" => "Description", "class" => "form-control border-form", "rows"=>'2']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('facebook_url', 'Facebook URL', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('facebook_url', null, ["placeholder" => "Facebook URL", "class" => "form-control border-form "]) !!}
                        </div>

                        {!! Form::label('twitter_url', 'Twitter URL', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('twitter_url', null, ["placeholder" => "Twitter URL", "class" => "form-control border-form "]) !!}
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('whatsapp_url', 'WhatsApp URL', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('whatsapp_url', null, ["placeholder" => "WhatsApp URL", "class" => "form-control border-form "]) !!}

                        </div>

                        {!! Form::label('linkedIn_url', 'LinkedIn URL', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('linkedIn_url', null, ["placeholder" => "LinkedIn URL", "class" => "form-control border-form"]) !!}
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
                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('rank', 'Rank', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::number('rank', null, ["placeholder" => "Enter Rank", "class" => "form-control border-form"]) !!}

                        </div>

                        <label class="col-sm-2 control-label" for="status"> Status </label>

                        <div class="col-sm-4">
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

            </div>
        </div>
    </div>
</div>