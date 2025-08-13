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
