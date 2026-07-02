<script type="text/javascript">
    $(document).ready(function () {
        //var end_date = $('input[name="end_date"]').val();
        document.getElementById('leave_application').style.display="none";
    });

    function applicationValidation(){
        var flag = false;
        var applicationType = $('select[name="application_type_id"]').val();
        var date = $('input[name="date"]').val();
        var end_date = $('input[name="end_date"]').val();
        var subject = $('input[name="subject"]').val();
        var message = $('textarea[name="message"]').val();

        if (applicationType == 0) {
            toastr.info("Please, Select Application Type", "Info:");
            flag = true;
            return false;
        }

        if (applicationType == 1) {
            if (date == "") {
                toastr.info("Please, Enter Start Date of Leaving", "Info:");
                flag = true;
                return false;
            }
            if (end_date == "") {
                toastr.info("Please, Enter End Date of Leaving", "Info:");
                flag = true;
                return false;
            }
        }

        if (subject == "") {
            toastr.info("Please, Enter Subject of Application", "Info:");
            flag = true;
            return false;
        }

        if (message == "") {
            toastr.info("Please, Enter Short Message of Application", "Info:");
            flag = true;
            return false;
        }

        if(flag){
            toastr.warning("Something is Wrong, Please Check", "Info:");
            return false;
        }else{
            return true;
        }

    }

    function checkApplicationType(){
        var application_type_id = $('select[name="application_type_id"]').val();
        if(application_type_id == 1){
            document.getElementById('leave_application').style.display="block";
        }else{
            document.getElementById('leave_application').style.display="none";
        }
    }
</script>