<?php /*  Ausgabe der Katalog-Seite (Übersicht) */ ?>
<div class="container px-5">

    <div class="flex flex-wrap border-4" >

    <?php /*
        <!-- <aside class="uk-width-1-4 uk-visible@m tm-aside-column uk-first-column">
            <nav class="uk-card uk-card-default uk-card-body uk-card-small uk-sticky" uk-sticky="bottom: true; offset: 90" style="">
                <ul class="uk-nav uk-nav-default" uk-scrollspy-nav="closest: li; scroll: true; offset: 90">
                    <?php foreach ($this->tree as $main_item) : ?>
                        <li><a href="#<?= rex_string::normalize($main_item['name_raw']) ?>"><?= $main_item['name_raw'] ?></a></li>
                    <?php endforeach ?>
                </ul>
            </nav>
            <div class="uk-sticky-placeholder" style="height: 185px; margin: 0px;" hidden=""></div>
        </aside>
    */
    ?>



                <?php foreach ($this->tree as $main_item) : ?>
                    <div class="shadow-lg w-full border-4" id="<?= rex_string::normalize($main_item['name_raw']) ?>">
                        <div class="">
                            <header class="">
                                <div class="">
                                    <?php if ($main_item['image']) : ?>
                                    <a href="<?= rex_getUrl('', '', ['category_id' => $main_item['id']]) ?>"><img src="/images/cat_thumb/<?= $main_item['image'] ?>" alt="<?= $main_item['name_raw'] ?>" width="100" height="100"></a>
                                    <?php endif ?>
                                    <div class="">
                                        <h2 class=""><a class="" href="<?= rex_getUrl('', '', ['category_id' => $main_item['id']]) ?>"><?= $main_item['name_raw'] ?></a></h2>
                                    </div>
                                </div>
                            </header>
                            <?php if (isset($main_item['level'])) : ?>
                                <div class="">
                                    <ul class="">
                                        <?php foreach ($main_item['level'] as $sub_item) : ?>
                                            <li><a href="<?= rex_getUrl('', '', ['category_id' => $sub_item['id']]) ?>"><?= $sub_item['name_raw'] ?></a></li>
                                        <?php endforeach ?>
                                    </ul>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>
    </div>
</div>
