<?php
// Generate a simple working certificate for QZ Tray
$config = [
    "digest_alg" => "sha256",
    "private_key_bits" => 1024, // Smaller key for simplicity
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
];

$dn = [
    "countryName" => "US",
    "commonName" => "localhost",
];

// Generate private key
$privkey = openssl_pkey_new($config);
if (!$privkey) {
    die("Failed to generate private key: " . openssl_error_string() . "\n");
}

// Generate certificate
$csr = openssl_csr_new($dn, $privkey, $config);
if (!$csr) {
    die("Failed to generate CSR: " . openssl_error_string() . "\n");
}

$cert = openssl_csr_sign($csr, null, $privkey, 365, $config);
if (!$cert) {
    die("Failed to generate certificate: " . openssl_error_string() . "\n");
}

// Export private key
if (!openssl_pkey_export($privkey, $pkeyout)) {
    die("Failed to export private key: " . openssl_error_string() . "\n");
}

// Export certificate
if (!openssl_x509_export($cert, $certout)) {
    die("Failed to export certificate: " . openssl_error_string() . "\n");
}

// Save files
$certDir = __DIR__ . '/storage/app/qz-tray';
if (!file_exists($certDir)) {
    mkdir($certDir, 0755, true);
}

file_put_contents("$certDir/private-key.pem", $pkeyout);
file_put_contents("$certDir/digital-certificate.txt", $certout);

echo "Certificate and private key generated successfully!\n";

// Test signing
$data = 'test';
$signature = '';
if (openssl_sign($data, $signature, $privkey, OPENSSL_ALGO_SHA256)) {
    echo "Test signing successful!\n";
} else {
    echo "Test signing failed: " . openssl_error_string() . "\n";
}
?>