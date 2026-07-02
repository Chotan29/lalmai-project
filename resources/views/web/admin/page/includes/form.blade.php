<div class="form-group">
    {!! Form::label('parent_id', 'Choose Parent', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('parent_id', $data['select_categories'], null, ['class' => 'form-control border-form']) !!}
        @include('includes.form_fields_validation_message', ['name' => 'parent_id'])
    </div>
</div>

<div class="form-group">
    {!! Form::label('title', 'Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('title', null, ["placeholder" => "", "class" => "form-control border-form", "required"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'title'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('short_desc', 'ShortDesc', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('short_desc', null, ["placeholder" => "Title Description", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'short_desc'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('page_type', 'Page Type', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('page_type', ['content-page' => 'Content Page', 'link-page' => 'Link Page', 'predefine-link' => 'Predefile Link'], null, ["class" => "form-control border-form", "id" => "page_type"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'page_type'])
    </div>
</div>

<div class="space-4"></div>


<div id="link">
    <div class="form-group">
        {!! Form::label('link', 'Link', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('link', null, ["placeholder" => "Link", "class" => "form-control border-form"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'link'])
        </div>
    </div>
</div>

<div id="content">
    @if (isset($data['row']))
        <div class="form-group">
            <label class="col-sm-2 control-label">Existing Image</label>
            <div class="col-sm-10">
                @if ($data['row']->image)
                    <img src="{{ asset('web/'.$folder_name.'/'.$data['row']->image) }}" width="500">
                @else
                    <p>No image.</p>
                @endif

            </div>
        </div>
    @endif

    <div class="space-4"></div>

    <div class="form-group">
        {!! Form::label('main_image', 'Page Banner', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::file('main_image') !!}
            @include('includes.form_fields_validation_message', ['name' => 'main_image'])
            <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Recommended Image Size 870px X 439px(72dpi)</span>
        </div>
    </div>

    <div class="space-4"></div>

    <div class="form-group">
        {!! Form::label('detail_desc', 'Description', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::textarea('detail_desc', null, ["placeholder" => "Detail Description", "class" => "form-control border-form summernote"]) !!}
            @include('includes.form_fields_validation_message', ['name' => 'detail_desc'])
        </div>
    </div>

</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('seo_title', 'SEO Title', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('seo_title', null, ["placeholder" => "SEO Title", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'seo_title'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('seo_keywords', 'SEO Keyword', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('seo_keywords', null, ["placeholder" => "SEO Keywords", "class" => "form-control border-form"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'seo_keywords'])
    </div>
</div>

<div class="space-4"></div>

<div class="form-group">
    {!! Form::label('seo_description', 'SEO Description', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::textarea('seo_description', null, ["placeholder" => "", "class" => "form-control border-form form-control","rows"=>"3"]) !!}
        @include('includes.form_fields_validation_message', ['name' => 'seo_description'])
    </div>
</div>

<div class="space-4"></div>


<div class="form-group">
    <label class="col-sm-2 control-label" for="status"> Status </label>

    <div class="col-sm-10">
        <div class="control-group">
            <div class="radio">
                <label>
                    {!! Form::radio('status', 'active', true, ['class' => 'ace']) !!}
                    <span class="lbl"> Active</span>
                </label>
                <label>
                    {!! Form::radio('status', 'in-active', false, ['class' => 'ace']) !!}
                    <span class="lbl"> Inactive</span>
                </label>
            </div>
            @include('includes.form_fields_validation_message', ['name' => 'status'])
        </div>
    </div>
</div>

<div class="space-4"></div>