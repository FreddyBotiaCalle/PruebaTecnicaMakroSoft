<?php

namespace App\Controller;

use App\DTO\CreateContractRequest;
use App\DTO\InstallmentProjectionRequest;
use App\DTO\InstallmentProjectionResponse;
use App\Entity\Contract;
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
#[Route('/api/contracts')]
class ContractController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private InstallmentProjectionService $projectionService,
        private ContractPaymentServiceResolver $paymentServiceResolver,
    ) {}

    /**
     * Crear un nuevo contrato
     *
     * @Route("", methods={"POST"})
     */
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

            // Crear entidad Contract
            $contract = new Contract();
            $contract->setContractNumber($contractRequest->getContractNumber());
            $contract->setContractDate(new DateTime($contractRequest->getContractDate()));
            $contract->setContractValue($contractRequest->getContractValue());
            $contract->setPaymentMethod($contractRequest->getPaymentMethod());
            $contract->setClientName($contractRequest->getClientName());
            $contract->setDescription($contractRequest->getDescription());
            $contract->setStatus('PENDING');

            // Persistir en BD
            $this->entityManager->persist($contract);
            $this->entityManager->flush();

            return $this->json([
                'status' => 'success',
                'message' => 'Contrato creado exitosamente',
                'data' => [
                    'id' => $contract->getId(),
                    'contractNumber' => $contract->getContractNumber(),
                    'contractDate' => $contract->getContractDate()->format('Y-m-d'),
                    'contractValue' => $contract->getContractValue(),
                    'paymentMethod' => $contract->getPaymentMethod(),
                    'clientName' => $contract->getClientName(),
                ]
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
     *
     * @Route("", methods={"GET"})
     */
    public function getAllContracts(): JsonResponse
    {
        try {
            $contracts = $this->entityManager->getRepository(Contract::class)->findAll();

            $contractsData = array_map(function (Contract $contract) {
                return [
                    'id' => $contract->getId(),
                    'contractNumber' => $contract->getContractNumber(),
                    'contractDate' => $contract->getContractDate()->format('Y-m-d'),
                    'contractValue' => $contract->getContractValue(),
                    'paymentMethod' => $contract->getPaymentMethod(),
                    'clientName' => $contract->getClientName(),
                    'status' => $contract->getStatus(),
                    'createdAt' => $contract->getCreatedAt()->format('Y-m-d H:i:s'),
                ];
            }, $contracts);

            return $this->json([
                'status' => 'success',
                'data' => $contractsData,
                'total' => count($contractsData)
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
     *
     * @Route("/{id}", methods={"GET"})
     */
    public function getContractById(int $id): JsonResponse
    {
        try {
            $contract = $this->entityManager->getRepository(Contract::class)->find($id);

            if (!$contract) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Contrato no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json([
                'status' => 'success',
                'data' => [
                    'id' => $contract->getId(),
                    'contractNumber' => $contract->getContractNumber(),
                    'contractDate' => $contract->getContractDate()->format('Y-m-d'),
                    'contractValue' => $contract->getContractValue(),
                    'paymentMethod' => $contract->getPaymentMethod(),
                    'clientName' => $contract->getClientName(),
                    'description' => $contract->getDescription(),
                    'status' => $contract->getStatus(),
                    'createdAt' => $contract->getCreatedAt()->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Error al obtener el contrato: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Proyectar cuotas de un contrato
     *
     * @Route("/projection/calculate", methods={"POST"})
     */
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

            // Obtener contrato
            $contract = $this->entityManager->getRepository(Contract::class)
                ->find($projectionRequest->getContractId());

            if (!$contract) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Contrato no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            // Obtener servicio de pago apropiado
            $paymentService = $this->paymentServiceResolver->resolve(
                $projectionRequest->getPaymentMethod()
            );

            // Proyectar cuotas
            $installments = $this->projectionService->projectInstallments(
                (float)$contract->getContractValue(),
                $projectionRequest->getNumberOfMonths(),
                $contract->getContractDate(),
                $paymentService
            );

            // Calcular totales
            $totalAmount = $this->projectionService->calculateTotalAmount($installments);
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

            // Construir respuesta
            $response = new InstallmentProjectionResponse();
            $response->setContractId($contract->getId());
            $response->setContractNumber($contract->getContractNumber());
            $response->setContractDate($contract->getContractDate()->format('Y-m-d'));
            $response->setContractValue((float)$contract->getContractValue());
            $response->setPaymentMethod($projectionRequest->getPaymentMethod());
            $response->setClientName($contract->getClientName() ?? '');
            $response->setNumberOfMonths($projectionRequest->getNumberOfMonths());
            $response->setInstallments($installments);
            $response->setTotalAmount(round($totalAmount, 2));
            $response->setTotalInterest(round($totalInterest, 2));
            $response->setTotalFee(round($totalFee, 2));

            return $this->json([
                'status' => 'success',
                'data' => [
                    'contractId' => $response->getContractId(),
                    'contractNumber' => $response->getContractNumber(),
                    'contractDate' => $response->getContractDate(),
                    'contractValue' => $response->getContractValue(),
                    'paymentMethod' => $response->getPaymentMethod(),
                    'clientName' => $response->getClientName(),
                    'numberOfMonths' => $response->getNumberOfMonths(),
                    'installments' => $response->getInstallments(),
                    'summary' => [
                        'baseTotal' => round((float)$contract->getContractValue(), 2),
                        'totalInterest' => $response->getTotalInterest(),
                        'totalFee' => $response->getTotalFee(),
                        'totalAmount' => $response->getTotalAmount(),
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
