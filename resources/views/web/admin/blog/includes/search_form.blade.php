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
                        {!! Form::label('category_id', 'Category', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::select('category_id', $data['categories'], null, ["placeholder" => "Select Category", "class" => "form-control border-form"]) !!}
                            @include('includes.form_fields_validation_message', ['name' => 'category_id'])
                        </div>
                    </div>
                    <div class="space-4"></div>


                    <div class="form-group">
                        {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('title', null, ["placeholder" => "Title", "class" => "form-control border-form"]) !!}
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('description', 'Description', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::textarea('description', null, ["placeholder" => "Description", "class" => "form-control border-form","rows"=>"1"]) !!}
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('publish_date', 'Publish', ['class' => 'col-sm-1 control-label']) !!}
                        <div class=" col-sm-3">
                            <div class="input-group ">
                                {!! Form::text('publish_start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                                <span class="input-group-addon">
                                    <i class="fa fa-exchange"></i>
                                </span>
                                {!! Form::text('publish_end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                            </div>
                        </div>

                        {!! Form::label('create_date', 'Created', ['class' => 'col-sm-1 control-label']) !!}
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
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('seo_title', 'SEO Title', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('seo_title', null, ["placeholder" => "SEO Title", "class" => "form-control border-form"]) !!}
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('seo_keywords', 'SEO Keyword', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::text('seo_keywords', null, ["placeholder" => "abc, efg", "class" => "form-control border-form"]) !!}
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        {!! Form::label('seo_description', 'SEO Description', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10">
                            {!! Form::textarea('seo_description', null, ["placeholder" => "Seo Description", "class" => "form-control border-form","rows"=>"1"]) !!}
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