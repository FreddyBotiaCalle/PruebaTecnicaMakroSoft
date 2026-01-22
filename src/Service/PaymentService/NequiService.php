<?php

namespace App\Service\PaymentService;

/**
 * Class NequiService
 * 
 * Implementación del servicio de pago Nequi.
 * Aplica un interés del 0.5% sobre el saldo pendiente y una tarifa del 1.5% por pago.
 */
class NequiService implements PaymentServiceInterface
{
    private const INTEREST_RATE = 0.5;
    private const PAYMENT_FEE = 1.5;

    public function calculateInstallment(
        float $installmentValue,
        float $pendingBalance,
        int $installmentNumber
    ): float {
        // Calcular interés sobre el saldo pendiente
        $interest = ($pendingBalance * self::INTEREST_RATE) / 100;

        // Sumar la cuota base con el interés
        $installmentWithInterest = $installmentValue + $interest;

        // Calcular tarifa de pago sobre el total (cuota + interés)
        $fee = ($installmentWithInterest * self::PAYMENT_FEE) / 100;

        // Retornar el valor final
        return $installmentWithInterest + $fee;
    }

    public function getName(): string
    {
        return 'Nequi';
    }

    public function getInterestRate(): float
    {
        return self::INTEREST_RATE;
    }

    public function getPaymentFee(): float
    {
        return self::PAYMENT_FEE;
    }
}
