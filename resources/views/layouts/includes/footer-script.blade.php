<!-- Footer Scripts -->
<!--[if !IE]> -->
<script src="{{ asset('assets/js/jquery-2.1.4.min.js') }}"></script>
<!-- <![endif]-->

<!--[if IE]>
<script src="{{ asset('assets/js/jquery-1.11.3.min.js') }}"></script>
<![endif]-->

<!-- Conditional Mobile Touch Script -->
<script>
    if('ontouchstart' in document.documentElement) {
        document.write('<script src="{{ asset('assets/js/jquery.mobile.custom.min.js') }}"><\/script>');
    }
</script>

<!-- Core JS -->
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

<!-- Plugin Scripts -->
<script src="{{ asset('assets/js/toastr.min.js') }}" defer></script>
<script src="{{ asset('assets/js/select2.min.js') }}" defer></script>
<script src="{{ asset('assets/js/chosen.jquery.min.js') }}" defer></script>

<!-- Chosen Select Initialization -->
<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        if(!ace.vars['touch']) {
            $('.chosen-select').chosen({allow_single_deselect:true});
            
            // Handle window resize for chosen selects
            var resizeChosen = function() {
                $('.chosen-select').each(function() {
                    $(this).next().css({'width': $(this).parent().width()});
                });
            };
            
            $(window)
                .off('resize.chosen')
                .on('resize.chosen', resizeChosen)
                .trigger('resize.chosen');
            
            // Handle sidebar collapse/expand
            $(document).on('settings.ace.chosen', function(e, event_name) {
                if(event_name === 'sidebar_collapsed') {
                    resizeChosen();
                }
            });
        }
    });
</script>

<!-- ACE Scripts -->
<script src="{{ asset('assets/js/ace-elements.min.js') }}" defer></script>
<script src="{{ asset('assets/js/ace.min.js') }}" defer></script>

<!-- Top Menu Fixed Sidebar Script -->
<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        jQuery(function($) {
            var $sidebar = $('.sidebar').eq(0);
            if( !$sidebar.hasClass('h-sidebar') ) return;

            $(document).on('settings.ace.top_menu', function(ev, event_name, fixed) {
                if( event_name !== 'sidebar_fixed' ) return;

                var sidebar = $sidebar.get(0);
                var $window = $(window);
                var sidebar_vars = $sidebar.ace_sidebar('vars');

                // Reset if sidebar is not fixed or in mobile view
                if( !fixed || ( sidebar_vars['mobile_view'] || sidebar_vars['collapsible'] ) ) {
                    $sidebar.removeClass('lower-highlight');
                    sidebar.style.marginTop = '';
                    $window.off('scroll.ace.top_menu');
                    return;
                }

                var done = false;
                $window.on('scroll.ace.top_menu', function() {
                    var scroll = Math.min(Math.floor($window.scrollTop() / 4), 17);
                    
                    if (scroll > 16) {
                        if(!done) {
                            $sidebar.addClass('lower-highlight');
                            done = true;
                        }
                    } else {
                        if(done) {
                            $sidebar.removeClass('lower-highlight');
                            done = false;
                        }
                    }

                    sidebar.style.marginTop = (17-scroll)+'px';
                }).triggerHandler('scroll.ace.top_menu');

            }).triggerHandler('settings.ace.top_menu', ['sidebar_fixed', $sidebar.hasClass('sidebar-fixed')]);

            $(window).on('resize.ace.top_menu', function() {
                $(document).triggerHandler('settings.ace.top_menu', ['sidebar_fixed', $sidebar.hasClass('sidebar-fixed')]);
            });
        });
    });
</script>

<!-- Preloader Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth fade out for preloader
        $('#overlay').fadeOut(300, 'linear');
        
        // Apply skin class from localStorage
        var skinClass = localStorage.getItem('ace_skin');
        if(skinClass) {
            document.body.classList.add(skinClass);
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Tracking Script -->
@include('includes.scripts.tracking')

<!-- Inline Scripts Section -->
@yield('footer-scripts')