<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="contracts")
 */
class Contract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"contract:read", "contract:write"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Groups({"contract:read", "contract:write"})
     */
    private string $contractNumber;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"contract:read", "contract:write"})
     */
    private DateTime $contractDate;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     * @Groups({"contract:read", "contract:write"})
     */
    private string $contractValue;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"contract:read", "contract:write"})
     */
    private string $paymentMethod;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"contract:read", "contract:write"})
     */
    private ?string $clientName = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"contract:read", "contract:write"})
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $updatedAt = null;

    /**
     * @ORM\Column(type="string", length=20, options={"default": "PENDING"})
     * @Groups({"contract:read", "contract:write"})
     */
    private string $status = 'PENDING';

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContractDate(): DateTime
    {
        return $this->contractDate;
    }

    public function setContractDate(DateTime $contractDate): self
    {
        $this->contractDate = $contractDate;
        return $this;
    }

    public function getContractValue(): string
    {
        return $this->contractValue;
    }

    public function setContractValue(string|float $contractValue): self
    {
        $this->contractValue = (string)$contractValue;
        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        if (!in_array($paymentMethod, ['PayPal', 'PayOnline'])) {
            throw new \InvalidArgumentException('Método de pago no válido');
        }
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

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, ['PENDING', 'ACTIVE', 'COMPLETED', 'CANCELLED'])) {
            throw new \InvalidArgumentException('Estado no válido');
        }
        $this->status = $status;
        return $this;
    }
}
