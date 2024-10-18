<?php $warehouse_prop = rex::getProperty('warehouse_prop') ?>
<div
    class="uk-navbar-dropdown uk-margin-remove uk-padding-remove-vertical uk-drop" uk-drop="pos: bottom-justify;delay-show: 125;delay-hide: 50;duration: 75;boundary: .tm-navbar-container;boundary-align: true;pos: bottom-justify;flip: x">
    <div class="uk-container">
        <ul class="uk-navbar-dropdown-grid uk-child-width-1-5 uk-grid uk-grid-stack" uk-grid="">                                            
            <?php foreach ($warehouse_prop['tree'] as $main_item) : // Katalog Navi ?>
                <li>
                    <div class="uk-margin-top uk-margin-bottom">
                        <a class="uk-link-reset" href="<?= rex_getUrl('', '', ['category_id' => $main_item['id']]) ?>">
                            <img class="uk-display-block uk-margin-auto uk-margin-bottom" src="/images/cat_thumb/<?= $main_item['image'] ?>" alt="<?= $main_item['name_raw'] ?>" width="80" height="80">
                            <div class="uk-text-bolder"><?= $main_item['name_raw'] ?></div>
                        </a>
                        <?php if (isset($main_item['level'])) : ?>
                            <ul class="uk-nav uk-nav-default">
                                <?php foreach ($main_item['level'] as $sub_item) : ?>
                                    <li><a href="<?= rex_getUrl('', '', ['category_id' => $sub_item['id']]) ?>"><?= $sub_item['name_raw'] ?></a></li>
                                <?php endforeach ?>
                            </ul>
                        <?php endif ?>
                    </div>
                </li>                                                
            <?php endforeach ?>
        </ul>
    </div>
</div>
