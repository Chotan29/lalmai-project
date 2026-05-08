<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script type="text/javascript">

       function loadPaymentLedger($this) {
        $.ajax({
            type: 'POST',
            url: '{{ route('inventory.product.find-category') }}',
            data: {
                _token: '{{ csrf_token() }}',
                cat_id: $this.value
            },
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.error) {
                    $.notify(data.message, "warning");
                } else {
                    $('.paymentledger').html('').append('<option value="0">Select Sub Category....</option>');
                    $.each(data.paymentledger, function(key,valueObj){
                        $('.paymentledger').append('<option value="'+valueObj.subCategoryId+'">'+valueObj.subCategoryTitle+'</option>');
                    });
                }
            }
        });

    }


    /*Change Field Value on Capital Letter When Keyup*/
    $(function() {
        $('.upper').keyup(function() {
            this.value = this.value.toUpperCase();
        });
    });
    /*end capital function*/





</script>