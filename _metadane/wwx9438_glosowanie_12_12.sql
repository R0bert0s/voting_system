-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2023 at 03:43 PM
-- Wersja serwera: 10.4.28-MariaDB
-- Wersja PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wwx9438_glosowanie`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `administracja`
--

CREATE TABLE `administracja` (
  `id` int(11) NOT NULL,
  `id_uzytkownika_admin` int(11) NOT NULL,
  `data_waznosci` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `glosowania`
--

CREATE TABLE `glosowania` (
  `id` int(11) NOT NULL,
  `tytul` varchar(65) NOT NULL,
  `opis` text NOT NULL,
  `rozpoczecie` timestamp NOT NULL DEFAULT current_timestamp(),
  `zakonczenie` timestamp NOT NULL DEFAULT current_timestamp(),
  `kworum` tinyint(1) NOT NULL,
  `autor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `glosy`
--

CREATE TABLE `glosy` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `id_opcji` int(11) NOT NULL,
  `data_dodania` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `opcje`
--

CREATE TABLE `opcje` (
  `id` int(11) NOT NULL,
  `id_glosowania` int(11) NOT NULL,
  `nazwa` varchar(65) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL,
  `imie` text NOT NULL,
  `nazwisko` text NOT NULL,
  `email` text NOT NULL,
  `haslo` varchar(65) NOT NULL,
  `data_utworzenia` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `administracja`
--
ALTER TABLE `administracja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika_admin`);

--
-- Indeksy dla tabeli `glosowania`
--
ALTER TABLE `glosowania`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autor_id` (`autor_id`);

--
-- Indeksy dla tabeli `glosy`
--
ALTER TABLE `glosy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`,`id_opcji`),
  ADD KEY `id_opcji` (`id_opcji`);

--
-- Indeksy dla tabeli `opcje`
--
ALTER TABLE `opcje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_glosowania` (`id_glosowania`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administracja`
--
ALTER TABLE `administracja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `glosowania`
--
ALTER TABLE `glosowania`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `glosy`
--
ALTER TABLE `glosy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opcje`
--
ALTER TABLE `opcje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `administracja`
--
ALTER TABLE `administracja`
  ADD CONSTRAINT `administracja_ibfk_1` FOREIGN KEY (`id_uzytkownika_admin`) REFERENCES `uzytkownicy` (`id`);

--
-- Constraints for table `glosy`
--
ALTER TABLE `glosy`
  ADD CONSTRAINT `glosy_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`),
  ADD CONSTRAINT `glosy_ibfk_2` FOREIGN KEY (`id_opcji`) REFERENCES `opcje` (`id`);

--
-- Constraints for table `opcje`
--
ALTER TABLE `opcje`
  ADD CONSTRAINT `opcje_ibfk_1` FOREIGN KEY (`id_glosowania`) REFERENCES `glosowania` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
