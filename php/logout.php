<?php
session_start(); 
session_unset(); 
session_destroy(); 


header("Location: ../html_css/index.html"); // Redireciona para a página inicial
exit();
?>