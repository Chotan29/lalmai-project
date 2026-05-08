<div class="row">
    <div class="col-xs-12">
    @include('includes.data_table_header')
    <!-- div.table-responsive -->
        <!-- div.table-responsive -->
        <div class="table-responsive">
            <table id="sample-table-1" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="center">
                            <label>
                                <input type="checkbox" class="ace" />
                                <span class="lbl"></span>
                            </label>
                        </th>
                        <th>Detail</th>
                        <th>Rank</th>
                        <th>{{ __('common.status')}}</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>


                @if (isset($data['rows']) && $data['rows']->count() > 0)

                    {!! Form::open(['route' => $base_route.'.bulk-action', 'id' => 'bulk_action_form']) !!}

                    @foreach($data['rows'] as $row)
                        <tr>
                            <td class="center">
                                <label>
                                    <input type="checkbox" name="chkIds[]" value="{{ encrypt($row->id) }}" class="ace" />
                                    <span class="lbl"></span>
                                </label>
                            </td>

                            <td>
                                <div id="faq-list-4" class="panel-group accordion-style1 accordion-style2">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <a href="#{{$row->id}}" data-parent="#faq-list-4" data-toggle="collapse" class="accordion-toggle collapsed">
                                                <i class="ace-icon fa fa-arrow-down"></i>
                                                &nbsp;
                                                {{ $row->name }} - {{ $row->position }}
                                                <hr class="hr-2">
                                                <span class="label label-sm label-primary">
                                                    Created On - {{ date('jS M, Y', strtotime($row->created_at)) }}
                                                </span>
                                                <span class="label label-sm label-success">
                                                    Last Updated - {{ date('jS M, Y', strtotime($row->updated_at)) }}
                                                </span>
                                            </a>
                                        </div>

                                        <div class="panel-collapse collapse" id="{{$row->id}}" style="height: 0px;">
                                            <div class="panel-body center">
                                                @if ($row->profile_image)
                                                    <img src="{{ asset('web/'.$folder_name.'/'.$row->profile_image) }}" width="100">
                                                    <br>
                                                @else
                                                    <p>No image</p>
                                                @endif
                                                    {{ $row->name }} <br>
                                                    {{ $row->position }}<br>
                                                    {{ $row->address }}<br>
                                                    {{ $row->tel }} , {{ $row->cell_1 }} , {{ $row->cell_2 }} , {{ $row->email }}<br>
                                                    <ul class="social-icon-two list-inline">
                                                        <li>
                                                            <a href="{{ isset($row->twitter_url)?$row->twitter_url:"#" }}" target="_blank">
                                                                <span class="fa fa-twitter"></span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ isset($row->linkedIn_url)?$row->linkedIn_url:"#" }}" target="_blank">
                                                                <span class="fa fa-linkedin"></span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ isset($row->facebook_url)?$row->facebook_url:"#" }}" target="_blank">
                                                                <span class="fa fa-facebook"></span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ isset($row->instagram_url)?$row->instagram_url:"#" }}" target="_blank">
                                                                <span class="fa fa-instagram"></span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ isset($row->whatsapp_url)?$row->whatsapp_url:"#" }}" target="_blank">
                                                                <span class="fa fa-whatsapp"></span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ isset($row->skype_url)?$row->skype_url:"#" }}" target="_blank">
                                                                <span class="fa fa-skype"></span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ isset($row->pinterest_url)?$row->pinterest_url:"#" }}" target="_blank">
                                                                <span class="fa fa-pinterest"></span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    {!! $row->description !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{$row->rank}}</td>

                            <td>
                                <div class="btn-group">
                                    <button data-toggle="dropdown" class="btn btn-minier dropdown-toggle {{ $row->status == 'active'?"btn-info":"btn-warning" }}" >
                                        {{ $row->status == 'active'?"Active":"In Active" }}
                                        <i class="ace-icon fa fa-caret-down"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route($base_route.'.active', ['id' => encrypt($row->id)]) }}" title="Active"><i class="ace-icon fa fa-check"></i> Active</a>
                                        </li>

                                        <li>
                                            <a href="{{ route($base_route.'.in-active', ['id' => encrypt($row->id)]) }}" title="In-Active"><i class="ace-icon fa fa-remove"></i> InActive</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>

                            <td>
                                <div class=" btn-group">

                                    <a href="{{ route('web.staffs.detail', ['id' => encrypt($row->id)]) }}" target="_blank" class="btn btn-xs btn-primary">
                                        <i class="ace-icon fa fa-globe"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.view', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-success">
                                        <i class="ace-icon fa fa-eye"></i>
                                    </a>


                                    <a href="{{ route($base_route.'.edit', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-info">
                                        <i class="ace-icon fa fa-edit"></i>
                                    </a>

                                    <a href="{{ route($base_route.'.delete', ['id' => encrypt($row->id)]) }}" class="btn btn-xs btn-danger bootbox-confirm">
                                        <i class="ace-icon fa fa-trash"></i>
                                    </a>

                                </div>

                            </td>
                        </tr>

                    @endforeach

                    {!! Form::close() !!}

                    <tr>
                        <td colspan="5">{!! $data['rows']->appends($data['filter_query'])->links() !!}</td>
                    </tr>

                @else
                    <tr><td colspan="5">No data fount.</td></tr>
                @endif
                </tbody>
            </table>
        </div><!-- /.table-responsive -->
    </div>
</div>
