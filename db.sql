-- dump.sql atualizado

-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS `terreiro` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `terreiro`;

-- Tabela 'dividas'
DROP TABLE IF EXISTS `dividas`;
CREATE TABLE `dividas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(255) NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `data` DATE NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pendente',
  `data_pagamento` DATE DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserção de dados de exemplo na tabela 'dividas'
INSERT INTO `dividas` (`descricao`, `valor`, `data`, `status`, `data_pagamento`) VALUES
('Conta de Luz - Janeiro', 120.50, '2023-01-15', 'pago', '2023-01-15'),
('Conta de Água - Janeiro', 80.75, '2023-01-20', 'pago', '2023-01-20'),
('Internet - Janeiro', 99.90, '2023-01-25', 'pendente', NULL),
('Cartão de Crédito', 1500.00, '2023-02-05', 'pendente', NULL),
('Aluguel', 750.00, '2023-02-10', 'pendente', NULL);

-- Tabela 'trabalhos'
DROP TABLE IF EXISTS `trabalhos`;
CREATE TABLE `trabalhos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(255) NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `data_solicitacao` DATE NOT NULL,
  `data_realizado` DATE DEFAULT NULL,
  `data_pagamento` DATE DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'pendente',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserção de dados de exemplo na tabela 'trabalhos'
INSERT INTO `trabalhos` (`descricao`, `valor`, `data_solicitacao`, `data_realizado`, `data_pagamento`, `status`) VALUES
('Trabalho A', 500.00, '2023-01-10', '2023-01-15', NULL, 'pendente'),
('Trabalho B', 750.00, '2023-02-05', '2023-02-10', '2023-02-10', 'pago'),
('Trabalho C', 300.00, '2023-03-01', NULL, NULL, 'pendente');

-- Tabela 'usuarios' (opcional, caso deseje implementar login no futuro)
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserção de um usuário administrador de exemplo
INSERT INTO `usuarios` (`nome`, `email`, `senha`) VALUES
('Administrador', 'admin@example.com', MD5('123456'));
