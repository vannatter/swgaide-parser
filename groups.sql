
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table group_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `group_items`;

CREATE TABLE `group_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int DEFAULT NULL,
  `type_code` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

LOCK TABLES `group_items` WRITE;
/*!40000 ALTER TABLE `group_items` DISABLE KEYS */;

INSERT INTO `group_items` (`id`, `group_id`, `type_code`)
VALUES
	(1,1,'doirn'),
	(2,1,'dvirn'),
	(3,1,'kairn'),
	(4,1,'plirn'),
	(5,1,'axirn'),
	(6,1,'brirn'),
	(7,1,'poirn'),
	(8,1,'bistl'),
	(9,1,'hastl'),
	(10,1,'cbstl'),
	(11,1,'nestl'),
	(12,1,'dtstl'),
	(13,1,'dlstl'),
	(14,1,'qustl'),
	(15,1,'custl'),
	(16,1,'thstl'),
	(17,1,'kistl'),
	(18,1,'rhstl');

/*!40000 ALTER TABLE `group_items` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;

INSERT INTO `groups` (`id`, `name`)
VALUES
	(1,'Ferrous Metal');

/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
