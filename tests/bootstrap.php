<?php

require __DIR__.'/../vendor/autoload.php';

$asaasClient = new AsaasPhpSdk\AsaasClient(sandboxConfig());
$payments = $asaasClient->payment()->list([
    'limit' => 100,
    'status' => 'PENDING',
]);

foreach ($payments['data'] as $payment) {
    $asaasClient->payment()->delete($payment['id']);
}
