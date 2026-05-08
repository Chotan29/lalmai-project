
<script>
    $(document).ready(function () {
        $pageType = $("#page_type").val();

       if($pageType == 'content-page'){
           $("#content").show();
           $("#link").hide();
       }else{
           $("#content").hide();
           $("#link").show();
       }

        $('#page_type').on('change',function(){
            var selection = $(this).val();
            switch(selection){
                case "content-page":
                    $("#content").show();
                    $("#link").hide();
                    break;
                case "link-page":
                    $("#content").hide();
                    $("#link").show();
                    break;
                case "predefine-link":
                    $("#content").hide();
                    $("#link").show();
                    break;
                default:
                    $("#link").hide()
            }
        });
    });
</script>