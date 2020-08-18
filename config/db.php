<?php
/**
 * Database Configuration
 *
 * All of your system's database connection settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/DbConfig.php.
 *
 * @see craft\config\DbConfig
 */

use config\Env;

return [
    'dsn' => Env::DB_DSN,
    'user' => Env::DB_USER,
    'password' => Env::DB_PASSWORD,
    'tablePrefix' => Env::DB_TABLEPREFIX,
];

