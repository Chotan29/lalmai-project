{{--
<div class="row">
    <div class="col-md-2 align-left">
        @if(isset($generalSetting->logo))
            <img src="{{ asset('images'.DIRECTORY_SEPARATOR.'setting'.DIRECTORY_SEPARATOR.'general'.DIRECTORY_SEPARATOR.$generalSetting->logo) }}" width="150px">
        @endif
    </div>
    <div class="col-md-10" style="">
        <div class="text-center">
            <h6 class="no-margin-top" style="font-family: 'Times New Roman', cursive; font-size: 26px; margin-bottom: 2px">
                {{isset($generalSetting->salogan)?$generalSetting->salogan:""}}
            </h6>
            <h2 class="no-margin-top" style="font-family: 'Times New Roman', cursive; font-size: 24px;margin-bottom: 2px;margin-right: 98px">
                <strong>{{isset($generalSetting->institute)?$generalSetting->institute:""}}</strong>
            </h2>
            <h5 class="no-margin-top" style="font-family: 'Times New Roman', cursive; font-size: 14px;margin-bottom: 2px;margin-right: 72px;">
                {{isset($generalSetting->address)?$generalSetting->address:""}}
            </h5>
            <h5 class="no-margin-top" style="font-family: 'Times New Roman', cursive; font-size: 14px;margin-bottom: 2px;margin-right: 93px">
                {{isset($generalSetting->phone)?$generalSetting->phone:""}}{{isset($generalSetting->email)?', '.$generalSetting->email:""}}
            </h5>
            <h5 class="no-margin-top" style="font-family: 'Times New Roman', cursive; font-size: 14px">
                {{isset($generalSetting->website)?$generalSetting->website:""}}
            </h5>
        </div>
    </div>
</div>--}}
@include('print.includes.institution-detail')