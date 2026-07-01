<?php

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    ? 'https'
    : 'http';

define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST']);