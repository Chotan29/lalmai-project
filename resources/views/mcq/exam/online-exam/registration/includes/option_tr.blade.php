<tr class="option_value">
    <td>
        {!! Form::text('option_title[]', null, ["placeholder" => "Option $number", "id" => "option$number","class" => "col-xs-10 col-sm-11","rows"=>'1', "required"]) !!}
    </td>
    <td>
        {!! Form::file('option_image_'.$number) !!}
    </td>
    <td>
        @if($type == 'single')
            <div class="form-group">
                <div class="col-sm-8">
                    {!! Form::hidden('answer_status[]', 0, ["class" => "col-xs-10 col-sm-11","rows"=>'1', "required"]) !!}
                    <label>{!! Form::radio('answer_status_marker[]', true, false, ["class" => "ace radio-maker"]) !!}<span class="lbl"> Mark Answer </span></label>
                    @include('includes.form_fields_validation_message', ['name' => 'answer_status'])
                </div>
            </div>
        @elseif($type == 'multiple')
            <div class="form-group">
                <div class="col-sm-8">
                    {!! Form::hidden('answer_status[]', 0, ["class" => "col-xs-10 col-sm-11","rows"=>'1', "required"]) !!}
                    <label>{!! Form::checkbox('answer_status_marker[]', true, false, ["class" => "ace check-maker"]) !!}<span class="lbl"> Mark Answer </span></label>
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

<script>
    $('.radio-maker').on('click' , function(){
        $(this).closest("table").find('input[name="answer_status[]"]').val('0');
        $(this).closest("tr").find('input[name="answer_status[]"]').val('1');
    });

    $('.check-maker').on('click' , function(){

        if($(this).closest('tr').find('input:checkbox').is(':checked'))
            $(this).closest("tr").find('input[name="answer_status[]"]').val('1');
        else
            $(this).closest("tr").find('input[name="answer_status[]"]').val('0');

   });
</script>

