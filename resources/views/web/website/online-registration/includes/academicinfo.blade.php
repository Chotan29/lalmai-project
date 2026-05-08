<hr class="hr-double">
<h4 class="header large lighter blue">Academic Qualification</h4>
<hr class="hr-double">
<div class="form-group">
    <table id="responsive" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th width="90%">Detail</th>
                <th width="10%">
                    <button type="button" class="btn btn-xs btn-primary pull-right" id="load-academicinfo-html">
                        <i class="fa fa-plus" aria-hidden="true"></i> Qualification
                    </button>
                </th>
            </tr>
        </thead>

        <tbody id="academicInfo_wrapper">

        @if (isset($data['academicInfo-html']))
            {!! $data['academicInfo-html'] !!}
        @endif

        </tbody>

    </table>
</div>


