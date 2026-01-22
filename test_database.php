<?php
/**
 * Script para probar la conexión a la base de datos
 * y verificar que las migraciones se ejecutaron correctamente
 */

require_once 'vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\Common\EventManager;

// Cargar variables de entorno
$dotenv = new Dotenv();
$dotenv->loadEnv('.env');

// Obtener la URL de la base de datos
$databaseUrl = $_ENV['DATABASE_URL'] ?? null;

if (!$databaseUrl) {
    echo "❌ Error: DATABASE_URL no está configurada\n";
    exit(1);
}

echo "================================================================================\n";
echo "🔍 PRUEBA DE CONEXIÓN A LA BASE DE DATOS\n";
echo "================================================================================\n\n";

// Mostrar configuración
echo "📋 Configuración:\n";
echo "─────────────────────────────────────────────────────────────────────────────\n";
echo "Ambiente:     " . ($_ENV['APP_ENV'] ?? 'dev') . "\n";
echo "Database URL: " . preg_replace('/\/\/.*:.*@/', '//***:***@', $databaseUrl) . "\n";
echo "\n";

try {
    // Configurar Doctrine
    $config = ORMSetup::createAttributeMetadataConfiguration(
        [__DIR__ . '/src'],
        $_ENV['APP_ENV'] === 'dev'
    );

    // Crear conexión
    $connection = \Doctrine\DBAL\DriverManager::getConnection(
        ['url' => $databaseUrl],
        $config
    );

    echo "✅ Conexión exitosa a la base de datos\n";
    echo "   Driver: " . get_class($connection->getDriver()) . "\n\n";

    // Obtener información de las tablas
    $schemaManager = $connection->createSchemaManager();
    $tables = $schemaManager->listTableNames();

    if (count($tables) === 0) {
        echo "⚠️  No hay tablas en la base de datos\n";
    } else {
        echo "📊 Tablas en la base de datos:\n";
        echo "─────────────────────────────────────────────────────────────────────────────\n";
        foreach ($tables as $table) {
            $schema = $schemaManager->introspectTable($table);
            $columns = $schema->getColumns();
            echo "  📦 $table (" . count($columns) . " columnas)\n";
            foreach ($columns as $column) {
                echo "      ├─ {$column->getName()}: " . $column->getType() . "\n";
            }
        }
        echo "\n";
    }

    // Verificar tabla de migraciones
    if (in_array('doctrine_migration_versions', $tables)) {
        $result = $connection->executeQuery(
            'SELECT version FROM doctrine_migration_versions ORDER BY version DESC LIMIT 5'
        );
        $migrations = $result->fetchAllAssociative();
        
        echo "🔄 Migraciones ejecutadas:\n";
        echo "─────────────────────────────────────────────────────────────────────────────\n";
        foreach ($migrations as $row) {
            echo "  ✓ " . $row['version'] . "\n";
        }
        echo "\n";
    }

    // Probar inserción de datos
    echo "📝 Prueba de inserción de datos:\n";
    echo "─────────────────────────────────────────────────────────────────────────────\n";

    $em = new EntityManager($connection, $config);
    
    $connection->beginTransaction();
    
    try {
        // Insertar un contrato de prueba
        $insertSql = <<<SQL
INSERT INTO contracts (
    contract_number, contract_date, contract_value, 
    payment_method, client_name, description, status, 
    created_at, updated_at
) VALUES (
    :contractNumber, :contractDate, :contractValue,
    :paymentMethod, :clientName, :description, :status,
    :createdAt, :updatedAt
)
SQL;

        $now = new DateTime();
        $connection->executeStatement($insertSql, [
            'contractNumber' => 'TEST-' . date('Y-m-d-His'),
            'contractDate' => $now->format('Y-m-d'),
            'contractValue' => 5000.00,
            'paymentMethod' => 'paypal',
            'clientName' => 'Cliente de Prueba',
            'description' => 'Contrato de prueba para validar la base de datos',
            'status' => 'active',
            'createdAt' => $now->format('Y-m-d H:i:s'),
            'updatedAt' => $now->format('Y-m-d H:i:s'),
        ]);

        echo "  ✓ Contrato de prueba insertado correctamente\n";

        // Consultar el contrato
        $result = $connection->executeQuery(
            'SELECT COUNT(*) as total FROM contracts'
        );
        $count = $result->fetchAssociative();
        echo "  ✓ Total de contratos en la BD: " . $count['total'] . "\n";

        // Rollback para no guardar los datos de prueba
        $connection->rollBack();
        echo "  ℹ️  Cambios revertidos (rollback)\n\n";
    } catch (Exception $e) {
        $connection->rollBack();
        throw $e;
    }

    echo "✅ TODAS LAS PRUEBAS COMPLETADAS EXITOSAMENTE\n";
    echo "================================================================================\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\n📌 Soluciones posibles:\n";
    echo "   1. Verificar que la base de datos está accesible\n";
    echo "   2. Verificar la URL de la base de datos en .env\n";
    echo "   3. Ejecutar: php bin/console doctrine:migrations:migrate\n";
    echo "\n";
    exit(1);
}
