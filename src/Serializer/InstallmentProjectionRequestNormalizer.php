<?php

namespace App\Serializer;

use App\DTO\InstallmentProjectionRequest;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AutoconfigureTag('serializer.normalizer')]
class InstallmentProjectionRequestNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @param InstallmentProjectionRequest $object
     * @param string $format
     * @param array $context
     * @return array
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (!$object instanceof InstallmentProjectionRequest) {
            return null;
        }

        return [
            'contractId' => $object->getContractId(),
            'numberOfMonths' => $object->getNumberOfMonths(),
            'paymentMethod' => $object->getPaymentMethod(),
        ];
    }

    /**
     * @param mixed $data
     * @param string $type
     * @param string $format
     * @param array $context
     * @return InstallmentProjectionRequest
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if ($type !== InstallmentProjectionRequest::class) {
            return null;
        }

        $request = new InstallmentProjectionRequest();
        $request->setContractId($data['contractId'] ?? null);
        $request->setNumberOfMonths($data['numberOfMonths'] ?? null);
        $request->setPaymentMethod($data['paymentMethod'] ?? null);

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
        return $type === InstallmentProjectionRequest::class && is_array($data);
    }

    /**
     * @param mixed $data
     * @param string $format
     * @return bool
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof InstallmentProjectionRequest;
    }

    /**
     * @return array
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            InstallmentProjectionRequest::class => true,
        ];
    }
}
