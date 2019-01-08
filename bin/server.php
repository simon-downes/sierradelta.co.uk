<?php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$content_types = [
    'html' => 'text/html',
    'js'   => 'application/x-javascript',
    'css'  => 'text/css',
    'less' => 'text/css',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'gif'  => 'image/gif',
    'svg'  => 'image/svg+xml',
    'txt'  => 'text/plain',
];

$uri = urldecode($uri);

$requested = (__DIR__. $uri);

if ($uri !== '/' and file_exists($requested)) {


    if( substr($requested, -4) == '.php' ) {
        include $requested;
    }
    else {
        $ext  = pathinfo($requested, PATHINFO_EXTENSION);
        $type = isset($content_types[$ext]) ? $content_types[$ext] : 'application/octet-stream';
        header("Content-Type: {$type}");
        readfile($requested);
    }

}
else {
    return false;
}

//require_once __DIR__.'/entrypoints/index.php';