<div class="form-group">
    <div class="text-right" style="margin-bottom:5px;">
        <button type="button" id="applyFirstRowAll" class="btn btn-xs btn-info" title="Copy 1st row's time & marks to every row below">
            <i class="fa fa-clone"></i> Apply 1st Row Time &amp; Marks to All
        </button>
    </div>
    <table id="subjectsTable" class="table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th>Sort</th>
            <th width="30%">Subject</th>
            <th>Date</th>
            <th>StartTime</th>
            <th>EndTime</th>
            <th>FM (T)</th>
            <th>PM (T)</th>
            <th>FM (P)</th>
            <th>PM (P)</th>
            <th></th>
        </tr>
        </thead>

        <tbody id="subject_wrapper">
        {{--@if($schedule)
        @include('examination.schedule.includes.subject_tr_rows')
        @endif--}}
        {{--@if (isset($data['schedule']))

            {!! $data['schedule'] !!}

        @endif--}}

        </tbody>

    </table>
</div>
@include('includes.scripts.inputMask_script')

<script>
    jQuery(function ($) {
        $('#applyFirstRowAll').on('click', function () {
            var $rows = $('#subject_wrapper tr.option_value');
            if ($rows.length < 2) {
                if (window.toastr) toastr.info('Load subjects first (need at least 2 rows).', 'Info:');
                return;
            }
            var fields = ['start_time[]', 'end_time[]', 'full_mark_theory[]',
                          'pass_mark_theory[]', 'full_mark_practical[]', 'pass_mark_practical[]'];
            var $first = $rows.first();
            $rows.slice(1).each(function () {
                var $row = $(this);
                fields.forEach(function (f) {
                    var v = $first.find('input[name="' + f + '"]').val();
                    var $target = $row.find('input[name="' + f + '"]');
                    if ($target.length && v !== undefined) $target.val(v);
                });
            });
            if (window.toastr) toastr.success('1st row time & marks applied to all rows. Now set each date.', 'Success:');
        });
    });
</script>