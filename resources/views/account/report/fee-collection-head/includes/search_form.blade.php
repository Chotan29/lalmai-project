{{-- fg-filter marks this one form so its styling in css/custom.css cannot reach the other
     filter panels that share the .filter-form class. --}}
<div id="accordion" class="filter-form fg-filter accordion-style1 panel-group hidden-print">
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
                {{-- Labels sit above their fields rather than in grey boxes beside them: the
                     three controls then line up on one baseline and the row reads left to
                     right in the order it is filled in. --}}
                <div class="fg-filter-row">
                    {{-- Several Main Fee Heads can be chosen at once, and the report below is
                         then all of them added together. Meant for one admission split across
                         departments - fees that share no students. Where they do share students
                         the sheet says so, and the letter refuses. --}}
                    <div class="fg-field fg-field-head">
                        <label for="fee_heads">Fee Head</label>
                        {!! Form::select('fee_heads[]', $data['fee_heads'],
                                $data['fee_heads_selected'] ?? null,
                                ['class' => 'form-control chosen-select', 'id' => 'fee_heads',
                                 'multiple' => 'multiple']) !!}
                        <small class="fg-hint">Pick one or more Main Fee Heads &mdash; several are added together</small>
                    </div>

                    <div class="fg-field fg-field-range">
                        <label>Date Range</label>
                        <div class="input-group">
                            {!! Form::text('start_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "form-control input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                            <span class="input-group-addon"><i class="fa fa-arrow-right"></i></span>
                            {!! Form::text('end_date', null, ["placeholder" => "YYYY-MM-DD", "class" => "form-control input-mask-date date-picker", "data-date-format" => "yyyy-mm-dd"]) !!}
                        </div>
                        {{-- No longer required for a Main Fee Head. A fee is collected over a
                             season and nobody remembers the day the first receipt was written;
                             left empty, the report finds the first and last collection itself and
                             prints that span at the top. --}}
                        <small class="fg-hint">Leave empty for the whole fee</small>
                    </div>

                    {{-- The same sheet for one programme instead of all of them. Only the
                         programmes with money in the chosen fee and period are listed, so every
                         option leads to a page with something on it. Shown once a Main Fee Head
                         and a period have produced a report - before that there is nothing to
                         build the list from. --}}
                    @if(!empty($data['fg_programmes']) && count($data['fg_programmes']))
                        <div class="fg-field fg-field-head">
                            <label for="programme">Programme / Class</label>
                            {!! Form::select('programme',
                                    ['' => 'All programmes'] + $data['fg_programmes']->toArray(),
                                    $data['fg_programme_id'] ?? null,
                                    ['class' => 'form-control chosen-select', 'id' => 'programme']) !!}
                            <small class="fg-hint">Leave as All for the whole college</small>
                        </div>
                    @endif

                    <div class="fg-field fg-field-type">
                        <label for="report_type">Report Type</label>
                        {!! Form::select('report_type', [""=>"Select Report Type...", "daily"=>"Daily", "weekly"=>"Weekly", "monthly"=>"Monthly","yearly"=>"Yearly"], null, ['class' => 'form-control', 'id' => 'report_type']) !!}
                        @include('includes.form_fields_validation_message', ['name' => 'report_type'])
                        <small class="fg-hint">Not needed for a Main Fee Head</small>
                    </div>

                    <div class="fg-field fg-field-action">
                        <button class="btn btn-info fg-filter-btn" type="submit" id="filter-btn">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
