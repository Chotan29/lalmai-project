
<script src="{{ asset('website/js/jquery.js') }}"></script>
<script src="{{ asset('website/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('website/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('website/js/jquery.fancybox.pack.js') }}"></script>
<script src="{{ asset('website/js/jquery.fancybox-media.js') }}"></script>
<script src="{{ asset('website/js/owl.js') }}"></script>
<script src="{{ asset('website/js/mixitup.js') }}"></script>
<script src="{{ asset('website/js/appear.js') }}"></script>
<script src="{{ asset('website/js/script.js') }}"></script>

{{--<script src="http://maps.google.com/maps/api/js?key=AIzaSyBKS14AnP3HCIVlUpPKtGp7CbYuMtcXE2o"></script>
<script src="js/map-script.js"></script>--}}
<script>
   /* $(document).ready(function () {

        $('.counter').each(function () {
            $(this).prop('Counter',0).animate({
                Counter: $(this).text()
            }, {
                duration: 4000,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
    });*/
</script>

{!! isset($generalSetting->footer_codes)?$generalSetting->footer_codes:"" !!}
