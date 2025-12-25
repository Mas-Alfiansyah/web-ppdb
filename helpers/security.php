<?php
// helpers/security.php

// Generate CSRF Token
function generate_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF Token
function verify_csrf_token($token)
{
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}

// CSRF Field for Forms
function csrf_field()
{
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// XSS Filter
function xss_clean($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = xss_clean($value);
        }
        return $data;
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Get system setting by key
 */
function get_setting($key, $default = '')
{
    global $conn;
    if (!isset($conn)) {
        require_once __DIR__ . '/../config/database.php';
    }

    $stmt = $conn->prepare("SELECT nilai FROM pengaturan WHERE kunci = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['nilai'];
    }
    return $default;
}
