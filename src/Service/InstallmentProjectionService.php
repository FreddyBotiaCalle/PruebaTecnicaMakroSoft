<?php

namespace App\Service;

use App\Service\PaymentService\PaymentServiceInterface;
use DateTime;

/**
 * Class InstallmentProjectionService
 * 
 * Servicio responsable de proyectar las cuotas de un contrato.
 * Implementa el patrón de inyección de dependencias para mayor flexibilidad.
 */
class InstallmentProjectionService
{
    /**
     * Proyecta las cuotas de un contrato
     *
     * @param float $contractValue Valor total del contrato
     * @param int $numberOfMonths Número de meses para pagar
     * @param DateTime $contractDate Fecha del contrato
     * @param PaymentServiceInterface $paymentService Servicio de pago a utilizar
     *
     * @return array Array con las cuotas proyectadas
     *              Cada cuota contiene: ['number', 'dueDate', 'value', 'interest', 'fee', 'totalValue']
     */
    public function projectInstallments(
        float $contractValue,
        int $numberOfMonths,
        DateTime $contractDate,
        PaymentServiceInterface $paymentService
    ): array {
        if ($numberOfMonths <= 0) {
            throw new \InvalidArgumentException('El número de meses debe ser mayor a 0');
        }

        if ($contractValue <= 0) {
            throw new \InvalidArgumentException('El valor del contrato debe ser mayor a 0');
        }

        $installments = [];
        $baseInstallmentValue = $contractValue / $numberOfMonths;
        $pendingBalance = $contractValue;
        $interestRate = $paymentService->getInterestRate() / 100;
        $paymentFee = $paymentService->getPaymentFee() / 100;

        for ($i = 1; $i <= $numberOfMonths; $i++) {
            // Calcular fecha de pago (un mes después por cada cuota)
            $dueDate = clone $contractDate;
            $dueDate->modify("+{$i} month");

            // Calcular interés sobre el saldo pendiente
            $interest = $pendingBalance * $interestRate;

            // Valor de la cuota con interés
            $installmentWithInterest = $baseInstallmentValue + $interest;

            // Calcular tarifa
            $fee = $installmentWithInterest * $paymentFee;

            // Valor final de la cuota
            $totalInstallmentValue = $installmentWithInterest + $fee;

            // Deducir del saldo pendiente
            $pendingBalance -= $baseInstallmentValue;

            $installments[] = [
                'number' => $i,
                'dueDate' => $dueDate->format('Y-m-d'),
                'baseValue' => round($baseInstallmentValue, 2),
                'interest' => round($interest, 2),
                'fee' => round($fee, 2),
                'totalValue' => round($totalInstallmentValue, 2),
            ];
        }

        return $installments;
    }

    /**
     * Calcula el total a pagar incluidos intereses y tarifas
     *
     * @param array $installments Array de cuotas proyectadas
     * @return float Total a pagar
     */
    public function calculateTotalAmount(array $installments): float
    {
        return array_reduce(
            $installments,
            fn($carry, $item) => $carry + $item['totalValue'],
            0
        );
    }
}
