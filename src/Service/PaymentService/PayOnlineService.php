<?php

namespace App\Service\PaymentService;

/**
 * Class PayOnlineService
 * 
 * Implementación del servicio de pago PayOnline.
 * Aplica un interés del 2% sobre el saldo pendiente y una tarifa del 1% por pago.
 */
class PayOnlineService implements PaymentServiceInterface
{
    private const INTEREST_RATE = 2.0;
    private const PAYMENT_FEE = 1.0;

    public function calculateInstallment(
        float $installmentValue,
        float $pendingBalance,
        int $installmentNumber
    ): float {
        // Calcular interés sobre el saldo pendiente
        $interest = ($pendingBalance * $this->INTEREST_RATE) / 100;

        // Sumar la cuota base con el interés
        $installmentWithInterest = $installmentValue + $interest;

        // Calcular tarifa de pago sobre el total (cuota + interés)
        $fee = ($installmentWithInterest * $this->PAYMENT_FEE) / 100;

        // Retornar el valor final
        return $installmentWithInterest + $fee;
    }

    public function getName(): string
    {
        return 'PayOnline';
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
