# swgaide-parser


CREATE TABLE `resources` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type_code` varchar(200) DEFAULT NULL,
  `type_name` varchar(255) DEFAULT NULL,
  `cr` varchar(50) DEFAULT NULL,
  `dr` varchar(50) DEFAULT NULL,
  `hr` varchar(50) DEFAULT NULL,
  `ma` varchar(50) DEFAULT NULL,
  `oq` varchar(50) DEFAULT NULL,
  `sr` varchar(50) DEFAULT NULL,
  `ut` varchar(50) DEFAULT NULL,
  `fl` varchar(50) DEFAULT NULL,
  `pe` varchar(50) DEFAULT NULL,
  `timestamp` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=584 DEFAULT CHARSET=utf8;
