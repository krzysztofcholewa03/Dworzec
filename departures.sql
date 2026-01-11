-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sty 11, 2026 at 10:00 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dworzec`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `departures`
--

CREATE TABLE `departures` (
  `id` int(11) NOT NULL,
  `departure_time` time NOT NULL,
  `dest` varchar(120) NOT NULL,
  `platform` tinyint(3) UNSIGNED NOT NULL,
  `train_number` varchar(20) NOT NULL,
  `status` varchar(120) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departures`
--

INSERT INTO `departures` (`id`, `departure_time`, `dest`, `platform`, `train_number`, `status`, `created_at`) VALUES
(1, '12:45:00', 'Poznań Główny', 3, 'R 7810', 'Planowy', '2026-01-11 02:35:51'),
(2, '13:10:00', 'Warszawa', 1, 'IC 4520', 'Opóźniony ~10 min', '2026-01-11 02:35:51'),
(3, '13:30:00', 'Wrocław', 2, 'R 9821', 'Planowy', '2026-01-11 02:35:51'),
(4, '14:05:00', 'Gdańsk', 4, 'IC 5301', 'Planowy', '2026-01-11 02:35:51'),
(5, '15:15:00', 'Wolsztyn', 3, 'WR 6767', 'Planowy', '2026-01-11 02:35:51'),
(6, '15:23:00', 'Szczecin Główny', 5, 'EC 2243', 'Planowy', '2026-01-11 02:35:51'),
(7, '15:37:00', 'Leszno', 2, 'CD 4432', 'Opóźniony ~5 min', '2026-01-11 02:35:51'),
(8, '15:54:00', 'Lublin', 1, 'B 7400', 'Planowy', '2026-01-11 02:35:51');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `departures`
--
ALTER TABLE `departures`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departures`
--
ALTER TABLE `departures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
