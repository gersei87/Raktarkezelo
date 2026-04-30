-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Ápr 30. 04:29
-- Kiszolgáló verziója: 10.4.6-MariaDB
-- PHP verzió: 7.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `vrdb`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `beszallitok`
--

CREATE TABLE `beszallitok` (
  `beszallitok_id` int(11) NOT NULL,
  `beszallito` varchar(100) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `beszallitok`
--

INSERT INTO `beszallitok` (`beszallitok_id`, `beszallito`, `email`) VALUES
(1, 'HungaroFuel Kft.', 'info@hungarofuel.hu'),
(2, 'TruckParts Kft.', 'sales@truckparts.hu'),
(3, 'LogiPack Zrt.', 'info@logipack.hu'),
(4, 'RaktarTech Kft.', 'info@raktartech.hu'),
(5, 'CoolTrans Kft.', 'support@cooltrans.hu'),
(6, 'SafeWork Kft.', 'info@safework.hu'),
(7, 'AutoPro Kft.', 'rendeles@autopro.hu'),
(8, 'MegaStorage Kft.', 'info@megastorage.hu'),
(9, 'Bűkkfa kft', 'bukkfa@gmail.com');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `felhasznalo`
--

CREATE TABLE `felhasznalo` (
  `felhasznalo_id` int(11) NOT NULL,
  `felhasznalonev` varchar(100) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `jelszo_hash` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `aktiv` tinyint(1) DEFAULT 1,
  `letrehozva` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `felhasznalo`
--

INSERT INTO `felhasznalo` (`felhasznalo_id`, `felhasznalonev`, `email`, `jelszo_hash`, `aktiv`, `letrehozva`) VALUES
(1, 'Admin Vilma', 'admin@gmail.com', '$2y$10$TYxJv1fIq4QQ.bb.r5hBw.bJ6RKk2R52JPbZE5/qP4tzN.CecR0X2', 1, '2026-04-07 14:46:03'),
(2, 'Raktáros Béla', 'raktaros@gmail.com', '$2y$10$NZD.ubxMvdVWgi5gDKWVBeT8cKCNQIxJaorJG9tX842IrpEylx4ca', 1, '2026-04-07 14:52:29'),
(3, 'Olvasó Olga', 'olvaso@gmail.com', '$2y$10$lTlgjEKLGuMF/Vl1HQuzg.6V9ycqeiYDDs9M76jOn9j3Jd3ZjZchq', 1, '2026-04-07 14:53:17'),
(24, 'Virág Elemér', 'velemer@gmail.com', '$2y$10$/3jKX85451lDea86iplG8O2Yx3eTQCnclva9oNNajbu3yr66ctXM.', 1, '2026-04-07 21:45:47'),
(25, 'Szappanos Ottilia', 'szotti@gmail.com', '$2y$10$KaDnPFWjffkk/VD29bR1.OEhQk.ZGSwmCbWNm5utzVXtqCZpDxJLW', 1, '2026-04-07 21:50:20'),
(27, 'Tégla Béla', 'teglabela@freemail.hu', '$2y$10$jeezInKMV5Zk0JP..i4E5OniBVzctygthWfTFzUDPOZXFOMalZO0C', 1, '2026-04-07 22:00:44'),
(28, 'Víg Etelka', 'vetelka@citromail.hu', '$2y$10$3MVxkBBg/QdtKtfkefaOcuI4LIh1XJUcrTIkySUcd/S4r3Jb8Mh8u', 1, '2026-04-07 22:40:38'),
(30, 'Lapos Lajos', 'lalajos@freemail.hu', '$2y$10$jXCuVgkWxg/2uXiDXdbY3.g3KFyGIQn0ueBH0ua2O31z0YEtZIYJ2', 1, '2026-04-07 22:43:21'),
(32, 'Németh Levente', 'metlevi@gmail.com', '$2y$10$HzkAePcxz2Luh55qaXWoBuUERoDIhVDav39waBQZ74/.MGXS0UsHS', 1, '2026-04-07 22:59:15'),
(33, 'Varga Anna', 'vanna@citromail.hu', '$2y$10$7Lpehvr5VorrBh2Kln4f4udYfNgJANH3Ce9BlWz8RvgfffWSQBlv6', 1, '2026-04-07 23:07:10'),
(34, 'Szabó László', 'szala@gmail.com', '$2y$10$VWPgYz0oDjKn7sW64gJ4Lug2jignQRSJh1E7z72.rFZ9pVHbrjtvq', 1, '2026-04-07 23:10:08'),
(37, 'Szabó Aladár', 'szaladar@gmail.com', '$2y$10$L.t6hxXOA6Ob/sHrULlvsOYn2D3Fgs5d0.W.Lyh/UzO0a/Xhmjmiu', 1, '2026-04-07 23:20:39'),
(38, 'Szabó László', 'szabolaci@gmail.com', '$2y$10$b8NOKT81w/SbMPUZ3zsLxuH/nzAF44uKUwrTbzcI3NaOR7AFJ2ORu', 1, '2026-04-07 23:40:04'),
(39, 'Só Malvin', 'somalvin@freemail.hu', '$2y$10$ee2fZxvgyN5N1.hWA3zuUelpzGxfthmSOtnJvD0tEv930l9mGmVs.', 1, '2026-04-11 17:04:25'),
(40, 'Víg Elemér', 'velemer@freemail.hu', '$2y$10$Y10YPo02iYzWLixZI6UNOetnpsTbLqwUmjen3fJz2xhTCqLOQdgf2', 1, '2026-04-11 17:20:21'),
(41, 'Varga László', 'laci23@valami.hu', '$2y$10$ffmNXPrzGsaZJcezhvg/tu8zw.jksi0IjJbzdj150q3J..66lJXqe', 1, '2026-04-11 18:06:15'),
(42, 'Szabó Tamás', 'tomi32@gmail.com', '$2y$10$ek/9Za5X48oqNRVp56mEqefbJ2pdRyxFxcWF4gex6ZCAclmZ/eeMG', 1, '2026-04-11 18:15:45'),
(43, 'Balogh Ádám', 'baloghadam@freemail.hu', '$2y$10$BXkhBCRfsEAPd/p5ahcTB.7kHWm6iW.AwQXcRctW0lmfl3RNyr3jW', 1, '2026-04-11 18:27:22'),
(44, 'Vas Anna', 'vasanna@citromail.hu', '$2y$10$39Rz0YFIv8hncnYlKYsqLuiZ2tIljS3aD3lU.fvbIeSoj1EECnB3K', 1, '2026-04-13 13:00:36'),
(45, 'Gersei Gábor', 'gersei@pataky.hu', '$2y$10$BgCaXFY53fDmmHaEZDyexezscPJZUwKJRRRbgFdHtDj6a.06yZiNy', 1, '2026-04-20 20:00:52'),
(46, 'Toldi Miklós', 'miki@gmail.com', '$2y$10$ZPi3tgJtWA547CxP9DbrMO3ZoZp2cZ/mCzqRMDLGqmBbEFKjoOOqW', 1, '2026-04-21 21:32:48');

--
-- Eseményindítók `felhasznalo`
--
DELIMITER $$
CREATE TRIGGER `trig_felhasznalo_insert` AFTER INSERT ON `felhasznalo` FOR EACH ROW BEGIN
    INSERT INTO naplo(felhasznalo_id, tipus, leiras)
    VALUES (NEW.felhasznalo_id, 'felhasznalo_hozzaadas', CONCAT('Új felhasználó regisztrálva: ', NEW.felhasznalonev, ', email: ', NEW.email));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `felhasznalo_szerepkor`
--

CREATE TABLE `felhasznalo_szerepkor` (
  `felhasznalo_id` int(11) NOT NULL,
  `szerepkor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- A tábla adatainak kiíratása `felhasznalo_szerepkor`
--

INSERT INTO `felhasznalo_szerepkor` (`felhasznalo_id`, `szerepkor_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(24, 3),
(25, 3),
(27, 3),
(28, 3),
(30, 3),
(32, 3),
(33, 3),
(34, 3),
(37, 3),
(38, 3),
(39, 3),
(40, 3),
(41, 3),
(42, 3),
(43, 3),
(44, 3),
(45, 1),
(46, 3);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `jogosultsag`
--

CREATE TABLE `jogosultsag` (
  `jogosultsag_id` int(11) NOT NULL,
  `kod` varchar(100) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `leiras` varchar(255) COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `jogosultsag`
--

INSERT INTO `jogosultsag` (`jogosultsag_id`, `kod`, `leiras`) VALUES
(1, 'TERMEK_OLVAS', 'Termekek megtekintese'),
(2, 'TERMEK_KEZEL', 'Termekek letrehozasa es modositasa'),
(3, 'RAKTARKESZLET_OLVAS', 'Raktarkeszlet megtekintese'),
(4, 'RAKTARKESZLET_KEZEL', 'Raktarkeszlet letrehozasa es modositasa'),
(5, 'FELHASZNALO_KEZEL', 'Felhasznalok kezelese'),
(6, 'NAPLO_OLVAS', 'Naplobejegyzesek megtekintese'),
(7, 'TORLES', 'Törlése bármilyen rekordnak');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `keszlet_mozgas`
--

CREATE TABLE `keszlet_mozgas` (
  `mozgas_id` int(11) NOT NULL,
  `termek_id` int(11) NOT NULL,
  `raktar_id` int(11) NOT NULL,
  `tarolohely_id` int(11) NOT NULL,
  `felhasznalo_id` int(11) NOT NULL,
  `mennyiseg` int(11) NOT NULL,
  `tipus` enum('bevetel','kiadas','athelyezes') COLLATE utf8mb4_hungarian_ci NOT NULL,
  `datum` datetime DEFAULT current_timestamp(),
  `megjegyzes` text COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Eseményindítók `keszlet_mozgas`
--
DELIMITER $$
CREATE TRIGGER `trg_keszlet_ellenorzes` BEFORE UPDATE ON `keszlet_mozgas` FOR EACH ROW BEGIN
    DECLARE aktualis INT;

    IF NEW.tipus = 'kiadas' THEN

        SELECT mennyiseg INTO aktualis
        FROM raktarkeszlet
        WHERE termek_id = NEW.termek_id
        AND tarolohely_id = NEW.tarolohely_id;

        IF aktualis IS NULL OR aktualis < NEW.mennyiseg THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Nincs elegendő készlet!';
        END IF;

    END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `naplo`
--

CREATE TABLE `naplo` (
  `naplo_id` int(11) NOT NULL,
  `felhasznalo_id` int(11) DEFAULT NULL,
  `tipus` varchar(50) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `leiras` text COLLATE utf8mb4_hungarian_ci NOT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `raktar`
--

CREATE TABLE `raktar` (
  `raktar_id` int(11) NOT NULL,
  `nev` varchar(100) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `cim` text COLLATE utf8mb4_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `raktar`
--

INSERT INTO `raktar` (`raktar_id`, `nev`, `cim`) VALUES
(1, 'Központi Raktár', '1010 Budapest, Fő utca 1.'),
(2, 'Északi Raktár', '1020 Budapest, Északi út 12.'),
(3, 'Déli Raktár', '1030 Budapest, Déli tér 5.');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `raktarkeszlet`
--

CREATE TABLE `raktarkeszlet` (
  `raktarkeszlet_id` int(11) NOT NULL,
  `termek_id` int(11) NOT NULL,
  `raktar_id` int(11) NOT NULL,
  `tarolohely_id` int(11) NOT NULL,
  `sorozatszam` varchar(150) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `leltari_szam` varchar(150) COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `mennyiseg` int(11) DEFAULT 1,
  `allapot` enum('uj','hasznalt','hibas','selejt') COLLATE utf8mb4_hungarian_ci NOT NULL,
  `beszerzes_datuma` date DEFAULT NULL,
  `megjegyzes` text COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `letrehozva` datetime DEFAULT current_timestamp(),
  `felhasznalo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- Eseményindítók `raktarkeszlet`
--
DELIMITER $$
CREATE TRIGGER `trig_raktarkeszlet_delete` AFTER DELETE ON `raktarkeszlet` FOR EACH ROW BEGIN
    DECLARE raktarNev VARCHAR(255);
    DECLARE taroloKod VARCHAR(255);
    DECLARE termek_Nev VARCHAR(255);
    DECLARE userNev VARCHAR(255);

    -- Adatok lekérése (DELETE-nél OLD!)
    SELECT nev INTO raktarNev FROM raktar WHERE raktar_id = OLD.raktar_id;
    SELECT kod INTO taroloKod FROM tarolohely WHERE tarolohely_id = OLD.tarolohely_id;
    SELECT termeknev INTO termek_Nev FROM termekek WHERE termek_id = OLD.termek_id;
    SELECT felhasznalonev INTO userNev FROM felhasznalo WHERE felhasznalo_id = OLD.felhasznalo_id;

    -- Naplózás
    INSERT INTO naplo(felhasznalo_id, tipus, leiras)
    VALUES (
        OLD.felhasznalo_id,
        'keszlet_torles',
        CONCAT(
            'Felhasználó=', IFNULL(userNev, 'ismeretlen'),
            ', termék_id=', OLD.termek_id,
            ', név=', IFNULL(termek_Nev, 'ismeretlen'),
            ', raktár=', IFNULL(raktarNev, 'ismeretlen'),
            ', tároló=', IFNULL(taroloKod, 'ismeretlen'),
            ', állapot=', IFNULL(OLD.allapot, 'nincs megadva')
        )
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trig_raktarkeszlet_insert` AFTER INSERT ON `raktarkeszlet` FOR EACH ROW BEGIN
    DECLARE raktarNev VARCHAR(255);
    DECLARE taroloKod VARCHAR(255);
    DECLARE termek_Nev VARCHAR(255);
    DECLARE userNev VARCHAR(255);

    -- Adatok lekérése
    SELECT nev INTO raktarNev FROM raktar WHERE raktar_id = NEW.raktar_id;
    SELECT kod INTO taroloKod FROM tarolohely WHERE tarolohely_id = NEW.tarolohely_id;
    SELECT termeknev INTO termek_Nev FROM termekek WHERE termek_id = NEW.termek_id;
    SELECT felhasznalonev INTO userNev FROM felhasznalo WHERE felhasznalo_id = NEW.felhasznalo_id;

    -- Naplózás
    INSERT INTO naplo(felhasznalo_id, tipus, leiras)
    VALUES (
        NEW.felhasznalo_id,
        'keszlet_felvetel',
        CONCAT(
            'Felhasználó=', IFNULL(userNev, 'ismeretlen'),
            ', termék_id=', NEW.termek_id,
            ', név=', IFNULL(termek_Nev, 'ismeretlen'),
            ', raktár=', IFNULL(raktarNev, 'ismeretlen'),
            ', tároló=', IFNULL(taroloKod, 'ismeretlen'),
            ', állapot=', IFNULL(NEW.allapot, 'nincs megadva')
        )
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trig_raktarkeszlet_update` AFTER UPDATE ON `raktarkeszlet` FOR EACH ROW BEGIN
    DECLARE raktarNev VARCHAR(255);
    DECLARE taroloKod VARCHAR(255);
    DECLARE termek_Nev VARCHAR(255);
    DECLARE userNev VARCHAR(255);
    DECLARE mennyValtozas INT;

    -- mennyiség változás
    SET mennyValtozas = IFNULL(NEW.mennyiseg, 0) - IFNULL(OLD.mennyiseg, 0);

    -- EREDETI MŰKÖDÉS (külön SELECT-ekkel – ez biztosan hozza a neveket)
    SELECT nev 
    INTO raktarNev 
    FROM raktar 
    WHERE raktar_id = NEW.raktar_id;

    SELECT kod 
    INTO taroloKod 
    FROM tarolohely 
    WHERE tarolohely_id = NEW.tarolohely_id;

    SELECT termeknev 
    INTO termek_Nev 
    FROM termekek 
    WHERE termek_id = NEW.termek_id;

    SELECT felhasznalonev 
    INTO userNev 
    FROM felhasznalo 
    WHERE felhasznalo_id = NEW.felhasznalo_id;

    -- naplózás
    INSERT INTO naplo(felhasznalo_id, tipus, leiras)
    VALUES (
        NEW.felhasznalo_id,
        'keszlet_modositas',
        CONCAT(
            'Felhasználó=', IFNULL(userNev, 'ismeretlen'),
            ', termék_id=', NEW.termek_id,
            ', név=', IFNULL(termek_Nev, 'ismeretlen'),
            ', raktár=', IFNULL(raktarNev, 'ismeretlen'),
            ', tároló=', IFNULL(taroloKod, 'ismeretlen'),
            ', állapot=', IFNULL(NEW.allapot, 'nincs megadva'),
            ', mennyiség=', IFNULL(NEW.mennyiseg, 0),
            ', változás=', 
            CASE 
                WHEN mennyValtozas > 0 THEN CONCAT('+', mennyValtozas)
                ELSE mennyValtozas
            END
        )
    );

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `szerepkor`
--

CREATE TABLE `szerepkor` (
  `szerepkor_id` int(11) NOT NULL,
  `nev` varchar(50) COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `szerepkor`
--

INSERT INTO `szerepkor` (`szerepkor_id`, `nev`) VALUES
(1, 'admin'),
(3, 'olvaso'),
(2, 'raktaros');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `szerepkor_jogosultsag`
--

CREATE TABLE `szerepkor_jogosultsag` (
  `szerepkor_id` int(11) NOT NULL,
  `jogosultsag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- A tábla adatainak kiíratása `szerepkor_jogosultsag`
--

INSERT INTO `szerepkor_jogosultsag` (`szerepkor_id`, `jogosultsag_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 6),
(2, 7),
(3, 1),
(3, 3);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `tarolohely`
--

CREATE TABLE `tarolohely` (
  `tarolohely_id` int(11) NOT NULL,
  `raktar_id` int(11) NOT NULL,
  `sor` int(11) NOT NULL,
  `oszlop` char(1) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `szint` int(11) DEFAULT 1,
  `kod` varchar(20) GENERATED ALWAYS AS (concat(`sor`,'-',`oszlop`,'-',`szint`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `tarolohely`
--

INSERT INTO `tarolohely` (`tarolohely_id`, `raktar_id`, `sor`, `oszlop`, `szint`) VALUES
(105, 1, 1, 'A', 1),
(106, 1, 1, 'A', 2),
(107, 1, 1, 'A', 3),
(108, 1, 1, 'B', 1),
(109, 1, 1, 'B', 2),
(110, 1, 1, 'B', 3),
(111, 1, 1, 'C', 1),
(112, 1, 1, 'C', 2),
(113, 1, 1, 'C', 3),
(114, 1, 1, 'D', 1),
(115, 1, 1, 'D', 2),
(116, 1, 1, 'D', 3),
(117, 1, 2, 'A', 1),
(118, 1, 2, 'A', 2),
(119, 1, 2, 'A', 3),
(120, 1, 2, 'B', 1),
(121, 1, 2, 'B', 2),
(122, 1, 2, 'B', 3),
(123, 1, 2, 'C', 1),
(124, 1, 2, 'C', 2),
(125, 1, 2, 'C', 3),
(126, 1, 2, 'D', 1),
(127, 1, 2, 'D', 2),
(128, 1, 2, 'D', 3),
(129, 1, 3, 'A', 1),
(130, 1, 3, 'A', 2),
(131, 1, 3, 'A', 3),
(132, 1, 3, 'B', 1),
(133, 1, 3, 'B', 2),
(134, 1, 3, 'B', 3),
(135, 1, 3, 'C', 1),
(136, 1, 3, 'C', 2),
(137, 1, 3, 'C', 3),
(138, 1, 3, 'D', 1),
(139, 1, 3, 'D', 2),
(140, 1, 3, 'D', 3),
(141, 2, 1, 'A', 1),
(142, 2, 1, 'A', 2),
(143, 2, 1, 'A', 3),
(144, 2, 1, 'B', 1),
(145, 2, 1, 'B', 2),
(146, 2, 1, 'B', 3),
(147, 2, 1, 'C', 1),
(148, 2, 1, 'C', 2),
(149, 2, 1, 'C', 3),
(150, 2, 1, 'D', 1),
(151, 2, 1, 'D', 2),
(152, 2, 1, 'D', 3),
(153, 2, 2, 'A', 1),
(154, 2, 2, 'A', 2),
(155, 2, 2, 'A', 3),
(156, 2, 2, 'B', 1),
(157, 2, 2, 'B', 2),
(158, 2, 2, 'B', 3),
(159, 2, 2, 'C', 1),
(160, 2, 2, 'C', 2),
(161, 2, 2, 'C', 3),
(162, 2, 2, 'D', 1),
(163, 2, 2, 'D', 2),
(164, 2, 2, 'D', 3),
(165, 3, 1, 'A', 1),
(166, 3, 1, 'A', 2),
(167, 3, 1, 'A', 3),
(168, 3, 1, 'B', 1),
(169, 3, 1, 'B', 2),
(170, 3, 1, 'B', 3),
(171, 3, 1, 'C', 1),
(172, 3, 1, 'C', 2),
(173, 3, 1, 'C', 3),
(174, 3, 1, 'D', 1),
(175, 3, 1, 'D', 2),
(176, 3, 1, 'D', 3),
(177, 3, 2, 'A', 1),
(178, 3, 2, 'A', 2),
(179, 3, 2, 'A', 3),
(180, 3, 2, 'B', 1),
(181, 3, 2, 'B', 2),
(182, 3, 2, 'B', 3),
(183, 3, 2, 'C', 1),
(184, 3, 2, 'C', 2),
(185, 3, 2, 'C', 3),
(186, 3, 2, 'D', 1),
(187, 3, 2, 'D', 2),
(188, 3, 2, 'D', 3),
(189, 3, 3, 'A', 1),
(190, 3, 3, 'A', 2),
(191, 3, 3, 'A', 3),
(192, 3, 3, 'B', 1),
(193, 3, 3, 'B', 2),
(194, 3, 3, 'B', 3),
(195, 3, 3, 'C', 1),
(196, 3, 3, 'C', 2),
(197, 3, 3, 'C', 3),
(198, 3, 3, 'D', 1),
(199, 3, 3, 'D', 2),
(200, 3, 3, 'D', 3),
(201, 3, 4, 'A', 1),
(202, 3, 4, 'A', 2),
(203, 3, 4, 'A', 3),
(204, 3, 5, 'X', 1),
(205, 3, 5, 'X', 2),
(206, 3, 5, 'X', 3),
(207, 3, 5, 'X', 4),
(208, 3, 5, 'X', 5);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `termekek`
--

CREATE TABLE `termekek` (
  `termek_id` int(11) NOT NULL,
  `termeknev` varchar(255) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `cikkszam` varchar(100) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `leiras` text COLLATE utf8mb4_hungarian_ci DEFAULT NULL,
  `mertekegyseg` varchar(20) COLLATE utf8mb4_hungarian_ci NOT NULL,
  `egysegar` int(11) DEFAULT NULL,
  `letrehozva` datetime DEFAULT current_timestamp(),
  `kategoria_id` int(11) DEFAULT NULL,
  `beszallitok_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `termekek`
--

INSERT INTO `termekek` (`termek_id`, `termeknev`, `cikkszam`, `leiras`, `mertekegyseg`, `egysegar`, `letrehozva`, `kategoria_id`, `beszallitok_id`) VALUES
(1, 'Raklap fólia', 'RF-001', 'Stretch fólia raklapok rögzítéséhez', 'tekercs', 1200, '2026-04-05 21:12:27', 1, 3),
(2, 'Dízel üzemanyag', 'DU-002', 'Prémium dízel kamionokhoz', 'liter', 520, '2026-04-05 21:12:27', 2, 1),
(3, 'Raklap emelő villa', 'RE-003', 'Kézi targoncához emelő villa', 'db', 35000, '2026-04-05 21:12:27', 3, 2),
(4, 'Szállító doboz', 'SD-004', 'Műanyag láda áruszállításhoz', 'db', 2500, '2026-04-05 21:12:27', 4, 3),
(5, 'Kamion gumiabroncs', 'KG-005', '18 colos teherautó abroncs', 'db', 75000, '2026-04-05 21:12:27', 5, 7),
(6, 'Motorolaj 10W40', 'MO-006', 'Nagy terhelésű motorolaj', 'liter', 4500, '2026-04-05 21:12:27', 2, 1),
(7, 'Biztonsági mellény', 'BM-007', 'Láthatósági mellény raktári dolgozóknak', 'db', 3200, '2026-04-05 21:12:27', 6, 6),
(8, 'Raklap', 'R-008', 'EUR raklap 120x80 cm', 'db', 4200, '2026-04-05 21:12:27', 4, 8),
(9, 'Hűtőkonténer bérlés', 'HK-009', 'Hűtött szállítás rövid távra', 'nap', 15000, '2026-04-05 21:12:27', 8, 5),
(10, 'Emelőheveder', 'EH-010', 'Teheremelő heveder', 'db', 2800, '2026-04-05 21:12:27', 3, 2),
(11, 'Teherautó ponyva', 'TP-011', 'Vízálló takaró ponyva', 'db', 18000, '2026-04-05 21:12:27', 5, 2),
(12, 'Targonca akkumulátor', 'TA-012', 'Elektromos targonca akku', 'db', 95000, '2026-04-05 21:12:27', 5, 7),
(13, 'Raktári polc', 'RP-013', 'Fém tároló polcrendszer', 'db', 22000, '2026-04-05 21:12:27', 7, 4),
(14, 'Olajszűrő', 'OS-014', 'Teherautó olajszűrő', 'db', 4200, '2026-04-05 21:12:27', 5, 7),
(15, 'Féktárcsa', 'FT-015', 'Első féktárcsa kamionhoz', 'db', 12500, '2026-04-05 21:12:27', 5, 7),
(16, 'Hűtőközeg', 'HK-016', 'Klíma hűtőközeg', 'liter', 3200, '2026-04-05 21:12:27', 8, 5),
(17, 'Szállítószalag', 'SS-017', 'Raktári szalag rendszer', 'db', 12000, '2026-04-05 21:12:27', 7, 4),
(18, 'Karabiner', 'KA-018', 'Biztonsági karabiner', 'db', 1500, '2026-04-05 21:12:27', 3, 6),
(19, 'Rakodóállvány', 'RA-019', 'Ideiglenes raklap tároló', 'db', 9000, '2026-04-05 21:12:27', 7, 4),
(20, 'Tisztítószer', 'TS-020', 'Jármű tisztító folyadék', 'liter', 1800, '2026-04-05 21:12:27', 2, 1),
(21, 'Emelőgép', 'EG-021', 'Raktári daru', 'db', 42000, '2026-04-05 21:12:27', 3, 2),
(22, 'Hűtőszekrény', 'HS-022', 'Élelmiszerhűtő kamionhoz', 'db', 120000, '2026-04-05 21:12:27', 8, 5),
(23, 'Mérleg platform', 'MP-023', 'Raklapok súlymérésére', 'db', 85000, '2026-04-05 21:12:27', 4, 4),
(24, 'Olajleeresztő tálca', 'OT-024', 'Motorolaj gyűjtésére szolgáló tálca', 'db', 3500, '2026-04-05 21:12:27', 5, 7),
(25, 'Gépjármű tisztítószer', 'GT-025', 'Teherautók külső tisztítására', 'liter', 1800, '2026-04-05 21:12:27', 2, 1),
(26, 'Szemetes konténer', 'SK-026', 'Raktári hulladékgyűjtő konténer', 'db', 7200, '2026-04-05 21:12:27', 7, 4),
(27, 'Pneumatikus emelő', 'PE-027', 'Emelő berendezés raklapokhoz', 'db', 38000, '2026-04-05 21:12:27', 3, 2),
(28, 'Raktári világítás', 'RV-028', 'LED lámpák raktári csarnokhoz', 'db', 12000, '2026-04-05 21:12:27', 7, 4),
(29, 'Teherautó üzemanyag tartály', 'TT-029', 'Extra üzemanyagtartály kamionokhoz', 'db', 55000, '2026-04-05 21:12:27', 2, 1),
(30, 'Raklap alátét', 'RA-030', 'Raklap stabilizáló fa alátét', 'db', 7892, '2026-04-05 21:12:27', 4, 1),
(31, 'Teherautó lánc', 'TL-031', 'Hólánc teherautókhoz', 'db', 17000, '2026-04-05 21:12:27', 5, 7),
(32, 'Targonca gumi', 'TG-032', 'Targonca kerekekhez gumiabroncs', 'db', 12500, '2026-04-05 21:12:27', 5, 7),
(33, 'Biztonsági sisak', 'BS-033', 'Védősisak raktári dolgozóknak', 'db', 8800, '2026-04-05 21:12:27', 6, 6),
(34, 'Teherautó hőmérő', 'TH-034', 'Raktér hőmérséklet ellenőrzésére', 'db', 4200, '2026-04-05 21:12:27', 2, 1),
(35, 'Rakodó rámpa', 'RR-035', 'Raktári rakodó rámpa kamionhoz', 'db', 16000, '2026-04-05 21:12:27', 7, 4),
(36, 'Olajpumpa', 'OP-036', 'Motorolaj pumpáló berendezés', 'db', 7200, '2026-04-05 21:12:27', 5, 7),
(37, 'Faláda', 'ZX-77-87', 'láda', 'db', 2346, '2026-04-05 00:00:00', 4, 3),
(39, 'Müanyagláda', 'ZX-77-89', 'láda', 'db', 2345, '2026-04-06 00:00:00', 4, 3),
(40, 'Ponyva', 'ZX-77-90', 'teherautókhoz', 'db', 23567, '2026-04-06 00:00:00', 5, 7),
(42, 'izzólámpa', 'ZX-77-77', 'hátsó lámpához', 'db', 1234, '2026-04-06 00:00:00', 5, 4),
(49, 'Falétra', 'fa-345-7', 'Fatermékek szállítója', 'db', 15432, '2026-04-25 12:04:55', 7, 9),
(50, 'Mosószappan', 'ZX7-F03', NULL, 'db', 234, '2026-04-27 20:26:06', 6, 6),
(51, 'Ponyva', 'PO39-41', NULL, 'db', 12567, '2026-04-29 21:19:17', 5, 5);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `termek_kategoria`
--

CREATE TABLE `termek_kategoria` (
  `kategoria_id` int(11) NOT NULL,
  `kategoria` varchar(100) COLLATE utf8mb4_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `termek_kategoria`
--

INSERT INTO `termek_kategoria` (`kategoria_id`, `kategoria`) VALUES
(1, 'Csomagolóanyagok'),
(3, 'Emelő eszközök'),
(8, 'Hűtés'),
(5, 'Jármű alkatrész'),
(6, 'Munkavédelem'),
(7, 'Raktári eszközök'),
(4, 'Tárolás'),
(2, 'Üzemanyag és kenőanyag');

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `v_keszlet_mozgas`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `v_keszlet_mozgas` (
`mozgas_id` int(11)
,`termeknev` varchar(255)
,`raktar` varchar(100)
,`tarolohely` varchar(20)
,`mennyiseg` int(11)
,`tipus` enum('bevetel','kiadas','athelyezes')
,`datum` datetime
,`felhasznalonev` varchar(100)
);

-- --------------------------------------------------------

--
-- A nézet helyettes szerkezete `v_raktarkeszlet`
-- (Lásd alább az aktuális nézetet)
--
CREATE TABLE `v_raktarkeszlet` (
`raktarkeszlet_id` int(11)
,`termek` varchar(255)
,`cikkszam` varchar(100)
,`raktar` varchar(100)
,`tarolohely` varchar(25)
,`mennyiseg` int(11)
,`allapot` enum('uj','hasznalt','hibas','selejt')
,`beszerzes_datuma` date
,`rogzitette` varchar(100)
);

-- --------------------------------------------------------

--
-- Nézet szerkezete `v_keszlet_mozgas`
--
DROP TABLE IF EXISTS `v_keszlet_mozgas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_keszlet_mozgas`  AS  select `km`.`mozgas_id` AS `mozgas_id`,`t`.`termeknev` AS `termeknev`,`r`.`nev` AS `raktar`,`th`.`kod` AS `tarolohely`,`km`.`mennyiseg` AS `mennyiseg`,`km`.`tipus` AS `tipus`,`km`.`datum` AS `datum`,`f`.`felhasznalonev` AS `felhasznalonev` from ((((`keszlet_mozgas` `km` join `termekek` `t` on(`km`.`termek_id` = `t`.`termek_id`)) join `raktar` `r` on(`km`.`raktar_id` = `r`.`raktar_id`)) join `tarolohely` `th` on(`km`.`tarolohely_id` = `th`.`tarolohely_id`)) join `felhasznalo` `f` on(`km`.`felhasznalo_id` = `f`.`felhasznalo_id`)) ;

-- --------------------------------------------------------

--
-- Nézet szerkezete `v_raktarkeszlet`
--
DROP TABLE IF EXISTS `v_raktarkeszlet`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_raktarkeszlet`  AS  select `rk`.`raktarkeszlet_id` AS `raktarkeszlet_id`,`t`.`termeknev` AS `termek`,`t`.`cikkszam` AS `cikkszam`,`r`.`nev` AS `raktar`,concat(`th`.`sor`,'-',`th`.`oszlop`,'-',`th`.`szint`) AS `tarolohely`,`rk`.`mennyiseg` AS `mennyiseg`,`rk`.`allapot` AS `allapot`,`rk`.`beszerzes_datuma` AS `beszerzes_datuma`,`f`.`felhasznalonev` AS `rogzitette` from ((((`raktarkeszlet` `rk` join `termekek` `t` on(`rk`.`termek_id` = `t`.`termek_id`)) join `raktar` `r` on(`rk`.`raktar_id` = `r`.`raktar_id`)) join `tarolohely` `th` on(`rk`.`tarolohely_id` = `th`.`tarolohely_id`)) join `felhasznalo` `f` on(`rk`.`felhasznalo_id` = `f`.`felhasznalo_id`)) ;

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `beszallitok`
--
ALTER TABLE `beszallitok`
  ADD PRIMARY KEY (`beszallitok_id`),
  ADD UNIQUE KEY `beszallitonev` (`beszallito`),
  ADD UNIQUE KEY `email` (`email`);

--
-- A tábla indexei `felhasznalo`
--
ALTER TABLE `felhasznalo`
  ADD PRIMARY KEY (`felhasznalo_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- A tábla indexei `felhasznalo_szerepkor`
--
ALTER TABLE `felhasznalo_szerepkor`
  ADD PRIMARY KEY (`felhasznalo_id`,`szerepkor_id`),
  ADD KEY `szerepkor_id` (`szerepkor_id`);

--
-- A tábla indexei `jogosultsag`
--
ALTER TABLE `jogosultsag`
  ADD PRIMARY KEY (`jogosultsag_id`),
  ADD UNIQUE KEY `kod` (`kod`);

--
-- A tábla indexei `keszlet_mozgas`
--
ALTER TABLE `keszlet_mozgas`
  ADD PRIMARY KEY (`mozgas_id`),
  ADD KEY `termek_id` (`termek_id`),
  ADD KEY `raktar_id` (`raktar_id`),
  ADD KEY `tarolohely_id` (`tarolohely_id`),
  ADD KEY `felhasznalo_id` (`felhasznalo_id`);

--
-- A tábla indexei `naplo`
--
ALTER TABLE `naplo`
  ADD PRIMARY KEY (`naplo_id`),
  ADD KEY `felhasznalo_id` (`felhasznalo_id`);

--
-- A tábla indexei `raktar`
--
ALTER TABLE `raktar`
  ADD PRIMARY KEY (`raktar_id`);

--
-- A tábla indexei `raktarkeszlet`
--
ALTER TABLE `raktarkeszlet`
  ADD PRIMARY KEY (`raktarkeszlet_id`),
  ADD KEY `termek_id` (`termek_id`),
  ADD KEY `raktar_id` (`raktar_id`),
  ADD KEY `tarolohely_id` (`tarolohely_id`),
  ADD KEY `fk_raktarkeszlet_felhasznalo` (`felhasznalo_id`);

--
-- A tábla indexei `szerepkor`
--
ALTER TABLE `szerepkor`
  ADD PRIMARY KEY (`szerepkor_id`),
  ADD UNIQUE KEY `nev` (`nev`);

--
-- A tábla indexei `szerepkor_jogosultsag`
--
ALTER TABLE `szerepkor_jogosultsag`
  ADD PRIMARY KEY (`szerepkor_id`,`jogosultsag_id`),
  ADD KEY `jogosultsag_id` (`jogosultsag_id`);

--
-- A tábla indexei `tarolohely`
--
ALTER TABLE `tarolohely`
  ADD PRIMARY KEY (`tarolohely_id`),
  ADD UNIQUE KEY `egyedi_hely` (`raktar_id`,`sor`,`oszlop`,`szint`);

--
-- A tábla indexei `termekek`
--
ALTER TABLE `termekek`
  ADD PRIMARY KEY (`termek_id`),
  ADD UNIQUE KEY `cikkszam` (`cikkszam`),
  ADD KEY `fk_termek_kategoria` (`kategoria_id`),
  ADD KEY `fk_termek_beszallito` (`beszallitok_id`);

--
-- A tábla indexei `termek_kategoria`
--
ALTER TABLE `termek_kategoria`
  ADD PRIMARY KEY (`kategoria_id`),
  ADD UNIQUE KEY `kategoria_nev` (`kategoria`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `beszallitok`
--
ALTER TABLE `beszallitok`
  MODIFY `beszallitok_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT a táblához `felhasznalo`
--
ALTER TABLE `felhasznalo`
  MODIFY `felhasznalo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT a táblához `jogosultsag`
--
ALTER TABLE `jogosultsag`
  MODIFY `jogosultsag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT a táblához `keszlet_mozgas`
--
ALTER TABLE `keszlet_mozgas`
  MODIFY `mozgas_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `naplo`
--
ALTER TABLE `naplo`
  MODIFY `naplo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `raktar`
--
ALTER TABLE `raktar`
  MODIFY `raktar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT a táblához `raktarkeszlet`
--
ALTER TABLE `raktarkeszlet`
  MODIFY `raktarkeszlet_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `szerepkor`
--
ALTER TABLE `szerepkor`
  MODIFY `szerepkor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT a táblához `tarolohely`
--
ALTER TABLE `tarolohely`
  MODIFY `tarolohely_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;

--
-- AUTO_INCREMENT a táblához `termekek`
--
ALTER TABLE `termekek`
  MODIFY `termek_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT a táblához `termek_kategoria`
--
ALTER TABLE `termek_kategoria`
  MODIFY `kategoria_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `felhasznalo_szerepkor`
--
ALTER TABLE `felhasznalo_szerepkor`
  ADD CONSTRAINT `felhasznalo_szerepkor_ibfk_1` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`felhasznalo_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `felhasznalo_szerepkor_ibfk_2` FOREIGN KEY (`szerepkor_id`) REFERENCES `szerepkor_jogosultsag` (`szerepkor_id`);

--
-- Megkötések a táblához `keszlet_mozgas`
--
ALTER TABLE `keszlet_mozgas`
  ADD CONSTRAINT `keszlet_mozgas_ibfk_1` FOREIGN KEY (`termek_id`) REFERENCES `termekek` (`termek_id`),
  ADD CONSTRAINT `keszlet_mozgas_ibfk_2` FOREIGN KEY (`raktar_id`) REFERENCES `raktar` (`raktar_id`),
  ADD CONSTRAINT `keszlet_mozgas_ibfk_3` FOREIGN KEY (`tarolohely_id`) REFERENCES `tarolohely` (`tarolohely_id`),
  ADD CONSTRAINT `keszlet_mozgas_ibfk_4` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`felhasznalo_id`);

--
-- Megkötések a táblához `naplo`
--
ALTER TABLE `naplo`
  ADD CONSTRAINT `naplo_ibfk_1` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`felhasznalo_id`) ON DELETE SET NULL;

--
-- Megkötések a táblához `raktarkeszlet`
--
ALTER TABLE `raktarkeszlet`
  ADD CONSTRAINT `fk_raktarkeszlet_felhasznalo` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`felhasznalo_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `raktarkeszlet_ibfk_1` FOREIGN KEY (`termek_id`) REFERENCES `termekek` (`termek_id`),
  ADD CONSTRAINT `raktarkeszlet_ibfk_2` FOREIGN KEY (`raktar_id`) REFERENCES `raktar` (`raktar_id`),
  ADD CONSTRAINT `raktarkeszlet_ibfk_3` FOREIGN KEY (`tarolohely_id`) REFERENCES `tarolohely` (`tarolohely_id`);

--
-- Megkötések a táblához `szerepkor_jogosultsag`
--
ALTER TABLE `szerepkor_jogosultsag`
  ADD CONSTRAINT `szerepkor_jogosultsag_ibfk_1` FOREIGN KEY (`szerepkor_id`) REFERENCES `szerepkor` (`szerepkor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `szerepkor_jogosultsag_ibfk_2` FOREIGN KEY (`jogosultsag_id`) REFERENCES `jogosultsag` (`jogosultsag_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `tarolohely`
--
ALTER TABLE `tarolohely`
  ADD CONSTRAINT `tarolohely_ibfk_1` FOREIGN KEY (`raktar_id`) REFERENCES `raktar` (`raktar_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `termekek`
--
ALTER TABLE `termekek`
  ADD CONSTRAINT `fk_termek_beszallito` FOREIGN KEY (`beszallitok_id`) REFERENCES `beszallitok` (`beszallitok_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_termek_kategoria` FOREIGN KEY (`kategoria_id`) REFERENCES `termek_kategoria` (`kategoria_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
