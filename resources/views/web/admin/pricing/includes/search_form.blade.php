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
                        {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('title', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                        </div>
                    </div>
                    <div class="space-4"></div>
                    <div class="form-group">
                        {!! Form::label('new_price', 'New Price', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            {!! Form::number('new_price', null, ["placeholder" => "", "class" => "form-control border-form", "step" => "any"]) !!}
                        </div>
                        {!! Form::label('old_price', 'Old Price', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-2">
                            {!! Form::number('old_price', null, ["placeholder" => "", "class" => "form-control border-form",  "step" => "any"]) !!}
                        </div>
                        {!! Form::label('per_term', 'Term', ['class' => 'col-sm-1 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::text('per_term', null, ["placeholder" => "Monthly/Fixed", "class" => "form-control border-form"]) !!}
                        </div>
                    </div>
                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::textarea('description', null, ["placeholder" => "Description", "class" => "form-control border-form form-control", "rows"=>'2']) !!}
                        </div>
                    </div>
                    <div class="space-4"></div>
                    <div class="form-group">
                        {!! Form::label('rank', 'Rank', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::number('rank', null, ["placeholder" => "Enter Rank", "class" => "form-control border-form"]) !!}
                        </div>

                        {!! Form::label('tag', 'Tag', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-4">
                            {!! Form::text('tag', null, ["placeholder" => "Best", "class" => "form-control border-form"]) !!}
                        </div>

                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('link', 'Target Link', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-5">
                            {!! Form::text('link', null, ["placeholder" => "URL", "class" => "form-control border-form"]) !!}
                        </div>

                        {!! Form::label('open_in', 'Target Page', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-3">
                            {!! Form::select('open_in', ['_self' => 'Same Page', '_blank' => 'New Tab'], null, ["placeholder" => "Choose Target Page", "class" => "form-control border-form"]) !!}
                        </div>
                    </div>

                    <div class="space-4"></div>

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
                        <label class="col-sm-2 control-label" for="status"> Status </label>

                        <div class="col-sm-10">
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

                    <div class="space-4"></div>
                </div>
                <div class="clearfix form-actions">
                    <div class="align-right">
                        <button class="btn btn-info" onclick="submitForm()" type="submit" id="filter-btn">
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