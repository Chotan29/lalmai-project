<script type="text/javascript">

    function clearFieldValidation(selector) {
        var $field = $(selector);
        $field.removeClass('field-invalid');
        if ($field.hasClass('chosen-select')) {
            $field.next('.chosen-container').removeClass('field-invalid');
        }
        $field.closest('div').find('.validation-note').removeClass('is-visible').remove();
    }

    function markFieldInvalid(selector, message) {
        var $field = $(selector);
        if (!$field.length) return;
        $field.addClass('field-invalid');
        if ($field.hasClass('chosen-select')) {
            $field.next('.chosen-container').addClass('field-invalid');
        }
        var $container = $field.closest('div');
        var $note = $container.find('.validation-note');
        if (!$note.length) {
            $note = $('<div class="validation-note"></div>');
            $container.append($note);
        }
        $note.text(message).addClass('is-visible');
    }

    function focusAndScrollToField(selector) {
        if (!selector) return;
        var $field = $(selector).first();
        if (!$field.length) return;
        var targetTop = Math.max($field.offset().top - 130, 0);
        $('html, body').stop(true).animate({ scrollTop: targetTop }, 300);
        setTimeout(function () {
            if ($field.hasClass('chosen-select')) {
                $field.trigger('chosen:open');
            } else {
                $field.trigger('focus');
            }
        }, 320);
    }

    function invalidateFieldAndStop(selector, noteMessage, toastMessage, tabActivator) {
        if (selector) markFieldInvalid(selector, noteMessage || 'This field is required.');
        if (typeof tabActivator === 'function') tabActivator(true);
        focusAndScrollToField(selector);
        toastr.warning(toastMessage, 'Info:');
        return false;
    }

    function staffValidation() {
        var reg_no       = $('input[name="reg_no"]').val();
        var join_date    = $('input[name="join_date"]').val();
        var designation  = $('select[name="designation"]').val();
        var first_name   = $('input[name="first_name"]').val();
        var last_name    = $('input[name="last_name"]').val();
        var date_of_birth = $('input[name="date_of_birth"]').val();
        var gender       = $('select[name="gender"]').val();
        var nationality  = $('input[name="nationality"]').val();
        var qualification = $('input[name="qualification"]').val();
        var email        = $('input[name="email"]').val();
        var mobile_1     = $('input[name="mobile_1"]').val();
        var address      = $('input[name="address"]').val();
        var state        = $('select[name="state"]').val();

        if (!reg_no)
            return invalidateFieldAndStop('input[name="reg_no"]', 'Staff Id is required.', "Please, Enter Staff Id.", activeGeneralInfo);
        if (!/^[0-9]+$/.test(reg_no))
            return invalidateFieldAndStop('input[name="reg_no"]', 'Staff Id must be numeric (digits only).', "Staff Id must be numeric. Letters/symbols are not allowed.", activeGeneralInfo);
        if (!join_date)
            return invalidateFieldAndStop('input[name="join_date"]', 'Join Date is required.', "Please, Enter Join Date.", activeGeneralInfo);
        if (!designation || designation == '0')
            return invalidateFieldAndStop('select[name="designation"]', 'Designation is required.', "Please, Select Designation.", activeGeneralInfo);
        if (!first_name)
            return invalidateFieldAndStop('input[name="first_name"]', 'First name is required.', "Please, Enter Staff Name.", activeGeneralInfo);
        if (!last_name)
            return invalidateFieldAndStop('input[name="last_name"]', 'Last name is required.', "Please, Enter Staff Last Name.", activeGeneralInfo);
        if (!date_of_birth)
            return invalidateFieldAndStop('input[name="date_of_birth"]', 'Date of birth is required.', "Please, Enter Date of Birth.", activeGeneralInfo);
        if (!gender || gender == '0')
            return invalidateFieldAndStop('select[name="gender"]', 'Gender is required.', "Please, Select Gender.", activeGeneralInfo);
        if (!nationality)
            return invalidateFieldAndStop('input[name="nationality"]', 'Nationality is required.', "Please, Enter Nationality.", activeGeneralInfo);
        if (!qualification)
            return invalidateFieldAndStop('input[name="qualification"]', 'Qualification is required.', "Please, Enter Qualification.", activeGeneralInfo);
        if (!email) {
            return invalidateFieldAndStop('input[name="email"]', 'Email is required.', "Please, Enter Email Address.", activeGeneralInfo);
        } else {
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email))
                return invalidateFieldAndStop('input[name="email"]', 'Enter a valid email address.', "Please, Enter Valid Email Address.", activeGeneralInfo);
        }
        if (!mobile_1) {
            return invalidateFieldAndStop('input[name="mobile_1"]', 'Mobile number is required.', "Please, Enter Mobile Number.", activeGeneralInfo);
        } else {
            var bdMobilePattern = /^01[3-9]\d{8}$/;
            if (!bdMobilePattern.test(mobile_1))
                return invalidateFieldAndStop('input[name="mobile_1"]', 'Must be a valid Bangladesh mobile number (e.g. 01XXXXXXXXX).', "Invalid mobile number! Enter Bangladesh format (e.g. 01712345678).", activeGeneralInfo);
        }
        if (!address)
            return invalidateFieldAndStop('input[name="address"]', 'Address is required.', "Please, Enter Address.", activeGeneralInfo);
        if (!state || state == '0')
            return invalidateFieldAndStop('select[name="state"]', 'Division is required.', "Please, Select Division.", activeGeneralInfo);

        /*Staff photo required (add form only: window.staffPhotoRequired set on add page)*/
        if (window.staffPhotoRequired === true) {
            var photoInput = $('input[name="main_image"]')[0];
            if (!photoInput || !photoInput.files || !photoInput.files.length)
                return invalidateFieldAndStop('input[name="main_image"]', 'Staff photo is required.', "Please, Upload Passport Size Photo (white background). Registration cannot complete without photo.", activeProfileImage);
            if (window.staffPhotoValid === false)
                return invalidateFieldAndStop('input[name="main_image"]', 'Uploaded photo is not valid.', "Photo is not valid. Upload a passport size photo with white background.", activeProfileImage);
        }

        return true;
    }

    /*Staff Id: block non-numeric typing/paste*/
    $(document).on('input', 'input[name="reg_no"]', function () {
        var cleaned = this.value.replace(/[^0-9]/g, '');
        if (this.value !== cleaned) this.value = cleaned;
    });

    /*Staff photo: instant check + preview on choose*/
    $(document).on('change', 'input[name="main_image"]', function () {
        var input = this;
        window.staffPhotoValid = null;
        $('#staff-photo-preview').hide();

        function rejectPhoto(msg) {
            window.staffPhotoValid = false;
            input.value = '';
            markFieldInvalid(input, msg);
            toastr.error(msg, "Invalid Photo:");
        }

        if (!input.files || !input.files.length) return;

        var file = input.files[0];
        if (['image/jpeg', 'image/png'].indexOf(file.type) === -1) {
            rejectPhoto('Photo must be JPG or PNG format.');
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            rejectPhoto('Photo file size cannot exceed 5MB.');
            return;
        }

        var url = URL.createObjectURL(file);
        var img = new Image();
        img.onload = function () {
            var w = img.width, h = img.height, ratio = w / h;
            if (w < 240 || h < 320) {
                rejectPhoto('Photo resolution too low. Minimum 240x320 px (passport size).');
            } else if (h <= w) {
                rejectPhoto('Photo must be portrait (vertical), like a passport photo.');
            } else if (ratio < 0.55 || ratio > 0.90) {
                rejectPhoto('Photo framing is not passport standard. Use a proper passport size photo.');
            } else {
                window.staffPhotoValid = true;
                clearFieldValidation(input);
                $('#staff-photo-preview').attr('src', url).show();
                toastr.success('Photo accepted. White background will be verified on save.', "Success:");
            }
        };
        img.onerror = function () {
            rejectPhoto('Uploaded file is not a valid image.');
        };
        img.src = url;
    });

    $(document).ready(function () {
        $('#validation-form').on('input change', 'input, select, textarea', function () {
            clearFieldValidation(this);
        });

        $('#validation-form').on('submit', function (e) {
            if (!staffValidation()) {
                e.preventDefault();
                return false;
            }
            if ($(this).data('submitting') === true) {
                e.preventDefault();
                return false;
            }
            $(this).data('submitting', true);
            $('#add-staff, #add-staff-another').prop('disabled', true);
            return true;
        });
    });

    /*Change Field Value on Capital Letter When Keyup*/
    $(function() {
        $('.upper').keyup(function() {
            this.value = this.value.toUpperCase();
        });
    });
    /*end capital function*/

    /*copy permanent address on temporary address*/
    function CopyAddress(f) {
        if(f.permanent_address_copier.checked == true) {
            f.temp_address.value = f.address.value;
            f.temp_state.value = f.state.value;
            f.temp_postal_code.value = f.postal_code.value;
        }
    }

    function activeGeneralInfo(skipValidation) {
        deActiveAllTabs();
        $('#generalInfoTab').addClass('active');
        $('#generalInfo').addClass('active');
        return true;
    }

    function activeProfileImage(skipValidation) {
        if (skipValidation !== true && !staffValidation()) {
            return false;
        }
        deActiveAllTabs();
        $('#profileImageTab').addClass('active');
        $('#profileImage').addClass('active');
        return true;
    }

    function activeExtraInfo(skipValidation) {
        if (skipValidation !== true && !staffValidation()) {
            return false;
        }
        deActiveAllTabs();
        $('#extraInfoTab').addClass('active');
        $('#extraInfo').addClass('active');
        return true;
    }

    function deActiveAllTabs(){
        $('#generalInfoTab').removeClass('active');
        $('#generalInfo').removeClass('active');
        $('#profileImageTab').removeClass('active');
        $('#profileImage').removeClass('active');
        $('#extraInfoTab').removeClass('active');
        $('#extraInfo').removeClass('active');
    }

</script>