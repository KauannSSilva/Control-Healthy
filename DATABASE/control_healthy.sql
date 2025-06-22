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

--
-- Extraindo dados da tabela `medico_paciente`
--

INSERT INTO `medico_paciente` (`medico_id`, `paciente_id`) VALUES
(20, 21),
(23, 22);

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

--
-- Extraindo dados da tabela `registros_pressao`
--

INSERT INTO `registros_pressao` (`id`, `paciente_id`, `data_hora`, `sistolica`, `diastolica`) VALUES
(18, 21, '2025-06-21 02:54:00', 123, 80),
(19, 21, '2025-06-21 02:56:00', 34, 234),
(20, 22, '2025-06-21 11:51:00', 123, 167),
(21, 22, '2025-06-21 12:14:00', 299, 167);

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
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `data_cadastro`) VALUES
(1, 'Administrador', 'admin@controlhealthy.com', '$2y$10$9G2Y10P9q8X7oB3qB8r2oO/AbCDEFGHijklmnopqrst.u', 'admin', '2025-06-16 02:33:04'),
(19, 'teste', 'teste@gmail.com', '$2y$10$6LDfXmPRbr7sOGB8ss41yeE0tKSBRpsIqVrK45vY.F2T8b0oU8Oom', 'medico', '2025-06-21 05:52:44'),
(20, 'Kauann', 'kauannreimondo@gmail.com', '$2y$10$8tRTFyUGv8bhH57lqy8OIObuI5f.zSoJwCKORQM5riEz.0mRXxtCa', 'medico', '2025-06-21 05:53:30'),
(21, 'Kau', 'kauann23silva@gmail.com', '$2y$10$O7bR8DA/phb7RBQXen8ppu50Wv8mNojApsIIfchLQYmIxg2UKV7bu', 'paciente', '2025-06-21 05:54:02'),
(22, 'sfgdexfg', 'santos87kauann@gmail.com', '$2y$10$ZY4qUSfkNVRH4y5rcRrjD.h/AdfMGA5dPwBZt/WsPLN/psLec6Sy2', 'paciente', '2025-06-21 14:50:17'),
(23, 'kkk', 'kauanncontrol@gmail.com', '$2y$10$c63ap70NtsZLa98puqcD7.84NZYknkl9oDUt7Wsb0w7St8fKHrk5u', 'medico', '2025-06-21 14:52:34');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `registros_pressao`
--
ALTER TABLE `registros_pressao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
