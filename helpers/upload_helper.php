<?php
// helpers/upload_helper.php

function upload_file($file, $destination, $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'], $max_size = 2048000)
{
    // Check error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'message' => "Upload error code: " . $file['error']];
    }

    // Check size
    if ($file['size'] > $max_size) {
        return ['status' => false, 'message' => "Ukuran file terlalu besar (Max " . ($max_size / 1024) . "KB)"];
    }

    // Check extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) {
        return ['status' => false, 'message' => "Tipe file tidak diizinkan! (Hanya " . implode(', ', $allowed_types) . ")"];
    }

    // Check MIME type (Security)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed_mimes = [
        'application/pdf',
        'image/jpeg',
        'image/png'
    ];
    if (!in_array($mime, $allowed_mimes)) {
        return ['status' => false, 'message' => "MIME type invalid!"];
    }

    // Generate unique name
    $new_name = uniqid() . '.' . $ext;
    $target = $destination . '/' . $new_name;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return ['status' => true, 'filename' => $new_name];
    } else {
        return ['status' => false, 'message' => "Gagal memindahkan file upload."];
    }
}
