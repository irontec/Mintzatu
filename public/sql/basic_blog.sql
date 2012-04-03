--
-- Table structure for table `concept_news_categories`
--

DROP TABLE IF EXISTS `concept_news_categories`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `concept_news_categories` (
  `id_categoria` mediumint(8) unsigned NOT NULL auto_increment,
  `borrado` enum('0','1') NOT NULL default '0',
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `url` varchar(150) default NULL,
  `nombre` varchar(150) default NULL,
  `descripcion` varchar(255) default NULL,
  `estado` enum('0','1') default '0',
  PRIMARY KEY  (`id_categoria`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table `concept_news`
--

DROP TABLE IF EXISTS `concept_news`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `concept_news` (
  `id_news` mediumint(8) unsigned NOT NULL auto_increment,
  `titulo` varchar(150) default NULL,  
  `url` varchar(150) default NULL,
  `resumen` varchar(255) default NULL,
  `texto` text,
  `fecha_news` datetime NOT NULL default '0000-00-00 00:00:00',
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `estado` enum('0','1') NOT NULL default '0',
  `borrado` enum('0','1') NOT NULL default '0',
  `id_usuario` mediumint(8) unsigned default NULL,
  KEY `id_usuario` (`id_usuario`),
  FOREIGN KEY (`id_usuario`) REFERENCES `karma_usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  `id_categoria` mediumint(8) unsigned default NULL,
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `concept_news_categories_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `concept_news_categories` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE,
  PRIMARY KEY  (`id_news`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table `concept_tags`
--


#DROP TABLE IF EXISTS `concept_tags`;
#SET @saved_cs_client     = @@character_set_client;
#SET character_set_client = utf8;
#CREATE TABLE `concept_tags` (
  #`id_tag` mediumint(8) unsigned NOT NULL auto_increment,
  #`borrado` enum('0','1') NOT NULL default '0',
  #`fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  #`url` varchar(150) default NULL,
  #`nombre` varchar(150) default NULL,
  #PRIMARY KEY  (`id_tag`),
  #UNIQUE KEY `url` (`url`)
#) ENGINE=InnoDB DEFAULT CHARSET=utf8;
#SET character_set_client = @saved_cs_client;


--
-- Table structure for table `concept_rel_tags_news`
--


DROP TABLE IF EXISTS `concept_rel_tags_news`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `concept_rel_tags_news` (
  `id_rel` mediumint(8) unsigned NOT NULL auto_increment,
  `id_tag` mediumint(8) unsigned NOT NULL,
  `id_news` mediumint(8) unsigned NOT NULL,
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_rel`),
  KEY `id_news` (`id_news`),
  FOREIGN KEY (`id_news`) REFERENCES `concept_news` (`id_news`) ON DELETE CASCADE ON UPDATE CASCADE,  
  KEY `id_tag` (`id_tag`),
  FOREIGN KEY (`id_tag`) REFERENCES `concept_tags` (`id_tag`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
