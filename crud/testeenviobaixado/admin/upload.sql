-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
-- 
-- Servidor: localhost
-- Tempo de Geração: Out 23, 2011 as 10:24 PM
-- Versão do Servidor: 5.0.45
-- Versão do PHP: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Banco de Dados: `upload`
-- 

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `arquivos`
-- 

CREATE TABLE `arquivos` (
  `arq_id` int(11) NOT NULL auto_increment,
  `arq_nome` varchar(80) NOT NULL,
  `arq_data` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`arq_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Extraindo dados da tabela `arquivos`
-- 

INSERT INTO `arquivos` (`arq_id`, `arq_nome`, `arq_data`, `user_id`) VALUES 
(1, 'Desktop.rar', '23/10/2011', 1),
(2, 'berries.jpg', '23/10/2011', 1),
(3, 'forestfloor-thumbnail.jpg', '23/10/2011', 2),
(4, 'sunset-thumbnail.jpg', '24/10/2011', 3),
(5, 'Desert.jpg', '23/10/2011', 4);

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `nivel`
-- 

CREATE TABLE `nivel` (
  `lev_id` int(11) NOT NULL auto_increment,
  `lev_nome` varchar(10) NOT NULL,
  PRIMARY KEY  (`lev_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Extraindo dados da tabela `nivel`
-- 

INSERT INTO `nivel` (`lev_id`, `lev_nome`) VALUES 
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

-- 
-- Estrutura da tabela `usuarios`
-- 

CREATE TABLE `usuarios` (
  `user_id` int(11) NOT NULL auto_increment,
  `user_nome` varchar(20) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_senha` varchar(20) NOT NULL,
  `lev_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Extraindo dados da tabela `usuarios`
-- 

INSERT INTO `usuarios` (`user_id`, `user_nome`, `user_email`, `user_senha`, `lev_id`) VALUES 
(1, 'gleidson', 'sac@gahost.com.br', '123', 1),
(2, 'tatiana', 'taty@gahost.com.br', '123', 0),
(3, 'igor', 'igor@gahost.com.br', '123', 0),
(4, 'gabriel', 'gabriel@gahost.com.br', '123', 2);
