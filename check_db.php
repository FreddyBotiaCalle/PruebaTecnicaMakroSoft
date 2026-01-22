<?php
$host = '127.0.0.1';
$user = 'root';
$pass = 'root';
$db = 'contratos_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    $stmt = $pdo->query('SELECT * FROM contracts');
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($contracts) > 0) {
        echo "Registros encontrados: " . count($contracts) . "\n\n";
        foreach($contracts as $row) {
            echo "ID: " . $row['id'] . "\n";
            echo "Número: " . $row['contract_number'] . "\n";
            echo "Valor: " . $row['contract_value'] . "\n";
            echo "Método: " . $row['payment_method'] . "\n";
            echo "Cliente: " . $row['client_name'] . "\n";
            echo "---\n\n";
        }
    } else {
        echo "No hay registros en la tabla contracts\n";
    }
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>
