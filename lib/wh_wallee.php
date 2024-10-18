<?php

if (rex::isDebugMode()) {
    ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}

class warehouse_wallee {

    static $space_id;
    static $user_id;
    static $secret;

    function __construct() {
        self::$space_id = rex_config::get('warehouse', 'wallee_sandboxmode') ? rex_config::get('warehouse', 'wallee_sandbox_space_id') : rex_config::get('warehouse', 'wallee_live_space_id');
        self::$user_id = rex_config::get('warehouse', 'wallee_sandboxmode') ? rex_config::get('warehouse', 'wallee_sandbox_user_id') : rex_config::get('warehouse', 'wallee_live_user_id');
        self::$secret = rex_config::get('warehouse', 'wallee_sandboxmode') ? rex_config::get('warehouse', 'wallee_sandbox_secret') : rex_config::get('warehouse', 'wallee_live_secret');
    }




}

?>
