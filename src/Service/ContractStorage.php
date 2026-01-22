<?php

namespace App\Service;

/**
 * Servicio para almacenar contratos en memoria y archivo JSON
 * Actúa como un repositorio temporal sin acceso a base de datos
 */
class ContractStorage
{
    private static array $contracts = [];
    private static int $nextId = 1;
    private static string $storageFile = __DIR__ . '/../../var/contracts.json';
    private static bool $initialized = false;

    /**
     * Inicializar el almacenamiento desde archivo si existe
     */
    public static function initialize(): void
    {
        if (self::$initialized) {
            return;
        }

        if (file_exists(self::$storageFile)) {
            $data = json_decode(file_get_contents(self::$storageFile), true);
            if ($data) {
                self::$contracts = $data['contracts'] ?? [];
                self::$nextId = $data['nextId'] ?? 1;
            }
        }

        // Datos iniciales de demostración si está vacío
        if (empty(self::$contracts)) {
            self::$contracts = [
                1 => [
                    'id' => 1,
                    'contractNumber' => 'CNT-2025-001',
                    'contractDate' => '2025-01-22',
                    'contractValue' => 10000,
                    'paymentMethod' => 'PayPal',
                    'clientName' => 'Cliente ABC',
                    'description' => 'Contrato de servicios profesionales',
                    'status' => 'ACTIVE',
                    'createdAt' => '2025-01-22 09:15:00',
                ],
                2 => [
                    'id' => 2,
                    'contractNumber' => 'CNT-2025-002',
                    'contractDate' => '2025-01-22',
                    'contractValue' => 25000,
                    'paymentMethod' => 'PayOnline',
                    'clientName' => 'Empresa XYZ',
                    'description' => 'Contrato de consultoría empresarial',
                    'status' => 'PENDING',
                    'createdAt' => '2025-01-22 10:30:00',
                ]
            ];
            self::$nextId = 3;
        }

        self::$initialized = true;
    }

    /**
     * Guardar los contratos en el archivo JSON
     */
    private static function save(): void
    {
        $dir = dirname(self::$storageFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(self::$storageFile, json_encode([
            'contracts' => self::$contracts,
            'nextId' => self::$nextId
        ], JSON_PRETTY_PRINT));
    }

    /**
     * Obtener un contrato por ID
     */
    public static function getContract(int $id): ?array
    {
        self::initialize();
        return self::$contracts[$id] ?? null;
    }

    /**
     * Obtener todos los contratos
     */
    public static function getAllContracts(): array
    {
        self::initialize();
        return array_values(self::$contracts);
    }

    /**
     * Crear un nuevo contrato
     */
    public static function createContract(array $data): array
    {
        self::initialize();
        $id = self::$nextId++;

        $contract = [
            'id' => $id,
            'contractNumber' => $data['contractNumber'] ?? 'CNT-2025-' . str_pad($id, 3, '0', STR_PAD_LEFT),
            'contractDate' => $data['contractDate'] ?? date('Y-m-d'),
            'contractValue' => (float)($data['contractValue'] ?? 0),
            'paymentMethod' => $data['paymentMethod'] ?? 'PayPal',
            'clientName' => $data['clientName'] ?? 'Sin nombre',
            'description' => $data['description'] ?? '',
            'status' => $data['status'] ?? 'PENDING',
            'createdAt' => date('Y-m-d H:i:s'),
        ];

        self::$contracts[$id] = $contract;
        self::save();
        return $contract;
    }

    /**
     * Actualizar un contrato
     */
    public static function updateContract(int $id, array $data): ?array
    {
        self::initialize();
        if (!isset(self::$contracts[$id])) {
            return null;
        }

        $contract = self::$contracts[$id];

        if (isset($data['contractNumber'])) {
            $contract['contractNumber'] = $data['contractNumber'];
        }
        if (isset($data['contractDate'])) {
            $contract['contractDate'] = $data['contractDate'];
        }
        if (isset($data['contractValue'])) {
            $contract['contractValue'] = (float)$data['contractValue'];
        }
        if (isset($data['paymentMethod'])) {
            $contract['paymentMethod'] = $data['paymentMethod'];
        }
        if (isset($data['clientName'])) {
            $contract['clientName'] = $data['clientName'];
        }
        if (isset($data['description'])) {
            $contract['description'] = $data['description'];
        }
        if (isset($data['status'])) {
            $contract['status'] = $data['status'];
        }

        self::$contracts[$id] = $contract;
        self::save();
        return $contract;
    }

    /**
     * Eliminar un contrato
     */
    public static function deleteContract(int $id): bool
    {
        self::initialize();
        if (!isset(self::$contracts[$id])) {
            return false;
        }

        unset(self::$contracts[$id]);
        self::save();
        return true;
    }

    /**
     * Obtener el próximo ID disponible
     */
    public static function getNextId(): int
    {
        self::initialize();
        return self::$nextId;
    }
}

