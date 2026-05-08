<!--Start of Tawk.to Script-->
{{--<script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5b00fb3b5f7cdf4f05345e19/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
</script>--}}
<!--End of Tawk.to Script-->

<!-- Global site tag (gtag.js) - Google Analytics -->
{{--
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-163356973-2"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-163356973-2');
</script>
--}}


<!-- Facebook Pixel Code -->
{{--<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '296383100789172');
    fbq('track', 'PageView');
</script>
<noscript>
    <img height="1" width="1" style="display:none"
               src="https://www.facebook.com/tr?id=296383100789172&ev=PageView&noscript=1"
    />
</noscript>--}}
<!-- End Facebook Pixel Code -->
@if(isset($generalSetting->tracking_code))
    {!! $generalSetting->tracking_code !!}
@endif