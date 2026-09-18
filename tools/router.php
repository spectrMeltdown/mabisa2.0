<?php

// Development router for PHP's built-in server.
// Serves the app under the /mabisa2.0/ prefix so the hardcoded absolute
// paths (auth redirects, favicon links) resolve the same way they do in
// the original Apache/XAMPP setup.
//
// Usage (from the repo root):
//   php -S 0.0.0.0:8000 tools/router.php

$ROOT = dirname(__DIR__);

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$prefix = '/mabisa2.0';

if (str_starts_with($uri, $prefix)) {
    $uri = substr($uri, strlen($prefix));
}

if ($uri === '' || $uri === '/') {
    $uri = '/index.php';
}

$path = realpath($ROOT . $uri);

if ($path === false || str_starts_with($path, $ROOT . DIRECTORY_SEPARATOR) === false || !is_file($path)) {
    http_response_code(404);
    header('Content-Type: text/plain');
    echo 'Not Found';
    return true;
}

// Never expose local secrets / quarantined data.
$relative = ltrim(substr($path, strlen($ROOT)), DIRECTORY_SEPARATOR);
if (str_starts_with($relative, 'sensitive/') || str_starts_with($relative, '.env')) {
    http_response_code(403);
    header('Content-Type: text/plain');
    echo 'Forbidden';
    return true;
}

if (str_ends_with($path, '.php')) {
    // Resolve the app's relative require paths against the script's own
    // directory, matching the original Apache behaviour.
    chdir(dirname($path));
    require $path;
    return true;
}

$mime = mime_content_type($path) ?: 'application/octet-stream';
header('Content-Type: ' . $mime);
readfile($path);
return true;
