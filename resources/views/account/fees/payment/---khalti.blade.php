@ability('super-admin', 'fees-payment-khalti-payment')
{{--<hr class="hr-2">--}}
{{--<!-- Place this where you need payment button -->--}}
{{--<button class="payment-button" style="background-color: #773292; color: #fff; border: none; padding: 5px 10px; border-radius: 2px;"--}}
{{--        data-due="{{ $net_balance*100 }}"--}}
{{--        data-product-identity="{{ $data['student']->reg_no }}"--}}
{{--        data-product-name="{{ ViewHelper::getSemesterById($feemaster->semester) }}-{{ ViewHelper::getFeeHeadById($feemaster->fee_head) }}">--}}
{{--    Pay with Khalti--}}
{{--</button>--}}
@endability

Khalti Active

<hr class="hr-2">
<!-- Place this where you need payment button -->
{{--<button class="payment-button" style="background-color: #773292; color: #fff; border: none; padding: 5px 10px; border-radius: 2px;"--}}
{{--        data-due="{{ 100*100 }}"--}}
{{--        data-product-identity="{{ $data['student']->reg_no }}"--}}
{{--        data-product-name="Registration Fee - API Testing">--}}
{{--    Pay with Khalti--}}
{{--</button>--}}

{{--<a href="#" data-amount='100' id='payment-button-2' class="btn btn-primary pay-khalti">Pay with Khalti</a>--}}

@section('js')

{{--    <script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>--}}
{{--    <script src="{{ asset('assets/resources/khalti/js/khalti-client.js') }}" type="text/javascript"></script>--}}

{{--    <link rel="stylesheet" href="https://rawgit.com/google/code-prettify/master/styles/sons-of-obsidian.css" />--}}
{{--    <script type="text/javascript">--}}

{{--        $(function(){--}}
{{--            // just show the live js here.--}}
{{--            $.ajax({url: "{{ asset('assets/resources/khalti/js/khalti-client.js') }}", success: function(resp){--}}
{{--                    $("#js-code-here").text(resp.trim());--}}
{{--                    addEventListener('load', function(event) { PR.prettyPrint(); }, false);--}}
{{--                }, dataType: 'html'});--}}
{{--            $.get({url: "example.js", success: function(resp){--}}
{{--                    $("#js-example-here").text(resp.trim());--}}
{{--                    addEventListener('load', function(event) { PR.prettyPrint(); }, false);--}}
{{--                }, dataType: 'html'});--}}
{{--        });--}}
{{--    </script>--}}

<script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>
<script>
    var config = {
        // replace the publicKey with yours
        "publicKey": "test_public_key_dc74e0fd57cb46cd93832aee0a390234",
        "productIdentity": "1234567890",
        "productName": "Dragon",
        "productUrl": "http://gameofthrones.wikia.com/wiki/Dragons",
        "paymentPreference": [
            "KHALTI",
            "EBANKING",
            "MOBILE_BANKING",
            "CONNECT_IPS",
            "SCT",
        ],
        "eventHandler": {
            onSuccess (payload) {
                // hit merchant api for initiating verfication
                console.log(payload);
            },
            onError (error) {
                console.log(error);
            },
            onClose () {
                console.log('widget is closing');
            }
        }
    };

    var checkout = new KhaltiCheckout(config);
    var btn = document.getElementById("payment-button");
    btn.onclick = function () {
        // minimum transaction amount must be 10, i.e 1000 in paisa.
        checkout.show({amount: 1000});
    }
</script>
@endsection
<button id="payment-button">Pay with Khalti</button>