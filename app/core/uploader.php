<?php
declare(strict_types=1);

/** Secure image upload handling: extension + MIME + real image check + size + random filename. */
final class Uploader
{
    private const TYPES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /** @return array{0:?string,1:?string} [storedFilename, errorMessage]; both null when no file was sent. */
    public static function save(?array $file, string $folder, int $maxBytes = 2097152): array
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return [null, null];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [null, in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                ? 'Image is too large.' : 'Image upload failed. Please try again.'];
        }
        if (!is_uploaded_file($file['tmp_name'])) return [null, 'Invalid upload.'];
        if ($file['size'] > $maxBytes) return [null, 'Image must be ' . round($maxBytes / 1048576, 1) . ' MB or smaller.'];

        $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::EXTENSIONS, true)) return [null, 'Only JPG, PNG, GIF or WEBP images are allowed.'];

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset(self::TYPES[$mime]) || @getimagesize($file['tmp_name']) === false) {
            return [null, 'That file is not a valid image.'];
        }

        $name = bin2hex(random_bytes(16)) . '.' . self::TYPES[$mime];
        $dir  = BASE_PATH . '/uploads/' . $folder;
        if (!is_dir($dir) && !mkdir($dir, 0775, true)) return [null, 'Upload folder is not writable.'];
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) return [null, 'Could not save the image.'];

        return [$name, null];
    }

    public static function delete(?string $filename, string $folder): void
    {
        if ($filename && preg_match('/^[a-f0-9]{32}\.(jpg|png|gif|webp)$/', $filename) && in_array($folder, ['posts', 'profiles'], true)) {
            $path = BASE_PATH . "/uploads/{$folder}/{$filename}";
            if (is_file($path)) @unlink($path);
        }
    }
}