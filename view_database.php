#!/usr/bin/env php
<?php

/**
 * Script para visualizar los contratos almacenados en la base de datos
 * y mostrar sus proyecciones de cuotas
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Service\InstallmentProjectionService;
use App\Service\PaymentService\PayPalService;
use App\Service\PaymentService\PayOnlineService;
use DateTime;

// Crear instancias de servicios
$payPalService = new PayPalService();
$payOnlineService = new PayOnlineService();
$projectionService = new InstallmentProjectionService();

// Cargar contratos del archivo JSON
$contractsFile = __DIR__ . '/var/contracts.json';

if (!file_exists($contractsFile)) {
    echo "❌ Error: No se encontró el archivo de contratos en $contractsFile\n";
    exit(1);
}

$jsonContent = file_get_contents($contractsFile);
$data = json_decode($jsonContent, true);

if (!$data || !isset($data['contracts'])) {
    echo "❌ Error: El archivo de contratos no contiene datos válidos\n";
    exit(1);
}

$contracts = $data['contracts'];

if (empty($contracts)) {
    echo "⚠️  No hay contratos almacenados en la base de datos\n";
    exit(0);
}

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "    CONTRATOS ALMACENADOS EN LA BASE DE DATOS\n";
echo "════════════════════════════════════════════════════════════════\n\n";

foreach ($contracts as $contract) {
    $contractNumber = $contract['contractNumber'] ?? 'SIN NÚMERO';
    $contractValue = $contract['contractValue'] ?? 0;
    $contractDate = $contract['contractDate'] ?? '2025-01-22';
    $paymentMethod = $contract['paymentMethod'] ?? 'PayPal';
    $clientName = $contract['clientName'] ?? 'SIN CLIENTE';
    $description = $contract['description'] ?? '';
    $status = $contract['status'] ?? 'UNKNOWN';

    // Validar datos
    if ($contractValue <= 0) {
        echo "⚠️  Contrato $contractNumber ignorado: valor inválido\n\n";
        continue;
    }

    try {
        $date = new DateTime($contractDate);
    } catch (Exception $e) {
        echo "⚠️  Contrato $contractNumber ignorado: fecha inválida\n\n";
        continue;
    }

    // Número de meses por defecto (12)
    $numberOfMonths = 12;

    echo "📋 CONTRATO: $contractNumber\n";
    echo "─────────────────────────────────────────────────────────────────\n";
    echo "  Cliente:               $clientName\n";
    echo "  Descripción:           $description\n";
    echo "  Fecha del Contrato:    " . $date->format('Y-m-d') . "\n";
    echo "  Valor Total:           $" . number_format($contractValue, 2) . "\n";
    echo "  Método de Pago:        $paymentMethod\n";
    echo "  Estado:                $status\n";
    echo "  Número de Meses:       $numberOfMonths\n\n";

    // Determinar servicio de pago
    $paymentService = strtolower($paymentMethod) === 'payonline' ? $payOnlineService : $payPalService;
    $methodName = strtolower($paymentMethod) === 'payonline' ? 'PayOnline' : 'PayPal';

    // Proyección de cuotas
    echo "💳 PROYECCIÓN CON " . strtoupper($methodName) . "\n";
    echo "─────────────────────────────────────────────────────────────────\n";

    $installments = $projectionService->projectInstallments(
        $contractValue,
        $numberOfMonths,
        $date,
        $paymentService
    );

    // Encabezados de la tabla
    printf("%-6s │ %-10s │ %-9s │ %-9s │ %-9s │ %-9s\n", 'Cuota', 'Fecha Pago', 'Base', 'Interés', 'Tarifa', 'Total');
    echo "───────┼────────────┼───────────┼───────────┼───────────┼──────────\n";

    $totalBase = 0;
    $totalInterest = 0;
    $totalFee = 0;
    $totalAmount = 0;

    foreach ($installments as $installment) {
        $number = $installment['number'];
        $dueDate = $installment['dueDate'];
        $baseAmount = $installment['baseValue'];
        $interest = $installment['interest'];
        $fee = $installment['fee'];
        $total = $installment['totalValue'];

        $totalBase += $baseAmount;
        $totalInterest += $interest;
        $totalFee += $fee;
        $totalAmount += $total;

        printf("%-6d │ %s │ $%8.2f │ $%8.2f │ $%8.2f │ $%8.2f\n",
            $number,
            $dueDate,
            $baseAmount,
            $interest,
            $fee,
            $total
        );
    }

    echo "───────┴────────────┴───────────┴───────────┴───────────┴──────────\n";
    printf("%-6s │ %-10s │ $%8.2f │ $%8.2f │ $%8.2f │ $%8.2f\n",
        '',
        'TOTAL',
        $totalBase,
        $totalInterest,
        $totalFee,
        $totalAmount
    );
    echo "\n";
}

echo "════════════════════════════════════════════════════════════════\n";
echo "✅ Visualización completada\n";
echo "════════════════════════════════════════════════════════════════\n\n";
