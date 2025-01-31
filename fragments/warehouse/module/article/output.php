<?php
if (rex::isBackend()) {
    echo rex_view::info('Einzelausgabe für Artikel "REX_VALUE[2]"');
    return;
}
?>

<h3>REX_VALUE[2]</h3>
<p>Preis: <?= number_format("REX_VALUE[3]",2,',',"'"). ' '.rex_config::get("warehouse","currency_symbol") ?></p>
<form action="/" method="post" id="warehouse_form_detail">
    <input type="hidden" name="art_id" value="REX_VALUE[1]">
    <input type="hidden" name="action" value="add_to_cart">
    <input type="hidden" name="article_id" value="REX_ARTICLE_ID">
    <input type="hidden" name="art_type" value="warehouse_single">
    <p class="uk-text-small uk-margin-remove-top">inkl. MwSt. zzgl. <a href="#shipping_modal" uk-toggle="">Versandkosten</a></p>
    <label for="warehouse_count_REX_SLICE_ID" class="switch_count uk-button uk-button-primary uk-padding-remove uk-margin-bottom" data-value="-1">
        <span uk-icon="icon: minus;" class="uk-icon"><svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="minus">
                <rect height="1" width="18" y="9" x="1"></rect>
            </svg></span>
    </label>
    <input name="order_count" type="text" class="uk-input uk-inline order_count uk-margin-bottom" id="warehouse_count_REX_SLICE_ID" value="1">
    <label for="warehouse_count_REX_SLICE_ID" class="uk-button uk-button-primary uk-padding-remove switch_count uk-margin-bottom" data-value="+1">
        <span uk-icon="icon: plus;" class="uk-icon"><svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="plus">
                <rect x="9" y="1" width="1" height="17"></rect>
                <rect x="1" y="9" width="17" height="1"></rect>
            </svg></span>
    </label>
    <button type="submit" name="submit" value="1" class="uk-button order_button uk-margin-bottom">Bestellen</button>
    <!-- input type="hidden" name="warehouse_attr[]" value="spon0" class="warehouse_attribute" -->
</form>
