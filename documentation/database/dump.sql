-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.3.16-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Copiando estrutura para tabela app_drink_water.Credentials
CREATE TABLE IF NOT EXISTS `Credentials` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `UsersId` int(11) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(150) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_Credential_Users_idx` (`UsersId`),
  CONSTRAINT `fk_Credential_Users` FOREIGN KEY (`UsersId`) REFERENCES `Users` (`Id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela app_drink_water.DrinkCounter
CREATE TABLE IF NOT EXISTS `DrinkCounter` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `UsersId` int(11) NOT NULL,
  `Counter` int(6) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NULL DEFAULT NULL,
  `DeletedAt` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_DrinkCounter_Users1_idx` (`UsersId`),
  CONSTRAINT `fk_DrinkCounter_Users1` FOREIGN KEY (`UsersId`) REFERENCES `Users` (`Id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela app_drink_water.Tokens
CREATE TABLE IF NOT EXISTS `Tokens` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `UsersId` int(11) NOT NULL,
  `Token` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_Tokens_Users1_idx` (`UsersId`),
  CONSTRAINT `fk_Tokens_Users1` FOREIGN KEY (`UsersId`) REFERENCES `Users` (`Id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela app_drink_water.Users
CREATE TABLE IF NOT EXISTS `Users` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NULL DEFAULT NULL,
  `DeletedAt` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
