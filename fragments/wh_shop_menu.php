<?php $warehouse_prop = rex::getProperty('warehouse_prop') ?>
<?= html_entity_decode($this->wrapper[0]); ?>

    <ul class="lev2 <?= $this->ul_class ?>">
        <?php foreach ($warehouse_prop['tree'] as $main_item) : // Katalog Navi ?>
            <li>
                <a class="uk-link-reset" href="<?= rex_getUrl('', '', ['category_id' => $main_item['id']]) ?>">
                    <?php /* img class="uk-display-block uk-margin-auto uk-margin-bottom" src="/images/cat_thumb/<?= $main_item['image'] ?>" alt="<?= $main_item['name_raw'] ?>" width="80" height="80" */ ?>
                    <?= $main_item['name_raw'] ?>
                </a>
                <?php if (isset($main_item['level'])) : ?>
                    <ul class="lev3">
                        <?php foreach ($main_item['level'] as $sub_item) : ?>
                            <li><a href="<?= rex_getUrl('', '', ['category_id' => $sub_item['id']]) ?>"><?= $sub_item['name_raw'] ?></a></li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
            </li>                                                
        <?php endforeach ?>
    </ul>

<?= html_entity_decode($this->wrapper[1]); ?>
