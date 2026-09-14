-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14/09/2026 às 03:31
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `motorapp`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamento`
--

CREATE TABLE `agendamento` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_veiculo` int(11) NOT NULL,
  `data_agendamento` date NOT NULL,
  `hora_agendamento` time NOT NULL,
  `descricao` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamento`
--

INSERT INTO `agendamento` (`id`, `id_cliente`, `id_veiculo`, `data_agendamento`, `hora_agendamento`, `descricao`, `status`) VALUES
(1, 4, 1, '2026-06-05', '16:20:00', 'barrulho estranho na roda dianteira', 'Concluído'),
(2, 4, 1, '2026-05-31', '20:25:00', 'não liga', 'Concluído'),
(3, 5, 2, '2026-06-01', '22:02:00', 'não engata a marcha', 'Concluído'),
(4, 6, 3, '2026-06-13', '21:30:00', 'revisão', 'Pendente'),
(5, 7, 4, '2026-06-12', '21:46:00', 'revisão', 'Pendente'),
(6, 6, 5, '2026-06-15', '10:30:00', 'revisão', 'Pendente'),
(7, 8, 6, '2026-06-15', '09:07:00', 'troca de oleo', 'Pendente'),
(8, 5, 2, '2026-06-15', '08:42:00', 'troca de óleo', 'Pendente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id`, `nome`, `email`, `telefone`, `senha`, `tipo`) VALUES
(4, 'Kleiton', 'nen@com', '1188334826', '$2y$10$Y3jL03tbQu8iEvY1pxsU7OBBnoX7xbhCQ3YKRDfIbHgbgpo5yXUr2', 'mecanico'),
(5, 'kleiton', 'kleiton@com', '1188334826', '$2y$10$BIGD8N6dhPd3TSf6sqRnY.VRZXRXn5a6mGceNDLo3IinnY0TXpzoC', 'cliente'),
(6, 'nen', 'nenjordan23@hotmail.com', '190', '$2y$10$Zwim3DhOcbpGOGn14MRWteTS.kCMhHxunnbgnvF3WI96tIUeJLV0K', 'mecanico'),
(7, 'William Oliveira', 'william.olever@gmail.com', '11981163835', '$2y$10$boSHx3YX.KlvULPLSKnK5Odn/EWcdRpBvNTkF5.G7IvSQEScmhsXC', 'cliente'),
(8, 'cris', 'cris@com', '1234', '$2y$10$dig7z6LZgEyt3AUVUzPls.DyOclzHxs6l9sPYHRqzXQVyLLUj8IW.', 'cliente'),
(9, 'nen', 'nen@', '123', '123', 'cliente'),
(10, 'Fernando', 'nando@', '123', '123', 'cliente'),
(11, 'tainara', 'taina@', '123', '123', 'cliente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculo`
--

CREATE TABLE `veiculo` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `marca` varchar(50) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `ano` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculo`
--

INSERT INTO `veiculo` (`id`, `cliente_id`, `modelo`, `marca`, `placa`, `ano`) VALUES
(1, 4, 'corsa', 'gm', 'asd1234', NULL),
(2, 5, 'ka', 'ford', 'asd1234', NULL),
(3, 6, 'corsa', 'gm', 'asd1234', NULL),
(4, 7, 'onix', 'gm', 'ehm1234', NULL),
(5, 6, 'gol', 'vw', 'aaa1234', NULL),
(6, 8, 'corsa', 'gm', 'asd1234', NULL),
(7, 1, 'civic', 'Honda', 'qwe1234', '2012'),
(8, 1, 'escort', 'Ford', 'asd4321', '1996'),
(9, 9, 'civic', 'Honda', 'asd1234', ''),
(10, 9, 'cosrsa', 'gm', 'abc1234', '2004'),
(11, 9, 'city', 'Honda', 'asd2345', ''),
(12, 10, 'Eco esport', 'Ford', 'xxx1234', '2014'),
(13, 9, 'Focus', 'ford', 'qwe4321', '2015'),
(14, 9, 'corsa', 'gm', 'dcx9887', '2001'),
(15, 10, 'Corsa', 'Gm', '1234\n1234', '2004');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamento`
--
ALTER TABLE `agendamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `veiculo`
--
ALTER TABLE `veiculo`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamento`
--
ALTER TABLE `agendamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `veiculo`
--
ALTER TABLE `veiculo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
