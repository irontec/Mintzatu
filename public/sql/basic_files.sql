--
-- Table structure for table `vertedero`
--

DROP TABLE IF EXISTS `vertedero`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `vertedero` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `id_fich` varchar(150) NOT NULL default '',
  `data` mediumblob,
  `borrado` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `concept_ficheros`
--

DROP TABLE IF EXISTS `concept_ficheros`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `concept_ficheros` (
  `id_fich` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `size` varchar(150) default NULL,
  `mimetype` varchar(255) default NULL,
  `borrado` enum('0','1') NOT NULL default '0',
  `estado` enum('0','1') default '0',
  `desc_fich` text,
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_fich`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
