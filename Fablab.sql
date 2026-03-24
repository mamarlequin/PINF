
-- ========================
-- TABLE Utilisateur
-- ========================
CREATE TABLE `Utilisateur` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `promotion` int DEFAULT NULL,
  `motDePasse` varchar(255) NOT NULL,
  `role` int NOT NULL,
  `adresseMail` varchar(255) NOT NULL,
  `dateFinRole` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `adresseMail` (`adresseMail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABLE Equipement
-- ========================
CREATE TABLE `Equipement` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `enMaintenance` tinyint(1) NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL,
  `risque` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABLE Outil
-- ========================
CREATE TABLE `Outil` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `risque` varchar(255) DEFAULT NULL,
  `emprunte` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABLE Creneau
-- ========================
CREATE TABLE `Creneau` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idAdmin` int(10) UNSIGNED NOT NULL,
  `dateDebut` datetime NOT NULL,
  `dateFin` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`idAdmin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABLE Reservation
-- ========================
CREATE TABLE `Reservation` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateDebut` datetime NOT NULL,
  `dateFin` datetime NOT NULL,
  `idEquipement` int(10) UNSIGNED NOT NULL,
  `idUser` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`idEquipement`),
  KEY (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABLE Emprunt
-- ========================
CREATE TABLE `Emprunt` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idUser` int(10) UNSIGNED NOT NULL,
  `idEquipement` int(10) UNSIGNED NOT NULL,
  `dateDebut` datetime NOT NULL,
  `dateRenduTheorique` datetime NOT NULL,
  `dateRenduReel` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`idUser`),
  KEY (`idEquipement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABLE Commentaire
-- ========================
CREATE TABLE `Commentaire` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idEquipement` int(10) UNSIGNED NOT NULL,
  `idUser` int(10) UNSIGNED NOT NULL,
  `idReservation` int(10) UNSIGNED DEFAULT NULL,
  `contenu` varchar(255) NOT NULL,
  `resolu` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY (`idEquipement`),
  KEY (`idUser`),
  KEY (`idReservation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- TABLE Notification
-- ========================
CREATE TABLE `Notification` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idUser` int(10) UNSIGNED NOT NULL,
  `contenu` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================
-- INSERTS
-- ========================

INSERT INTO `Utilisateur` (`nom`, `prenom`, `promotion`, `motDePasse`, `role`, `adresseMail`) VALUES
('DEGEZELLE', 'Eulalie', 2029, 'azerty', 2, 'eulaliedegezelle@gmail.com'),
('DEHILES', 'COUIX', 2029, 'couix', 1, 'couix.dehiles@gmail.com'),
('DEHILES', 'LARA', 2029, 'lara', 1, 'lara.dehiles@gmail.com'),
('Gargamel', 'azraEl', 2026, 'Ap4kcSw0', 1, 'gargameldetestlessmurfs@outlook.org');

INSERT INTO `Equipement` (`nom`, `type`, `enMaintenance`, `description`, `risque`) VALUES
('Machine 3', 'Laser', 1, 'une certaine description', 'aucun, c''est tranquille'),
('Imprimante 2', 'Imprimante 3D', 0, 'quelque chose', 'safe'),
('machine', 'imprimante', 0, 'elle imprime', 'les feuilles ça coupe'),
('imprimante', 'rayon laser', 0, 'piou', 'le plus safe des safe'),
('Machine', 'machine', 0, 'c''est une machine', 'tranquille'),
('Machine à coudre', 'pfaff 3.0 / Patty', 0, 'machine semi électronique', 'RAS'),
('Surjeteuse', 'Brother 4 fils', 0, 'surjeteuse industrielle', 'attention aux doigts');

-- ========================
-- CONTRAINTES
-- ========================

ALTER TABLE `Creneau`
ADD CONSTRAINT `fk_creneau_user`
FOREIGN KEY (`idAdmin`) REFERENCES `Utilisateur`(`id`) ON DELETE CASCADE;

ALTER TABLE `Reservation`
ADD CONSTRAINT `fk_resa_equipement`
FOREIGN KEY (`idEquipement`) REFERENCES `Equipement`(`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_resa_user`
FOREIGN KEY (`idUser`) REFERENCES `Utilisateur`(`id`) ON DELETE CASCADE;

ALTER TABLE `Emprunt`
ADD CONSTRAINT `fk_emprunt_user`
FOREIGN KEY (`idUser`) REFERENCES `Utilisateur`(`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_emprunt_equipement`
FOREIGN KEY (`idEquipement`) REFERENCES `Outil`(`id`) ON DELETE CASCADE;

ALTER TABLE `Commentaire`
ADD CONSTRAINT `fk_commentaire_equipement`
FOREIGN KEY (`idEquipement`) REFERENCES `Equipement`(`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_commentaire_user`
FOREIGN KEY (`idUser`) REFERENCES `Utilisateur`(`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_commentaire_resa`
FOREIGN KEY (`idReservation`) REFERENCES `Reservation`(`id`) ON DELETE SET NULL;

ALTER TABLE `Notification`
ADD CONSTRAINT `fk_notification_user`
FOREIGN KEY (`idUser`) REFERENCES `Utilisateur`(`id`) ON DELETE CASCADE;

COMMIT;