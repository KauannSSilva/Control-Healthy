-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22-Jun-2025 às 04:36
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `control_healthy`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `informacoes_paciente`
--

CREATE TABLE `informacoes_paciente` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `medico_paciente`
--

CREATE TABLE `medico_paciente` (
  `medico_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `registros_pressao`
--

CREATE TABLE `registros_pressao` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `sistolica` int(11) NOT NULL,
  `diastolica` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('paciente','medico','admin') NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `informacoes_paciente`
--
ALTER TABLE `informacoes_paciente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `medico_paciente`
--
ALTER TABLE `medico_paciente`
  ADD PRIMARY KEY (`medico_id`,`paciente_id`),
  ADD KEY `paciente_id` (`paciente_id`);

--
-- Índices para tabela `registros_pressao`
--
ALTER TABLE `registros_pressao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id` (`paciente_id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `informacoes_paciente`
--
ALTER TABLE `informacoes_paciente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `registros_pressao`
--
ALTER TABLE `registros_pressao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `informacoes_paciente`
--
ALTER TABLE `informacoes_paciente`
  ADD CONSTRAINT `informacoes_paciente_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `medico_paciente`
--
ALTER TABLE `medico_paciente`
  ADD CONSTRAINT `medico_paciente_ibfk_1` FOREIGN KEY (`medico_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medico_paciente_ibfk_2` FOREIGN KEY (`paciente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `registros_pressao`
--
ALTER TABLE `registros_pressao`
  ADD CONSTRAINT `registros_pressao_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
