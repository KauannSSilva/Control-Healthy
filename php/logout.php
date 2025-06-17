<?php
session_start(); // Inicia a sessão para poder destruí-la
session_unset(); // Remove todas as variáveis de sessão
session_destroy(); // Destrói a sessão

header("Location: ../html_css/index.html"); // Redireciona para a página inicial
exit();
?>