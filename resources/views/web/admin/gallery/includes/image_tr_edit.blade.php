<tr class="option_value">
    <td width="5%">
        <input type="hidden" name="gallery_id[]" value="{{ $image->id }}">
        <div class="btn-group">
                <span class="btn btn-xs btn-primary" >
                    <i class="fa fa-arrows bigger-120"></i>
                </span>
        </div>
    </td>
    <td>
        {{--{!! WebsiteViewHelper::loadImage($image->image, $folder_name, '300') !!}--}}
        @if(isset($image->image))
            <img src="{{ asset('web/'.$folder_name.'/'.$image->image) }}" class="img-preview" class="img-responsive" width="300px">
            <hr class="hr-2">
        @endif
        {!! Form::file('gallery_image[]') !!}
    </td>
    <td>
        {!! Form::text('gallery_caption[]', $image->caption, ["placeholder" => "Caption", "class" => "col-xs-10 col-sm-11"]) !!}
    </td>
    <td>
        {!! Form::select('gallery_status[]', ['active' => 'Active', 'in-active' => 'Inactive'], $image->status, ['class' => 'from-control']) !!}
    </td>
    <td>
        <div class="btn-group">
            <button type="button" class="btn btn-xs btn-danger" onclick="$(this).closest('tr').remove();">
                <i class="ace-icon fa fa-trash"></i>
            </button>
        </div>

    </td>
</tr>

