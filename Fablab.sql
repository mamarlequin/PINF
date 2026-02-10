-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 10 fév. 2026 à 07:00
-- Version du serveur : 10.11.13-MariaDB-0ubuntu0.24.04.1
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `Fablab`
--

-- --------------------------------------------------------

--
-- Structure de la table `Commentaire`
--

CREATE TABLE `Commentaire` (
  `id` int(10) UNSIGNED NOT NULL,
  `idEquipement` int(10) UNSIGNED NOT NULL,
  `idUser` int(10) UNSIGNED NOT NULL,
  `idReservation` int(10) UNSIGNED DEFAULT NULL,
  `contenu` varchar(255) NOT NULL,
  `resolu` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Commentaire`
--

INSERT INTO `Commentaire` (`id`, `idEquipement`, `idUser`, `idReservation`, `contenu`, `resolu`) VALUES
(2, 1, 1, NULL, 'super commentaire', 0),
(3, 1, 1, NULL, 'couix', 1);

-- --------------------------------------------------------

--
-- Structure de la table `Creneau`
--

CREATE TABLE `Creneau` (
  `id` int(10) UNSIGNED NOT NULL,
  `idAdmin` int(10) UNSIGNED NOT NULL,
  `dateDebut` datetime NOT NULL,
  `dateFin` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Emprunt`
--

CREATE TABLE `Emprunt` (
  `id` int(10) UNSIGNED NOT NULL,
  `idUser` int(10) UNSIGNED NOT NULL,
  `idEquipement` int(10) UNSIGNED NOT NULL,
  `dateDebut` datetime NOT NULL,
  `dateRenduTheorique` datetime NOT NULL,
  `dateRenduReel` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Equipement`
--

CREATE TABLE `Equipement` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `enMaintenance` tinyint(1) NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL,
  `risque` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Equipement`
--

INSERT INTO `Equipement` (`id`, `nom`, `type`, `enMaintenance`, `description`, `risque`) VALUES
(1, 'ma machine', 'super imprimante', 0, 'ma super imprimante jolie et rose', 'trop belle');

-- --------------------------------------------------------

--
-- Structure de la table `Notification`
--

CREATE TABLE `Notification` (
  `id` int(10) UNSIGNED NOT NULL,
  `idUser` int(10) UNSIGNED NOT NULL,
  `contenu` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Notification`
--

INSERT INTO `Notification` (`id`, `idUser`, `contenu`) VALUES
(1, 2, 'Vous avez été promu Admin'),
(2, 2, 'Vous avez été promu Admin'),
(3, 2, 'Vous avez été promu Admin'),
(4, 4, 'Vous avez été promu Admin'),
(5, 2, 'Votre période de délégation est terminée. Vous êtes redevenu Admin.'),
(6, 2, 'Votre période de délégation est terminée. Vous êtes redevenu Admin.'),
(7, 2, 'Votre période de délégation est terminée. Vous êtes redevenu Admin.'),
(8, 2, 'Votre période de délégation est terminée.'),
(9, 2, 'Votre période de délégation est terminée.'),
(10, 2, 'Vous avez été promu Admin'),
(11, 4, 'Vous avez été promu Admin'),
(12, 2, 'Vous avez été promu Admin'),
(13, 2, 'Vous avez été promu Admin');

-- --------------------------------------------------------

--
-- Structure de la table `Reservation`
--

CREATE TABLE `Reservation` (
  `id` int(10) UNSIGNED NOT NULL,
  `dateDebut` datetime NOT NULL,
  `dateFin` datetime NOT NULL,
  `idEquipement` int(10) UNSIGNED NOT NULL,
  `idUser` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Utilisateur`
--

CREATE TABLE `Utilisateur` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `promotion` int(11) DEFAULT NULL,
  `motDePasse` varchar(255) NOT NULL,
  `role` int(11) NOT NULL,
  `adresseMail` varchar(255) NOT NULL,
  `dateFinRole` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `Utilisateur`
--

INSERT INTO `Utilisateur` (`id`, `nom`, `prenom`, `promotion`, `motDePasse`, `role`, `adresseMail`, `dateFinRole`) VALUES
(1, 'lara', 'lara', 2026, 'lara', 2, 'admin@fablab.fr', NULL),
(2, 'c', 'couix', 2026, 'FsaXjzvC', 1, 'dhss.lara@gmail.com', '2026-02-09 20:26:00'),
(4, 'couixtest', 'c', 2026, 'DH42jJx0', 1, 'marceau.luciani@ig2i.centralelille.fr', '2026-02-09 00:00:00');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Commentaire`
--
ALTER TABLE `Commentaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commentaire_idequipement_fk` (`idEquipement`),
  ADD KEY `commentaire_iduser_fk` (`idUser`),
  ADD KEY `commentaire_idreservation_fk` (`idReservation`);

--
-- Index pour la table `Creneau`
--
ALTER TABLE `Creneau`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creneau_idadmin_fk` (`idAdmin`);

--
-- Index pour la table `Emprunt`
--
ALTER TABLE `Emprunt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emprunt_iduser_fk` (`idUser`),
  ADD KEY `emprunt_idequipement_fk` (`idEquipement`);

--
-- Index pour la table `Equipement`
--
ALTER TABLE `Equipement`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `Notification`
--
ALTER TABLE `Notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_idUser_fk` (`idUser`);

--
-- Index pour la table `Reservation`
--
ALTER TABLE `Reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_idequipement_fk` (`idEquipement`),
  ADD KEY `reservation_iduser_fk` (`idUser`);

--
-- Index pour la table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `adresseMail` (`adresseMail`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `Commentaire`
--
ALTER TABLE `Commentaire`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `Creneau`
--
ALTER TABLE `Creneau`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Emprunt`
--
ALTER TABLE `Emprunt`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Equipement`
--
ALTER TABLE `Equipement`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `Notification`
--
ALTER TABLE `Notification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `Reservation`
--
ALTER TABLE `Reservation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Utilisateur`
--
ALTER TABLE `Utilisateur`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Commentaire`
--
ALTER TABLE `Commentaire`
  ADD CONSTRAINT `commentaire_idequipement_fk` FOREIGN KEY (`idEquipement`) REFERENCES `Equipement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commentaire_idreservation_fk` FOREIGN KEY (`idReservation`) REFERENCES `Reservation` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `commentaire_iduser_fk` FOREIGN KEY (`idUser`) REFERENCES `Utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `Creneau`
--
ALTER TABLE `Creneau`
  ADD CONSTRAINT `creneau_idadmin_fk` FOREIGN KEY (`idAdmin`) REFERENCES `Utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `Emprunt`
--
ALTER TABLE `Emprunt`
  ADD CONSTRAINT `emprunt_idequipement_fk` FOREIGN KEY (`idEquipement`) REFERENCES `Equipement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `emprunt_iduser_fk` FOREIGN KEY (`idUser`) REFERENCES `Utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `Notification`
--
ALTER TABLE `Notification`
  ADD CONSTRAINT `notification_idUser_fk` FOREIGN KEY (`idUser`) REFERENCES `Utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `Reservation`
--
ALTER TABLE `Reservation`
  ADD CONSTRAINT `reservation_idequipement_fk` FOREIGN KEY (`idEquipement`) REFERENCES `Equipement` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_iduser_fk` FOREIGN KEY (`idUser`) REFERENCES `Utilisateur` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
