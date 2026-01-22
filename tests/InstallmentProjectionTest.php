<?php

namespace App\Tests;

use App\Service\InstallmentProjectionService;
use App\Service\PaymentService\PayPalService;
use App\Service\PaymentService\PayOnlineService;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Test para demostrar la funcionalidad de proyección de cuotas
 */
class InstallmentProjectionTest extends TestCase
{
    private InstallmentProjectionService $projectionService;

    protected function setUp(): void
    {
        $this->projectionService = new InstallmentProjectionService();
    }

    /**
     * Prueba: Proyectar cuotas con PayPal
     */
    public function testProjectInstallmentsWithPayPal(): void
    {
        $paymentService = new PayPalService();
        $contractDate = new DateTime('2025-01-22');
        $contractValue = 10000;
        $numberOfMonths = 12;

        $installments = $this->projectionService->projectInstallments(
            $contractValue,
            $numberOfMonths,
            $contractDate,
            $paymentService
        );

        $this->assertCount(12, $installments);
        $this->assertEquals('2025-02-22', $installments[0]['dueDate']);
        $this->assertGreaterThan(833.33, $installments[0]['totalValue']);
    }

    /**
     * Prueba: Proyectar cuotas con PayOnline
     */
    public function testProjectInstallmentsWithPayOnline(): void
    {
        $paymentService = new PayOnlineService();
        $contractDate = new DateTime('2025-01-22');
        $contractValue = 5000;
        $numberOfMonths = 6;

        $installments = $this->projectionService->projectInstallments(
            $contractValue,
            $numberOfMonths,
            $contractDate,
            $paymentService
        );

        $this->assertCount(6, $installments);
        $this->assertGreaterThan(833.33, $installments[0]['totalValue']);
    }

    /**
     * Prueba: Comparación de costos entre PayPal y PayOnline
     */
    public function testComparePaymentServices(): void
    {
        $payPalService = new PayPalService();
        $payOnlineService = new PayOnlineService();
        $contractDate = new DateTime('2025-01-22');
        $contractValue = 10000;
        $numberOfMonths = 12;

        $payPalInstallments = $this->projectionService->projectInstallments(
            $contractValue,
            $numberOfMonths,
            $contractDate,
            $payPalService
        );

        $payOnlineInstallments = $this->projectionService->projectInstallments(
            $contractValue,
            $numberOfMonths,
            $contractDate,
            $payOnlineService
        );

        $payPalTotal = $this->projectionService->calculateTotalAmount($payPalInstallments);
        $payOnlineTotal = $this->projectionService->calculateTotalAmount($payOnlineInstallments);

        $this->assertGreaterThan(0, $payPalTotal);
        $this->assertGreaterThan(0, $payOnlineTotal);
        $this->assertNotEquals($payPalTotal, $payOnlineTotal);
    }

    /**
     * Prueba: Validación de entrada - número de meses inválido
     */
    public function testInvalidNumberOfMonths(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $paymentService = new PayPalService();
        $contractDate = new DateTime('2025-01-22');

        $this->projectionService->projectInstallments(
            10000,
            0,
            $contractDate,
            $paymentService
        );
    }

    /**
     * Prueba: Validación de entrada - valor de contrato inválido
     */
    public function testInvalidContractValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $paymentService = new PayPalService();
        $contractDate = new DateTime('2025-01-22');

        $this->projectionService->projectInstallments(
            -5000,
            12,
            $contractDate,
            $paymentService
        );
    }
}
