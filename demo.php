#!/usr/bin/env php
<?php

/**
 * Script de demostración de la funcionalidad de proyección de cuotas
 * Este script muestra cómo funciona la aplicación sin necesidad de base de datos
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

// Datos de ejemplo del contrato
$contractValue = 10000;
$contractDate = new DateTime('2025-01-22');
$numberOfMonths = 12;

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "     APLICACIÓN DE TRAMITACIÓN DE CONTRATOS - DEMOSTRACIÓN\n";
echo "════════════════════════════════════════════════════════════════\n\n";

echo "📋 DATOS DEL CONTRATO:\n";
echo "─────────────────────────────────────────────────────────────────\n";
echo "  Número de Contrato:    CNT-2025-001\n";
echo "  Fecha del Contrato:    " . $contractDate->format('Y-m-d') . "\n";
echo "  Valor Total:           $" . number_format($contractValue, 2) . "\n";
echo "  Número de Meses:       $numberOfMonths\n\n";

// Proyección con PayPal
echo "💳 PROYECCIÓN CON PAYPAL (1% interés, 2% tarifa)\n";
echo "─────────────────────────────────────────────────────────────────\n";

$payPalInstallments = $projectionService->projectInstallments(
    $contractValue,
    $numberOfMonths,
    $contractDate,
    $payPalService
);

printInstallments($payPalInstallments, $projectionService);

// Proyección con PayOnline
echo "\n💳 PROYECCIÓN CON PAYONLINE (2% interés, 1% tarifa)\n";
echo "─────────────────────────────────────────────────────────────────\n";

$payOnlineInstallments = $projectionService->projectInstallments(
    $contractValue,
    $numberOfMonths,
    $contractDate,
    $payOnlineService
);

printInstallments($payOnlineInstallments, $projectionService);

// Comparación
echo "\n📊 COMPARACIÓN DE SERVICIOS DE PAGO\n";
echo "─────────────────────────────────────────────────────────────────\n";

$payPalTotal = $projectionService->calculateTotalAmount($payPalInstallments);
$payOnlineTotal = $projectionService->calculateTotalAmount($payOnlineInstallments);

$payPalInterest = array_reduce($payPalInstallments, fn($c, $i) => $c + $i['interest'], 0);
$payPalFee = array_reduce($payPalInstallments, fn($c, $i) => $c + $i['fee'], 0);

$payOnlineInterest = array_reduce($payOnlineInstallments, fn($c, $i) => $c + $i['interest'], 0);
$payOnlineFee = array_reduce($payOnlineInstallments, fn($c, $i) => $c + $i['fee'], 0);

echo sprintf("%-25s %15s %15s\n", "Concepto", "PayPal", "PayOnline");
echo str_repeat("─", 55) . "\n";
echo sprintf("%-25s $%14.2f $%14.2f\n", "Valor Base", $contractValue, $contractValue);
echo sprintf("%-25s $%14.2f $%14.2f\n", "Total Interés", $payPalInterest, $payOnlineInterest);
echo sprintf("%-25s $%14.2f $%14.2f\n", "Total Tarifa", $payPalFee, $payOnlineFee);
echo str_repeat("─", 55) . "\n";
echo sprintf("%-25s $%14.2f $%14.2f\n", "TOTAL A PAGAR", $payPalTotal, $payOnlineTotal);
echo str_repeat("─", 55) . "\n";

$difference = abs($payPalTotal - $payOnlineTotal);
$cheaper = $payPalTotal < $payOnlineTotal ? "PayPal" : "PayOnline";
echo "\n✨ $" . number_format($difference, 2) . " de diferencia a favor de $cheaper\n";

echo "\n════════════════════════════════════════════════════════════════\n";
echo "✅ Demostración completada exitosamente\n";
echo "════════════════════════════════════════════════════════════════\n\n";

/**
 * Función auxiliar para imprimir cuotas
 */
function printInstallments(array $installments, InstallmentProjectionService $projectionService): void
{
    $total = $projectionService->calculateTotalAmount($installments);
    $interest = array_reduce($installments, fn($c, $i) => $c + $i['interest'], 0);
    $fee = array_reduce($installments, fn($c, $i) => $c + $i['fee'], 0);

    echo "Cuota  │ Fecha Pago │ Base      │ Interés   │ Tarifa    │ Total\n";
    echo "───────┼────────────┼───────────┼───────────┼───────────┼─────────\n";

    foreach ($installments as $installment) {
        echo sprintf(
            "%5d  │ %s │ $%8.2f │ $%8.2f │ $%8.2f │ $%8.2f\n",
            $installment['number'],
            $installment['dueDate'],
            $installment['baseValue'],
            $installment['interest'],
            $installment['fee'],
            $installment['totalValue']
        );
    }

    echo "───────┴────────────┴───────────┴───────────┴───────────┴─────────\n";
    echo sprintf(
        "       │ TOTAL      │ $%8.2f │ $%8.2f │ $%8.2f │ $%8.2f\n",
        10000,
        $interest,
        $fee,
        $total
    );
}
