
<div class="form-group">
    <table id="option-table" class="table-bordered text-center" width="100%">
        <thead>
        <tr>
            <th width="30%">Option</th>
            <th>Image</th>
            <th>Answer</th>
            <th>{{ __('common.status')}}</th>
            <th>
                <button type="button" class="btn btn-xs btn-primary pull-right" id="load-option-html">
                    <i class="fa fa-plus bigger-120"></i> Add More Option
                </button>
            </th>
        </tr>
        </thead>

        <tbody id="option_wrapper">

            @if (isset($data['options']) && $data['options']->count() > 0)
                @php($i=1)
                @foreach($data['options'] as $option)
                    @include($view_path.'.registration.includes.option_tr_edit')
                @php($i++)
                @endforeach
            @endif

        </tbody>

    </table>
</div>