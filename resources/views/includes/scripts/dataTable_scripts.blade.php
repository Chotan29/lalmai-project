<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.select.min.js') }}"></script>

<script type="text/javascript">
    jQuery(function($) {
        // Function to initialize DataTables with common options
        function initializeDataTable(selector) {
            // Check if the DataTable is already initialized to prevent errors
            if ($.fn.DataTable.isDataTable(selector)) {
                // If it is, destroy it first (optional, only if you need to re-initialize)
                // If you just want to skip, remove the destroy line and the return
                $(selector).DataTable().destroy();
            }

            return $(selector).DataTable({
                bAutoWidth: true,
                "lengthMenu": [ [50, 100, 200, 300, 400, 500, -1], [50, 100, 200, 300, 400, 500, 'All'] ],
                "aaSorting": [],
                "autoWidth": true,
                "fixedColumns": true,
                "sScrollX": "100%",
                "sScrollXInner": "100%",
                "bScrollCollapse": true,
                select: {
                    style: 'multi'
                }
            });
        }

        // Initialize specific DataTables by ID first
        var myTable = initializeDataTable('#dynamic-table');
        var myTable1 = initializeDataTable('#dynamic-table-1');

        // Initialize any other tables with the class 'dynamic-table'
        // Ensure that tables with specific IDs like #dynamic-table or #dynamic-table-1
        // are EXCLUDED if they also happen to have the class 'dynamic-table',
        // to prevent double initialization.
        $('table.dynamic-table:not(#dynamic-table):not(#dynamic-table-1)').each(function() {
            initializeDataTable($(this));
        });

        // Initialize Buttons for myTable (assuming this is your primary table for buttons)
        if (myTable) {
            $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

            new $.fn.dataTable.Buttons( myTable, {
                buttons: [
                    {
                        "extend": "colvis",
                        "text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
                        "className": "btn btn-white btn-primary btn-bold",
                        columns: ':not(:first):not(:last)'
                    },
                    {
                        "extend": "copy",
                        "text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "csv",
                        "title": "REPORT ON CSV",
                        "text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "excel",
                        "title": "REPORT ON EXCEL",
                        "text": "<i class='fa fa-file-excel bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "pdf",
                        "text": "<i class='fa fa-file-pdf bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
                        "className": "btn btn-white btn-primary btn-bold"
                    },
                    {
                        "extend": "print",
                        "text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
                        "className": "btn btn-white btn-primary btn-bold",
                        autoPrint: true,
                        message: "{{ $panel }} List"
                    }
                ]
            } );
            myTable.buttons().container().appendTo( $('.tableTools-container') );

            // Style the message box for myTable
            var defaultCopyAction = myTable.button(1).action();
            myTable.button(1).action(function (e, dt, button, config) {
                defaultCopyAction(e, dt, button, config);
                $('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
            });

            var defaultColvisAction = myTable.button(0).action();
            myTable.button(0).action(function (e, dt, button, config) {
                defaultColvisAction(e, dt, button, config);
                if($('.dt-button-collection > .dropdown-menu').length == 0) {
                    $('.dt-button-collection')
                        .wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
                        .find('a').attr('href', '#').wrap("<li />")
                }
                $('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
            });

            // Add tooltip for small view action buttons
            setTimeout(function() {
                $($('.tableTools-container')).find('a.dt-button').each(function() {
                    var div = $(this).find(' > div').first();
                    if(div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
                    else $(this).tooltip({container: 'body', title: $(this).text()});
                });
            }, 500);

            // Row selection for myTable
            myTable.on( 'select', function ( e, dt, type, index ) {
                if ( type === 'row' ) {
                    $( myTable.row( index ).node() ).find('input:checkbox').prop('checked', true);
                }
            });
            myTable.on( 'deselect', function ( e, dt, type, index ) {
                if ( type === 'row' ) {
                    $( myTable.row( index ).node() ).find('input:checkbox').prop('checked', false);
                }
            });

            // Table checkboxes for myTable
            $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);
            $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function(){
                var th_checked = this.checked;
                $('#dynamic-table').find('tbody > tr').each(function(){
                    var row = this;
                    if(th_checked) myTable.row(row).select();
                    else myTable.row(row).deselect();
                });
            });

            $('#dynamic-table').on('click', 'td input[type=checkbox]' , function(){
                var row = $(this).closest('tr').get(0);
                if(this.checked) myTable.row(row).deselect();
                else myTable.row(row).select();
            });

            $(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {
                e.stopImmediatePropagation();
                e.stopPropagation();
                e.preventDefault();
            });
        }


        // For the first simple table (not a DataTable)
        var active_class = 'active';
        $('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function(){
            var th_checked = this.checked;
            $(this).closest('table').find('tbody > tr').each(function(){
                var row = this;
                if(th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
                else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
            });
        });

        $('#simple-table').on('click', 'td input[type=checkbox]' , function(){
            var $row = $(this).closest('tr');
            if($row.is('.detail-row ')) return;
            if(this.checked) $row.addClass(active_class);
            else $row.removeClass(active_class);
        });

        // Tooltip for action buttons
        $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
        function tooltip_placement(context, source) {
            var $source = $(source);
            var $parent = $source.closest('table')
            var off1 = $parent.offset();
            var w1 = $parent.width();
            var off2 = $source.offset();
            if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
            return 'left';
        }

        // Show/hide details button
        $('.show-details-btn').on('click', function(e) {
            e.preventDefault();
            $(this).closest('tr').next().toggleClass('open');
            $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
        });

        // Add horizontal scrollbars to a simple table
        $('#simple-table').css({'width':'2000px', 'max-width': 'none'}).wrap('<div style="width: 1000px;" />').parent().ace_scroll(
            {
                horizontal: true,
                styleClass: 'scroll-top scroll-dark scroll-visible',
                size: 2000,
                mouseWheelLock: true
            }
        ).css('padding-top', '12px');

    });

    $('.sorting').click(function(){
        console.log('i was clicked');
    });
</script>