<?php

namespace App\Service;

use App\Entity\Contract;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Servicio para almacenar contratos en MySQL usando Doctrine ORM
 * Acceso a base de datos a través de EntityManager
 */
class ContractStorage
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Obtener un contrato por ID
     */
    public static function getContract(int $id): ?array
    {
        // Este método será reemplazado por inyección de dependencias
        return null;
    }

    /**
     * Obtener todos los contratos
     */
    public static function getAllContracts(): array
    {
        // Este método será reemplazado por inyección de dependencias
        return [];
    }

    /**
     * Obtener un contrato por ID (versión de instancia)
     */
    public function getContractById(int $id): ?array
    {
        $contract = $this->entityManager->getRepository(Contract::class)->find($id);
        
        if (!$contract) {
            return null;
        }

        return $this->contractToArray($contract);
    }

    /**
     * Obtener todos los contratos (versión de instancia)
     */
    public function getAllContractsFromDb(): array
    {
        $contracts = $this->entityManager->getRepository(Contract::class)->findAll();
        
        return array_map(fn($contract) => $this->contractToArray($contract), $contracts);
    }

    /**
     * Crear un nuevo contrato
     */
    public static function createContract(array $data): array
    {
        // Será reemplazado por la versión de instancia
        return [];
    }

    /**
     * Crear un nuevo contrato (versión de instancia)
     */
    public function createNewContract(array $data): array
    {
        $contract = new Contract();
        $contract->setContractNumber($data['contractNumber'] ?? 'CNT-' . date('Y-m-d-His'));
        
        // Parsear fecha
        $dateString = $data['contractDate'] ?? date('Y-m-d');
        $contract->setContractDate(new DateTime($dateString));
        
        $contract->setContractValue((float)($data['contractValue'] ?? 0));
        $contract->setPaymentMethod($data['paymentMethod'] ?? 'PayPal');
        $contract->setClientName($data['clientName'] ?? 'Sin nombre');
        $contract->setDescription($data['description'] ?? '');
        $contract->setStatus($data['status'] ?? 'PENDING');

        // Guardar en MySQL
        $this->entityManager->persist($contract);
        $this->entityManager->flush();

        return $this->contractToArray($contract);
    }

    /**
     * Actualizar un contrato
     */
    public static function updateContract(int $id, array $data): ?array
    {
        // Será reemplazado por la versión de instancia
        return null;
    }

    /**
     * Actualizar un contrato (versión de instancia)
     */
    public function updateExistingContract(int $id, array $data): ?array
    {
        $contract = $this->entityManager->getRepository(Contract::class)->find($id);
        
        if (!$contract) {
            return null;
        }

        if (isset($data['contractNumber'])) {
            $contract->setContractNumber($data['contractNumber']);
        }
        if (isset($data['contractDate'])) {
            $contract->setContractDate(new DateTime($data['contractDate']));
        }
        if (isset($data['contractValue'])) {
            $contract->setContractValue((float)$data['contractValue']);
        }
        if (isset($data['paymentMethod'])) {
            $contract->setPaymentMethod($data['paymentMethod']);
        }
        if (isset($data['clientName'])) {
            $contract->setClientName($data['clientName']);
        }
        if (isset($data['description'])) {
            $contract->setDescription($data['description']);
        }
        if (isset($data['status'])) {
            $contract->setStatus($data['status']);
        }

        $contract->setUpdatedAt(new DateTime());
        $this->entityManager->flush();

        return $this->contractToArray($contract);
    }

    /**
     * Eliminar un contrato
     */
    public static function deleteContract(int $id): bool
    {
        // Será reemplazado por la versión de instancia
        return false;
    }

    /**
     * Eliminar un contrato (versión de instancia)
     */
    public function deleteExistingContract(int $id): bool
    {
        $contract = $this->entityManager->getRepository(Contract::class)->find($id);
        
        if (!$contract) {
            return false;
        }

        $this->entityManager->remove($contract);
        $this->entityManager->flush();
        return true;
    }

    /**
     * Convertir entidad Contract a array
     */
    private function contractToArray(Contract $contract): array
    {
        return [
            'id' => $contract->getId(),
            'contractNumber' => $contract->getContractNumber(),
            'contractDate' => $contract->getContractDate()->format('Y-m-d'),
            'contractValue' => (float)$contract->getContractValue(),
            'paymentMethod' => $contract->getPaymentMethod(),
            'clientName' => $contract->getClientName(),
            'description' => $contract->getDescription(),
            'status' => $contract->getStatus(),
            'createdAt' => $contract->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $contract->getUpdatedAt() ? $contract->getUpdatedAt()->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Obtener el próximo ID disponible (ya no necesario con MySQL auto-increment)
     */
    public static function getNextId(): int
    {
        // Con MySQL auto-increment, esto no es necesario
        return 0;
    }
}


