<?php $warehouse_prop = rex::getProperty('warehouse_prop') ?>
<ul class="uk-nav-sub uk-list-divider">
    <?php foreach ($warehouse_prop['tree'] as $main_item) : // Katalog Navi ?>
        <li>
            <a class="uk-link-reset" href="<?= rex_getUrl('', '', ['category_id' => $main_item['id']]) ?>">
                <?= $main_item['name_raw'] ?>
            </a>
            <?php if (isset($main_item['level'])) : ?>
                <ul>
                    <?php foreach ($main_item['level'] as $sub_item) : ?>
                        <li><a href="<?= rex_getUrl('', '', ['category_id' => $sub_item['id']]) ?>"><?= $sub_item['name_raw'] ?></a></li>
                    <?php endforeach ?>
                </ul>
            <?php endif ?>
        </li>                                                
    <?php endforeach ?>
</ul>
