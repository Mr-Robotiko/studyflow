-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 30. Jun 2025 um 14:43
-- Server-Version: 10.4.28-MariaDB
-- PHP-Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `studycal`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `entry`
--

CREATE TABLE `entry` (
  `EntryID` int(100) NOT NULL,
  `EventID` int(100) NOT NULL,
  `Daydate` date NOT NULL,
  `Begintime` time NOT NULL,
  `Endtime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `event`
--

CREATE TABLE `event` (
  `EventID` int(100) NOT NULL,
  `UserID` int(100) NOT NULL,
  `Eventname` varchar(100) NOT NULL,
  `Note` varchar(500) DEFAULT NULL,
  `Begindate` date NOT NULL,
  `Enddate` date NOT NULL,
  `Eventseverity` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `todo`
--

CREATE TABLE `todo` (
  `TID` int(100) NOT NULL,
  `UserID` int(100) NOT NULL,
  `TName` varchar(100) NOT NULL,
  `TEnddate` date NOT NULL,
  `Checked` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE `user` (
  `UserID` int(100) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Surname` varchar(100) NOT NULL,
  `Securitypassphrase` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Admin` tinyint(1) NOT NULL DEFAULT 0,
  `DPS` tinyint(1) NOT NULL DEFAULT 0,
  `Mode` tinyint(1) NOT NULL DEFAULT 1,
  `ILT` varchar(100) NOT NULL,
  `AutoLogoutTimer` int(11) NOT NULL DEFAULT 600
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`UserID`, `Username`, `Name`, `Surname`, `Securitypassphrase`, `Password`, `Admin`, `DPS`, `Mode`, `ILT`, `AutoLogoutTimer`) VALUES
(1, 'admin', 'Nutzer', 'Admin', '$2y$10$3wJwfVf7BNrpiTWDmctn.e8zXJsWoLho64hFINiCTkDeExs/cgfkO', '$2y$10$KEjmIGaqMItbU8ijwBcvoOn6lVA9tXK6zCuxS3ouh04N8bx0b01TC', 1, 0, 0, '2', 600);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `entry`
--
ALTER TABLE `entry`
  ADD PRIMARY KEY (`EntryID`,`EventID`),
  ADD KEY `consists` (`EventID`);

--
-- Indizes für die Tabelle `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`EventID`),
  ADD KEY `creates` (`UserID`);

--
-- Indizes für die Tabelle `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`TID`),
  ADD KEY `sets` (`UserID`);

--
-- Indizes für die Tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`) USING BTREE,
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `entry`
--
ALTER TABLE `entry`
  MODIFY `EntryID` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `event`
--
ALTER TABLE `event`
  MODIFY `EventID` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `todo`
--
ALTER TABLE `todo`
  MODIFY `TID` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `entry`
--
ALTER TABLE `entry`
  ADD CONSTRAINT `consistsof` FOREIGN KEY (`EventID`) REFERENCES `event` (`EventID`);

--
-- Constraints der Tabelle `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `creates` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints der Tabelle `todo`
--
ALTER TABLE `todo`
  ADD CONSTRAINT `sets` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
