<?php

defined('ASSET_URL') || define('ASSET_URL', $_ENV['ASSET_URL']);
if (isset($_SERVER['SERVER_NAME'])) {
    defined('HOST_BASE') || define('HOST_BASE', $_SERVER['SERVER_NAME']);
}
