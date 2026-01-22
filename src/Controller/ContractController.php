<?php

namespace App\Controller;

use App\DTO\CreateContractRequest;
use App\DTO\InstallmentProjectionRequest;
use App\DTO\InstallmentProjectionResponse;
use App\Entity\Contract;
use App\Service\ContractStorage;
use App\Service\InstallmentProjectionService;
use App\Service\PaymentService\PaymentServiceInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ContractController
 * 
 * Controlador REST para la gestión de contratos y proyección de cuotas.
 * Implementa endpoints para crear contratos y proyectar cuotas.
 */
class ContractController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private InstallmentProjectionService $projectionService,
        private ContractStorage $contractStorage,
    ) {}

    /**
     * Crear un nuevo contrato
     */
    #[Route('/api/contracts', methods: ['POST'])]
    public function createContract(Request $request): JsonResponse
    {
        try {
            // Deserializar JSON a DTO
            $contractRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreateContractRequest::class,
                'json'
            );

            // Validar DTO
            $errors = $this->validator->validate($contractRequest);
            if (count($errors) > 0) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Datos inválidos',
                    'errors' => $this->getErrorMessages($errors)
                ], Response::HTTP_BAD_REQUEST);
            }

            // Crear contrato usando el servicio de almacenamiento
            $contract = $this->contractStorage->createNewContract([
                'contractNumber' => $contractRequest->getContractNumber(),
                'contractDate' => $contractRequest->getContractDate(),
                'contractValue' => $contractRequest->getContractValue(),
                'paymentMethod' => $contractRequest->getPaymentMethod(),
                'clientName' => $contractRequest->getClientName(),
                'description' => $contractRequest->getDescription(),
            ]);

            return $this->json([
                'status' => 'success',
                'message' => 'Contrato creado exitosamente',
                'data' => $contract
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Error al crear el contrato: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener todos los contratos
     */
    #[Route('/api/contracts', methods: ['GET'])]
    public function getAllContracts(): JsonResponse
    {
        try {
            $contracts = $this->contractStorage->getAllContractsFromDb();

            return $this->json([
                'status' => 'success',
                'data' => $contracts
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Error al obtener contratos: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener un contrato por ID
     */
    #[Route('/api/contracts/{id}', methods: ['GET'])]
    public function getContractById(int $id): JsonResponse
    {
        try {
            $contract = $this->contractStorage->getContractById($id);

            if (!$contract) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Contrato no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json([
                'status' => 'success',
                'data' => $contract
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Error al obtener el contrato: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Eliminar un contrato por ID
     */
    #[Route('/api/contracts/{id}', methods: ['DELETE'])]
    public function deleteContract(int $id): JsonResponse
    {
        try {
            // Validar que el contrato existe
            $contract = $this->contractStorage->getContractById($id);
            
            if (!$contract) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Contrato no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            // Eliminar el contrato
            $deleted = $this->contractStorage->deleteExistingContract($id);

            if (!$deleted) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Error al eliminar el contrato'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->json([
                'status' => 'success',
                'message' => 'Contrato eliminado exitosamente',
                'data' => [
                    'id' => $id,
                    'contractNumber' => $contract['contractNumber']
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Error al eliminar el contrato: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Proyectar cuotas de un contrato
     */
    #[Route('/api/contracts/projection/calculate', methods: ['POST'])]
    public function projectInstallments(Request $request): JsonResponse
    {
        try {
            // Deserializar JSON a DTO
            $projectionRequest = $this->serializer->deserialize(
                $request->getContent(),
                InstallmentProjectionRequest::class,
                'json'
            );

            // Validar DTO
            $errors = $this->validator->validate($projectionRequest);
            if (count($errors) > 0) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Datos inválidos',
                    'errors' => $this->getErrorMessages($errors)
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validar que el contrato existe
            $contractId = $projectionRequest->getContractId();
            $contract = $this->contractStorage->getContractById($contractId);
            
            if (!$contract) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Contrato no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            $contractValue = (float)$contract['contractValue'];
            $numberOfMonths = $projectionRequest->getNumberOfMonths();
            $paymentMethod = $projectionRequest->getPaymentMethod();

            // Calcular cuotas con lógica de demostración
            $installments = [];
            $interestRate = 0.05; // 5% annual interest
            $monthlyRate = $interestRate / 12;
            $monthlyBase = $contractValue / $numberOfMonths;

            // Parse contract date
            $startDate = new \DateTime($contract['contractDate']);
            $startDate->add(new \DateInterval('P1M')); // Start next month

            for ($i = 1; $i <= $numberOfMonths; $i++) {
                $dueDate = clone $startDate;
                $dueDate->add(new \DateInterval('P' . ($i - 1) . 'M'));

                $interest = $monthlyBase * $monthlyRate;
                $fee = 0;

                // Add PayOnline fee if applicable
                if ($paymentMethod === 'PayOnline') {
                    $fee = ($monthlyBase + $interest) * 0.01; // 1% fee
                }

                if ($nequiService === 'Nequi') {
                    $fee = ($monthlyBase + $interest) * 0.005; // 0.5% fee for Nequi
                }

                $installments[] = [
                    'number' => $i,
                    'dueDate' => $dueDate->format('Y-m-d'),
                    'amount' => round($monthlyBase, 2),
                    'interest' => round($interest, 2),
                    'fee' => round($fee, 2),
                    'total' => round($monthlyBase + $interest + $fee, 2)
                ];
            }

            // Calculate totals
            $totalAmount = array_reduce(
                $installments,
                fn($carry, $item) => $carry + $item['amount'],
                0
            );
            $totalInterest = array_reduce(
                $installments,
                fn($carry, $item) => $carry + $item['interest'],
                0
            );
            $totalFee = array_reduce(
                $installments,
                fn($carry, $item) => $carry + $item['fee'],
                0
            );

            return $this->json([
                'status' => 'success',
                'data' => [
                    'contractId' => $contract['id'],
                    'contractNumber' => $contract['contractNumber'],
                    'contractDate' => $contract['contractDate'],
                    'contractValue' => $contractValue,
                    'paymentMethod' => $paymentMethod,
                    'clientName' => $contract['clientName'],
                    'numberOfMonths' => $numberOfMonths,
                    'installments' => $installments,
                    'summary' => [
                        'baseTotal' => round($contractValue, 2),
                        'totalInterest' => round($totalInterest, 2),
                        'totalFee' => round($totalFee, 2),
                        'totalAmount' => round($totalAmount + $totalInterest + $totalFee, 2),
                    ]
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Error al proyectar cuotas: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Obtener errores de validación
     */
    private function getErrorMessages($errors): array
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[] = [
                'field' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }
        return $messages;
    }
}
