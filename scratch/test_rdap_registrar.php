<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$response = Http::timeout(5)->withoutVerifying()->get("https://rdap.org/domain/ncompasstrac.com");
if ($response->successful()) {
    $data = $response->json();
    echo "RDAP DATA KEYS: " . implode(', ', array_keys($data)) . "\n";
    
    // Look for registrar in entities
    $registrarName = null;
    foreach ($data['entities'] ?? [] as $entity) {
        if (in_array('registrar', $entity['roles'] ?? [])) {
            $registrarName = $entity['vcardArray'][1][1][3] ?? $entity['fn'] ?? null;
            if (!$registrarName && isset($entity['vcardArray'][1])) {
                foreach ($entity['vcardArray'][1] as $vc) {
                    if ($vc[0] === 'fn') {
                        $registrarName = $vc[3];
                        break;
                    }
                }
            }
        }
    }
    echo "REGISTRAR: " . ($registrarName ?? 'Not found') . "\n";
}
