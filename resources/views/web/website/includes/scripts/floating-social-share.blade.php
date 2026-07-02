<script src="{{ asset('website/js/jquery.floating-social-share.js') }}"></script>
<script>
    $("body").floatingSocialShare({
        place:'top-left',
        buttons:[
            'mail','facebook','twitter','pinterest','linkedin','whatsapp'
        ],
        title:document.title,
        url:window.location.href,
        text:{
            'default':'share with',
            'facebook':'share with facebook',
            'twitter':'tweet'
        },
        description:$('meta[name="description"]').attr('content'),
        media:$('meta[property="og:image"]').attr('content'),
        popup:true,
        popop_width:600,
        popup_height: 500,
        counter:true,
        twitter_counter:false,
        text_title_case:false
    });
</script>