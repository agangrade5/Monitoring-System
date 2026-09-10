<?php
require 'vendor/autoload.php';

$g = stream_context_create(['ssl' => ['capture_peer_cert' => true, 'verify_peer' => false]]);
$s = @stream_socket_client("ssl://google.com:443", $e, $es, 5, STREAM_CLIENT_CONNECT, $g);
if ($s) {
    $p = stream_context_get_params($s);
    if (isset($p['options']['ssl']['peer_certificate'])) {
        $cert = openssl_x509_parse($p['options']['ssl']['peer_certificate']);
        echo "ISSUER ARRAY:\n";
        print_r($cert['issuer'] ?? null);
        
        $issuer = $cert['issuer']['O'] ?? $cert['issuer']['CN'] ?? (is_array($cert['issuer'] ?? null) ? implode(', ', $cert['issuer']) : ($cert['issuer'] ?? null));
        echo "\nEXTRACTED ISSUER: " . $issuer . "\n";
    }
}
