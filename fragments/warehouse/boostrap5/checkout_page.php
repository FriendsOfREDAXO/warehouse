<div class="uk-grid-medium uk-child-width-1-1 uk-grid uk-grid-stack" uk-grid="">
    <section class="uk-text-center uk-first-column"><a class="uk-link-muted uk-text-small" href="<?= rex_getUrl(rex_config::get('warehouse','cart_page')) ?>"><span class="uk-margin-xsmall-right uk-icon" uk-icon="icon: arrow-left; ratio: .75;"></span>{{ return to cart }}</a>
        <h1 class="uk-margin-small-top uk-margin-remove-bottom">{{ checkout }}</h1>
    </section>
    <section class="uk-grid-margin uk-first-column">
        <div class="uk-grid-medium uk-grid" uk-grid="">
            <div class="uk-form-stacked uk-width-1-1 tm-checkout uk-width-expand@m uk-first-column">
                <?= html_entity_decode($this->form); ?>
            </div>            
        </div>
        <p><a class="uk-link-muted uk-text-small" href="<?= rex_getUrl(rex_config::get('warehouse', 'cart_page')) ?>">{{ return to cart }}</a></p>
    </section>
</div>