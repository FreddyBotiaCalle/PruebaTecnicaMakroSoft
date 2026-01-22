<?php

namespace App\Serializer;

use App\DTO\CreateContractRequest;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AutoconfigureTag('serializer.normalizer')]
class CreateContractRequestNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @param CreateContractRequest $object
     * @param string $format
     * @param array $context
     * @return array
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (!$object instanceof CreateContractRequest) {
            return null;
        }

        return [
            'contractNumber' => $object->getContractNumber(),
            'contractDate' => $object->getContractDate(),
            'contractValue' => $object->getContractValue(),
            'paymentMethod' => $object->getPaymentMethod(),
            'clientName' => $object->getClientName(),
            'description' => $object->getDescription(),
        ];
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string $format
     * @param array $context
     * @return CreateContractRequest
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if ($type !== CreateContractRequest::class) {
            return null;
        }

        $request = new CreateContractRequest();
        $request->setContractNumber($data['contractNumber'] ?? null);
        $request->setContractDate($data['contractDate'] ?? null);
        $request->setContractValue($data['contractValue'] ?? null);
        $request->setPaymentMethod($data['paymentMethod'] ?? null);
        $request->setClientName($data['clientName'] ?? null);
        $request->setDescription($data['description'] ?? null);

        return $request;
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string $format
     * @return bool
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === CreateContractRequest::class && is_array($data);
    }

    /**
     * @param mixed $data
     * @param string $format
     * @return bool
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof CreateContractRequest;
    }

    /**
     * @return array
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            CreateContractRequest::class => true,
        ];
    }
}
