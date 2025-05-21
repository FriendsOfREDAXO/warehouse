<div class="btn-group dropdown">
    <form action="/" method="get" id="warehouse_search">
        <input type="hidden" name="rex-api-call" value="warehouse_search">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="warehouseSearchQuicknavigationButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="rex-icon fa-shopping-cart"> </i> <span class="caret"></span>
        </button>
        <div style="max-width: calc(100vw - 200px); width: 700px" class="quicknavi quicknavi-items list-group dropdown-menu dropdown-menu-right" aria-labelledby="warehouseSearch">
            <div style="padding: 10px;">
                <div class="input-group">
                    <input type="search" class="form-control" name="q" placeholder="<?= \rex_i18n::msg('warehouse_quicknavigation_search_placeholder') ?>">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" id="warehouseSearchButton"><span class="rex-icon fa-search"></span> <?= \rex_i18n::msg('warehouse_quicknavigation_search_button') ?></button>
                    </span>
                </div>
    </form>
            <p class="help-block small">
                <?= rex_i18n::msg('warehouse.search.description') ?>
            </p>

    <div id="warehouseSearchResults" style="padding-top: 10px;">
    </div>
</div>

</div>

</div>
