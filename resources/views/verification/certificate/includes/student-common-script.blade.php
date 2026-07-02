<script type="text/javascript">

    $(document).ready(function () {
        $('.calculate-percent').change(function() {
            $obtainMark = $(this).closest('tr').find('.mark-obtained').val();
            $maximumMark = $(this).closest('tr').find('.maximum-mark').val();
            $percentage = (($obtainMark * 100) / $maximumMark).toFixed(2);
            $(this).closest('tr').find('.percent-value').val($percentage);
        });
        //validation

        $("#add-student").click(function () {
            registrationValidation();
        });

        $("#add-student-another").click(function () {
            registrationValidation();
        });

        $('#load-academicinfo-html').click(function () {
            $.ajax({
                type: 'POST',
                url: '{{ route('student.academicInfo-html') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function (response) {
                    var data = $.parseJSON(response);

                    if (data.error) {
                        //$.notify(data.message, "warning");
                    } else {

                        $('#academicInfo_wrapper').append(data.html);
                        //$(document).find('option[value="0"]').attr("value", "");
                    }
                }
            });

        });

        if(document.getElementById('guardian-detail') !==null){
            document.getElementById('guardian-detail').style.display = 'block';
            document.getElementById('link-guardian-detail').style.display = 'none';
        }


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
                        var data = $.parseJSON(response);
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

    });


    function activeGeneralInfo() {
        //$('ul li').removeClass('active');
        deActiveAllTabs();
        $('#generalInfoTab').addClass('active');
        $('#generalInfo').addClass('active');
    }

    function activeAcademicInfo() {
        //$('ul li').removeClass('active');
        registrationValidation();
        deActiveAllTabs();
        $('#academicInfoTab').addClass('active');
        $('#academicInfo').addClass('active');
    }

    function activeProfileImage() {
        //$('ul li').removeClass('active');
        registrationValidation();
        deActiveAllTabs();
        $('#profileImageTab').addClass('active');
        $('#profileImage').addClass('active');
    }

    function activeRuleAgreement() {
        //$('ul li').removeClass('active');
        registrationValidation();
        deActiveAllTabs();
        $('#ruleAgreementTab').addClass('active');
        $('#ruleAgreement').addClass('active');
    }

    function activeExtraInfo() {
        //$('ul li').removeClass('active');
        registrationValidation();
        deActiveAllTabs();
        $('#extraInfoTab').addClass('active');
        $('#extraInfo').addClass('active');
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
        var flag = false;
        var reg_date = $('input[name="reg_date"]').val();
        var faculty = $('select[name="faculty"]').val();
        var semester = $('select[name="semester"]').val();
        var batch = $('select[name="batch"]').val();
        var first_name = $('input[name="first_name"]').val();
        var last_name = $('input[name="last_name"]').val();
        var date_of_birth = $('input[name="date_of_birth"]').val();
        var gender = $('select[name="gender"]').val();
        var nationality = $('input[name="nationality"]').val();
        var mobile_1 = $('input[name="mobile_1"]').val();
        var address = $('input[name="address"]').val();
        var state = $('input[name="state"]').val();
        var country = $('input[name="country"]').val();

        if (reg_date !== '') {

        }else{
            toastr.warning("Please, Enter Registration Date.", "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }

        if (faculty > 0 && semester > 0) {

        }else{
            toastr.warning("Please, Select Faculty/Program/Class & Sem./Sec.", "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }


        if (batch > 0) {

        }else{
            toastr.warning("Please, Select "+__('form_fields.student.fields.batch'), "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }



        if (first_name !== "" && last_name !=="") {

        }else{
            toastr.warning("Please, Enter Student First & Last Name", "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }

        if (date_of_birth !== '') {

        }else{
            toastr.warning("Please, Enter Date of Birth.", "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }

        if (gender !== '') {

        }else{
            toastr.warning("Please, Select Gender.", "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }

        if (nationality !== '') {

        }else{
            toastr.warning("Please, Enter Nationality.", "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }

        if (mobile_1 !== '') {

        }else{
            toastr.warning("Please, Enter Mobile Number.", "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }

        if (address !== '' && state !== '' && country !== '') {

        }else{
            toastr.warning("Please, Enter Address, State & Country Info.", "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }

        var father_first_name = $('input[name="father_first_name"]').val();
        var father_last_name = $('input[name="father_last_name"]').val();
        var mother_first_name = $('input[name="mother_first_name"]').val();
        var mother_last_name = $('input[name="mother_last_name"]').val();

        if (father_first_name !== '' && father_last_name !== '') {

        }else{
            toastr.warning("Please, Enter Father First Name & Last Name.", "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }

        if (mother_first_name !== '' && mother_last_name !== '') {

        }else{
            toastr.warning("Please, Enter Mother First Name & Last Name.", "Info:");
            activeGeneralInfo();
            flag = true;
            return false;
        }

        var guardian_is = $('input[name="guardian_is"]:checked').val();

        if(guardian_is == 'father_as_guardian' || guardian_is == 'mother_as_guardian' || guardian_is == 'other_guardian'){
            var guardian_first_name = $('input[name="guardian_first_name"]').val();
            var guardian_last_name = $('input[name="guardian_last_name"]').val();
            var guardian_relation = $('input[name="guardian_relation"]').val();
            if (guardian_first_name !== '' && guardian_last_name !== '' && guardian_relation !== '') {

            }else{
                toastr.warning("Please, Enter Guardian First Name, Last Name & Relation.", "Info:");
                activeGeneralInfo();
                flag = true;
                return false;
            }
        }else{
            removeRequiredFieldInGuardian();
            var guardian_link_id = $('select[name="guardian_link_id"]').val();
            if (guardian_link_id !=="" && guardian_link_id > 0) {

            }else{
                // toastr.warning("Please, Find & Link Guardian Info", "Info:");
                // activeGeneralInfo();
                // flag = true;
                // return false;
            }
        }

        activeGeneralInfo();

        if(flag){
            toastr.warning("Something is Wrong, Please Check", "Info:");
            activeGeneralInfo();
            $('#validation-form').submit(function(){
                return false;
            });
        }
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
                    var data = $.parseJSON(response);
                    if (data.error) {
                        $('#subjects_wrapper').html('')
                        toastr.warning(data.error, "Warning:");
                    } else {
                        $('#subjects_wrapper').html('')
                        $('#subjects_wrapper').append(data.subjects);
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
        console.log($semester);
        $.ajax({
            type: 'POST',
            url: '{{ route('student.academicInfo-html') }}',
            data: {
                _token: '{{ csrf_token() }}',
                semester_id: $semester
            },
            success: function (response) {
                var data = $.parseJSON(response);
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
            data: {
                _token: '{{ csrf_token() }}',
                faculty_id: $this.value
            },
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.error) {
                    $.notify(data.message, "warning");
                } else {
                    $('.semester').html('').append('<option value="0">Select Sem./Sec.</option>');
                    $.each(data.semester, function(key,valueObj){
                        $('.semester').append('<option value="'+valueObj.id+'">'+valueObj.semester+'</option>');
                    });
                }
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

    /*copy Father Detail on Guardian Detail*//*guardian_is*/
    function FatherAsGuardian(f) {
        document.getElementById('guardian-detail').style.display = 'block';
        document.getElementById('link-guardian-detail').style.display = 'none';
        addRequiredFieldInGuardian();
        if(f.guardian_is.value == 'father_as_guardian') {
            f.guardian_first_name.value = f.father_first_name.value;
            f.guardian_middle_name.value = f.father_middle_name.value;
            f.guardian_last_name.value = f.father_last_name.value;
            f.guardian_eligibility.value = f.father_eligibility.value;
            f.guardian_occupation.value = f.father_occupation.value;
            f.guardian_office.value = f.father_office.value;
            f.guardian_office_number.value = f.father_office_number.value;
            f.guardian_residence_number.value = f.father_residence_number.value;
            f.guardian_mobile_1.value = f.father_mobile_1.value;
            f.guardian_mobile_2.value = f.father_mobile_2.value;
            f.guardian_email.value = f.father_email.value;
            f.guardian_relation.value = "FATHER";
            f.mother_as_guardian.checked == false;
            f.self_guardian.checked == false;
            f.other_guardian.checked == false;
        }
    }

    /*copy Mother Detail on Guardian Detail*/
    function MotherAsGuardian(f) {
        document.getElementById('guardian-detail').style.display = 'block';
        document.getElementById('link-guardian-detail').style.display = 'none';
        addRequiredFieldInGuardian();
        if(f.guardian_is.value == 'mother_as_guardian') {
            f.guardian_first_name.value = f.mother_first_name.value;
            f.guardian_middle_name.value = f.mother_middle_name.value;
            f.guardian_last_name.value = f.mother_last_name.value;
            f.guardian_eligibility.value = f.mother_eligibility.value;
            f.guardian_occupation.value = f.mother_occupation.value;
            f.guardian_office.value = f.mother_office.value;
            f.guardian_office_number.value = f.mother_office_number.value;
            f.guardian_residence_number.value = f.mother_residence_number.value;
            f.guardian_mobile_1.value = f.mother_mobile_1.value;
            f.guardian_mobile_2.value = f.mother_mobile_2.value;
            f.guardian_email.value = f.mother_email.value;
            f.guardian_relation.value = "MOTHER";
            f.father_as_guardian.checked == false;
            f.self_guardian.checked == false;
            f.other_guardian.checked == false;
        }
    }

    /*copy Mother Detail on Guardian Detail*/
    function SelfGuardian(f) {
        document.getElementById('guardian-detail').style.display = 'block';
        document.getElementById('link-guardian-detail').style.display = 'none';
        addRequiredFieldInGuardian();
        if(f.guardian_is.value == 'self_guardian') {
            f.guardian_first_name.value = f.first_name.value;
            f.guardian_middle_name.value = f.middle_name.value;
            f.guardian_last_name.value = f.last_name.value;
            f.guardian_residence_number.value = f.home_phone.value;
            f.guardian_mobile_1.value = f.mobile_1.value;
            f.guardian_mobile_2.value = f.mobile_2.value;
            f.guardian_email.value = f.email.value;
            f.guardian_address.value = f.address.value;
            f.guardian_relation.value = "SELF";
            f.father_as_guardian.checked == false;
            f.mother_as_guardian.checked == false;
            f.other_guardian.checked == false;
        }
    }

    /*Blank Guardian Detail to Enter New*/
    function OtherGuardian(f) {
        document.getElementById('guardian-detail').style.display = 'block';
        document.getElementById('link-guardian-detail').style.display = 'none';
        addRequiredFieldInGuardian();
        if(f.guardian_is.value == 'other_guardian') {
            f.guardian_first_name.value = "";
            f.guardian_middle_name.value = "";
            f.guardian_last_name.value = "";
            f.guardian_eligibility.value = "";
            f.guardian_occupation.value = "";
            f.guardian_office.value = "";
            f.guardian_office_number.value = "";
            f.guardian_residence_number.value = "";
            f.guardian_mobile_1.value = "";
            f.guardian_mobile_2.value = "";
            f.guardian_email.value = "";
            f.guardian_relation.value = "";
            f.father_as_guardian.checked == false;
            f.mother_as_guardian.checked == false;
            f.self_guardian.checked == false;
        }
    }

    function linkGuardian() {
        document.getElementById('guardian-detail').style.display = 'none';
        document.getElementById('link-guardian-detail').style.display = 'block';
        removeRequiredFieldInGuardian();
        f.guardian_first_name.value = f.father_first_name.value;
        f.guardian_middle_name.value = f.father_middle_name.value;
        f.guardian_last_name.value = f.father_last_name.value;
        f.guardian_eligibility.value = f.father_eligibility.value;
        f.guardian_occupation.value = f.father_occupation.value;
        f.guardian_office.value = f.father_office.value;
        f.guardian_office_number.value = f.father_office_number.value;
        f.guardian_residence_number.value = f.father_residence_number.value;
        f.guardian_mobile_1.value = f.father_mobile_1.value;
        f.guardian_mobile_2.value = f.father_mobile_2.value;
        f.guardian_email.value = f.father_email.value;
        f.guardian_relation.value = "FATHER";
        f.father_as_guardian.checked == false;
        f.mother_as_guardian.checked == false;
        f.self_guardian.checked == false;
        f.other_guardian.checked == false;

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

    function checkSubjectMinMaxSelection(){
        //subject checked validation
        $max_subjects_count = $('input[name="max_subjects_count"]').val();
        $subjectChkIds = document.getElementsByName('subject[]');
        var $subjectChkCount = 0;
        $length = $subjectChkIds.length;

        for (var $i = 0; $i < $length; $i++) {
            if ($subjectChkIds[$i].type == 'checkbox' && $subjectChkIds[$i].checked) {
                $subjectChkCount++;
            }
        }

        if ($subjectChkCount == 0 || $subjectChkCount < $max_subjects_count) {
            toastr.warning("Please, Select At Least "+ $max_subjects_count +" Subject.", "Info:");
            flag = true;
            return false;
        }

        if($subjectChkCount > $max_subjects_count){
            toastr.warning("You are not eligible to choose greater than "+ $max_subjects_count +" subject.", "Warning:");
            flag = true;
            return false;
        }
    }

</script>