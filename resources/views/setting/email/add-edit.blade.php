@extends('layouts.master')

@section('css')
<style>
    :root {
        --primary: #2563eb;
        --gray: #6b7280;
        --light: #f9fafb;
        --border: #e5e7eb;
        --success: #16a34a;
        --danger: #dc2626;
    }

    .page-wrapper {
        padding: 2rem;
        background-color: var(--light);
    }

    .card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2rem;
        max-width: 800px;
        margin: auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .header-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #1f2937;
        text-align: center;
        margin-bottom: 2rem;
    }

    .form-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group {
        flex: 1;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: var(--gray);
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .form-group input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 1rem;
    }

    .form-actions {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .btn {
        padding: 0.5rem 1.25rem;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        cursor: pointer;
    }

    .btn-reset {
        background: #e5e7eb;
        color: black;
    }

    .btn-save {
        background: var(--primary);
        color: white;
    }

    .toggle-switch {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }

    .toggle-switch input[type="checkbox"] {
        width: 40px;
        height: 20px;
        appearance: none;
        background: #ccc;
        border-radius: 20px;
        position: relative;
        cursor: pointer;
        outline: none;
        transition: 0.3s;
    }

    .toggle-switch input[type="checkbox"]:checked {
        background: var(--success);
    }

    .toggle-switch input[type="checkbox"]::before {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: white;
        top: 1px;
        left: 1px;
        transition: 0.3s;
    }

    .toggle-switch input[type="checkbox"]:checked::before {
        transform: translateX(20px);
    }

    .toggle-label {
        font-size: 0.9rem;
        color: var(--gray);
    }
</style>
@endsection

@section('content')
<div class="page-wrapper">
    @include('layouts.includes.template_setting')
    @include('setting.includes.buttons')
    @include('includes.flash_messages')

    <div class="card">
        <div class="header-title">
            {{ $isEdit ? 'Edit Email Configuration' : 'Add Email Configuration' }}
        </div>

        {!! Form::model($data['row'] ?? null, [
            'route' => $isEdit ? [$base_route.'.update', encrypt($data['row']->id)] : $base_route.'.store',
            'method' => 'POST',
            'class' => '',
            'enctype' => 'multipart/form-data',
            'id' => 'validation-form'
        ]) !!}
        @if($isEdit)
            {!! Form::hidden('id', $data['row']->id) !!}
        @endif

        {{-- EMAIL CONFIGURATION FIELDS --}}
        <div class="form-row">
            <div class="form-group">
                <label for="driver">Driver</label>
                {!! Form::text('driver', null, ['placeholder'=>'e.g.SMTP', "class" => "", "required", "autofocus"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'driver'])
            </div>
            <div class="form-group">
                <label for="host">Host</label>
                {!! Form::text('host', null, ['placeholder'=>'e.g.mail.google.com', "class" => "", "required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'host'])
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="port">Port</label>
                {!! Form::text('port', null, ['placeholder'=>'e.g.465', "class" => "", "required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'port'])
            </div>
            <div class="form-group">
                <label for="encryption">Encryption</label>
                {!! Form::text('encryption', null, ['placeholder'=>'e.g.TLS', "class" => "", "required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'encryption'])
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="user_name">User Name</label>
                {!! Form::text('user_name', null, ["class" => "", "required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'user_name'])
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                {!! Form::text('password', null,["class" => "", "required"]) !!}
                @include('includes.form_fields_validation_message', ['name' => 'password'])
            </div>
        </div>

        {{-- STATUS SWITCH --}}
        @if($isEdit)
            <div class="toggle-switch">
                <input type="checkbox" id="status-button" onchange="onToggle()" {{ $data['row']->status == 'active' ? 'checked' : '' }}>
                <label for="status-button" class="toggle-label">Status: {{ ucfirst($data['row']->status) }}</label>
            </div>
        @endif

        {{-- ACTION BUTTONS --}}
        <div class="form-actions">
            <button type="reset" class="btn btn-reset">
                <i class="fa fa-undo"></i> Reset
            </button>
            <button type="submit" class="btn btn-save">
                <i class="fa fa-save"></i> Save
            </button>
        </div>

        {!! Form::close() !!}
    </div>
</div>
@endsection

@section('js')
    @include('includes.scripts.jquery_validation_scripts')

    <script>
        function onToggle() {
            const isChecked = document.querySelector('#status-button').checked;
            const status = isChecked ? 'active' : 'in-active';
            const id = document.querySelector('input[name="id"]').value;

            $.ajax({
                type: 'POST',
                url: '{{ route('setting.email.change-status') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    status: status
                },
                success: function (res) {
                    toastr.success('Status updated successfully');
                },
                error: function () {
                    toastr.error('Failed to update status');
                }
            });
        }
    </script>
@endsection
