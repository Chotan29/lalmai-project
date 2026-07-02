<div class="tabbable">
    <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="customscripts">
        <li class="active">
            <a data-toggle="tab" href="#header-script">
                <i class="ace-icon fa fa-code bigger-110"></i>
                Header Script
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#footer-script">
                <i class="ace-icon fa fa-code bigger-110"></i>
                Footer Script
            </a>
        </li>

        <li>
            <a data-toggle="tab" href="#post-foot-script">
                <i class="ace-icon fa fa-code"></i>
                Post Footer Script
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="header-script" class="tab-pane in active">
            <div class="btn btn-block btn-primary">Header Script</div>
            <hr class="hr-2" />
            <div class="form-group">
                <div class="col-sm-12">
                    {!! Form::textarea('header_codes', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'header_codes'])
                </div>
            </div>
        </div>

        <div id="footer-script" class="tab-pane">
            <div class="btn btn-block btn-primary">Footer Script</div>
            <hr class="hr-2" />
            <div class="form-group">
                <div class="col-sm-12">
                    {!! Form::textarea('footer_codes', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'footer_codes'])
                </div>
            </div>
        </div>

        <div id="post-foot-script" class="tab-pane">
            <div class="btn btn-block btn-primary">Post Foot Script</div>
            <hr class="hr-2" />
            <div class="form-group">
                <div class="col-sm-12">
                    {!! Form::textarea('post_detail_foot', null, ["placeholder" => "", "class" => "form-control border-form"]) !!}
                    @include('includes.form_fields_validation_message', ['name' => 'post_detail_foot'])
                    <span class="help-block"> <span class="help-button" data-rel="popover" data-trigger="hover" data-placement="left" data-content="More details." title="" data-original-title="Popover on hover">?</span> Post Footer Like <a href="https://platform.sharethis.com/inline-share-buttons" target="_blank">Sharethis Codes</a>, <a href="https://developers.facebook.com/docs/plugins/comments/" target="_blank"> Facebook Comment</a> Script.</span>
                </div>
            </div>
        </div>
    </div>
</div>

