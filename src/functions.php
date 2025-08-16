<?php

if (!defined('SECRET_KEY')) {
    throw new Exception('SECRET_KEY belum didefinisikan. Silakan set SECRET_KEY berukuran 32 Byte');
}

function encrypt_url($data) {
    $key = SECRET_KEY;
    $iv = random_bytes(16);
    $ciphertext = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha3-256', $iv . $ciphertext, $key, true);
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($iv . $ciphertext . $hmac));
}

function decrypt_url($data) {
    $key = SECRET_KEY;
    $missing_padding = 4 - (strlen($data) % 4);
    if ($missing_padding < 4) $data .= str_repeat('=', $missing_padding);
    $decoded = base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    if ($decoded === false || strlen($decoded) < 48) return false;
    $iv = substr($decoded, 0, 16);
    $ciphertext = substr($decoded, 16, -32);
    $hmac = substr($decoded, -32); 
    $calculated_hmac = hash_hmac('sha3-256', $iv . $ciphertext, $key, true);
    if (!hash_equals($hmac, $calculated_hmac)) return false;
    $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    if ($plaintext === false) return false;
    return $plaintext;
}

function detectUrl() {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $domain = $scheme . "://" . $_SERVER['HTTP_HOST'] . "/";

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
    $segments = $path === '' ? [] : explode('/', trim($path, '/'));

    if (count($segments) === 1 && $segments[0] !== '') {
        return [
            'mode'   => 'full',
            'domain' => $domain,
            'page'   => $segments[0]
        ];
    } elseif (count($segments) > 1) {
        $data = array_pop($segments);
        return [
            'mode'   => 'partial',
            'domain' => $domain,
            'path'   => implode('/', $segments) . '/',
            'data'   => $data
        ];
    }

    return [
        'mode'   => 'empty',
        'domain' => $domain
    ];
}

function is_ciphertext($value) {
    $result = decrypt_url($value);
    return $result !== false;
}

function update_url() {
    $urlMode = detectUrl();
    $newUrl = null;

    if ($urlMode['mode'] === 'full') {
        if (is_ciphertext($urlMode['page'])) {
            $page = decrypt_url($urlMode['page']);
            $newUrl = $urlMode['domain'] . encrypt_url($page);
        }
    } elseif ($urlMode['mode'] === 'partial') {
        if (is_ciphertext($urlMode['data'])) {
            $data = decrypt_url($urlMode['data']);
            $newUrl = $urlMode['domain'] . $urlMode['path'] . encrypt_url($data);
        }
    }

    if ($newUrl !== null) {
        echo "<script type=\"text/javascript\">history.pushState(null, '', '" . $newUrl . "');</script>";
    }
}
