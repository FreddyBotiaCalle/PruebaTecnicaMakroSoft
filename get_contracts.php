#!/usr/bin/env php
<?php

/**
 * Script para listar todos los contratos (sin necesidad de servidor)
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Service\ContractStorage;

$contractsFile = __DIR__ . '/var/contracts.json';

if (!file_exists($contractsFile)) {
    echo "❌ Error: No se encontró el archivo de contratos\n";
    exit(1);
}

// Cargar datos del JSON
$jsonContent = file_get_contents($contractsFile);
$data = json_decode($jsonContent, true);

if (!$data || !isset($data['contracts'])) {
    echo "❌ Error: El archivo de contratos no contiene datos válidos\n";
    exit(1);
}

$contracts = $data['contracts'];

if (empty($contracts)) {
    echo "No hay contratos almacenados\n";
    exit(0);
}

// Mostrar resultado como JSON (como si fuera una respuesta API)
$response = [
    'status' => 'success',
    'data' => array_values($contracts)
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
echo "\n";
