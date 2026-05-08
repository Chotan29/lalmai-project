    var url = '{{ $data['url'] }}';
    var flag = false;
    var reg_no = $('input[name="reg_no"]').val();
    var reg_start_date = $('input[name="reg_start_date"]').val();
    var reg_end_date = $('input[name="reg_end_date"]').val();
    var faculty = $('select[name="faculty"]').val();
    var semester_select = $('select[name="semester_select"]').val();
    var batch = $('select[name="batch"]').val();
    var academic_status = $('select[name="academic_status"]').val();
    var status = $('select[name="status"]').val();

    var first_name = $('input[name="first_name"]').val();
    var middle_name = $('input[name="middle_name"]').val();
    var last_name = $('input[name="last_name"]').val();
    var gender = $('select[name="gender"]').val();
    var blood_group = $('select[name="blood_group"]').val();
    var date_of_birth_start_date = $('input[name="date_of_birth_start_date"]').val();
    var date_of_birth_end_date = $('input[name="date_of_birth_end_date"]').val();
    var university_reg = $('input[name="university_reg"]').val();

    var religion = $('input[name="religion"]').val();
    var caste = $('input[name="caste"]').val();
    var mother_tongue = $('input[name="mother_tongue"]').val();

    var nationality = $('input[name="nationality"]').val();
    var national_id_1 = $('input[name="national_id_1"]').val();
    var national_id_2 = $('input[name="national_id_2"]').val();
    var national_id_3 = $('input[name="national_id_3"]').val();
    var national_id_4 = $('input[name="national_id_4"]').val();





    if (reg_no !== '') {
        url += '?reg_no=' + reg_no;
        flag = true;
    }

    if (reg_start_date !== '') {
        if (flag) {
            url += '&reg_start_date=' + reg_start_date;
        } else {
            url += '?reg_start_date=' + reg_start_date;
            flag = true;
        }
    }

    if (reg_end_date !== '') {
        if (flag) {
            url += '&reg_end_date=' + reg_end_date;
        } else {
            url += '?reg_end_date=' + reg_end_date;
            flag = true;
        }
    }

    if (faculty >0) {
        if (flag) {
            url += '&faculty=' + faculty;
        } else {
            url += '?faculty=' + faculty;
            flag = true;
        }
    }

    if (semester_select > 0) {
        if (flag) {
            url += '&semester_select=' + semester_select;
        } else {
            url += '?semester_select=' + semester_select;
            flag = true;
        }
    }

    if (batch > 0) {
        if (flag) {
            url += '&batch=' + batch;
        } else {
            url += '?batch=' + batch;
            flag = true;
        }
    }

    if (status !=='all') {
        if (flag) {
            url += '&status=' + status;
        } else {
            url += '?status=' + status;
            flag = true;
        }
    }

    if (academic_status >0) {
        if (flag) {
            url += '&academic_status=' + academic_status;
        } else {
            url += '?academic_status=' + academic_status;
        }
    }

    if (first_name !== '') {
        if (flag) {
            url += '&first_name=' + first_name;
        } else {
            url += '?first_name=' + first_name;
            flag = true;
        }
    }


    if (middle_name !== '') {
        if (flag) {
            url += '&middle_name=' + middle_name;
        } else {
            url += '?middle_name=' + middle_name;
            flag = true;
        }
    }

    if (last_name !== '') {
        if (flag) {
            url += '&last_name=' + last_name;
        } else {
            url += '?last_name=' + last_name;
            flag = true;
        }
    }

    if (gender !== '') {
        if (flag) {
            url += '&gender=' + gender;
        } else {
            url += '?gender=' + gender;
            flag = true;
        }
    }

    if (blood_group !== '') {
        if (flag) {
            url += '&blood_group=' + blood_group;
        } else {
            url += '?blood_group=' + blood_group;
            flag = true;
        }
    }

    if (date_of_birth_start_date !== '') {
        if (flag) {
            url += '&date_of_birth_start_date=' + date_of_birth_start_date;
        } else {
            url += '?date_of_birth_start_date=' + date_of_birth_start_date;
            flag = true;
        }
    }

    if (date_of_birth_end_date !== '') {
        if (flag) {
            url += '&date_of_birth_end_date=' + date_of_birth_end_date;
        } else {
            url += '?date_of_birth_end_date=' + date_of_birth_end_date;
            flag = true;
        }
    }

    if (university_reg !== '') {
        if (flag) {
            url += '&university_reg=' + university_reg;
        } else {
            url += '?university_reg=' + university_reg;
            flag = true;
        }
    }


    if (religion !== '') {
        if (flag) {
            url += '&religion=' + religion;
        } else {
            url += '?religion=' + religion;
            flag = true;
        }
    }

    if (caste !== '') {
        if (flag) {
            url += '&caste=' + caste;
        } else {
            url += '?caste=' + caste;
            flag = true;
        }
    }

    if (nationality !== '') {
        if (flag) {
            url += '&nationality=' + nationality;
        } else {
            url += '?nationality=' + nationality;
            flag = true;
        }
    }

    if (national_id_1 !== '') {
        if (flag) {
            url += '&national_id_1=' + national_id_1;
        } else {
            url += '?national_id_1=' + national_id_1;
            flag = true;
        }
    }

    if (national_id_2 !== '') {
        if (flag) {
            url += '&national_id_2=' + national_id_2;
        } else {
            url += '?national_id_2=' + national_id_2;
            flag = true;
        }
    }

    if (national_id_3 !== '') {
        if (flag) {
            url += '&national_id_3=' + national_id_3;
        } else {
            url += '?national_id_3=' + national_id_3;
            flag = true;
        }
    }

    if (national_id_4 !== '') {
        if (flag) {
            url += '&national_id_4=' + national_id_4;
        } else {
            url += '?national_id_4=' + national_id_4;
            flag = true;
        }
    }

    if (mother_tongue !== '') {
        if (flag) {
            url += '&mother_tongue=' + mother_tongue;
        } else {
            url += '?mother_tongue=' + mother_tongue;
            flag = true;
        }
    }
