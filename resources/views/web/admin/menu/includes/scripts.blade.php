<script>
    $(document).ready(function () {

        $('#menu-pages-html').click(function () {
            $.ajax({
                url: '{{ route($base_route.'.menu-pages-html') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function (response) {
                    var data = $.parseJSON(response);

                    if (data.error) {
                        $.notify(data.message, "warning");
                    } else {

                        $('#page_value_wrapper').append(data.html);
                        $.notify(data.message, "success");
                    }
                }
            });

        });

    });
</script>