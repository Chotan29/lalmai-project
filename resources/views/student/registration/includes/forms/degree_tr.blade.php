<div class="form-group">
    <div class="table-responsive">
        <table id="responsive" class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th>DMC's</th>
                <th>Total Marks</th>
                <th>Obtained Marks</th>
                <th>Receive In College(Date)</th>
                <th>Issue Date</th>
                <th></th>
            </tr>
            </thead>

            <tbody id="degree_wrapper">
                @if(isset($data['degrees']) && $data['degrees']->count() >0)
                    @foreach($data['degrees'] as $degree)
                        <tr class="option_value">
                            <td>
                                {!! Form::label('degree_title', $degree->title, ['class' => 'col-sm-12 control-label']) !!}

                                {!! Form::hidden('degrees_id[]', $degree->id, ["class" => "col-md-12"]) !!}
                                {{--["class" => "col-xs-10 col-sm-11"]--}}
                            </td>

                            <td>
                                {!! Form::number('total_marks[]', $degree->total_marks>0?$degree->total_marks:0, ["class" => "col-md-12 mark-obtained calculate-percent"]) !!}
                            </td>

                            <td>
                                {!! Form::number('obtained_mark[]',$degree->obtained_mark>0?$degree->obtained_mark:0, ["class" => "col-md-12 maximum-mark calculate-percent"]) !!}
                            </td>

                            <td>
                                {!! Form::text('receive_in_college_date[]', $degree->receive_in_college_date, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
                            </td>

                            <td>
                                {!! Form::text('issue_date[]', $degree->issue_date, ["class" => "form-control date-picker border-form input-mask-date"]) !!}
                            </td>
                            <td></td>

                        </tr>
                    @endforeach
                @endif

            </tbody>
        </table>
    </div>
</div>


<script>
    $(function() {
        $('.calculate-percent').change(function() {
            $obtainMark = $(this).closest('tr').find('.mark-obtained').val();
            $maximumMark = $(this).closest('tr').find('.maximum-mark').val();
            $percentage = (($obtainMark * 100) / $maximumMark).toFixed(2);
            $(this).closest('tr').find('.percent-value').val($percentage);
        });
    });
</script>

