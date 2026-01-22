<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class InstallmentProjectionRequest
 * 
 * DTO para la solicitud de proyección de cuotas.
 * Define las validaciones necesarias para los datos de entrada.
 */
class InstallmentProjectionRequest
{
    /**
     * @Assert\NotBlank(message="El ID del contrato es requerido")
     * @Assert\Type(type="integer", message="El ID del contrato debe ser un número entero")
     */
    private int $contractId;

    /**
     * @Assert\NotBlank(message="El número de meses es requerido")
     * @Assert\Type(type="integer", message="El número de meses debe ser un número entero")
     * @Assert\Range(min=1, max=360, message="El número de meses debe estar entre 1 y 360")
     */
    private int $numberOfMonths;

    /**
     * @Assert\NotBlank(message="El método de pago es requerido")
     * @Assert\Choice(
     *     choices={"PayPal", "PayOnline"},
     *     message="El método de pago debe ser 'PayPal' o 'PayOnline'"
     * )
     */
    private string $paymentMethod;

    public function getContractId(): int
    {
        return $this->contractId;
    }

    public function setContractId(int $contractId): self
    {
        $this->contractId = $contractId;
        return $this;
    }

    public function getNumberOfMonths(): int
    {
        return $this->numberOfMonths;
    }

    public function setNumberOfMonths(int $numberOfMonths): self
    {
        $this->numberOfMonths = $numberOfMonths;
        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }
}
