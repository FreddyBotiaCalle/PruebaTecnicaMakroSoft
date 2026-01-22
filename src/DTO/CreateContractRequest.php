<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CreateContractRequest
 * 
 * DTO para la creación de un contrato.
 */
class CreateContractRequest
{
    /**
     * @Assert\NotBlank(message="El número de contrato es requerido")
     * @Assert\Length(min=3, max=50, message="El número de contrato debe tener entre 3 y 50 caracteres")
     */
    private string $contractNumber;

    /**
     * @Assert\NotBlank(message="La fecha del contrato es requerida")
     * @Assert\DateTime(format="Y-m-d", message="La fecha debe estar en formato Y-m-d")
     */
    private string $contractDate;

    /**
     * @Assert\NotBlank(message="El valor del contrato es requerido")
     * @Assert\Type(type="numeric", message="El valor debe ser un número")
     * @Assert\GreaterThan(value=0, message="El valor debe ser mayor a 0")
     */
    private float $contractValue;

    /**
     * @Assert\NotBlank(message="El método de pago es requerido")
     * @Assert\Choice(
     *     choices={"PayPal", "PayOnline"},
     *     message="El método de pago debe ser 'PayPal' o 'PayOnline'"
     * )
     */
    private string $paymentMethod;

    /**
     * @Assert\Length(min=3, max=100, message="El nombre debe tener entre 3 y 100 caracteres")
     */
    private ?string $clientName = null;

    /**
     * @Assert\Length(max=500, message="La descripción no puede tener más de 500 caracteres")
     */
    private ?string $description = null;

    public function getContractNumber(): string
    {
        return $this->contractNumber;
    }

    public function setContractNumber(string $contractNumber): self
    {
        $this->contractNumber = $contractNumber;
        return $this;
    }

    public function getContractDate(): string
    {
        return $this->contractDate;
    }

    public function setContractDate(string $contractDate): self
    {
        $this->contractDate = $contractDate;
        return $this;
    }

    public function getContractValue(): float
    {
        return $this->contractValue;
    }

    public function setContractValue(float $contractValue): self
    {
        $this->contractValue = $contractValue;
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

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(?string $clientName): self
    {
        $this->clientName = $clientName;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
