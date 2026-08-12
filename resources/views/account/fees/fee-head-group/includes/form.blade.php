@php($row = isset($data['row']) ? $data['row'] : null)

<div class="form-group">
    {!! Form::label('title', 'Main Fee Head', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('title', $row ? $row->title : null, ["placeholder" => "e.g. ADMISSION FEE 2025-2026", "class" => "form-control border-form upper", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>

    {!! Form::label('session', 'Session', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('session', $row ? $row->session : null, ["placeholder" => "e.g. 2025-2026", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'session'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('total_amount', 'Total Amount', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('total_amount', $row ? ($row->total_amount + 0) : null, ["placeholder" => "e.g. 7400", "class" => "form-control border-form", "id" => "totalAmount", "required"]) !!}
        <span class="help-block small">This is the only amount the student sees and pays.</span>
        @include('includes.form_fields_validation_message', ['name' => 'total_amount'])
    </div>

    {!! Form::label('description', 'Note', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-4">
        {!! Form::text('description', $row ? $row->description : null, ["placeholder" => "Optional", "class" => "form-control border-form"]) !!}
    </div>
</div>

<div class="hr hr-18 dotted hr-double"></div>

<h4 class="header smaller lighter blue">
    <i class="fa fa-list" aria-hidden="true"></i>&nbsp;Sub Heads
    <small>&nbsp;&mdash;&nbsp;drag to reorder. Money fills them top to bottom, so keep college heads above department heads.</small>
</h4>

<div class="table-responsive">
    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th width="5%"></th>
                <th width="55%">Sub Head</th>
                <th width="30%">Amount</th>
                <th width="10%"></th>
            </tr>
        </thead>
        <tbody id="sub_head_wrapper">
            @if($row && $row->items->count() > 0)
                @foreach($row->items as $item)
                    @include($view_path.'.includes.head_tr', ['item' => $item])
                @endforeach
            @else
                @include($view_path.'.includes.head_tr')
            @endif
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="align-right">Sub Head Total</th>
                <th colspan="2">
                    <span id="subHeadTotal">0.00</span>
                    <span id="subHeadBalanceNote" class="small"></span>
                </th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="clearfix">
    <button type="button" class="btn btn-sm btn-success" id="add-sub-head">
        <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add Sub Head
    </button>

    {{-- Creating a sub head used to mean leaving this page for Fees Head and starting the whole
         fee again from an empty form. Only offered to whoever may create fee heads anyway - the
         route carries the same permission, so hiding the button is courtesy, not the guard. --}}
    @if(auth()->user() && (auth()->user()->ability('super-admin', 'fees-head-add')))
        <button type="button" class="btn btn-sm btn-default" id="toggle-new-sub-head">
            <i class="fa fa-plus-square-o" aria-hidden="true"></i>&nbsp;Not on the list? Create a Sub Head
        </button>
    @endif
</div>

@if(auth()->user() && (auth()->user()->ability('super-admin', 'fees-head-add')))
    {{-- Plain inputs, not a nested form: this block sits inside the Main Fee Head form, and a
         form inside a form is not valid markup - the browser drops the inner one and the Enter
         key would submit the fee instead. Saved by ajax. --}}
    <div id="new-sub-head-panel" class="well" style="display:none; margin-top:10px;">
        <div class="row">
            <div class="col-sm-5">
                <label for="new_sub_head_title">Sub Head Name</label>
                <input type="text" id="new_sub_head_title" class="form-control" maxlength="100"
                       placeholder="e.g. LIBRARY FEE">
            </div>
            <div class="col-sm-3">
                <label for="new_sub_head_collected_by">Collected By</label>
                <select id="new_sub_head_collected_by" class="form-control">
                    <option value="college">College</option>
                    <option value="department">Department</option>
                </select>
                <span class="grey small">Decides which side of the fee head report it counts on.</span>
            </div>
            <div class="col-sm-2">
                <label for="new_sub_head_amount">Default Amount</label>
                <input type="number" id="new_sub_head_amount" class="form-control" min="0" step="1"
                       placeholder="0">
            </div>
            <div class="col-sm-2">
                <label class="hidden-xs">&nbsp;</label>
                <button type="button" class="btn btn-primary btn-block" id="save-new-sub-head">
                    <i class="fa fa-save" aria-hidden="true"></i>&nbsp;Save
                </button>
            </div>
        </div>
        <div id="new_sub_head_error" class="text-danger small" style="margin-top:6px;"></div>
    </div>
@endif

<div class="hr hr-24"></div>

{{-- The row template the Add button clones.

     Its fields are disabled, and that is not decoration: this block sits inside the form, so a
     live template row would post an empty sub head and an empty amount on every save and fail
     validation before the real rows were ever looked at. Disabled fields are not submitted. The
     Add button re-enables them on the copy it makes. --}}
<table style="display:none">
    <tbody id="sub_head_template">
        @include($view_path.'.includes.head_tr', ['template' => true])
    </tbody>
</table>
