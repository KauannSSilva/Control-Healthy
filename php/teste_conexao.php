<?php
require 'conexao.php'; // Usa o novo arquivo de conexão

if ($conn) {
    echo "Conexão com o banco de dados PostgreSQL bem-sucedida!";
} else {
    echo "Falha ao conectar com o banco de dados PostgreSQL.";
}

// Opcional: Fechar a conexão
pg_close($conn);
?>
