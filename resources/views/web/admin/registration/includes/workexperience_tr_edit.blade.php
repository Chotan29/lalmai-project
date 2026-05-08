@if(isset($workExperiences) && $workExperiences->count() > 0)
    @foreach($workExperiences as $workExperience)
        <tr class="option_value">
            <td>
                {!! Form::text('experience_info[]', $workExperience->experience_info, ["class" => "col-xs-12 col-sm-12"]) !!}
            </td>
            <td width="5%">
                <div class="btn-group">
                    <a href="{{ route('web.admin.registration.delete-workExperience', ['id' => $workExperience->id]) }}" class="btn-primary btn-sm btn-danger bootbox-confirm align-right" >
                        <i class="fa fa-trash bigger-130" title="Delete"></i> Delete
                    </a>
                </div>

            </td>
        </tr>
    @endforeach
@endif