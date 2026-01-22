<?php

namespace App\Controller;

use App\Service\PaymentService\PayPalService;
use App\Service\PaymentService\PaymentServiceInterface;
use App\Service\PaymentService\PayOnlineService;
use App\Service\PaymentService\NequiService;

/**
 * Class ContractPaymentServiceResolver
 * 
 * Resuelve el servicio de pago apropiado basado en el nombre.
 * Implementa el patrón Factory para la creación de servicios de pago.
 */
class ContractPaymentServiceResolver
{
    private PayPalService $payPalService;
    private PayOnlineService $payOnlineService;
    private NequiService $nequiService;

    public function __construct()
    {
        $this->payPalService = new PayPalService();
        $this->payOnlineService = new PayOnlineService();
        $this->nequiService = new NequiService();
    }

    /**
     * Resuelve y retorna el servicio de pago correcto
     *
     * @param string $paymentMethodName El nombre del método de pago
     * @return PaymentServiceInterface El servicio de pago correspondiente
     * @throws \InvalidArgumentException Si el método de pago no existe
     */
    public function resolve(string $paymentMethodName): PaymentServiceInterface
    {
        return match ($paymentMethodName) {
            'PayPal' => $this->payPalService,
            'PayOnline' => $this->payOnlineService,
            'Nequi' => $this->nequiService,
            default => throw new \InvalidArgumentException(
                "Método de pago '{$paymentMethodName}' no soportado. Use 'PayPal', 'PayOnline' o 'Nequi'."
            )
        };
    }
}
