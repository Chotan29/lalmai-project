<h4 class="header large lighter blue"><i class="ace-icon glyphicon glyphicon-certificate"></i>Academic Qualification</h4>

<div class="form-group">
    <table id="responsive" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th width="90%">Detail</th>
                <th width="10%">
                    <button type="button" class="btn btn-xs btn-primary pull-right" id="load-academicinfo-html">
                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i> Qualification
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


