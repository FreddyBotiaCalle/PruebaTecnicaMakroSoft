<?php

namespace App\Service\PaymentService;

use App\Entity\Installment;

/**
 * Interface PaymentServiceInterface
 * 
 * Define el contrato para los servicios de pago en línea.
 * Implementa el patrón Strategy para permitir diferentes cálculos de intereses y tarifas.
 */
interface PaymentServiceInterface
{
    /**
     * Calcula el valor de la cuota considerando interés y tarifa
     *
     * @param float $installmentValue Valor base de la cuota
     * @param float $pendingBalance Saldo pendiente del contrato
     * @param int $installmentNumber Número de la cuota (1-based)
     * @return float Valor final de la cuota con intereses y tarifas
     */
    public function calculateInstallment(
        float $installmentValue,
        float $pendingBalance,
        int $installmentNumber
    ): float;

    /**
     * Obtiene el nombre del servicio de pago
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Obtiene la tasa de interés del servicio
     *
     * @return float Porcentaje de interés (ej: 1.0 para 1%)
     */
    public function getInterestRate(): float;

    /**
     * Obtiene la tarifa de pago del servicio
     *
     * @return float Porcentaje de tarifa (ej: 2.0 para 2%)
     */
    public function getPaymentFee(): float;
}
