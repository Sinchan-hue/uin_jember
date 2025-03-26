<?php
// Kunci enkripsi (simpan di tempat aman)
define('ENCRYPTION_KEY', 'kunci_rahasia_anda_123!@#');

// Fungsi enkripsi
function encryptFile($filename) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($filename, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
    return urlencode(base64_encode($iv.$encrypted));
}

// Fungsi dekripsi
function decryptFile($encrypted) {
    $data = base64_decode(urldecode($encrypted));
    $iv_size = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($data, 0, $iv_size);
    $encrypted = substr($data, $iv_size);
    return openssl_decrypt($encrypted, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
}
?>