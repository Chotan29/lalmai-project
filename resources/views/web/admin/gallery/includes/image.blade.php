<div class="form-group">
    <table class="table-bordered text-center" WIDTH="100%">
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th>Image</th>
            <th>Alt Text/Caption</th>
            <th>{{ __('common.status')}}</th>
            <th>
                <button type="button" class="btn btn-xs btn-primary pull-right" id="load-image-html">
                    <i class="fa fa-plus bigger-120"></i> Add Image
                </button>
            </th>
        </tr>
        </thead>

        <tbody id="image_wrapper">
            @if (isset($data['images']) && $data['images']->count() > 0)
                @foreach($data['images'] as $image)
                    @include($view_path.'.includes.image_tr_edit')
                    @endforeach
                @endif

        </tbody>

    </table>
</div>