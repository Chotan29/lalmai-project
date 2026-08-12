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
        if (!$field.length) {
            return;
        }

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
        if (!selector) {
            return;
        }

        var $field = $(selector).first();
        if (!$field.length) {
            return;
        }

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
        if (selector) {
            markFieldInvalid(selector, noteMessage || 'This field is required.');
        }

        if (typeof tabActivator === 'function') {
            tabActivator(true);
        }

        focusAndScrollToField(selector);
        toastr.warning(toastMessage, 'Info:');
        return false;
    }

    function previewStudentProfileImage(input) {
        if (!input || !input.files || !input.files[0]) {
            return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#student-photo-preview').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }

    function clearRegistrationValidationState() {
        $('#validation-form .field-invalid').removeClass('field-invalid');
        $('#validation-form .validation-note').remove();
        clearSubjectWrapperInvalid();
    }

    function clearSubjectWrapperInvalid() {
        $('#subjects_wrapper').removeClass('field-invalid live-invalid-container');
        $('#subjects_wrapper').find('> .validation-note').remove();
    }

    function setSubjectWrapperInvalid(message, showToast) {
        var $wrapper = $('#subjects_wrapper');
        if (!$wrapper.length) {
            return false;
        }

        $wrapper.addClass('field-invalid live-invalid-container');
        $wrapper.find('> .validation-note').remove();
        $wrapper.prepend('<div class="validation-note is-visible">' + message + '</div>');

        if (showToast) {
            toastr.warning(message, 'Info:');
        }

        return false;
    }

    $(document).ready(function () {
        //validation

        $('#validation-form').on('input change', 'input, select, textarea', function () {
            clearFieldValidation(this);
        });

        $('#validation-form').on('submit', function (e) {
            if (!registrationValidation()) {
                e.preventDefault();
                return false;
            }

            if ($(this).data('submitting') === true) {
                e.preventDefault();
                return false;
            }

            $(this).data('submitting', true);
            $('#add-student, #add-student-another').prop('disabled', true);
            return true;
        });

        $('#load-academicinfo-html').click(function () {
            $.ajax({
                type: 'POST',
                url: '{{ route('student.academicInfo-html') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function (response) {
                    var data = (typeof response === 'string' ? $.parseJSON(response) : response);

                    if (data.error) {
                        //$.notify(data.message, "warning");
                    } else {

                        $('#academicInfo_wrapper').append(data.html);
                        //$(document).find('option[value="0"]').attr("value", "");
                    }
                }
            });

        });

        document.getElementById('guardian-detail').style.display = 'block';
        document.getElementById('link-guardian-detail').style.display = 'none';

        /*link guardian*/
        $('select[name="guardian_link_id"]').select2({
            placeholder: 'Select Guardian...',
            ajax: {
                url: '{{ route('student.guardian-name-autocomplete') }}',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data
                    };

                },
                cache: true
            }

        });

        $('#load-guardian-html-btn').click(function () {

            var guardians_id = $('select[name="guardian_link_id"]').val();
            if (!guardians_id)
                toastr.warning("Please, Find Guardian First.", "Warning");
            else {
                $('#guardian_wrapper').empty();
                $.ajax({
                    type: 'POST',
                    url: '{{ route('student.guardianInfo-html') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: guardians_id
                    },
                    success: function (response) {
                        var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                        if (data.error) {
                            toastr.warning(data.message, "warning");
                        } else {

                            $('#guardian_wrapper').append(data.html);
                            //toastr.success(data.message, "success");
                        }
                    }
                });
            }
        });

        $('#student_main_image').on('change', function () {
            previewStudentProfileImage(this);
        });

    });


    function activeGeneralInfo() {
        //$('ul li').removeClass('active');
        deActiveAllTabs();
        $('#generalInfoTab').addClass('active');
        $('#generalInfo').addClass('active');
        return true;
    }

    function activeAcademicInfo(skipValidation) {
        //$('ul li').removeClass('active');
        if (skipValidation !== true && !registrationValidation()) {
            return false;
        }
        deActiveAllTabs();
        $('#academicInfoTab').addClass('active');
        $('#academicInfo').addClass('active');
        return true;
    }

    function activeProfileImage(skipValidation) {
        //$('ul li').removeClass('active');
        if (skipValidation !== true && !registrationValidation()) {
            return false;
        }
        deActiveAllTabs();
        $('#profileImageTab').addClass('active');
        $('#profileImage').addClass('active');
        return true;
    }

    function activeRuleAgreement(skipValidation) {
        //$('ul li').removeClass('active');
        if (skipValidation !== true && !registrationValidation()) {
            return false;
        }
        deActiveAllTabs();
        $('#ruleAgreementTab').addClass('active');
        $('#ruleAgreement').addClass('active');
        return true;
    }

    function activeExtraInfo(skipValidation) {
        //$('ul li').removeClass('active');
        if (skipValidation !== true && !registrationValidation()) {
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
        $('#academicInfoTab').removeClass('active');
        $('#academicInfo').removeClass('active');
        $('#profileImageTab').removeClass('active');
        $('#profileImage').removeClass('active');
        $('#ruleAgreementTab').removeClass('active');
        $('#ruleAgreement').removeClass('active');
        $('#extraInfoTab').removeClass('active');
        $('#extraInfo').removeClass('active');

    }

    function registrationValidation(){
        clearRegistrationValidationState();

        var reg_no = $('input[name="reg_no"]').val();
        var reg_date = $('input[name="reg_date"]').val();
        var faculty = $('select[name="faculty"]').val();
        var semester = $('select[name="semester"]').val();
        var batch = $('select[name="batch"]').val();
        var academic_status = $('select[name="academic_status"]').val();
        var first_name = $('input[name="first_name"]').val();
        var last_name = $('input[name="last_name"]').val();
        var date_of_birth = $('input[name="date_of_birth"]').val();
        var gender = $('select[name="gender"]').val();
        var nationality = $('input[name="nationality"]').val();
        var religion = $('select[name="religion"]').val();
        var mobile_1 = $('input[name="mobile_1"]').val();
        var address = $('input[name="address"]').val();
        var state = $('input[name="state"]').val();
        var country = $('input[name="country"]').val();
        var email = $('input[name="email"]').val();

        if (reg_no !== '') {

        }else{
            return invalidateFieldAndStop('input[name="reg_no"]', 'Roll No. is required.', "Please, Enter Roll No.", activeGeneralInfo);
        }

        if (reg_date !== '') {

        }else{
            return invalidateFieldAndStop('input[name="reg_date"]', 'Registration date is required.', "Please, Enter Registration Date.", activeGeneralInfo);
        }

        if (faculty > 0 && semester > 0) {

        }else{
            if (!(faculty > 0)) {
                return invalidateFieldAndStop('select[name="faculty"]', 'Please select Faculty/Program/Class.', "Please, Select Faculty/Program/Class & Sem./Sec.", activeGeneralInfo);
            }
            return invalidateFieldAndStop('select[name="semester"]', 'Please select Sem./Sec.', "Please, Select Faculty/Program/Class & Sem./Sec.", activeGeneralInfo);
        }

        if ($('input[name="subject[]"]').length && !validateSubjectSelection(true)) {
            activeGeneralInfo(true);
            focusAndScrollToField('#subjects_wrapper');
            return false;
        }


        if (batch > 0) {

        }else{
            return invalidateFieldAndStop('select[name="batch"]', 'Please select batch.', "Please, Select "+__('form_fields.student.fields.batch'), activeGeneralInfo);
        }


        if (academic_status >0) {

        }else{
            return invalidateFieldAndStop('select[name="academic_status"]', 'Please select academic status.', "Please, Select Academic Status", activeGeneralInfo);
        }

        if (first_name !== "" && last_name !=="") {

        }else{
            if (first_name === '') {
                return invalidateFieldAndStop('input[name="first_name"]', 'First name is required.', "Please, Enter Student First & Last Name", activeGeneralInfo);
            }
            return invalidateFieldAndStop('input[name="last_name"]', 'Last name is required.', "Please, Enter Student First & Last Name", activeGeneralInfo);
        }

        if (date_of_birth !== '') {

        }else{
            return invalidateFieldAndStop('input[name="date_of_birth"]', 'Date of birth is required.', "Please, Enter Date of Birth.", activeGeneralInfo);
        }

        if (gender !== '') {

        }else{
            return invalidateFieldAndStop('select[name="gender"]', 'Please select gender.', "Please, Select Gender.", activeGeneralInfo);
        }

        if (nationality !== '') {

        }else{
            return invalidateFieldAndStop('input[name="nationality"]', 'Nationality is required.', "Please, Enter Nationality.", activeGeneralInfo);
        }

        if (religion !== '') {

        }else{
            return invalidateFieldAndStop('select[name="religion"]', 'Please select religion.', "Please, Select Religion.", activeGeneralInfo);
        }

        if (email !== '') {
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                return invalidateFieldAndStop('input[name="email"]', 'Enter a valid email address.', "Please, Enter Valid Email Address.", activeGeneralInfo);
            }
        }else{
            return invalidateFieldAndStop('input[name="email"]', 'Email address is required.', "Please, Enter Email Address.", activeGeneralInfo);
        }

        if (mobile_1 !== '') {
            var bdMobilePattern = /^01[3-9]\d{8}$/;
            if (!bdMobilePattern.test(mobile_1)) {
                return invalidateFieldAndStop('input[name="mobile_1"]', 'Must be a valid Bangladesh mobile number (e.g. 01XXXXXXXXX).', "Invalid mobile number! Enter Bangladesh format (e.g. 01712345678).", activeGeneralInfo);
            }
        }else{
            return invalidateFieldAndStop('input[name="mobile_1"]', 'Mobile number is required.', "Please, Enter Mobile Number.", activeGeneralInfo);
        }

        if (address !== '' && state !== '') {

        }else{
            if (address === '') {
                return invalidateFieldAndStop('input[name="address"]', 'Address is required.', "Please, Enter Address & State Info.", activeGeneralInfo);
            }
            return invalidateFieldAndStop('input[name="state"]', 'State/Division is required.', "Please, Enter Address & State Info.", activeGeneralInfo);
        }

        var father_first_name = $('input[name="father_first_name"]').val();
        var father_last_name = $('input[name="father_last_name"]').val();
        var mother_first_name = $('input[name="mother_first_name"]').val();
        var mother_last_name = $('input[name="mother_last_name"]').val();

        if (father_first_name !== '' && father_last_name !== '') {

        }else{
            if (father_first_name === '') {
                return invalidateFieldAndStop('input[name="father_first_name"]', 'Father first name is required.', "Please, Enter Father First Name & Last Name.", activeGeneralInfo);
            }
            return invalidateFieldAndStop('input[name="father_last_name"]', 'Father last name is required.', "Please, Enter Father First Name & Last Name.", activeGeneralInfo);
        }

        if (mother_first_name !== '' && mother_last_name !== '') {

        }else{
            if (mother_first_name === '') {
                return invalidateFieldAndStop('input[name="mother_first_name"]', 'Mother first name is required.', "Please, Enter Mother First Name & Last Name.", activeGeneralInfo);
            }
            return invalidateFieldAndStop('input[name="mother_last_name"]', 'Mother last name is required.', "Please, Enter Mother First Name & Last Name.", activeGeneralInfo);
        }

        var guardian_is = $('input[name="guardian_is"]:checked').val();

        if(guardian_is == 'father_as_guardian' || guardian_is == 'mother_as_guardian' || guardian_is == 'other_guardian'){
            var guardian_first_name = $('input[name="guardian_first_name"]').val();
            var guardian_last_name = $('input[name="guardian_last_name"]').val();
            var guardian_relation = $('input[name="guardian_relation"]').val();
            if (guardian_first_name !== '' && guardian_last_name !== '' && guardian_relation !== '') {

            }else{
                if (guardian_first_name === '') {
                    return invalidateFieldAndStop('input[name="guardian_first_name"]', 'Guardian first name is required.', "Please, Enter Guardian First Name, Last Name & Relation.", activeGeneralInfo);
                }
                if (guardian_last_name === '') {
                    return invalidateFieldAndStop('input[name="guardian_last_name"]', 'Guardian last name is required.', "Please, Enter Guardian First Name, Last Name & Relation.", activeGeneralInfo);
                }
                return invalidateFieldAndStop('input[name="guardian_relation"]', 'Guardian relation is required.', "Please, Enter Guardian First Name, Last Name & Relation.", activeGeneralInfo);
            }
        }else{
            removeRequiredFieldInGuardian();
            var guardian_link_id = $('select[name="guardian_link_id"]').val();
            if (guardian_link_id !=="" && guardian_link_id > 0) {

            }else{
                return invalidateFieldAndStop('select[name="guardian_link_id"]', 'Please find and link guardian info.', "Please, Find & Link Guardian Info", activeGeneralInfo);
            }
        }

        return true;
    }

    function loadSubject($this) {
        $('#subjects_wrapper').html('')
        var faculty = $('select[name="faculty"]').val();
        var semester = $('select[name="semester"]').val();


        if (faculty == 0) {
            toastr.info("Please, Select Faculty/Program/Class", "Info:");
            return false;
        }

        if (semester == 0) {
            toastr.info("Please, Select Sem./Sec.", "Info:");
            return false;
        }

        if (!semester)
            toastr.warning("Please, Choose Semester.", "Warning");
        else {

            $.ajax({
                type: 'POST',
                url: '{{ route('online-registration.find-subject') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    faculty_id: faculty,
                    semester_id: semester
                },
                success: function (response) {
                    var data = (typeof response === 'string' ? $.parseJSON(response) : response);
                    if (data.error) {
                        $('#subjects_wrapper').html('')
                        toastr.warning(data.error, "Warning:");
                    } else {
                        $('#subjects_wrapper').html('')
                        $('#subjects_wrapper').append(data.subjects);
                        clearSubjectWrapperInvalid();
                        //$(document).find('option[value="0"]').attr("value", "");
                        toastr.info(data.success, "Info:");
                    }
                }
            });
        }
        appendAcademicInfoRow(semester);

    }


    /*Change Field Value on Capital Letter When Keyup*/
    $(function() {
        $('.upper').keyup(function() {
            this.value = this.value.toUpperCase();
        });
    });
    /*end capital function*/

    function appendAcademicInfoRow($semester){
        $.ajax({
            type: 'POST',
            url: '{{ route('student.academicInfo-html') }}',
            data: {
                _token: '{{ csrf_token() }}',
                semester_id: $semester
            },
            success: function (response) {
                var data = (typeof response === 'string' ? $.parseJSON(response) : response);

                if (data.error) {
                    //$.notify(data.message, "warning");
                } else {
                    $('#academicInfo_wrapper').empty();
                    $('#academicInfo_wrapper').append(data.html);
                }
            }
        });
    }

    function loadSemesters($this) {

        $.ajax({
            type: 'POST',
            url: '{{ route('student.find-semester') }}',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                faculty_id: $this.value
            },
            success: function (response) {
                var data = response;
                if (typeof data === 'string') {
                    data = $.parseJSON(data);
                }
                if (data.error) {
                    $.notify(data.message, "warning");
                } else {
                    $('.semester').html('').append('<option value="0">Select Sem./Sec.</option>');
                    $.each(data.semester, function(key,valueObj){
                        $('.semester').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                    });
                }
            },
            error: function () {
                $('.semester').html('').append('<option value="0">Select Sem./Sec.</option>');
                $.notify('Semester/Section load failed. Please select program again.', 'warning');
            }
        });

    }

    /*copy permanent address on temporary address*/
    function CopyAddress(f) {
        if(f.permanent_address_copier.checked == true) {
            f.temp_address.value = f.address.value;
            f.temp_state.value = f.state.value;
            f.temp_postal_code.value = f.postal_code.value;
        }
    }

    /* The Guardian buttons.

       Each one used to end with four lines like

           f.father_as_guardian.checked == false;

       which were wrong twice over: the radios are one group named guardian_is, so there is no
       field called father_as_guardian, and '==' compares rather than assigns. The lines never
       did anything, and reaching them threw a TypeError that stopped the function where it
       stood. A browser unchecks the rest of a radio group by itself, so they are gone.

       They also used to fill different sets of fields - Father and Mother set occupation and
       office, Self did not - so moving a student from Father to Self left the father's
       occupation sitting under the student's own name, and it saved that way. Every button now
       goes through setGuardian, which writes every guardian field on every press. That is what
       makes a stale value impossible, rather than merely fixed once. */

    function showGuardianDetail() {
        document.getElementById('guardian-detail').style.display = 'block';
        document.getElementById('link-guardian-detail').style.display = 'none';
        addRequiredFieldInGuardian();
    }

    /* A form field's value, or an empty string if this form does not carry that field.
       The registration form and the online registration form do not hold quite the same set,
       and one missing input used to take the whole button down with it. */
    function fieldValue(f, name) {
        return (f[name] && typeof f[name].value === 'string') ? f[name].value : '';
    }

    function setGuardian(f, values) {
        var fields = ['first_name', 'middle_name', 'last_name', 'eligibility', 'occupation',
            'office', 'office_number', 'residence_number', 'mobile_1', 'mobile_2', 'email',
            'address', 'relation'];

        for (var i = 0; i < fields.length; i++) {
            var box = f['guardian_' + fields[i]];
            if (!box || typeof box.value !== 'string') { continue; }
            box.value = values[fields[i]] || '';
        }
    }

    /*copy Father Detail on Guardian Detail*//*guardian_is*/
    function FatherAsGuardian(f) {
        showGuardianDetail();
        if (f.guardian_is.value != 'father_as_guardian') { return; }

        setGuardian(f, {
            first_name: fieldValue(f, 'father_first_name'),
            middle_name: fieldValue(f, 'father_middle_name'),
            last_name: fieldValue(f, 'father_last_name'),
            eligibility: fieldValue(f, 'father_eligibility'),
            occupation: fieldValue(f, 'father_occupation'),
            office: fieldValue(f, 'father_office'),
            office_number: fieldValue(f, 'father_office_number'),
            residence_number: fieldValue(f, 'father_residence_number'),
            mobile_1: fieldValue(f, 'father_mobile_1'),
            mobile_2: fieldValue(f, 'father_mobile_2'),
            email: fieldValue(f, 'father_email'),
            /* A parent has no address of their own on this form; the household address is the
               student's. Leaving it untouched meant a previous guardian's address stayed. */
            address: fieldValue(f, 'address'),
            relation: 'FATHER'
        });
    }

    /*copy Mother Detail on Guardian Detail*/
    function MotherAsGuardian(f) {
        showGuardianDetail();
        if (f.guardian_is.value != 'mother_as_guardian') { return; }

        setGuardian(f, {
            first_name: fieldValue(f, 'mother_first_name'),
            middle_name: fieldValue(f, 'mother_middle_name'),
            last_name: fieldValue(f, 'mother_last_name'),
            eligibility: fieldValue(f, 'mother_eligibility'),
            occupation: fieldValue(f, 'mother_occupation'),
            office: fieldValue(f, 'mother_office'),
            office_number: fieldValue(f, 'mother_office_number'),
            residence_number: fieldValue(f, 'mother_residence_number'),
            mobile_1: fieldValue(f, 'mother_mobile_1'),
            mobile_2: fieldValue(f, 'mother_mobile_2'),
            email: fieldValue(f, 'mother_email'),
            address: fieldValue(f, 'address'),
            relation: 'MOTHER'
        });
    }

    /*the student stands as their own guardian*/
    function SelfGuardian(f) {
        showGuardianDetail();
        if (f.guardian_is.value != 'self_guardian') { return; }

        /* Eligibility, occupation, office and office number are left blank on purpose: they
           belong to whoever the guardian was before, and a student is not a farmer because
           their father is. This is the case that was reported. */
        setGuardian(f, {
            first_name: fieldValue(f, 'first_name'),
            middle_name: fieldValue(f, 'middle_name'),
            last_name: fieldValue(f, 'last_name'),
            eligibility: '',
            occupation: '',
            office: '',
            office_number: '',
            residence_number: fieldValue(f, 'home_phone'),
            mobile_1: fieldValue(f, 'mobile_1'),
            mobile_2: fieldValue(f, 'mobile_2'),
            email: fieldValue(f, 'email'),
            address: fieldValue(f, 'address'),
            relation: 'SELF'
        });
    }

    /*Blank Guardian Detail to Enter New*/
    function OtherGuardian(f) {
        showGuardianDetail();
        if (f.guardian_is.value != 'other_guardian') { return; }

        /* Everything, including the address - which used to be left behind, so a brand new
           guardian arrived carrying the last one's address. */
        setGuardian(f, {});
    }

    /* Declared with no parameter while the markup calls linkGuardian(this.form), so every line
       below read an undefined f and the button threw before it copied anything. */
    function linkGuardian(f) {
        document.getElementById('guardian-detail').style.display = 'none';
        document.getElementById('link-guardian-detail').style.display = 'block';
        removeRequiredFieldInGuardian();

        if (!f) { return; }

        setGuardian(f, {
            first_name: fieldValue(f, 'father_first_name'),
            middle_name: fieldValue(f, 'father_middle_name'),
            last_name: fieldValue(f, 'father_last_name'),
            eligibility: fieldValue(f, 'father_eligibility'),
            occupation: fieldValue(f, 'father_occupation'),
            office: fieldValue(f, 'father_office'),
            office_number: fieldValue(f, 'father_office_number'),
            residence_number: fieldValue(f, 'father_residence_number'),
            mobile_1: fieldValue(f, 'father_mobile_1'),
            mobile_2: fieldValue(f, 'father_mobile_2'),
            email: fieldValue(f, 'father_email'),
            address: fieldValue(f, 'address'),
            relation: 'FATHER'
        });
    }

    function addRequiredFieldInGuardian(){
        $('input[name="guardian_first_name"]').attr('required','required');
        // $('input[name="guardian_last_name"]').attr('required','required');
        $('input[name="guardian_mobile_1"]').attr('required','required');
        $('input[name="guardian_relation"]').attr('required','required');
        $('input[name="guardian_address"]').attr('required','required');
    }

    function removeRequiredFieldInGuardian(){
        $('input[name="guardian_first_name"]').removeAttr('required');
        // $('input[name="guardian_last_name"]').removeAttr('required');
        $('input[name="guardian_mobile_1"]').removeAttr('required');
        $('input[name="guardian_relation"]').removeAttr('required');
        $('input[name="guardian_address"]').removeAttr('required');
    }



    /* Limits are per class, set in Academic → Semester and rendered as hidden inputs by the
       subject picker. They used to be hard-coded to the HSC shape (6 compulsory + 1 optional,
       never more than 7), which made honours classes impossible - Accounting 3rd year has 8
       compulsory papers and no 4th subject. */
    function getSubjectLimit(name, fallback) {
        var value = parseInt($('input[name="' + name + '"]').val(), 10);
        return isNaN(value) ? fallback : value;
    }

    function getSubjectMaxCompulsory() {
        return getSubjectLimit('max_compulsory_count', 6);
    }

    function getSubjectMaxOptional() {
        return getSubjectLimit('max_optional_count', 1);
    }

    function getSubjectMaxCount() {
        var maxFromInput = getSubjectLimit('max_subjects_count', 0);
        if (maxFromInput > 0) {
            return maxFromInput;
        }

        var byLimits = getSubjectMaxCompulsory() + getSubjectMaxOptional();
        if (byLimits > 0) {
            return byLimits;
        }

        return $('#subjects_wrapper').find('input[name="subject[]"]').length;
    }

    function validateSubjectSelection(showToast) {
        var $wrapper = $('#subjects_wrapper');
        var $subjects = $wrapper.find('input[name="subject[]"]');

        if (!$subjects.length) {
            return setSubjectWrapperInvalid('Please load subjects for the selected semester.', showToast);
        }

        var selectedCount = $subjects.filter(':checked').length;
        var maxCount = getSubjectMaxCount();
        var selectedOptionalCount = $subjects.filter(':checked').filter('[data-subject-type="optional"]').length;
        var selectedCompulsoryCount = $subjects.filter(':checked').filter('[data-subject-type="compulsory"]').length;

        if (selectedCount < 1) {
            return setSubjectWrapperInvalid('Please select at least 1 subject.', showToast);
        }

        var maxOptional = getSubjectMaxOptional();
        var maxCompulsory = getSubjectMaxCompulsory();

        if (selectedOptionalCount > maxOptional) {
            return setSubjectWrapperInvalid(maxOptional > 0
                ? 'You can select maximum ' + maxOptional + ' optional subject(s) for this class.'
                : 'This class has no optional (4th) subject.', showToast);
        }

        if (selectedCompulsoryCount > maxCompulsory) {
            return setSubjectWrapperInvalid('You can select maximum ' + maxCompulsory + ' compulsory subjects for this class.', showToast);
        }

        if (selectedCount > maxCount) {
            return setSubjectWrapperInvalid('You can select maximum ' + maxCount + ' subjects for this class.', showToast);
        }

        clearSubjectWrapperInvalid();
        return true;
    }

    function checkSubjectMinMaxSelection(){
        return validateSubjectSelection(true);
    }

    $('#subjects_wrapper').on('change', 'input[name="subject[]"]', function() {
        var maxCount = getSubjectMaxCount();
        var $subjects = $('#subjects_wrapper').find('input[name="subject[]"]');
        var selectedCount = $subjects.filter(':checked').length;
        var selectedOptionalCount = $subjects.filter(':checked').filter('[data-subject-type="optional"]').length;
        var selectedCompulsoryCount = $subjects.filter(':checked').filter('[data-subject-type="compulsory"]').length;

        var maxOptional = getSubjectMaxOptional();
        var maxCompulsory = getSubjectMaxCompulsory();

        if ($(this).is(':checked') && $(this).data('subject-type') === 'optional' && selectedOptionalCount > maxOptional) {
            this.checked = false;
            setSubjectWrapperInvalid(maxOptional > 0
                ? 'You can select maximum ' + maxOptional + ' optional subject(s) for this class.'
                : 'This class has no optional (4th) subject.', true);
            return;
        }

        if ($(this).is(':checked') && $(this).data('subject-type') === 'compulsory' && selectedCompulsoryCount > maxCompulsory) {
            this.checked = false;
            setSubjectWrapperInvalid('You can select maximum ' + maxCompulsory + ' compulsory subjects for this class.', true);
            return;
        }

        if ($(this).is(':checked') && selectedCount > maxCount) {
            this.checked = false;
            setSubjectWrapperInvalid('You can select maximum ' + maxCount + ' subjects for this class.', true);
            return;
        }

        validateSubjectSelection(false);
    });

</script>