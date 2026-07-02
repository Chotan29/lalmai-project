<div class="login-access-container">
    <div class="row">
        <div class="col-md-12">
            <div class="info-card animate__animated animate__fadeIn">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div class="section-title">
                        <h3>Login Credentials</h3>
                        <p class="text-muted">Manage your account access and security settings</p>
                    </div>
                </div>
                
                {!! Form::model($data['student_login'], ['route' => ['user-student.password', $data['student_login']->id], 'method' => 'POST', 'class' => 'login-access-form']) !!}

                {!! Form::hidden('id', $data['student_login']->id) !!}
                {!! Form::hidden('role_id', 6) !!}
                {!! Form::hidden('hook_id', $data['student']->id) !!}

                <div class="form-section">
                    <h4 class="section-subtitle">
                        <i class="bi bi-person-badge"></i> Account Information
                    </h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                {!! Form::text('name', null, [
                                    "class" => "form-control", 
                                    "id" => "nameInput",
                                    "placeholder" => "Name",
                                    "disabled"
                                ]) !!}
                                {{-- <label for="nameInput">Full Name</label> --}}
                                <div class="form-icon">
                                    <i class="bi bi-person"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                {!! Form::email('email', null, [
                                    "class" => "form-control", 
                                    "id" => "emailInput",
                                    "placeholder" => "Email",
                                    "disabled"
                                ]) !!}
                                {{-- <label for="emailInput">Email Address</label> --}}
                                <div class="form-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4 class="section-subtitle">
                        <i class="bi bi-key"></i> Password Settings
                    </h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <div class="input-group">
                                    {!! Form::password('password', [
                                        "class" => "form-control", 
                                        "id" => "pass",
                                        "placeholder" => "New Password",
                                        "required",
                                        "autocomplete" => "new-password"
                                    ]) !!}
                                    {{-- <label for="pass">New Password</label> --}}
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#pass">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="password-feedback">
                                    <div class="password-strength mt-2">
                                        <div class="strength-meter">
                                            <div class="strength-bar" id="strengthBar"></div>
                                        </div>
                                        <small class="strength-text" id="strengthText">Password strength</small>
                                    </div>
                                    <div class="password-hints">
                                        <small class="text-muted d-block">Must contain at least:</small>
                                        <ul class="list-unstyled">
                                            <li class="hint-item" id="lengthHint"><i class="bi bi-check-circle"></i> 8 characters</li>
                                            <li class="hint-item" id="numberHint"><i class="bi bi-check-circle"></i> 1 number</li>
                                            <li class="hint-item" id="specialHint"><i class="bi bi-check-circle"></i> 1 special character</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <div class="input-group">
                                    {!! Form::password('confirmPassword', [
                                        "class" => "form-control", 
                                        "id" => "repatpass",
                                        "placeholder" => "Confirm Password",
                                        "required",
                                        "autocomplete" => "new-password"
                                    ]) !!}
                                    {{-- <label for="repatpass">Confirm Password</label> --}}
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#repatpass">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="password-match mt-2">
                                    <div class="match-feedback" id="matchFeedback">
                                        <i class="bi bi-check-circle-fill text-success d-none"></i>
                                        <span class="match-text">Passwords must match</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section status-section">
                    <h4 class="section-subtitle">
                        <i class="bi bi-shield-check"></i> Account Status
                    </h4>
                    <div class="status-container">
                        <div class="status-badge {{ $data['student_login']->status == 'active' ? 'active' : 'inactive' }}">
                            <i class="bi {{ $data['student_login']->status == 'active' ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                            <span>{{ $data['student_login']->status == 'active' ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <div class="status-toggle">
                            {{-- <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="statusToggle" 
                                    {{ $data['student_login']->status == 'active' ? 'checked' : '' }} disabled>
                                <label class="form-check-label" for="statusToggle">
                                    {{ $data['student_login']->status == 'active' ? 'Account is active' : 'Account is disabled' }}
                                </label>
                            </div> --}}
                            <small class="text-muted">Contact administrator to change status</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary btn-save" type="submit">
                        <i class="bi bi-save"></i> Update Password
                    </button>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<style>
    /* Login Access Container */
    .login-access-container {
        /* max-width: 1200px; */
        margin: 0 auto;
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .section-icon {
        background: linear-gradient(135deg, #4361ee, #3f37c9);
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: white;
        font-size: 1.5rem;
    }

    .section-title h3 {
        margin-bottom: 0.25rem;
        color: #2c3e50;
    }

    .section-title p {
        margin-bottom: 0;
        color: #7f8c8d;
    }

    /* Form Sections */
    .form-section {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #4361ee;
    }

    .section-subtitle {
        color: #4361ee;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        font-size: 1.1rem;
    }

    .section-subtitle i {
        margin-right: 0.75rem;
        font-size: 1.2rem;
    }

    /* Form Elements */
    .form-floating {
        position: relative;
    }

    .form-floating .form-icon {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        color: #7f8c8d;
        pointer-events: none;
    }

    .form-floating .form-control {
        padding-left: 40px;
        height: calc(3.5rem + 2px);
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }

    .form-floating label {
        padding-left: 40px;
        color: #7f8c8d;
    }

    .form-floating .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        border-color: #4361ee;
    }

    /* Password Fields */
    .input-group{
        width: 100%;
    }
    .input-group .btn.toggle-password {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        z-index: 5;
        border: none;
        background: transparent;
        color: #7f8c8d;
    }

    /* Password Feedback */
    .password-feedback {
        margin-top: 0.5rem;
    }

    .strength-meter {
        height: 4px;
        background-color: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 0.25rem;
    }

    .strength-bar {
        height: 100%;
        width: 0;
        background-color: #dc3545;
        transition: width 0.3s ease;
    }

    .password-hints {
        margin-top: 0.75rem;
    }

    .hint-item {
        color: #7f8c8d;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
    }

    .hint-item i {
        margin-right: 0.5rem;
        font-size: 0.75rem;
    }

    .hint-item.valid {
        color: #28a745;
    }

    .hint-item.valid i {
        color: #28a745;
    }

    /* Password Match Feedback */
    .password-match {
        margin-top: 0.5rem;
    }

    .match-feedback {
        display: flex;
        align-items: center;
    }

    .match-feedback i {
        margin-right: 0.5rem;
    }

    .match-text {
        color: #7f8c8d;
        font-size: 0.85rem;
    }

    /* Status Section */
    .status-section {
        background-color: #f8f9fa;
    }

    .status-container {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .status-badge i {
        margin-right: 0.5rem;
        font-size: 1rem;
    }

    .status-badge.active {
        background-color: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    .status-badge.inactive {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .status-toggle {
        flex: 1;
        min-width: 250px;
    }

    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        margin-right: 0.5rem;
    }

    .form-switch .form-check-label {
        font-weight: 500;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 1.5rem;
    }

    .btn-save {
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background-color: #4361ee;
        border-color: #4361ee;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        background-color: #3a56d4;
        border-color: #3a56d4;
        transform: translateY(-2px);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .section-icon {
            margin-bottom: 1rem;
        }
        
        .status-container {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>

@section('js')
<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const target = document.querySelector(this.getAttribute('data-target'));
            const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
            target.setAttribute('type', type);
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    });

    // Password strength checker
    const passwordInput = document.getElementById('pass');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const lengthHint = document.getElementById('lengthHint');
    const numberHint = document.getElementById('numberHint');
    const specialHint = document.getElementById('specialHint');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        // Check length
        if (password.length >= 8) {
            strength += 1;
            lengthHint.classList.add('valid');
        } else {
            lengthHint.classList.remove('valid');
        }
        
        // Check for numbers
        if (/\d/.test(password)) {
            strength += 1;
            numberHint.classList.add('valid');
        } else {
            numberHint.classList.remove('valid');
        }
        
        // Check for special characters
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            strength += 1;
            specialHint.classList.add('valid');
        } else {
            specialHint.classList.remove('valid');
        }
        
        // Update strength meter
        const width = (strength / 3) * 100;
        strengthBar.style.width = `${width}%`;
        
        // Update strength text and color
        if (password.length === 0) {
            strengthBar.style.backgroundColor = 'transparent';
            strengthText.textContent = 'Password strength';
        } else {
            if (strength === 0) {
                strengthBar.style.backgroundColor = '#dc3545';
                strengthText.textContent = 'Very Weak';
            } else if (strength === 1) {
                strengthBar.style.backgroundColor = '#fd7e14';
                strengthText.textContent = 'Weak';
            } else if (strength === 2) {
                strengthBar.style.backgroundColor = '#ffc107';
                strengthText.textContent = 'Moderate';
            } else {
                strengthBar.style.backgroundColor = '#28a745';
                strengthText.textContent = 'Strong';
            }
        }
        
        // Check password match
        checkPasswordMatch();
    });

    // Password match checker
    const confirmPasswordInput = document.getElementById('repatpass');
    const matchFeedback = document.getElementById('matchFeedback');
    const matchIcon = matchFeedback.querySelector('i');
    const matchText = matchFeedback.querySelector('.match-text');

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (password && confirmPassword) {
            if (password === confirmPassword) {
                matchIcon.classList.remove('d-none');
                matchText.textContent = 'Passwords match';
                matchText.style.color = '#28a745';
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid');
            } else {
                matchIcon.classList.add('d-none');
                matchText.textContent = 'Passwords do not match';
                matchText.style.color = '#dc3545';
                confirmPasswordInput.classList.remove('is-valid');
                confirmPasswordInput.classList.add('is-invalid');
            }
        } else {
            matchIcon.classList.add('d-none');
            matchText.textContent = 'Passwords must match';
            matchText.style.color = '#7f8c8d';
            confirmPasswordInput.classList.remove('is-valid');
            confirmPasswordInput.classList.remove('is-invalid');
        }
    }
</script>
@endsection