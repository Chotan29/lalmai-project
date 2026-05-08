<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>@if(isset($data['general_setting']->institute)){{$data['general_setting']->institute}} - Login@else লালমাই সরকারি কলেজ - Login@endif</title>
<meta name="description" content="লালমাই সরকারি কলেজ - IMS Login">
@if(isset($data['general_setting']->favicon))
<link rel="icon" href="{{ asset('images/setting/general/'.$data['general_setting']->favicon) }}" type="image/x-icon">
@endif
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{height:100%}
body{min-height:100%;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;background:linear-gradient(135deg,#e8f0fe 0%,#f0f4f8 50%,#dce8f8 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:1.5rem}
.card{background:#fff;width:100%;max-width:26rem;border-radius:20px;box-shadow:0 8px 40px rgba(14,76,139,.13),0 2px 8px rgba(14,76,139,.07);overflow:hidden}
.card-header{background:linear-gradient(135deg,#0e4c8b 0%,#1a6fc4 100%);padding:2rem 2rem 1.5rem;text-align:center;position:relative}
.card-header::after{content:'';position:absolute;bottom:-1px;left:0;right:0;height:24px;background:#fff;border-radius:20px 20px 0 0}
.logo-wrap{display:inline-flex;align-items:center;justify-content:center;width:96px;height:96px;border-radius:50%;background:#fff;box-shadow:0 4px 16px rgba(0,0,0,.15);overflow:hidden;margin-bottom:1rem;border:3px solid rgba(255,255,255,.6)}
.logo-wrap img{width:100%;height:100%;object-fit:cover}
.logo-icon{font-size:2.5rem;color:#0e4c8b}
.college-name-bn{font-size:1.2rem;font-weight:700;color:#fff;letter-spacing:.01em;margin-bottom:.2rem;text-shadow:0 1px 3px rgba(0,0,0,.2)}
.college-name-en{font-size:.8rem;color:rgba(255,255,255,.8);letter-spacing:.05em;text-transform:uppercase;font-weight:500}
.card-body{padding:1.75rem 2rem 2rem}
.signin-title{font-size:1rem;font-weight:600;color:#1f2937;text-align:center;margin-bottom:1.25rem}
.form-group{margin-bottom:1rem}
.form-label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.4rem;letter-spacing:.02em}
.input-wrap{position:relative}
.input-icon{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;display:flex}
.form-control{width:100%;padding:.625rem .75rem .625rem 2.4rem;border:1.5px solid #d1d5db;border-radius:10px;font-size:.875rem;color:#111827;background:#f9fafb;transition:border-color .2s,box-shadow .2s,background .2s;outline:none;-webkit-appearance:none}
.form-control:focus{border-color:#0e4c8b;box-shadow:0 0 0 3px rgba(14,76,139,.12);background:#fff}
.form-control.is-error{border-color:#dc2626;box-shadow:0 0 0 3px rgba(220,38,38,.1)}
.error-msg{font-size:.75rem;color:#dc2626;margin-top:.3rem;font-weight:500}
.pwd-toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;display:flex;padding:2px;border-radius:4px;transition:color .15s}
.pwd-toggle:hover{color:#4b5563}
.row-between{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;font-size:.8rem}
.check-label{display:flex;align-items:center;gap:.4rem;cursor:pointer;color:#374151;font-weight:500;user-select:none}
.check-label input{width:1rem;height:1rem;accent-color:#0e4c8b;cursor:pointer}
.forgot-link{color:#0e4c8b;font-weight:600;text-decoration:none;transition:color .15s}
.forgot-link:hover{color:#1a6fc4;text-decoration:underline}
.btn-signin{display:flex;align-items:center;justify-content:center;gap:.5rem;width:100%;padding:.75rem;background:linear-gradient(135deg,#0e4c8b 0%,#1a6fc4 100%);color:#fff;border:none;border-radius:10px;font-size:.9rem;font-weight:600;cursor:pointer;letter-spacing:.03em;transition:opacity .2s,transform .15s,box-shadow .2s;box-shadow:0 4px 12px rgba(14,76,139,.3)}
.btn-signin:hover{opacity:.92;transform:translateY(-1px);box-shadow:0 6px 18px rgba(14,76,139,.35)}
.btn-signin:active{transform:translateY(0);box-shadow:0 2px 8px rgba(14,76,139,.2)}
.btn-signin:disabled{opacity:.65;cursor:not-allowed;transform:none}
.alert{display:flex;align-items:flex-start;gap:.6rem;padding:.75rem 1rem;border-radius:8px;font-size:.825rem;font-weight:500;margin-bottom:1rem}
.alert-error{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
.alert-success{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.card-footer{text-align:center;padding:.75rem 1rem 1rem;font-size:.73rem;color:#9ca3af}
.card-footer a{color:#6b7280;text-decoration:none}
.card-footer a:hover{color:#0e4c8b;text-decoration:underline}
@media(max-width:480px){.card-header{padding:1.5rem 1.25rem 1.25rem}.card-body{padding:1.5rem 1.25rem 1.5rem}.logo-wrap{width:80px;height:80px}.college-name-bn{font-size:1.05rem}}
</style>
</head>
<body>

<div class="card">

    <div class="card-header">
        <div class="logo-wrap">
            @if(isset($data['general_setting']->logo))
                <img src="{{ asset('images/setting/general/'.$data['general_setting']->logo) }}"
                     alt="{{ $data['general_setting']->institute ?? 'লালমাই সরকারি কলেজ' }}"
                     loading="lazy" width="96" height="96">
            @else
                <span class="logo-icon">🎓</span>
            @endif
        </div>
        <div class="college-name-bn">
            {{ $data['general_setting']->institute ?? 'লালমাই সরকারি কলেজ' }}
        </div>
        <div class="college-name-en">লালমাই সরকারি কলেজ &nbsp;·&nbsp; Lalmai, Cumilla</div>
    </div>

    <div class="card-body">

        <p class="signin-title">আপনার অ্যাকাউন্টে সাইন ইন করুন</p>

        @if(session()->has('login_error'))
        <div class="alert alert-error">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" style="flex-shrink:0;margin-top:1px"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session()->get('login_error') }}
        </div>
        @endif

        @if(session('status'))
        <div class="alert alert-success">
            <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor" style="flex-shrink:0;margin-top:1px"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="login-form" novalidate>
            {{ csrf_field() }}

            <div class="form-group">
                <label class="form-label" for="email">ইমেইল ঠিকানা</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                    </span>
                    <input id="email" name="email" type="email" autocomplete="email"
                           class="form-control{{ $errors->has('email') ? ' is-error' : '' }}"
                           value="{{ old('email') }}" placeholder="example@email.com" required autofocus>
                </div>
                @if($errors->has('email'))
                <p class="error-msg">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="password">পাসওয়ার্ড</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                    </span>
                    <input id="password" name="password" type="password" autocomplete="current-password"
                           class="form-control{{ $errors->has('password') ? ' is-error' : '' }}"
                           placeholder="••••••••" required>
                    <button type="button" class="pwd-toggle" onclick="togglePwd()" aria-label="Show/hide password">
                        <svg id="eye-icon" width="18" height="18" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
                @if($errors->has('password'))
                <p class="error-msg">{{ $errors->first('password') }}</p>
                @endif
            </div>

            <div class="row-between">
                <label class="check-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    মনে রাখো
                </label>
                <a href="{{ route('password.request') }}" class="forgot-link">পাসওয়ার্ড ভুলে গেছেন?</a>
            </div>

            <button type="submit" class="btn-signin" id="signin-btn">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                সাইন ইন করুন
            </button>
        </form>
    </div>

    <div class="card-footer">
        @if(isset($data['general_setting']->copyright))
            {!! $data['general_setting']->copyright !!}
        @else
            &copy; {{ date('Y') }} লালমাই সরকারি কলেজ &mdash; সর্বস্বত্ব সংরক্ষিত
        @endif
    </div>
</div>

<script>
function togglePwd(){
    var i=document.getElementById('password'),
        e=document.getElementById('eye-icon');
    if(i.type==='password'){
        i.type='text';
        e.innerHTML='<path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    }else{
        i.type='password';
        e.innerHTML='<path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>';
    }
}
document.getElementById('login-form').addEventListener('submit',function(){
    var b=document.getElementById('signin-btn');
    b.disabled=true;
    b.innerHTML='<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg> অপেক্ষা করুন...';
});
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@include('includes.scripts.tracking')
</body>
</html>
