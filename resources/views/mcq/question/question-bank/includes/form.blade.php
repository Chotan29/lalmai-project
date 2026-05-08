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
                {!! Form::open(['route' => $base_route.'.index', 'method' => 'GET', 'class' => 'form-horizontal',
                              'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                <div class="clearfix">
                    <div class="form-horizontal" id="filterDiv">
                        <div class="form-group">
                            <label class="col-sm-1 control-label">Subject</label>
                            <div class="col-sm-5">
                                {!! Form::select('subjects_id', $data['subjects'], null, ['class' => 'form-control']) !!}
                                @include('includes.form_fields_validation_message', ['name' => 'subjects_id'])
                            </div>
                            <label class="col-sm-1 control-label">Group</label>
                            <div class="col-sm-2">
                                {!! Form::select('mcq_question_groups_id', $data['group'], null, ['class' => 'form-control subcategory']) !!}
                                @include('includes.form_fields_validation_message', ['name' => 'mcq_question_groups_id'])
                            </div>
                            <label class="col-sm-1 control-label">Level</label>
                            <div class="col-sm-2">
                                {!! Form::select('mcq_question_levels_id', $data['level'], null, ['class' => 'form-control']) !!}
                                @include('includes.form_fields_validation_message', ['name' => 'mcq_question_levels_id'])
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-1 control-label">Question</label>
                            <div class="col-sm-11">
                                {!! Form::text('question', null, ["class" => "form-control border-form", "rows"=>"2"]) !!}
                                @include('includes.form_fields_validation_message', ['name' => 'question'])
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-1 control-label">Mark</label>
                            <div class="col-sm-2">
                                {!! Form::text('mark', isset($data['mark'])?$data['mark']:null, ["class" => "form-control border-form"]) !!}
                                @include('includes.form_fields_validation_message', ['name' => 'mark'])
                            </div>

                            <label class="col-sm-2 control-label">Answer Type</label>
                            <div class="col-sm-3">
                                <select name="type" class="form-control" id="form-field-select-3" data-placeholder="Choose a Faculty/Program/Class..."  onChange ="appendOption(this)" >
                                    <option value="">  </option>
                                    <option value="single">Single</option>
                                    <option value="multiple">Multiple</option>
                                </select>
                                @include('includes.form_fields_validation_message', ['name' => 'type'])
                            </div>

                            <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-2">
                                <select class="form-control border-form" name="status" id="cat_id">
                                    <option value="all"> All </option>
                                    <option value="active" >{{ __('common.active')}}</option>
                                    <option value="in-active" >{{ __('common.in_active')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix form-actions">
                    <div class="align-right">
                        <button class="btn btn-info" type="submit" id="filter-btn">
                            <i class="fa fa-filter bigger-110"></i>
                            Filter
                        </button>
                    </div>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>

