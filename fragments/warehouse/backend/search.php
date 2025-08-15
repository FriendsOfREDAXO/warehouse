<?php

/** @var rex_fragment $this */

?>
<div class="panel">
    <div class="panel-body">
        <div class="form-group">
            <form class="form-inline" method="POST" action="<?= rex_url::backendPage('warehouse/search') ?>">
                <div class="form-group" style="width:100%">
                    <div class="input-group" style="width:100%">
                        <input class="form-control" type="search" name="query" value="<?= htmlspecialchars(rex_request('query', 'string', '')) ?>" placeholder="Suchbegriff eingeben" style="width:100%">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit">Suchen</button>
                        </span>
                    </div>
                </div>
            </form>
            <p class="help-block small">
                <?= rex_i18n::msg('warehouse.search.description') ?>
            </p>
        </div>
    </div>
</div>
