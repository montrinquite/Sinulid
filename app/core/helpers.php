<?php
declare(strict_types=1);

function e(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function url(string $path = '/'): string { return BASE_URL . '/' . ltrim($path, '/'); }

function asset(string $path): string { return url('assets/' . ltrim($path, '/')); }

function upload_url(?string $file, string $folder): string
{
    return $file ? url("media/{$folder}/" . rawurlencode($file)) : '';
}

function time_ago(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . 'm';
    if ($diff < 86400)  return floor($diff / 3600) . 'h';
    if ($diff < 604800) return floor($diff / 86400) . 'd';
    return date('M j, Y', strtotime($datetime));
}

function avatar(?string $file, string $name, int $size = 40): string
{
    $style = "width:{$size}px;height:{$size}px;font-size:" . round($size * 0.42) . 'px';
    if ($file) {
        return '<img class="avatar" src="' . e(upload_url($file, 'profiles')) . '" alt="' . e($name) . '" style="' . $style . '">';
    }
    $initial = mb_strtoupper(mb_substr(trim($name) !== '' ? $name : '?', 0, 1));
    return '<span class="avatar avatar-initial" aria-hidden="true" style="' . $style . '">' . e($initial) . '</span>';
}