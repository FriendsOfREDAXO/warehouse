;
$(function() {
    $('body').on('click','.wh-variants a',function() {
        $('input[name=art_id]').val($(this).data('art_id'));
        calc_price();
    });
    
    $('body').on('click','.wh-attributes a, .wh-attributes button',function() {
        $(this).parent().addClass('uk-active').siblings().removeClass('uk-active');
        calc_price();
    });
    
    function calc_price () {
        if ($('.wh-variants .uk-active a').length) {
            price = $('.wh-variants .uk-active a').data('price');
        } else {
            price = $('#warehouse_art_price').data('price');
        }
        $('input.warehouse_attribute').remove();
        $('.wh-attributes .uk-active a, .wh-attributes .uk-card-primary').each(function() {
            if ($(this).data('pricemode') == 'absolute') {
                price = parseFloat($(this).data('price'));
            } else {
                price = parseFloat(price) + parseFloat($(this).data('price'));
            }
            $('#warehouse_form_detail').append('<input type="hidden" name="warehouse_attr[]" value="'+$(this).data('attr_id')+'" class="warehouse_attribute">');
        });
        
        $('.product_price').html(parseFloat(price).toFixed(2));
        
    }
    
    if ($('#warehouse_art_price').length) {
        calc_price();
    }
    
    
    // +/- Buttons
    
    $('label.switch_count').click(function() {
        var for_id = $(this).attr('for');
        var cur_val = $('input#'+for_id).val();
        var new_val = eval(cur_val + $(this).data('value'));
        if (new_val < 1) {
            new_val = 1;
        }
        $('input#'+for_id).val(new_val);
    });    
    
    
});
