-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Aug 18, 2026 at 11:21 PM
-- Server version: 8.0.46
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myuni`
--

-- --------------------------------------------------------

--
-- Table structure for table `articoli`
--

CREATE TABLE `articoli` (
  `ID` int NOT NULL,
  `email_utente` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descrizione` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `checked` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categorie`
--

CREATE TABLE `categorie` (
  `ID` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `denominazione` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorie`
--

INSERT INTO `categorie` (`ID`, `denominazione`) VALUES
('casa', 'Casa e utenze'),
('cibo', 'Spesa e cibo'),
('extra', 'Extra'),
('studio', 'Università e studio'),
('svago', 'Svago e personale'),
('trasporti', 'Trasporti e viaggi');

-- --------------------------------------------------------

--
-- Table structure for table `spese`
--

CREATE TABLE `spese` (
  `ID` int NOT NULL,
  `email_utente` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `importo` decimal(6,2) NOT NULL,
  `descrizione` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_categoria` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `data` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spese`
--

INSERT INTO `spese` (`ID`, `email_utente`, `importo`, `descrizione`, `id_categoria`, `data`) VALUES
(53, 'katia.bezak@gmail.com', 5.00, 'esempio ', 'casa', '2026-08-18 00:02:05'),
(83, 'simonescainelli@gmail.com', 56.45, 'Benzina', 'trasporti', '2026-08-18 22:49:48'),
(84, 'simonescainelli@gmail.com', 78.34, 'Spesa per la settimana', 'cibo', '2026-08-18 22:50:02'),
(85, 'simonescainelli@gmail.com', 23.00, 'Frutta e verdura', 'casa', '2026-08-18 22:51:46'),
(86, 'simonescainelli@gmail.com', 7.67, 'Biglietto del cinema', 'svago', '2026-08-18 22:53:09'),
(92, 'simonescainelli@gmail.com', 22.00, '22', 'casa', '2026-08-18 22:57:02');

-- --------------------------------------------------------

--
-- Table structure for table `utenti`
--

CREATE TABLE `utenti` (
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `cognome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `budget_mensile` decimal(6,2) DEFAULT '500.00',
  `data_iscrizione` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utenti`
--

INSERT INTO `utenti` (`email`, `nome`, `cognome`, `password`, `budget_mensile`, `data_iscrizione`) VALUES
('katia.bezak@gmail.com', 'The best nigga', 'Bezak', '$2y$10$NvQR7lFbFg3ZzRMeoOX7cuKuppumhL9TD6Ei2UjgyOTGCM6ZpTpMW', 500.00, '2026-08-17 23:57:30'),
('simonescainelli@gmail.com', 'Simone', 'Scainelli', '$2y$10$miFM.VMUBnFTrn2qDSqsEerf.B7Mgjazit.43KkmPlfBUeV9sy53K', 500.00, '2026-08-15 00:52:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articoli`
--
ALTER TABLE `articoli`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `articoli_ibfk_1` (`email_utente`);

--
-- Indexes for table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `spese`
--
ALTER TABLE `spese`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `spese_ibfk_1` (`email_utente`);

--
-- Indexes for table `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articoli`
--
ALTER TABLE `articoli`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `spese`
--
ALTER TABLE `spese`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articoli`
--
ALTER TABLE `articoli`
  ADD CONSTRAINT `articoli_ibfk_1` FOREIGN KEY (`email_utente`) REFERENCES `utenti` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `spese`
--
ALTER TABLE `spese`
  ADD CONSTRAINT `spese_ibfk_1` FOREIGN KEY (`email_utente`) REFERENCES `utenti` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `spese_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorie` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
