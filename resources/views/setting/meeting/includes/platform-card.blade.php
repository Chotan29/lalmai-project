{!! Form::model($meeting, ['route' => [$base_route.'.update', $meeting->id], 'method' => 'POST', 'class' => 'platform-form', "enctype" => "multipart/form-data"]) !!}
<input type="hidden" value="{{$meeting->identity}}" name="identity">

<div class="platform-card {{ $meeting->status != 'active' ? 'disabled-card' : '' }}">
    <div class="card-header">
        <div class="platform-info">
            <div class="platform-logo">
                <img src="{{ asset('assets/images/meeting/'.$meeting->logo.'.png') }}" 
                     alt="{{ $meeting->identity }}" 
                     title="Visit {{ ucfirst($meeting->identity) }}">
            </div>
            <div class="platform-name">
                {{ ucfirst($meeting->identity) }}
            </div>
        </div>
        
        <div class="platform-actions">
            <label class="switch">
                <input type="checkbox" class="status-toggle" 
                       {{ $meeting->status == 'active' ? 'checked' : '' }}
                       data-id="{{ $meeting->id }}">
                <span class="slider round"></span>
                <span class="status-text">
                    {{ $meeting->status == 'active' ? 'Active' : 'Inactive' }}
                </span>
            </label>
            
            <a href="{{$meeting->link}}" target="_blank" class="btn btn-xs btn-info" title="Visit Platform">
                <i class="fa fa-external-link"></i>
            </a>
            
            <button type="button" class="btn btn-xs btn-primary toggle-config">
                <i class="fa fa-chevron-down"></i>
            </button>
        </div>
    </div>
    
    <div class="config-section" style="{{ $meeting->status != 'active' ? 'display:none;' : '' }}">
        @php($configurations = json_decode($meeting->config, true))
        @if(isset($configurations))
            <div class="config-fields">
                @foreach($configurations as $key => $conf)
                    <div class="form-group">
                        <label class="control-label config-label">
                            {{ ucwords(str_replace('_', ' ', $key)) }}
                        </label>
                        <input type="{{ $key == 'api_key' ? 'password' : 'text' }}" 
                               value="{{$conf}}" 
                               name="{{$key}}" 
                               class="form-control config-input"
                               {{ $meeting->status != "active" ? "disabled" : "" }}>
                    </div>
                @endforeach
            </div>
        @endif
        
        <div class="card-footer">
            <button class="btn btn-primary save-btn" type="submit"
                    {{ $meeting->status != "active" ? "disabled" : "" }}>
                <i class="fa fa-save"></i> Save Configuration
            </button>
        </div>
    </div>
</div>
{!! Form::close() !!}