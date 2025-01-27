<?php

define('ABSPATH', realpath(__DIR__ . '/../').'/');
define('WP_DEBUG', true);
define('DB_HOST', 'mariadb');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
define('DB_NAME', 'def');
define('DB_PASSWORD', 'root');
define('DB_USER', 'root');

define('WP_HOME', 'http://localhost');
define('WP_SITEURL', 'http://localhost');

define('AUTH_KEY', 'put your unique phrase here');
define('SECURE_AUTH_KEY', 'put your unique phrase here');
define('LOGGED_IN_KEY', 'put your unique phrase here');
define('NONCE_KEY', 'put your unique phrase here');
define('AUTH_SALT', 'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT', 'put your unique phrase here');
define('NONCE_SALT', 'put your unique phrase here');

$table_prefix = 'wp_';

define('WP_TESTS_DOMAIN', 'example.org');
define('WP_TESTS_EMAIL', 'admin@example.org');
define('WP_HTTP_BLOCK_EXTERNAL', true);
