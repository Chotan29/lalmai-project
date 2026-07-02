<h4 class="header large lighter blue"><i class="ace-icon glyphicon glyphicon-certificate"></i>Work Experience</h4>

<div class="form-group">
    <table id="responsive" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th width="90%">Experience Detail</th>
                <th width="10%">
                    <button type="button" class="btn btn-xs btn-primary pull-right" id="load-workexperience-html">
                        <i class="glyphicon glyphicon-plus" aria-hidden="true"></i> Experience
                    </button>
                </th>
            </tr>
        </thead>

        <tbody id="workExperience_wrapper">

        @if (isset($data['workExperience-html']))
            {!! $data['workExperience-html'] !!}
        @endif

        </tbody>

    </table>
</div>


