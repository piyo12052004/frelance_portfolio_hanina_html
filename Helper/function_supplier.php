<?php
require_once __DIR__ . '/../Config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// alert supplier
function set_flash($type, $title, $message)
{
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'error', 'warning', 'info'
        'title' => $title,
        'message' => $message
    ];
}