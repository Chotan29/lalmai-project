<div class="footer hidden-print">
    <div class="footer-inner">
        <div class="footer-content">
            <span class="bigger-120">
                @if(isset($generalSetting->copyright))
                    @if(isset($generalSetting->institute))
                        <span class="blue bolder"><a href="{{isset($generalSetting->website)?$generalSetting->website:'#'}}">{{$generalSetting->institute}}</a></span>
                    @endif
                    &copy;
                    {!! $generalSetting->copyright !!}
                @else
                    &copy;
                    <a href="#" target="_blank">Artificial Intelligent Technology Pvt. Ltd.</a>
                @endif
                    @if(env('APP_STATUS')=='true')
                            <span><i class="ace-icon fa fa-certificate bigger-110 red"></i> License Expired</span>
                    @else
                            <span><i class="ace-icon fa fa-certificate bigger-110 green"></i></span>
                    @endif

                    @if(env('HELP_STATUS')=='true')
                            <span><i class="ace-icon fa fa-phone bigger-110 red"></i> Support Expired</span>
                    @else
                            <span> <i class="ace-icon fa fa-comment bigger-110 green"></i></span>
                    @endif
            </span>
        </div>
    </div>
</div>

