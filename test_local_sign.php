<?php
// Test signing locally
$privateKeyPath = __DIR__ . '/storage/app/qz-tray/private-key.pem';
$certPath = __DIR__ . '/storage/app/qz-tray/digital-certificate.txt';

echo "Testing certificate and private key...\n";

// Check if files exist
if (!file_exists($privateKeyPath)) {
    die("Private key file not found\n");
}
if (!file_exists($certPath)) {
    die("Certificate file not found\n");
}

// Load private key
$privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));
if (!$privateKey) {
    die("Failed to load private key: " . openssl_error_string() . "\n");
}

echo "Private key loaded successfully\n";

// Load certificate
$cert = openssl_x509_read(file_get_contents($certPath));
if (!$cert) {
    die("Failed to load certificate: " . openssl_error_string() . "\n");
}

echo "Certificate loaded successfully\n";

// Test signing
$data = 'test data for signing';
$signature = '';
if (openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
    echo "Signing successful!\n";
    echo "Signature: " . base64_encode($signature) . "\n";
} else {
    echo "Signing failed: " . openssl_error_string() . "\n";
}

// Clean up
openssl_pkey_free($privateKey);
openssl_x509_free($cert);
?>