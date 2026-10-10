<?php
declare(strict_types=1);

class MediaController extends Controller
{
    public function show(string $folder, string $file): void
    {
        if (!in_array($folder, ['profiles', 'posts'], true) || !preg_match('/^[a-f0-9]{32}\.(jpg|png|gif|webp)$/', $file)) {
            http_response_code(404); exit;
        }
        $path = BASE_PATH . "/uploads/{$folder}/{$file}";
        if (!is_file($path)) { http_response_code(404); exit; }
        $types = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
        header('Content-Type: ' . $types[pathinfo($file, PATHINFO_EXTENSION)]);
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: public, max-age=86400');
        readfile($path);
    }
}