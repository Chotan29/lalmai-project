<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        <!-- div.table-responsive -->
            <!-- div.table-responsive -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Info Title</th>
                        <th>Total</th>
                        <th>File</th>
                        <th>LINK</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Total Number Of Route
                        </td>
                        <td>
                            {{$data['routes-info']}}
                        </td>
                        <td>
                            routes/web.php
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route($base_route.'.routes') }}" target="_blank" class="btn-primary btn-sm">
                                    <i class="ace-icon fa fa-list bigger-130"></i> Routes Detail Info
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Total Number Of Tables
                        </td>
                        <td>
                            {{$data['tables-info']}}
                        </td>
                        <td>
                            routes/web.php
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route($base_route.'.tables') }}" target="_blank" class="btn-primary btn-sm">
                                    <i class="ace-icon fa fa-list bigger-130"></i> Tables Detail Info
                                </a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>