<?php

use Alexplusde\Tracks\Media;
use Alexplusde\Tracks\Structure;
use FriendsOfRedaxo\Warehouse\Domain;

/* Medien in den Medienpool hinzufügen */
$files = [
    'warehouse_einhom_blau.jpeg' => 'Warehouse Demo - Einhorn blau',
    'warehouse_trex_blau.jpeg' => 'Warehouse Demo - T-Rex blau',
    'warehouse_trex_gruen.jpeg' => 'Warehouse Demo - T-Rex grün',
    'warehouse_trex_rot.jpeg' => 'Warehouse Demo - T-Rex rot',
    'warehouse_trex_rot_2.jpeg' => 'Warehouse Demo - T-Rex rot 2',
    'warehouse_triceratops_blau.jpeg' => 'Warehouse Demo - Triceratops blau',
    'warehouse_triceratops_gruen.jpeg' => 'Warehouse Demo - Triceratops grün',
];

foreach ($files as $filename => $title) {
    $path = __DIR__ . '/install/media/' . $filename;
    if (file_exists($path)) {
        Media::addImage($filename, $path, $title);
    }
}

/* To-Do - Slices befüllen */

if(rex_addon::get('yform_seeder')->isAvailable()) {
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
        $sql->setValue('image', $faker->randomElement(array_keys($files()));
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
}
