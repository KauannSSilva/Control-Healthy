<?php
// Inicia a sessão para acessar as variáveis de sessão
session_start();

// Verifica se o usuário está logado e é um paciente
// Se não, redireciona para a página de login
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'paciente') {
    header("Location: loginpaciente.html");
    exit(); // Garante que o script pare de ser executado após o redirecionamento
}

// Obtém o nome do usuário da sessão para exibição
$nomeUsuario = htmlspecialchars($_SESSION['usuario_nome']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="stylesheet" href="style4.css" />
    <script src="animações.js" defer></script>
    <link rel="shortcut icon" href="img/favicon-32x32.png" type="image/x-icon">
    <title>Formulário de Paciente</title>
  </head>
  <body>
    <div class="container">
      <nav class="header" aria-label="Ações principais">
        <button class="btn orange" type="button">Gerar PDF</button>
        <a href="../php/logout.php" class="btn black" style="text-align: center; text-decoration: none; padding: 10px 20px;">Deslogar</a>
      </nav>

      <main class="form-container">
        <form autocomplete="on">
          <h2>Olá, <?php echo $nomeUsuario; ?> (Paciente)</h2>
          <h3>Informações</h3>

          <div class="input-group">
            <input
              type="text"
              class="input nome"
              name="nome"
              placeholder="Nome"
              autocomplete="name"
              spellcheck="false"
              required
            />
          </div>

          <div class="input-group">
            <input
              type="text"
              class="input endereco"
              name="endereco"
              placeholder="Endereço"
              autocomplete="street-address"
              spellcheck="false"
              required
            />
          </div>

          <div class="input-group">
            <input
               type="tel" 
               class="input telefone"
              id="telefone" 
              name="telefone" 
              maxlength="11" 
              placeholder="Digite até 11 números" 
              required
              autocomplete="tel"
            />
            <button type="submit" class="btn save">Salvar Informações</button>
          </div>
        </form>

        <h3>Registrar Pressão</h3>
        <form autocomplete="off">
          <input
            type="datetime-local"
            class="input datahora"
            name="datahora"
            required
            value=""
            id="datahora-atual"
            
/>
          <input
           type="number"
            class="input systolic"
            name="sistolica"
            placeholder="Sistólica"
            inputmode="numeric"
            min="2"
            max="299"
            maxlength="3"
            required
            oninput="if(this.value.length>3)this.value=this.value.slice(0,3);"
            onkeydown="if(event.key==='e'||event.key==='E'||event.key==='+'||event.key==='-'){event.preventDefault();}"
          />
          <input
            type="number"
            class="input diastolic"
            name="diastolica"
            placeholder="Diastólica"
            inputmode="numeric"
            min="2"
            max="299"
            maxlength="3"
            required
            oninput="if(this.value.length>3)this.value=this.value.slice(0,3);"
            onkeydown="if(event.key==='e'||event.key==='E'||event.key==='+'||event.key==='-'){event.preventDefault();}"
          />
          <button type="submit" class="btn register">Registrar Pressão</button>
        </form>
      </main>
    </div>
  
  </body>
</html>