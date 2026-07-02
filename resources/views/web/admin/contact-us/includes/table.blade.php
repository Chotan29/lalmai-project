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
            <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>SN</th>
                        <th>Detail</th>
                        <th>Date</th>
                        <th>ViewStatus</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    @if (isset($data['rows']) && $data['rows']->count() > 0)

                        @php
                            $i=1;
                        @endphp

                        @foreach($data['rows'] as $row)

                            <tr>
                                <td>
                                    @php
                                        echo $i;
                                    @endphp
                                </td>

                                <td>
                                    <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <a href="#{{$row->id}}" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                                                    <i class="ace-icon fa fa-arrow-down"></i>
                                                    {{ $row->name }}
                                                    <hr class="hr-2">
                                                    <span class="label label-sm label-primary">
                                                        {{$row->id}}Created On - {{ date('jS M, Y', strtotime($row->created_at)) }}
                                                    </span>
                                                    <span class="label label-sm label-success">
                                                        Last Updated - {{ date('jS M, Y', strtotime($row->updated_at)) }}
                                                    </span>
                                                </a>
                                            </div>

                                            <div class="panel-collapse collapse" id="{{$row->id}}" style="height: 0px;">
                                                <div class="panel-body">
                                                    <span class="label label-sm label-primary">{{ $row->name }}</span><br>
                                                    {{ $row->email }}<br>
                                                    {{ $row->contact_number }}<br>
                                                    {{ $row->address }}<br>
                                                    {!! \Illuminate\Support\Str::words($row->message, 15,'....')  !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ date('Y-m-d', strtotime($row->created_at)) }}</td>

                                <td>
                                    @if ($row->view_status == 0)
                                        <span class="label label-sm label-success">Un-Read</span>
                                    @else
                                        <span class="label label-sm label-warning">Read</span>
                                    @endif
                                </td>

                                <td>
                                    <div class=" btn-group">

                                        <a href="{{ route('web.admin.contact-us.view', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-success">
                                            <i class="ace-icon fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('web.admin.contact-us.delete', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-danger bootbox-confirm">
                                            <i class="ace-icon fa fa-trash"></i>
                                        </a>

                                    </div>

                                </td>
                            </tr>

                            @php
                                $i++;
                            @endphp

                        @endforeach

                        <tr>
                            <td colspan="7">{!! $data['rows']->appends($data['filter_query'])->links() !!}</td>
                        </tr>

                    @else
                        <tr><td colspan="7">No data fount.</td></tr>
                    @endif
                </tbody>
            </table>
        </div><!-- /.table-responsive -->
    </div>
</div>