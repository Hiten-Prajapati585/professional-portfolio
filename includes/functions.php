<?php
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function base_url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function show_flash(): void
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo '<div class="alert alert-' . e($f['type']) . ' alert-dismissible fade show"><i class="bi bi-info-circle me-2"></i>' . e($f['message']) . '<button class="btn-close" data-bs-dismiss="alert"></button></div>';
        unset($_SESSION['flash']);
    }
}
function save_upload(array $file, string $folder, array $allowedMime, int $maxMb = 10): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('File upload failed.');
    if ($file['size'] > $maxMb * 1024 * 1024) throw new RuntimeException('File is larger than ' . $maxMb . ' MB.');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowedMime, true)) throw new RuntimeException('File type is not allowed.');
    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'application/pdf' => 'pdf',
        default => 'bin'
    };
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $dir = __DIR__ . '/../uploads/' . $folder;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) throw new RuntimeException('Could not save uploaded file.');
    return 'uploads/' . $folder . '/' . $name;
}
function delete_file(?string $relative): void
{
    if (!$relative) return;
    $root = realpath(__DIR__ . '/..');
    $full = realpath(__DIR__ . '/../' . $relative);
    if ($full && $root && str_starts_with($full, $root) && is_file($full)) @unlink($full);
}
function clean_url(string $url): string
{
    $url = trim($url);
    if ($url === '') return '';
    if (!preg_match('~^https?://~i', $url)) return 'https://' . $url;
    return $url;
}
