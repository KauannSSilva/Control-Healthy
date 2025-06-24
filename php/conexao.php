<?php
// Conexão segura usando as "Configurações de Aplicativo" da Azure
$host = getenv('DB_HOST');
$port = "5432";
$dbname = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

// Monta a string de conexão para o PostgreSQL
$conn_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password}";
$conn = pg_connect($conn_string);

if (!$conn) {
    // Em um ambiente real, seria melhor logar o erro em vez de exibi-lo na tela.
    die("Erro crítico: Não foi possível conectar ao servidor");
}
?>
