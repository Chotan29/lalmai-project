{{-- Staff form: show dates as DD-MM-YYYY, submit as YYYY-MM-DD (DB format).
     Must be included AFTER inputMask_script and datepicker_script. --}}
<script type="text/javascript">
    jQuery(function ($) {
        var dateSelector = 'input[name="join_date"], input[name="date_of_birth"], input[name="date_of_relieving"], input[name="date_of_rejoin"]';
        var $dates = $(dateSelector);

        if (!$dates.length) return;

        /*Re-mask: DD-MM-YYYY*/
        try { $dates.unmask(); } catch (e) {}
        try { $dates.mask('99-99-9999', {placeholder: 'DD-MM-YYYY'}); } catch (e) {
            try { $dates.mask('99-99-9999'); } catch (e2) {}
        }

        /*Re-init datepicker: day-month-year format*/
        try {
            $dates.datepicker('remove');
        } catch (e) {}
        $dates.datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy',
            orientation: 'bottom'
        });

        $dates.attr('placeholder', 'DD-MM-YYYY');

        /*Existing values (edit page / old input) come as YYYY-MM-DD -> show DD-MM-YYYY*/
        $dates.each(function () {
            var m = this.value.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (m) this.value = m[3] + '-' + m[2] + '-' + m[1];
        });

        /*Before submit convert back DD-MM-YYYY -> YYYY-MM-DD so database saves correctly*/
        $('#validation-form').on('submit', function (e) {
            if (e.isDefaultPrevented()) return;
            $(dateSelector).each(function () {
                var m = this.value.match(/^(\d{2})-(\d{2})-(\d{4})$/);
                if (m) this.value = m[3] + '-' + m[2] + '-' + m[1];
            });
        });
    });
</script>
