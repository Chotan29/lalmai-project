<style>
    #subjectsTable thead th { background: #f5f8fb; color: #2c3e50; font-size: 11px; text-transform: uppercase; letter-spacing: .3px; text-align: center; vertical-align: middle; border-bottom: 2px solid #337ab7; }
    #subjectsTable thead tr.sub-head th { font-size: 10px; color: #7f8c9a; border-bottom: 1px solid #dce4ec; }
    #subjectsTable td { vertical-align: middle; }
    #subjectsTable td input.form-control { text-align: center; padding: 4px 6px; height: 30px; font-size: 13px; }
    #subjectsTable td input[name="subjects_id[]"] { text-align: left; font-weight: 600; color: #2c3e50; background: #fdfdfd; }
    #subjectsTable tbody tr:hover { background: #f0f6fc !important; }
    #subjectsTable tbody tr[style*="lightgrey"] td input { background: #eef1f4; }
    #subjectsTable td input.sched-mcq { background: #f4f7fa; color: #337ab7; font-weight: 600; cursor: not-allowed; }
    .sched-th-theory { border-left: 2px solid #dce4ec !important; }
    .sched-th-practical { border-left: 2px solid #dce4ec !important; }
</style>

<div class="sched-panel">
    <div class="sched-panel-header clearfix">
        <h4><i class="fa fa-calendar-check-o"></i> Subject Schedule &amp; Marks</h4>
        <div class="pull-right">
            <button type="button" id="applyFirstRowAll" class="btn btn-xs btn-info" title="Copy 1st row's time & marks to every row below">
                <i class="fa fa-clone"></i> Apply 1st Row Time &amp; Marks to All
            </button>
        </div>
    </div>
    <div class="sched-panel-body" style="padding:0;">
        <table id="subjectsTable" class="table table-bordered table-hover" style="margin-bottom:0;">
            <thead>
            <tr>
                <th rowspan="2" style="width:4%;">Sort</th>
                <th rowspan="2" style="width:26%; text-align:left;">Subject</th>
                <th rowspan="2" style="width:11%;">Exam Date</th>
                <th rowspan="2" style="width:9%;">Start Time</th>
                <th rowspan="2" style="width:9%;">End Time</th>
                <th colspan="2" class="sched-th-theory">Theory Mark</th>
                <th rowspan="2" class="sched-th-theory" style="width:7%;" title="From Subject setup (Academic > Subject)">MCQ<br><small style="font-weight:normal; text-transform:none;">(subject)</small></th>
                <th colspan="2" class="sched-th-practical">Practical Mark</th>
                <th rowspan="2" style="width:5%;"></th>
            </tr>
            <tr class="sub-head">
                <th class="sched-th-theory" style="width:7%;">Full</th>
                <th style="width:7%;">Pass</th>
                <th class="sched-th-practical" style="width:7%;">Full</th>
                <th style="width:7%;">Pass</th>
            </tr>
            </thead>

            <tbody id="subject_wrapper">
            </tbody>
        </table>
    </div>
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
