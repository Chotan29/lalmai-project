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
                <div class="form-group">
                    <label class="col-sm-2 control-label">Application For</label>
                    <div class="col-sm-5">
                        <select name="web_registration_programmes_id" class="form-control" id="form-field-select-3" data-placeholder="Choose a Programme..." >
                            <option value=""> Choose Application For </option>
                            @foreach( $data['programmes'] as $key => $programme)
                                <option value="{{ $key }}">{{ $programme }}</option>
                            @endforeach
                        </select>
                    </div>

                    {!! Form::label('reg_date', 'Date', ['class' => 'col-sm-1 control-label']) !!}
                    <div class=" col-sm-4">
                        <div class="input-group ">
                            {!! Form::text('start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                            <span class="input-group-addon">
                                    <i class="fa fa-exchange"></i>
                                </span>
                            {!! Form::text('end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "input-sm form-control border-form input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label',]) !!}
                    <div class="col-sm-5">
                        {!! Form::text('name', null, ["class" => "form-control border-form"]) !!}
                    </div>
                    {!! Form::label('reg_no', 'RegNo', ['class' => 'col-sm-1 control-label',]) !!}
                    <div class="col-sm-4">
                        {!! Form::text('reg_no', null, ["class" => "form-control border-form"]) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('sex', 'Sex', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::select('sex', __('common.gender'), null, ['class'=>'form-control border-form']); !!}
                    </div>

                    {!! Form::label('date_of_birth', 'Date of Birth', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('date_of_birth', null, ["data-date-format" => "dd-mm-yyyy", "class" => "form-control border-form date-picker input-mask-date"]) !!}
                    </div>

                    {!! Form::label('blood_group', __('form_fields.student.fields.blood_group'), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('blood_group', null, ["class" => "form-control border-form"]) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('religion', __('form_fields.student.fields.religion'), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('religion', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                    </div>

                    {!! Form::label('caste', __('form_fields.student.fields.caste'), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('caste', null, ["class" => "form-control border-form"]) !!}
                    </div>

                    {!! Form::label('nationality', __('form_fields.student.fields.nationality'), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('nationality', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('mother_tongue', __('form_fields.student.fields.mother_tongue'), ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('mother_tongue', null, ["class" => "form-control border-form"]) !!}
                    </div>

                    {!! Form::label('state', 'State of Permanent Residence', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('state', null, ["class" => "form-control border-form"]) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('medicine_info', 'Allergic to any medicine. If yes, Detail', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                        {!! Form::textarea('medicine_info', null, ["class" => "form-control border-form", "rows"=>"1"]) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('disease_info', 'Affected by any recurring disease. If yes, Detail', ['class' => 'col-sm-4 control-label']) !!}
                    <div class="col-sm-8">
                        {!! Form::textarea('disease_info', null, ["class" => "form-control border-form", "rows"=>"1"]) !!}
                    </div>
                </div>

                <div class="label label-warning arrowed-in arrowed-right arrowed">Guardian Detail</div>
                <hr class="hr-8">
                <div class="form-group">
                    {!! Form::label('guardian_name', 'Name of Father/Guardian', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-5">
                        {!! Form::text('guardian_name', null, [ "class" => "form-control border-form"]) !!}
                    </div>

                    {!! Form::label('guardian_relation', 'Guardian Relation', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('guardian_relation', null, ["class" => "form-control border-form"]) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('guardian_occupation', 'Occupation', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-5">
                        {!! Form::text('guardian_occupation', null, [ "class" => "form-control border-form"]) !!}
                    </div>

                    {!! Form::label('guardian_annual_income', 'Annual Income', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-3">
                        <div class="input-group ">
                            {!! Form::text('annual_income_start', null, ["class" => "form-control border-form"]) !!}
                            <span class="input-group-addon">
                                    <i class="fa fa-exchange"></i>
                                </span>
                            {!! Form::text('annual_income_end', null, ["class" => "form-control border-form"]) !!}
                        </div>
                    </div>
                </div>


                <div class="label label-warning arrowed-in arrowed-right arrowed">Permanent Address</div>
                <hr class="hr-8">
                <div class="form-group">
                    {!! Form::label('address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('address', null, ["class" => "form-control border-form"]) !!}
                    </div>
                </div>
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

                <div class="label label-warning arrowed-in arrowed-right arrowed">Mailing Address</div>

                <hr class="hr-8">
                <div class="form-group">
                    {!! Form::label('mailing_address', 'Address', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-10">
                        {!! Form::text('mailing_address', null, ["class" => "form-control border-form"]) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('mailing_tel', 'Telephone', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('mailing_tel', null, ["class" => "form-control border-form input-mask-phone"]) !!}
                    </div>

                    {!! Form::label('mailing_cell_1', 'Mobile', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('mailing_cell_1', null, ["class" => "form-control border-form input-mask-cell"]) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('mailing_cell_2', 'Alternative Contact', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('mailing_cell_2', null, ["class" => "form-control border-form input-mask-cell"]) !!}
                    </div>

                    {!! Form::label('mailing_email', 'E-mail', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('mailing_email', null, ["class" => "form-control border-form"]) !!}
                    </div>
                </div>
                <hr class="hr-8">
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
                                    <span class="lbl"> Approve</span>
                                </label>
                                <label>
                                    {!! Form::radio('status', 'in-active', false, ['class' => 'ace']) !!}
                                    <span class="lbl"> Reject</span>
                                </label>
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