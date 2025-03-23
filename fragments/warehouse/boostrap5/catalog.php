<?php
/** @var rex_fragment $this */
?>
<div class="container px-5">

    <div class="flex flex-wrap border-4" >

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
