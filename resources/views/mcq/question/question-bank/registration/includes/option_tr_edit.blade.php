<tr class="option_value">
    <td>
        <input type="hidden" name="option_id[]" value="{{ $option->id }}">
        {!! Form::text('option_title[]', $option->option, ["id" => "option$i","class" => "col-xs-10 col-sm-11","rows"=>'1', "required"]) !!}
    </td>
    <td>
        @if(isset($option->image))
            <img src="{{ asset('images/'.$folder_name.'/'.$option->image) }}" class="img-preview" class="img-responsive" height="100px">
            <hr class="hr-2">
        @endif
        {!! Form::file('option_image_'.$i) !!}
    </td>
    <td>
        @if($data['row']->type == 'single')
            <div class="form-group">
                <div class="col-sm-8">
                    {!! Form::hidden('answer_status[]', $option->answer_status, ["class" => "col-xs-10 col-sm-11","rows"=>'1', "required"]) !!}
                    <label>{!! Form::radio('answer_status_marker[]', $option->answer_status, $option->answer_status, ["class" => "ace radio-maker"]) !!}<span class="lbl"> Mark Answer </span></label>
                    @include('includes.form_fields_validation_message', ['name' => 'answer_status'])
                </div>
            </div>
        @elseif($data['row']->type == 'multiple')
            <div class="form-group">
                <div class="col-sm-8">
                    {!! Form::hidden('answer_status[]', $option->answer_status, ["class" => "col-xs-10 col-sm-11","rows"=>'1', "required"]) !!}
                    <label>{!! Form::checkbox('answer_status_marker[]', $option->answer_status, $option->answer_status, ["class" => "ace check-maker"]) !!}<span class="lbl"> Mark Answer </span></label>
                    @include('includes.form_fields_validation_message', ['name' => 'answer_status'])
                </div>
            </div>
        @endif
    </td>
    <td>
        {!! Form::select('option_status[]', ['active' => 'Active', 'in-active' => 'Inactive'], request('status'), ['class' => 'from-control']) !!}
    </td>
    <td>
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                <i class="ace-icon fa fa-trash"></i>
            </button>
        </div>

    </td>
</tr>
