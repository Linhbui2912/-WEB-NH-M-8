<?php

declare(strict_types=1);

$pcDb = [
    'host' => '26.151.17.5',
    'name' => 'db_pawsconnect',
    'user' => 'paws_user',
    'pass' => '',
    'charset' => 'utf8mb4',
];

$localFile = __DIR__ . '/config.local.php';
if (is_file($localFile)) {
    /** @var mixed $loaded */
    $loaded = require $localFile;
    if (is_array($loaded)) {
        $pcDb = array_merge($pcDb, $loaded);
    }
}

define('DB_HOST', (string) $pcDb['host']);
define('DB_NAME', (string) $pcDb['name']);
define('DB_USER', (string) $pcDb['user']);
define('DB_PASS', (string) $pcDb['pass']);
define('DB_CHARSET', (string) $pcDb['charset']);

?>
