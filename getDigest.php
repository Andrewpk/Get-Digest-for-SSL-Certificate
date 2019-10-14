<?php
// Change if you want to see openssl warnings
error_reporting(E_ERROR | E_PARSE);

// Script
$cert = file_get_contents($argv[1]);
$digestMethod = $argv[2];

if (empty($digestMethod)) {
    $digestMethod = "sha256";
}

//if plaintext, assume pem, else, assume binary der
if (strpos($cert, "CERTIFICATE")) {
    $derData = pem2der($cert);
} else {
    $derData = $cert;
}

$digest = openssl_digest($derData, $digestMethod);
$errorMessage = openssl_error_string();

if (!empty($errorMessage)) {
    echo "Invalid digest method specified. Please use one of the following: \n";
    print_r(openssl_get_md_methods());
    echo "\n";
    exit();
}

echo "digest method: $digestMethod\n";
echo $digest . "\n";
exit();

// Helper
function pem2der($pem_data) {
    $begin = "CERTIFICATE-----";
    $end   = "-----END";
    $pem_data = substr($pem_data, strpos($pem_data, $begin)+strlen($begin));    
    $pem_data = substr($pem_data, 0, strpos($pem_data, $end));
    $der = base64_decode($pem_data);
    return $der;
}
