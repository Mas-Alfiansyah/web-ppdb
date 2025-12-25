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

/**
 * Validate image dimensions
 * @param array $file - $_FILES array element
 * @param int $min_width - Minimum width in pixels
 * @param int $min_height - Minimum height in pixels
 * @return array - ['status' => bool, 'message' => string, 'dimensions' => array]
 */
function validate_image_dimensions($file, $min_width = 300, $min_height = 400)
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'message' => 'File upload error'];
    }

    $image_info = getimagesize($file['tmp_name']);
    if ($image_info === false) {
        return ['status' => false, 'message' => 'File bukan gambar yang valid'];
    }

    list($width, $height) = $image_info;

    if ($width < $min_width || $height < $min_height) {
        return [
            'status' => false,
            'message' => "Dimensi foto minimal {$min_width}x{$min_height}px. Foto Anda: {$width}x{$height}px",
            'dimensions' => ['width' => $width, 'height' => $height]
        ];
    }

    return [
        'status' => true,
        'message' => 'Dimensi foto valid',
        'dimensions' => ['width' => $width, 'height' => $height]
    ];
}

/**
 * Generate thumbnail from image
 * @param string $source - Source image path
 * @param string $dest - Destination thumbnail path
 * @param int $max_width - Maximum width
 * @param int $max_height - Maximum height
 * @return bool - Success status
 */
function generate_thumbnail($source, $dest, $max_width = 150, $max_height = 150)
{
    $image_info = getimagesize($source);
    if ($image_info === false) return false;

    list($orig_width, $orig_height, $type) = $image_info;

    // Calculate new dimensions
    $ratio = min($max_width / $orig_width, $max_height / $orig_height);
    $new_width = (int)($orig_width * $ratio);
    $new_height = (int)($orig_height * $ratio);

    // Create source image
    switch ($type) {
        case IMAGETYPE_JPEG:
            $src_image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $src_image = imagecreatefrompng($source);
            break;
        default:
            return false;
    }

    // Create thumbnail
    $thumb = imagecreatetruecolor($new_width, $new_height);

    // Preserve transparency for PNG
    if ($type == IMAGETYPE_PNG) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }

    imagecopyresampled($thumb, $src_image, 0, 0, 0, 0, $new_width, $new_height, $orig_width, $orig_height);

    // Save thumbnail
    $result = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($thumb, $dest, 85);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($thumb, $dest, 8);
            break;
    }

    imagedestroy($src_image);
    imagedestroy($thumb);

    return $result;
}
