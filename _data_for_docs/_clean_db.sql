

CREATE TABLE `administracja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_uzytkownika_admin` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_uzytkownika_admin` (`id_uzytkownika_admin`),
  CONSTRAINT `administracja_ibfk_1` FOREIGN KEY (`id_uzytkownika_admin`) REFERENCES `uzytkownicy` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO administracja VALUES("1","1");



CREATE TABLE `glosowania` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tytul` varchar(65) NOT NULL,
  `opis` text NOT NULL,
  `rozpoczecie` timestamp NOT NULL DEFAULT current_timestamp(),
  `zakonczenie` timestamp NOT NULL DEFAULT current_timestamp(),
  `kworum` tinyint(1) NOT NULL,
  `zwykle` int(11) NOT NULL,
  `autor_id` int(11) NOT NULL,
  `voting_type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `autor_id` (`autor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `glosy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_uzytkownika` int(11) NOT NULL,
  `id_opcji` int(11) NOT NULL,
  `data_dodania` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_uzytkownika` (`id_uzytkownika`,`id_opcji`),
  KEY `id_opcji` (`id_opcji`),
  CONSTRAINT `glosy_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`),
  CONSTRAINT `glosy_ibfk_2` FOREIGN KEY (`id_opcji`) REFERENCES `opcje` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `opcje` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_glosowania` int(11) NOT NULL,
  `nazwa` varchar(65) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_glosowania` (`id_glosowania`),
  CONSTRAINT `opcje_ibfk_1` FOREIGN KEY (`id_glosowania`) REFERENCES `glosowania` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `sekretarze` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_uzytkownika` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_uzytkownika_2` (`id_uzytkownika`),
  KEY `id_uzytkownika` (`id_uzytkownika`),
  CONSTRAINT `sekretarze_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `uzytkownicy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imie` text NOT NULL,
  `nazwisko` text NOT NULL,
  `email` text NOT NULL,
  `haslo` varchar(65) NOT NULL,
  `data_utworzenia` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`) USING HASH,
  KEY `added_by` (`added_by`),
  CONSTRAINT `uzytkownicy_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `administracja` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO uzytkownicy VALUES("1","Robert","Admin","admin@system.com","test1","2023-12-12 00:00:00","1","0");



CREATE TABLE `wyniki` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_glosowania` int(11) NOT NULL,
  `voting_users` int(11) NOT NULL,
  `all_users` int(11) NOT NULL,
  `winner_opt` text NOT NULL,
  `winner_votes` int(11) NOT NULL,
  `kworum_type` int(11) NOT NULL,
  `kworum_ok` tinyint(1) NOT NULL,
  `powod` text NOT NULL,
  `generation_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_glosowania` (`id_glosowania`) USING BTREE,
  CONSTRAINT `wyniki_ibfk_1` FOREIGN KEY (`id_glosowania`) REFERENCES `glosowania` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


