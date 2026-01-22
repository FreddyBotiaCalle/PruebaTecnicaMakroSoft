<?php

namespace App\DTO;

/**
 * Class InstallmentProjectionResponse
 * 
 * DTO para la respuesta de proyección de cuotas.
 * Contiene la información del contrato y las cuotas proyectadas.
 */
class InstallmentProjectionResponse
{
    private int $contractId;
    private string $contractNumber;
    private string $contractDate;
    private float $contractValue;
    private string $paymentMethod;
    private string $clientName;
    private int $numberOfMonths;
    private array $installments = [];
    private float $totalAmount;
    private float $totalInterest;
    private float $totalFee;

    public function getContractId(): int
    {
        return $this->contractId;
    }

    public function setContractId(int $contractId): self
    {
        $this->contractId = $contractId;
        return $this;
    }

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

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): self
    {
        $this->clientName = $clientName;
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

    public function getInstallments(): array
    {
        return $this->installments;
    }

    public function setInstallments(array $installments): self
    {
        $this->installments = $installments;
        return $this;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getTotalInterest(): float
    {
        return $this->totalInterest;
    }

    public function setTotalInterest(float $totalInterest): self
    {
        $this->totalInterest = $totalInterest;
        return $this;
    }

    public function getTotalFee(): float
    {
        return $this->totalFee;
    }

    public function setTotalFee(float $totalFee): self
    {
        $this->totalFee = $totalFee;
        return $this;
    }
}
