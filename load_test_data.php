<?php
/**
 * Script para insertar datos de prueba en la base de datos
 */

require_once 'vendor/autoload.php';
require_once 'config/bootstrap.php';

use App\Entity\Contract;
use Doctrine\ORM\EntityManagerInterface;

// Obtener el kernel y el EntityManager
$kernel = new App\Kernel($_ENV['APP_ENV'] ?? 'dev', $_ENV['APP_DEBUG'] ?? false);
$kernel->boot();
$em = $kernel->getContainer()->get('doctrine')->getManager();

echo "================================================================================\n";
echo "📝 INSERCIÓN DE DATOS DE PRUEBA EN LA BASE DE DATOS\n";
echo "================================================================================\n\n";

try {
    // Crear 3 contratos de prueba
    $testContracts = [
        [
            'number' => 'CNT-2025-TEST-001',
            'value' => 5000.00,
            'method' => 'paypal',
            'client' => 'Empresa A',
            'description' => 'Primer contrato de prueba'
        ],
        [
            'number' => 'CNT-2025-TEST-002',
            'value' => 15000.00,
            'method' => 'payonline',
            'client' => 'Empresa B',
            'description' => 'Segundo contrato de prueba'
        ],
        [
            'number' => 'CNT-2025-TEST-003',
            'value' => 25000.00,
            'method' => 'paypal',
            'client' => 'Empresa C',
            'description' => 'Tercer contrato de prueba'
        ]
    ];

    echo "Insertando contratos de prueba...\n";
    echo "─────────────────────────────────────────────────────────────────────────────\n";

    foreach ($testContracts as $i => $data) {
        $contract = new Contract();
        $contract->setContractNumber($data['number']);
        $contract->setContractDate(new DateTime());
        $contract->setContractValue($data['value']);
        $contract->setPaymentMethod($data['method']);
        $contract->setClientName($data['client']);
        $contract->setDescription($data['description']);
        $contract->setStatus('active');

        $em->persist($contract);
        
        echo "  ✓ {$data['number']} - ${data['value']} - {$data['client']}\n";
    }

    $em->flush();
    echo "\n✅ Todos los contratos fueron insertados exitosamente\n\n";

    // Verificar los datos insertados
    echo "📊 Contratos en la base de datos:\n";
    echo "─────────────────────────────────────────────────────────────────────────────\n";

    $contracts = $em->getRepository(Contract::class)->findAll();
    foreach ($contracts as $contract) {
        echo "  📦 {$contract->getContractNumber()}\n";
        echo "     Cliente: {$contract->getClientName()}\n";
        echo "     Valor: \${$contract->getContractValue()}\n";
        echo "     Método: {$contract->getPaymentMethod()}\n";
        echo "     Estado: {$contract->getStatus()}\n";
        echo "\n";
    }

    echo "✅ BASE DE DATOS CONFIGURADA Y FUNCIONANDO CORRECTAMENTE\n";
    echo "================================================================================\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
} finally {
    $kernel->shutdown();
}
