<?php
// helpers/auth_helper.php

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function is_admin()
{
    return isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'panitia');
}

function require_login()
{
    if (!is_logged_in()) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit();
    }
}

function require_admin()
{
    if (!is_admin()) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit();
    }
}

function current_user_id()
{
    return $_SESSION['user_id'] ?? null;
}
