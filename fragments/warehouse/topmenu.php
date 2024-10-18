        <div class="col-md-9 col-sm-9 jtv-megamenu">
          <div class="mtmegamenu">
            <ul class="hidden-xs">
	            <?php
		           // dump($this->tree);
		            ?>
            <?php foreach ($this->tree as $main_item) : ?>
              <li class="mt-root demo_custom_link_cms">
                <div class="mt-root-item"><a href="<?= rex_getUrl('', '', ['category_id' => $main_item['id']]) ?>">
                  <div class="title title_font"><span class="title-text"><?= $main_item['name_raw'] ?></span></div>
                  </a></div>
            <?php if (isset($main_item['level'])) : ?>
                <ul class="menu-items col-md-3 col-sm-4 col-xs-12">
            <?php foreach ($main_item['level'] as $sub_item) : ?>
                  <li class="menu-item depth-1">
                    <div class="title"> <a href="<?= rex_getUrl('', '', ['category_id' => $sub_item['id']]) ?>"> <?= $sub_item['name_raw'] ?> </a></div>
                  </li>
            <?php endforeach ?>
                </ul>

            <?php endif ?>
              </li>
           <?php endforeach ?>

            </ul>
          </div>
        </div>