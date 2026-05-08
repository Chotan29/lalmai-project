<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th width="3%">#</th>
                        <th width="20%">Platform</th>
                        <th>Configuration</th>
                        <th width="10%">Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data['meetingSetting']) && $data['meetingSetting']->count() > 0)
                        @php($i = 1)
                        @foreach($data['meetingSetting'] as $meeting)
                            {!! Form::model($meeting, ['route' => [$base_route.'.update', $meeting->id], 'method' => 'POST', 'class' => 'form-horizontal', 'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
                            <input type="hidden" value="{{$meeting->identity}}" name="identity">
                            
                            <tr id="{{$meeting->identity}}">
                                <td>{{ $i }}</td>
                                <td>
                                    <div class="platform-card">
                                        <a href="{{$meeting->link}}" target="_blank" class="platform-logo">
                                            <img src="{{ asset('assets/images/meeting/'.$meeting->logo.'.png') }}" 
                                                 alt="{{ $meeting->identity }}" 
                                                 class="img-responsive"
                                                 title="Visit {{ ucfirst($meeting->identity) }}">
                                        </a>
                                        <div class="platform-name">
                                            {{ ucfirst($meeting->identity) }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php($configurations = json_decode($meeting->config, true))
                                    @if(isset($configurations))
                                        <div class="config-fields">
                                            @foreach($configurations as $key => $conf)
                                                <div class="form-group">
                                                    <label class="col-sm-3 control-label config-label">
                                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <input type="{{ $key == 'api_key' ? 'password' : 'text' }}" 
                                                               value="{{$conf}}" 
                                                               name="{{$key}}" 
                                                               class="form-control config-input"
                                                               {{ $meeting->status != "active" ? "disabled" : "" }}>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" class="status-toggle" 
                                               {{ $meeting->status == 'active' ? 'checked' : '' }}
                                               data-id="{{ $meeting->id }}">
                                        <span class="slider round"></span>
                                        <span class="status-text">
                                            {{ $meeting->status == 'active' ? 'Active' : 'Inactive' }}
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <button class="btn btn-primary save-btn" type="submit"
                                            {{ $meeting->status != "active" ? "disabled" : "" }}>
                                        <i class="fa fa-save"></i> Save
                                    </button>
                                </td>
                            </tr>
                            {!! Form::close() !!}
                            @php($i++)
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <i class="fa fa-info-circle"></i> No meeting platforms configured
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>