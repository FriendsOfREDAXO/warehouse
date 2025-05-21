<?php

namespace FriendsOfRedaxo\Warehouse;

use FriendsOfRedaxo\Warehouse\QuickNavigation\QuickNavigationButton;
use rex;
use rex_login;
use rex_extension;
use rex_yrewrite;
use rex_article;
use rex_config;
use rex_yform_manager_dataset;
use rex_yform;
use rex_addon;
use rex_be_controller;
use rex_view;
use Url\Url;

/** @var rex_addon $this */

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
    rex_yform_manager_dataset::setModelClass('rex_warehouse_article', Article::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_article_variant', ArticleVariant::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_category', Category::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_order', Order::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_settings_domain', Domain::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_country', Country::class);
    rex_yform_manager_dataset::setModelClass('rex_warehouse_shipping', Shipping::class);
}

rex_yform::addTemplatePath($this->getPath('ytemplates'));


// Nur, wenn auf Backend-Seiten des Warehouse-Addons
if (rex::isBackend() && rex_be_controller::getCurrentPagePart(1) == 'warehouse') {
    // CSS im Backend laden
    rex_view::addCssFile($this->getAssetsUrl('css/backend.css'));
}


if (rex::isFrontend()) {
    rex_login::startSession();

    rex_extension::register('PACKAGES_INCLUDED', function () {

        if (rex_addon::get('url') !== null) {
            $manager = Url::resolveCurrent();
            if ($manager) {
                $profile = $manager->getProfile();
                $seo = $manager->getSeo();

                $data_id = (int) $manager->getDatasetId();
                if ($profile->getTableName() == rex::getTable('warehouse_article')) {
                    $warehouse_prop['sitemode'] = 'article';
                } elseif ($profile->getTableName() == rex::getTable('warehouse_category')) {
                    $warehouse_prop['sitemode'] = 'category';
                    $warehouse_prop['seo_title'] = $seo['title'];
                    $warehouse_prop['path'] = Warehouse::getCategoryPath($data_id);
                }
                $curl = rtrim(rex_yrewrite::getFullPath(), '/') . $_SERVER['REQUEST_URI'];
                rex_set_session('current_page', $curl);
            }

            rex::setProperty('warehouse_prop', $warehouse_prop);

            if (rex_article::getCurrentId() == Warehouse::getConfig('thankyou_page')) {
                if (rex_get('paymentId')) {
                    PayPal::ExecutePayment();
                    Warehouse::emptyCart();
                }
            }
        }
    });
}

if (rex::isBackend()) {
    rex_extension::register('YFORM_DATA_LIST', Article::epYformDataList(...));
    rex_extension::register('YFORM_DATA_LIST', Category::epYformDataList(...));
    rex_extension::register('YFORM_DATA_LIST', Order::epYformDataList(...));
    rex_extension::register('YFORM_DATA_LIST_ACTION_BUTTONS', Order::epYformDataListActionButtons(...));
}

/* Javascript-Asset laden */
if (rex::isBackend() && rex::getUser()) {
    rex_view::addJsFile($this->getAssetsUrl('js/backend.js'));
}

/* Wenn quick_navigation installiert, dann */
if (rex::isBackend() && rex_addon::get('quick_navigation')->isAvailable()) {
    \FriendsOfRedaxo\QuickNavigation\Button\ButtonRegistry::registerButton(new QuickNavigationButton(), 5);
}

// Faker-Daten generieren

/*
$tableName = 'rex_warehouse_article';

$faker = \Faker\Factory::create();

$faker->addProvider(new \YformSeeder\Faker\Redaxo($faker));

for ($i = 1; $i < 10000; $i++) {
    $sql = \rex_sql::factory();
    $sql->setTable($tableName);
    $sql->setValue('id', $i);
    $sql->setValue('name', $faker->words(3, true));
    $sql->setValue('text', $faker->paragraphs(2, true));
    $sql->setValue('short_text', $faker->text(100));
    $sql->setValue('status', $faker->randomElement(Article::getStatusOptions()));
    $sql->setValue('availability', $faker->randomElement(array_keys((Article::getAvailabilityOptions()))));
    $sql->setValue('tax', $faker->randomElement(array_keys((Article::getTaxOptions()))));
    $sql->setValue('weight', $faker->randomFloat(2, 0, 1));
    $sql->setValue('uuid', $faker->uuid());
    $sql->setValue('createdate', $faker->dateTime()->format('Y-m-d H:i:s'));
    $sql->setValue('updatedate', $faker->dateTime()->format('Y-m-d H:i:s'));
    $sql->setValue('price', $faker->randomFloat(2, 1, 1111));
    $sql->setValue('category_id', $faker->biasedNumberBetween(0, 10));
    $sql->setValue('status', $faker->biasedNumberBetween(0, 10));

    $sql->insertOrUpdate();
}

$tableName = 'rex_warehouse_category';


$faker = \Faker\Factory::create();

$faker->addProvider(new \YformSeeder\Faker\Redaxo($faker));

for ($i = 1; $i < 100; $i++) {
    $sql = \rex_sql::factory();
    $sql->setTable($tableName);
    $sql->setValue('id', $i);
    $sql->setValue('name', $faker->words(2, true));
    $sql->setValue('uuid', $faker->uuid());
    $sql->setValue('createdate', $faker->dateTime()->format('Y-m-d H:i:s'));
    $sql->setValue('updatedate', $faker->dateTime()->format('Y-m-d H:i:s'));

    $sql->insertOrUpdate();
}


$tableName = 'rex_warehouse_order';

$faker = \Faker\Factory::create();

$faker->addProvider(new \YformSeeder\Faker\Redaxo($faker));


for ($i = 5; $i < 10000; $i++) {
    $sql = \rex_sql::factory();
    $sql->setTable($tableName);
    $sql->setValue('id', $i);
    $sql->setValue('salutation', $faker->randomElement(['Herr', 'Frau']));
    $sql->setValue('firstname', $faker->firstName());
    $sql->setValue('lastname', $faker->lastName());
    $sql->setValue('company', $faker->company());
    $sql->setValue('address', $faker->streetAddress());
    $sql->setValue('zip', $faker->postcode());
    $sql->setValue('city', $faker->city());
    $sql->setValue('country', $faker->randomElement(array_keys((PayPal::COUNTRY_CODES))));
    $sql->setValue('email', $faker->email());
    $sql->setValue('createdate', $faker->dateTime()->format('Y-m-d H:i:s'));
    $sql->setValue('paypal_id', $faker->uuid());
    $sql->setValue('payment_id', $faker->uuid());
    $sql->setValue('paypal_confirm_token', $faker->uuid());
    $sql->setValue('payment_confirm', $faker->uuid());
    $sql->setValue('order_text', $faker->text(100));
    $sql->setValue('order_json', $faker->text(100));
    $sql->setValue('order_total', $faker->randomFloat(2, 1, 1111));
    $sql->setValue('ycom_userid', $faker->biasedNumberBetween(0, 10));
    $sql->setValue('payment_type', $faker->randomElement(['paypal', 'creditcard', 'banktransfer']));
    $sql->setValue('payed', $faker->randomElement([0, 1]));
    $sql->setValue('imported', $faker->randomElement([0, 1]));
    $sql->setValue('createdate', $faker->dateTime()->format('Y-m-d H:i:s'));
    $sql->setValue('updatedate', $faker->dateTime()->format('Y-m-d H:i:s'));

    $sql->insertOrUpdate();
}
*/
