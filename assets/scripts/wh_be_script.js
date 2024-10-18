;
$(function () {
    $('.sortable tbody').sortable();
    $('.formbe_table tbody').sortable();
    $("body").on("click", ".sortable .row_add", function () {
        var $tr = $(this).closest("tr");
        var $clone = $tr.clone();
        $tr.after($clone);
    });
    $("body").on("click", ".sortable .row_del", function () {
        if ($(this).closest("tbody").find("tr").length > 1) {
            $(this).closest("tr").remove();
        }
    });

    $('.wh-aufklapp').on('click',function(e) {
        $(this).next('.wh-klappauf').toggleClass('open');
    });

    warehouse_edittable();

    function warehouse_edittable() {
        let n = 0;
        $('.edittable.warehouse_relayprice').each(function() {
            n++;
            let $elem = $(this);
            let the_id = $elem[0].id;

            if (!$elem.hasClass('has_edittable')) {
                $elem.addClass('has_edittable');
            
                window['mytable'+n] = $('#'+the_id).editTable({
                    data: [['']],           // Fill the table with a js array (this is overridden by the textarea content if not empty)
                    tableClass: 'inputtable',   // Table class, for styling
                    jsonData: false,        // Fill the table with json data (this will override data property)
                    headerCols: ["Menge", "Preis"],
                    maxRows: 999,           // Max number of rows which can be added
                    first_row: false,        // First row should be highlighted?
                    row_template: false,    // An array of column types set in field_templates
                    field_templates: false, // An array of custom field type objects
        
                    // Validate fields
                    validate_field: function (col_id, value, col_type, $element) {
                        return true;
                    }
                });
            }
        
    
        });
    
    }

    $(document).on('rex:ready', function() {
        warehouse_edittable();
        $('.edittable + table tbody').sortable();
    });
    
    
});
