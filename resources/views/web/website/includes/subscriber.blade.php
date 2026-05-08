<!--Newsletter Area Start-->
<div class="newsletter-area">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-sm-5">
                <div class="newsletter-content">
                    <h3>SUBSCRIBE</h3>
                    <h2>TO OUR NEWSLETTER</h2>
                </div>
            </div>
            <div class="col-md-7 col-sm-7">
                <div class="newsletter-form angle">
                    <form action="{{ route('web.subscribers.store') }}" method="POST" id="mc-form" class="mc-form footer-newsletter fix">
                        <div class="subscribe-form">
                            {{ csrf_field() }}
                            <input id="mc-email" type="email" name="email" placeholder="Enter your email address..." required>
                            <button id="mc-submit" type="submit">SUBSCRIBE</button>
                        </div>
                    </form>
                    <!-- mailchimp-alerts Start -->
                   {{-- <div class="mailchimp-alerts text-centre fix pull-right">
                        <div class="mailchimp-submitting"></div><!-- mailchimp-submitting end -->
                        <div class="mailchimp-success">{{ session()->get('subscribe') }}</div><!-- mailchimp-success end -->
                        <div class="mailchimp-error">{{ session()->get('subscribe_error') }}</div><!-- mailchimp-error end -->
                    </div>--}}
                    <!-- mailchimp-alerts end -->
                </div>
            </div>
        </div>
    </div>
</div>
<!--End of Newsletter Area-->