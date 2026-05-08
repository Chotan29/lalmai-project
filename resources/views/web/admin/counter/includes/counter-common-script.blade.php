<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script src="{{ asset('assets/js/fontawesome-iconpicker.js') }}"></script>

<script>
    $(function() {
        $('.icon-picker').iconpicker({
            title: 'Search FontAwesome Icons',
            selectedCustomClass: 'label label-success',
            placement:'bottomLeft',
        });

    });

</script>
