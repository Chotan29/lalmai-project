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
                {{--{!! Form::open(['route' => $base_route.'.transfer','method' => 'GET', 'class' => 'form-horizontal', "enctype" => "multipart/form-data"]) !!}--}}
                    <div class="clearfix">
                        @include('student.includes.search_form')

                        {{--Only this screen asks who passed, so the exam pickers live here and
                            not in the student filter every other screen shares.--}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Exam Result</label>
                            <div class="col-sm-4">
                                {!! Form::select('exam_group', $data['exam_groups'], $data['exam_group_selected'], ['class' => 'form-control', 'id' => 'filter-exam-group']) !!}
                            </div>

                            <label class="col-sm-1 control-label">Pass / Fail</label>
                            <div class="col-sm-2">
                                {!! Form::select('result_filter', $data['result_filters'], $data['result_filter_selected'], ['class' => 'form-control', 'id' => 'filter-result']) !!}
                            </div>

                            <div class="col-sm-3">
                                <span class="grey">Pick the exam first &mdash; the result column stays empty until you do.</span>
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
                {{--{!! Form::close() !!}--}}
            </div>
        </div>
    </div>
</div>