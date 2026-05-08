<div class="row">
    <div class="col-xs-12">
        <h4 class="header large lighter blue"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ $panel }} List</h4>
        <div class="clearfix">
            <span class="pull-right tableTools-container"></span>
        </div>
        {{--<div class="table-header">
            {{ $panel }}  Record list on table. Filter {{ $panel }} using the filter.
        </div>--}}
        <!-- div.table-responsive -->
            <!-- div.table-responsive -->
        <div class="table-responsive">
            {{--{!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}--}}
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Cleaner Type</th>
                        <th>Clean {{ __('common.actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-right">
                            <h3>Clean All</h3>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('super-suit.clear-all-cache') }}" class="btn btn-danger bootbox-confirm">
                                    <i class="ace-icon fa fa-eraser bigger-110 white"></i> Clean All Chache
                                </a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-right">
                            <h3>Clear Cache</h3>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('super-suit.clear-cache') }}" class="btn btn-danger bootbox-confirm">
                                    <i class="ace-icon fa fa-eraser bigger-110 white"></i> Clean All Cache
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">
                            <h3>Clear Route</h3>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('super-suit.clear-route') }}" class="btn btn-danger bootbox-confirm">
                                    <i class="ace-icon fa fa-eraser bigger-110 white"></i> Clear Route Cache
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">
                            <h3>Clear Config</h3>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('super-suit.clear-config') }}" class="btn btn-danger bootbox-confirm">
                                    <i class="ace-icon fa fa-eraser bigger-110 white"></i> Clear Config Cache
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">
                            <h3>Clear View</h3>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('super-suit.clear-view') }}" class="btn btn-danger bootbox-confirm">
                                    <i class="ace-icon fa fa-eraser bigger-110 white"></i> Clear View Cache
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-right">
                            <h3>Clear User Activity </h3>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('super-suit.clear-user-activity') }}" class="btn btn-danger bootbox-confirm">
                                    <i class="ace-icon fa fa-eraser bigger-110 white"></i> Clear User Activity
                                </a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td class="text-right">
                            <h3>Clear Student Operational Data</h3>
                            <p class="text-muted">Preserves master/setup/imported structure and clears student transactional records.</p>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('super-suit.clear-student-operational-data') }}" class="btn btn-danger bootbox-confirm" data-item-name="student operational data" data-custom-message="This will remove student records, guardian links, payments, attendance, academic and related operational data. Master/setup data will remain intact.">
                                    <i class="ace-icon fa fa-database bigger-110 white"></i> Clear Student Operational Data
                                </a>
                            </div>
                        </td>
                    </tr>

<!--                    .cleaner',
                    .clear-all
                    .clear-cache
                    .clear-route
                    .clear-config
                    .clear-view-->
                </tbody>
            </table>
            {{--{!! Form::close() !!}--}}

        </div>
    </div>
</div>