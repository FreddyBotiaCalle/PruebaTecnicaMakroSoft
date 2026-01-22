#!/usr/bin/env php
<?php

/**
 * Script para eliminar un contrato de la base de datos
 * 
 * USO:
 *   php delete_contract.php [id_contrato]
 * 
 * EJEMPLOS:
 *   php delete_contract.php                    # Muestra lista y pide elegir
 *   php delete_contract.php 1                  # Elimina contrato con ID 1
 */

require_once __DIR__ . '/vendor/autoload.php';

$contractsFile = __DIR__ . '/var/contracts.json';

if (!file_exists($contractsFile)) {
    echo "❌ Error: No se encontró el archivo de contratos\n";
    exit(1);
}

// Leer datos
$jsonContent = file_get_contents($contractsFile);
$data = json_decode($jsonContent, true);

if (!$data || !isset($data['contracts'])) {
    echo "❌ Error: El archivo de contratos no contiene datos válidos\n";
    exit(1);
}

$contracts = $data['contracts'];

if (empty($contracts)) {
    echo "⚠️  No hay contratos para eliminar\n";
    exit(0);
}

// Obtener ID del contrato a eliminar
$contractIdToDelete = isset($argv[1]) ? (int)$argv[1] : null;

// Si no se proporciona ID, mostrar lista y permitir elegir
if ($contractIdToDelete === null) {
    echo "\n════════════════════════════════════════════════════════════════\n";
    echo "              CONTRATOS DISPONIBLES PARA ELIMINAR\n";
    echo "════════════════════════════════════════════════════════════════\n\n";

    foreach ($contracts as $id => $contract) {
        echo "ID: $id\n";
        echo "  Número: " . $contract['contractNumber'] . "\n";
        echo "  Cliente: " . $contract['clientName'] . "\n";
        echo "  Valor: $" . number_format($contract['contractValue'], 2) . "\n";
        echo "  Estado: " . $contract['status'] . "\n";
        echo "  Creado: " . $contract['createdAt'] . "\n";
        echo "\n";
    }

    echo "════════════════════════════════════════════════════════════════\n";
    echo "Ingresa el ID del contrato a eliminar: ";
    
    $input = trim(fgets(STDIN));
    $contractIdToDelete = (int)$input;
}

// Validar que el ID existe
if (!isset($contracts[$contractIdToDelete])) {
    echo "❌ Error: No existe contrato con ID $contractIdToDelete\n";
    exit(1);
}

// Mostrar contrato a eliminar
$contract = $contracts[$contractIdToDelete];
echo "\n════════════════════════════════════════════════════════════════\n";
echo "              CONFIRMACIÓN DE ELIMINACIÓN\n";
echo "════════════════════════════════════════════════════════════════\n\n";
echo "¿Deseas eliminar el siguiente contrato?\n\n";
echo "ID: $contractIdToDelete\n";
echo "Número: " . $contract['contractNumber'] . "\n";
echo "Cliente: " . $contract['clientName'] . "\n";
echo "Valor: $" . number_format($contract['contractValue'], 2) . "\n";
echo "Estado: " . $contract['status'] . "\n";
echo "Descripción: " . $contract['description'] . "\n\n";

// Pedir confirmación
if (isset($argv[1])) {
    // Si se proporcionó el ID por parámetro, asumir confirmación automática
    $confirm = 'yes';
} else {
    echo "Escribe 'yes' para confirmar o 'no' para cancelar: ";
    $confirm = strtolower(trim(fgets(STDIN)));
}

if ($confirm !== 'yes' && $confirm !== 'y') {
    echo "\n❌ Operación cancelada\n\n";
    exit(0);
}

// Eliminar contrato
unset($contracts[$contractIdToDelete]);

// Guardar cambios
$data['contracts'] = $contracts;
$newJsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (file_put_contents($contractsFile, $newJsonContent) === false) {
    echo "\n❌ Error: No se pudo guardar los cambios\n";
    exit(1);
}

echo "\n✅ Contrato eliminado exitosamente\n";
echo "════════════════════════════════════════════════════════════════\n\n";

// Mostrar resumen
echo "📊 Contratos restantes: " . count($contracts) . "\n\n";
