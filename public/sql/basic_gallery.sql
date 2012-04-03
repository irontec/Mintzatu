--
-- Table structure for table `concept_gallery_categories`
--

DROP TABLE IF EXISTS `concept_gallery_categories`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `concept_gallery_categories` (
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
-- Table structure for table `concept_img_gallery`
--

DROP TABLE IF EXISTS `concept_img_gallery`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `concept_img_gallery` (
  `id_img` mediumint(8) unsigned NOT NULL auto_increment,
  `nombre_alt_img` varchar(150) default NULL,  
  `desc_img` varchar(255) default NULL,
  `img_binario` mediumblob,
  `size_img` varchar(20) default NULL,
  `nombre_img` varchar(150) default NULL,
  `fecha_img` datetime NOT NULL default '0000-00-00 00:00:00',
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `estado` enum('0','1') NOT NULL default '0',
  `borrado` enum('0','1') NOT NULL default '0',
  `id_usuario` mediumint(8) unsigned default NULL,
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `karma_usuarios_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `karma_usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  `id_categoria` mediumint(8) unsigned default NULL,
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `concept_gallery_categories_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `concept_gallery_categories` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE,
  PRIMARY KEY  (`id_img`),
  UNIQUE KEY `url` (`nombre_img`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table `concept_tags`
--


DROP TABLE IF EXISTS `concept_tags`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `concept_tags` (
  `id_tag` mediumint(8) unsigned NOT NULL auto_increment,
  `borrado` enum('0','1') NOT NULL default '0',
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `url` varchar(150) default NULL,
  `nombre` varchar(150) default NULL,
  PRIMARY KEY  (`id_tag`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table `concept_rel_tags_img`
--


DROP TABLE IF EXISTS `concept_rel_tags_img`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `concept_rel_tags_img` (
  `id_rel` mediumint(8) unsigned NOT NULL auto_increment,
  `id_tag` mediumint(8) unsigned NOT NULL,
  `id_img` mediumint(8) unsigned NOT NULL,
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_rel`),
  KEY `id_img` (`id_img`),
  CONSTRAINT `concept_img_gallery_ibfk_1` FOREIGN KEY (`id_img`) REFERENCES `concept_img_gallery` (`id_img`) ON DELETE CASCADE ON UPDATE CASCADE,  
  KEY `id_tag` (`id_tag`),
  CONSTRAINT `concept_tags_ibfk_1` FOREIGN KEY (`id_tag`) REFERENCES `concept_tags` (`id_tag`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
