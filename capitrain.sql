-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 19 nov. 2020 à 15:40
-- Version du serveur :  10.4.11-MariaDB
-- Version de PHP : 7.3.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `capitrain`
--

-- --------------------------------------------------------

--
-- Structure de la table `ip`
--

CREATE TABLE `ip` (
  `id` int(11) NOT NULL,
  `position_id` int(11) DEFAULT NULL,
  `is_shared` tinyint(1) NOT NULL,
  `ip` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ip`
--

INSERT INTO `ip` (`id`, `position_id`, `is_shared`, `ip`, `isp`) VALUES
(1, 1, 1, '1.1.1.1', 'operateur1'),
(2, 2, 1, '1.1.1.2', 'operateur2'),
(3, 3, 0, '1.1.1.3', 'operateur2'),
(4, 4, 1, '1.1.1.4', 'operateur2'),
(5, 5, 0, '1.1.1.5', 'operateur1');

-- --------------------------------------------------------

--
-- Structure de la table `packet_passage`
--

CREATE TABLE `packet_passage` (
  `id` int(11) NOT NULL,
  `ip_id` int(11) NOT NULL,
  `traceroute_id` int(11) NOT NULL,
  `indice` int(11) NOT NULL,
  `next_id` int(11) DEFAULT NULL,
  `previous_id` int(11) DEFAULT NULL,
  `is_ispexchange` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `packet_passage`
--

INSERT INTO `packet_passage` (`id`, `ip_id`, `traceroute_id`, `indice`, `next_id`, `previous_id`, `is_ispexchange`) VALUES
(2, 2, 1, 1, NULL, NULL, 0),
(3, 3, 1, 2, NULL, 2, 0),
(4, 4, 1, 3, NULL, 3, 0),
(5, 1, 2, 0, NULL, NULL, 0),
(6, 5, 2, 1, NULL, 5, 0),
(7, 2, 2, 2, NULL, 6, 1),
(8, 3, 2, 3, NULL, 7, 0),
(9, 2, 1, 0, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `position`
--

CREATE TABLE `position` (
  `id` int(11) NOT NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `position`
--

INSERT INTO `position` (`id`, `longitude`, `latitude`, `country`, `city`, `region`) VALUES
(1, -118.2436849, 34.0522342, 'test', 'A', 'Bretagne'),
(2, -118.2436849, 34.0522342, 'test', 'B', 'Bretagne'),
(3, 151.2092955, -33.8688197, 'Australia', 'C', 'test'),
(4, -118.2436849, 34.0522342, 'test', 'D', 'test'),
(5, -118.2436849, 34.0522342, 'test', 'B', 'Bretagne');

-- --------------------------------------------------------

--
-- Structure de la table `traceroute`
--

CREATE TABLE `traceroute` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `traceroute`
--

INSERT INTO `traceroute` (`id`, `date`) VALUES
(1, '2020-11-19 10:21:59'),
(2, '2020-11-19 10:21:59');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ip`
--
ALTER TABLE `ip`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_A5E3B32DDD842E46` (`position_id`);

--
-- Index pour la table `packet_passage`
--
ALTER TABLE `packet_passage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_64468A43AA23F6C8` (`next_id`),
  ADD UNIQUE KEY `UNIQ_64468A432DE62210` (`previous_id`),
  ADD KEY `IDX_64468A43A03F5E9F` (`ip_id`),
  ADD KEY `IDX_64468A4351F81614` (`traceroute_id`);

--
-- Index pour la table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `traceroute`
--
ALTER TABLE `traceroute`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `ip`
--
ALTER TABLE `ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `packet_passage`
--
ALTER TABLE `packet_passage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `position`
--
ALTER TABLE `position`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `traceroute`
--
ALTER TABLE `traceroute`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ip`
--
ALTER TABLE `ip`
  ADD CONSTRAINT `FK_A5E3B32DDD842E46` FOREIGN KEY (`position_id`) REFERENCES `position` (`id`);

--
-- Contraintes pour la table `packet_passage`
--
ALTER TABLE `packet_passage`
  ADD CONSTRAINT `FK_64468A432DE62210` FOREIGN KEY (`previous_id`) REFERENCES `packet_passage` (`id`),
  ADD CONSTRAINT `FK_64468A4351F81614` FOREIGN KEY (`traceroute_id`) REFERENCES `traceroute` (`id`),
  ADD CONSTRAINT `FK_64468A43A03F5E9F` FOREIGN KEY (`ip_id`) REFERENCES `ip` (`id`),
  ADD CONSTRAINT `FK_64468A43AA23F6C8` FOREIGN KEY (`next_id`) REFERENCES `packet_passage` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
